<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Deploy\Model;

use Magento\Deploy\Model\Process\ResourceConnectionProvider;

class ProcessQueueManager
{
    /**
     * @var ProcessTask[]
     */
    private $tasksQueue = [];

    /**
     * @var ProcessTask[]
     */
    private $processTaskMap = [];

    /**
     * @var int
     */
    private $maxProcesses = 4;

    /**
     * @var ProcessManager
     */
    private $processManager;

    /**
     * @var ResourceConnectionProvider
     */
    private $resourceConnectionProvider;

    /**
     * @param ProcessManager $processManager
     * @param ResourceConnectionProvider $resourceConnectionProvider
     */
    public function __construct(ProcessManager $processManager, ResourceConnectionProvider $resourceConnectionProvider)
    {
        $this->processManager = $processManager;
        $this->resourceConnectionProvider = $resourceConnectionProvider;
    }

    /**
     * @param int $maxProcesses
     * @return void
     */
    public function setMaxProcessesAmount($maxProcesses)
    {
        $this->maxProcesses = $maxProcesses;
    }

    /**
     * @param callable $task
     * @param callable[] $dependentTasks
     * @return void
     */
    public function addTaskToQueue(callable $task, $dependentTasks = [])
    {
        $dependentTasks = array_map(function (callable $task) {
            return $this->createTask($task);
        }, $dependentTasks);

        $task = $this->createTask($task, $dependentTasks);
        $this->tasksQueue[$task->getId()] = $task;
    }

    /**
     * Process tasks queue
     * @return int
     */
    public function process()
    {
        $processQueue = [];
        $this->internalQueueProcess($this->tasksQueue, $processQueue);

        $returnStatus = null;
        while (count($this->processManager->getProcesses()) > 0) {
            foreach ($this->processManager->getProcesses() as $process) {
                if ($process->isCompleted()) {
                    $dependedTasks = isset($this->processTaskMap[$process->getPid()])
                        ? $this->processTaskMap[$process->getPid()]
                        : [];

                    $this->processManager->delete($process);
                    $returnStatus |= $process->getStatus();

                    $this->internalQueueProcess(array_merge($processQueue, $dependedTasks), $processQueue);

                    if (count($this->processManager->getProcesses()) >= $this->maxProcesses) {
                        break 1;
                    }
                }
            }
            usleep(5000);
        }
        $this->resourceConnectionProvider->get()->closeConnection();

        return $returnStatus;
    }

    /**
     * @param ProcessTask[] $taskQueue
     * @param ProcessTask[] $processQueue
     * @return void
     */
    private function internalQueueProcess($taskQueue, &$processQueue)
    {
        $processNumber = count($this->processManager->getProcesses());
        foreach ($taskQueue as $task) {
            if ($processNumber >= $this->maxProcesses) {
                if (!isset($processQueue[$task->getId()])) {
                    $processQueue[$task->getId()] = $task;
                }
            } else {
                unset($processQueue[$task->getId()]);
                $this->fork($task);
                $processNumber++;
            }
        }
    }

    /**
     * @param callable $handler
     * @param array $dependentTasks
     * @return ProcessTask
     */
    private function createTask($handler, $dependentTasks = [])
    {
        return new ProcessTask($handler, $dependentTasks);
    }

    /**
     * @param ProcessTask $task
     * @return void
     */
    private function fork(ProcessTask $task)
    {
        $process = $this->processManager->fork($task->getHandler());
        if ($task->getDependentTasks()) {
            $pid = $process->getPid();
            foreach ($task->getDependentTasks() as $dependentTask) {
                $this->processTaskMap[$pid][$dependentTask->getId()] = $dependentTask;
            }
        }
    }
}

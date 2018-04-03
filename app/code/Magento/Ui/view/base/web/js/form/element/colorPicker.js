
/**
 * @api
 */
define([
    'jquery',
    'underscore',
    'uiLayout',
    'mage/translate',
    'Magento_Ui/js/form/element/abstract',
    'spectrum'
], function ($, _, layout, $t, Abstract, spectrum) {
    'use strict';

    var defaultColorPalette = [
        ['rgb(0,0,0)', 'rgb(52,52,52)', 'rgb(83,83,83)', 'rgb(135,135,135)', 'rgb(193,193,193)', 'rgb(234,234,234)', 'rgb(240,240,240)', 'rgb(255,255,255)'],
        ['rgb(252,0,9)', 'rgb(253,135,10)', 'rgb(255,255,13)', 'rgb(35,255,9)', 'rgb(33,255,255)', 'rgb(0,0,254)', 'rgb(132,0,254)', 'rgb(251,0,255)'],
        ['rgb(240,192,194)', 'rgb(251,223,194)', 'rgb(255,241,193)', 'rgb(210,230,201)', 'rgb(199,217,220)', 'rgb(197,219,240)', 'rgb(208,200,227)', 'rgb(229,199,212)'],
        ['rgb(228,133,135)', 'rgb(246,193,139)', 'rgb(254,225,136)', 'rgb(168,208,152)', 'rgb(146,184,190)', 'rgb(143,184,227)', 'rgb(165,148,204)', 'rgb(202,147,175)'],
        ['rgb(214,78,83)', 'rgb(243,163,88)', 'rgb(254,211,83)', 'rgb(130,187,106)', 'rgb(99,149,159)', 'rgb(93,150,211)', 'rgb(123,100,182)', 'rgb(180,100,142)'],
        ['rgb(190,0,5)', 'rgb(222,126,44)', 'rgb(236,183,39)', 'rgb(89,155,61)', 'rgb(55,110,123)', 'rgb(49,112,185)', 'rgb(83,55,150)', 'rgb(147,55,101)'],
        ['rgb(133,0,3)', 'rgb(163,74,10)', 'rgb(177,127,7)', 'rgb(45,101,23)', 'rgb(18,62,74)', 'rgb(14,62,129)', 'rgb(40,15,97)', 'rgb(95,16,55)'],
        ['rgb(81,0,1)', 'rgb(100,48,7)', 'rgb(107,78,3)', 'rgb(31,63,16)', 'rgb(13,39,46)', 'rgb(10,40,79)', 'rgb(24,12,59)', 'rgb(59,10,36)']
    ];

    return Abstract.extend({

        defaults: {
            colorPickerConfig: {
                chooseText: $t('Apply'),
                cancelText: $t('Cancel'),
                maxSelectionSize: 8,
                clickoutFiresChange: true,
                allowEmpty: true,
                localStorageKey: "magento.spectrum",
                palette: defaultColorPalette
            }
        },

        /**
         * Invokes initialize method of parent class,
         * contains initialization logic
         */
        initialize: function () {
            this._super();
            this.colorPickerConfig.value = this.value;
            // $(document).on('click', '.sp-replacer', function () {
            //     this.focused(true);
            // });
            return this;
        },

        // setFocus: function () {
        //     this.focused(true);
        // }

    });
});
 
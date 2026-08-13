(function($) {
    "use strict";

    CFKEF.initConditionalEditorLabels({
        modes: {
            hide: { repeater: 'Hide fields if' },
            show: { repeater: 'Show fields if' },
        },
        onAfterInit: function (mainWrp) {
            const logicMeet = mainWrp.find('.elementor-control-cfef_logic_meet .elementor-control-content .elementor-control-field .elementor-control-input-wrapper select option')[1];
            if (logicMeet && !logicMeet.innerHTML.includes('(PRO)')) {
                logicMeet.innerHTML += ' (PRO)';
                logicMeet.setAttribute('disabled', 'disabled');
                logicMeet.style.backgroundColor = '#00000015';
            }

            const controlsWrp = mainWrp.find('.elementor-control-cfef_logic_field_id')?.closest('.elementor-repeater-row-controls');
            controlsWrp?.addClass('editable');
        },
    });

    CFKEF.initConditionalDynamicTags();

    $( document ).ready( function () {
        jQuery(document).on('mouseenter', '.elementor-repeater-row-controls.editable .elementor-control-cfef_repeater_data .elementor-repeater-fields-wrapper', function() {
            const optionsFields = jQuery('.elementor-repeater-row-controls.editable .elementor-control-cfef_repeater_data .elementor-repeater-fields-wrapper .elementor-repeater-fields .elementor-repeater-row-controls .elementor-control-cfef_logic_field_is .elementor-control-content .elementor-control-field .elementor-control-input-wrapper select option');
            for(let i = 0;i < optionsFields.length;i++){
                if (optionsFields[i].value !== "==" && optionsFields[i].value !== "!=" && optionsFields[i].value !== ">" && optionsFields[i].value !== "<" && !optionsFields[i].innerHTML.includes('(PRO)')) {
                    optionsFields[i].innerHTML += ' (PRO)';
                    optionsFields[i].setAttribute('disabled', 'disabled');
                    optionsFields[i].style.backgroundColor = '#00000015';
                    }
            }
        });
    })
})(jQuery);

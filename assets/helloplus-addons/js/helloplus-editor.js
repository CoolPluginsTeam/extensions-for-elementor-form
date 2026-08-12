(function ($) {
    "use strict";
    // Hello Plus conditions tab labels (no Cool Form submit-button controls).
    $(document).ready(function () {
        jQuery(document).on('click', '.elementor-control-form_fields_conditions_tab', (e) => {
            const mainWrp = jQuery(e.currentTarget).closest('.elementor-repeater-row-controls.editable');
            const addBtn = mainWrp.find('.elementor-control-cfef_repeater_data .elementor-repeater-add');
            jQuery(addBtn).text('+ Add Conditions')

            const RawHTMLBox = mainWrp.find('.cfef_custom_html');
            RawHTMLBox.map((index, val) => {
                val.style.display = 'none';
            })

            const logicMode = mainWrp.find('.elementor-control-cfef_logic_mode .elementor-control-content .elementor-control-field .elementor-control-input-wrapper .elementor-choices input');
            const controlerTitle = mainWrp.find('.elementor-control-cfef_logic_mode .elementor-control-content .elementor-control-field .elementor-control-title')
            const showHideFieldLableText = mainWrp.find('.elementor-control-cfef_repeater_data .elementor-control-content label span')
            function updateLabel() {
                for (let i = 0; i < logicMode.length; i++) {
                    if (logicMode[i].checked) {
                        if (logicMode[i].value === 'hide') {
                            showHideFieldLableText[0].textContent = 'Hide fields if';
                            controlerTitle[0].textContent = "Hide Fields";
                        } else if (logicMode[i].value === "show") {
                            showHideFieldLableText[0].textContent = 'Show fields if';
                            controlerTitle[0].textContent = "Show Fields";
                        } else if (logicMode[i].value === "enable") {
                            showHideFieldLableText[0].textContent = 'Enable fields if';
                            controlerTitle[0].textContent = "Enable Fields";
                        } else if (logicMode[i].value === "disable") {
                            showHideFieldLableText[0].textContent = 'Disable fields if';
                            controlerTitle[0].textContent = "Disable Fields";
                        }
                        break;
                    }
                }
            }

            updateLabel();
            logicMode.on('change', updateLabel);
        });

        $(document).mouseup(function (e) {
            var container = $(".cfef-dynamic-tag");

            if (!container.is(e.target) && container.has(e.target).length === 0) {
                container.hide();
            }
        });
    })

})(jQuery);

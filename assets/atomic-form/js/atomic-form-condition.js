(function ($) {
    "use strict";

    function getFieldGroup(form, fieldId) {
        let targetINput= $(form).find(`#${fieldId}`);
        return targetINput;
    }

    function getFieldContainer(targetField) {
        var fieldContainer = targetField.closest(".cfef-atomic-field-group");
        return fieldContainer.length ? fieldContainer : targetField;
    }

    function getFieldControls(targetField) {
        var fieldContainer = getFieldContainer(targetField);
        var controls = fieldContainer.find("input, select, textarea");
        if (!controls.length && targetField.is("input, select, textarea")) {
            controls = targetField;
        }
        return controls;
    }

    function logixFixedRequiredHidden(targetField) {
        CFKEF.applyRequiredDemoOnControls(getFieldControls(targetField), {
            fileUploadClass: 'e-form-file-upload-base',
            telWithPlus: true,
        });
    }

    function logixFixedRequiredShow(targetField) {
        CFKEF.restoreRequiredDemoOnControls(getFieldControls(targetField), {
            fileUploadClass: 'e-form-file-upload-base',
        });
    }

    function getFieldValue(form, fieldId) {
        var fieldINput= getFieldGroup(form, fieldId);
        let value= "";

        if(fieldINput.length > 0) {

            if(fieldINput.attr('type') === 'checkbox' || fieldINput.attr('type') === 'radio') {
                if(fieldINput.is(':checked')) {
                    value = fieldINput.val();
                }else{
                    value = "";
                }
                return value;
            }else if(fieldINput.attr('type') === 'date') {
                return CFKEF.formatDateToMDY(fieldINput.val());
            }
            else if(fieldINput.attr('type') === 'time') {
                return CFKEF.convertTimeTo12Hour(fieldINput.val());
            }
            else{
                value = fieldINput.val();
                return value;
            }

        }
        return value;
        
    }

    function evaluateLogic(form, logicValue) {
        return CFKEF.evaluateLogicRules(logicValue, function (fieldId) {
            return getFieldValue(form, fieldId);
        });
    }

    function applyFieldLogic(form, targetFieldId, logicValue) {
        var targetField = getFieldGroup(form, targetFieldId);
        if (!targetField.length) {
            return;
        }
        var shouldShowField = evaluateLogic(form, logicValue);
        var fieldContainer = getFieldContainer(targetField);

        if (shouldShowField) {
            logixFixedRequiredShow(targetField);
            showFieldLabel(form, targetFieldId);
            fieldContainer.removeClass("cfef-hidden");
        } else {
            logixFixedRequiredHidden(targetField);
            hideFieldLabel(form, targetFieldId);
            fieldContainer.addClass("cfef-hidden");
        }
    }

    function showFieldLabel(form, targetFieldId) {
        let label_widget = $(form).find(`label[for="${targetFieldId}"]`);
        if(label_widget.length > 0) {
            label_widget.removeClass('cfef-hidden');
        }
    }

    function hideFieldLabel(form, targetFieldId) {
        let label_widget = $(form).find(`label[for="${targetFieldId}"]`);
        if(label_widget.length > 0) {
            label_widget.addClass('cfef-hidden');
        }
    }

    function readAtomicLogic(form) {
        var merged = {};
        $(form).find(".cfef-atomic-field-logic").each(function () {
            var raw = $(this).html();
            if (!raw) {
                return;
            }
            try {
                var data = JSON.parse(raw);
                merged = $.extend(true, {}, merged, data);
            } catch (e) {
                // Ignore invalid field payloads so one bad rule does not break the form.
            }
        });
        return merged;
    }

    function runAtomicLogic(form) {
        var logicMap = readAtomicLogic(form);
        $.each(logicMap, function (index, singleLogic) {
            $.each(singleLogic, function (targetFieldId, logicValue) {
                applyFieldLogic(form, targetFieldId, logicValue);
            });
        });
    }

    function getAtomicFormContainerFromElement(el) {
        return $(el).closest(".e-form-base");
    }

    function initAtomicForms() {

        $(".e-form-base").each(function () {
            var form = (this);
            filterTemplateLogic(form);
            if (form.length) {
                runAtomicLogic(form);
            }
        });
    }

    function filterTemplateLogic(form) {

        var $form = $(form);
        var all_fields_logic = [];

        if ($form.attr("template-extracted") === "true") {
            return;
        }

        $form.find(".cfef-atomic-field-group").each(function () {
            var field = $(this);
            var template = field.find("template").first();

            if (template.length) {
                try {
                    all_fields_logic.push(JSON.parse(template.html()));
                } catch (e) {
                    // Ignore malformed template payloads.
                }
            }
        });

        $form.find(".cfef-atomic-field-group").each(function () {
            var fieldGroup = $(this);
            var fieldControl = fieldGroup.find("input[data-e-type='widget'], select[data-e-type='widget'], textarea[data-e-type='widget']").first();

            // Fallback: keep compatibility if data-e-type is absent on some controls.
            if (!fieldControl.length) {
                fieldControl = fieldGroup.find("input, select, textarea").first();
            }

            if (!fieldControl.length) {
                return;
            }

            var country_wrapper = fieldControl.closest("div.ccfef-wrapper");
            var mask_wrapper = fieldControl.closest("div.ccfef-mask-wrapper");

            if (country_wrapper.length === 0 && mask_wrapper.length === 0) {
                fieldGroup.replaceWith(fieldControl);
            }
        });

        var template_tag_logic = $("<template>", {
            class: "cfef_logic_data_js cfef-atomic-field-logic cfef-hidden",
            html: JSON.stringify(all_fields_logic)
        });
        $form.find("template.cfef_logic_data_js").remove();
        $form.append(template_tag_logic);
        $form.attr("template-extracted", "true");
    }

    $(document).ready(function () {
        initAtomicForms();
    });

    $(document).on("elementor/popup/show", function () {
        initAtomicForms();
    });

    window.addEventListener("elementor/frontend/init", function () {
        initAtomicForms();
    });

    $("body").on("input change", ".e-form-base input, .e-form-base select, .e-form-base textarea", function () {
        var form = getAtomicFormContainerFromElement(this);
        if (form.length) {
            runAtomicLogic(form);
        }
    });

    $("form.e-form-base button[type='submit']").on("click", function (e ) {
        var form = getAtomicFormContainerFromElement(e.target);
        if (form.length) {
            runAtomicLogic(form);
        }
    });
})(jQuery);

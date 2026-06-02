(function ($) {
    "use strict";

    function decodeHTMLEntities(text) {
        if (text == null) {
            return "";
        }
        var str = String(text);
        var doc = new DOMParser().parseFromString(str, "text/html");
        return doc.body ? doc.body.textContent : "";
    }

    function checkFieldLogic(compareFieldValue, conditionOperation, compareValue) {
        var valueA = decodeHTMLEntities(compareFieldValue).trim();
        var valueB = decodeHTMLEntities(compareValue).trim();
        var values = valueA.split(",").map(function (v) {
            return v.trim();
        });
        var matchFound = values.indexOf(valueB) !== -1;
        switch (conditionOperation) {
            case "==":
                return matchFound && valueA !== "";
            case "!=":
                return !matchFound && valueA !== "";
            case "&gt;":
                return parseInt(valueA, 10) > parseInt(valueB, 10);
            case "&lt;":
                return parseInt(valueA, 10) < parseInt(valueB, 10);
            default:
                return false;
        }
    }

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

    function isRequiredControl(control) {
        return control.prop("required") || control.attr("required") !== undefined || control.attr("aria-required") === "true";
    }

    function getDemoValueForControl(control) {
        var nodeName = (control.prop("nodeName") || "").toLowerCase();
        var type = (control.attr("type") || "").toLowerCase();

        if (nodeName === "textarea") {
            return "cool_plugins";
        }
        if (nodeName === "select") {
            return null;
        }
        if (type === "email") {
            return "cool_plugins@abc.com";
        }
        if (type === "url") {
            return "https://testing.com";
        }
        if (type === "tel") {
            return "+1234567890";
        }
        if (type === "number") {
            return "000";
        }
        if (type === "date") {
            return "1003-01-01";
        }
        if (type === "time") {
            return "11:59";
        }
        return "cool23plugins";
    }

    function logixFixedRequiredHidden(targetField) {
        var controls = getFieldControls(targetField);

        controls.each(function () {
            var control = $(this);
            var nodeName = (control.prop("nodeName") || "").toLowerCase();
            var type = (control.attr("type") || "").toLowerCase();

            if (!isRequiredControl(control)) {
                return;
            }

            if (type === "checkbox" || type === "radio") {
                var groupControls = controls.filter('[type="' + type + '"][name="' + control.attr("name") + '"]');
                var checkedControl = groupControls.filter(":checked");
                if (typeof control.data("cfefOriginalCheckedValue") === "undefined") {
                    control.data("cfefOriginalCheckedValue", checkedControl.length ? checkedControl.val() : "");
                }
                if (!checkedControl.length && groupControls.length) {
                    var firstControl = groupControls.first();
                    firstControl.prop("checked", true);
                    firstControl.data("cfefDemoApplied", true);
                } else if (checkedControl.length) {
                    groupControls.prop("checked", false);
                    var firstChecked = groupControls.first();
                    firstChecked.prop("checked", true);
                    firstChecked.data("cfefDemoApplied", true);
                }
                return;
            }

            if (nodeName === "select") {
                if (typeof control.data("cfefOriginalValue") === "undefined") {
                    control.data("cfefOriginalValue", control.val() || "");
                }
                var firstOption = control.find("option[value!='']").first();
                if (!firstOption.length) {
                    firstOption = control.find("option").first();
                }
                if (firstOption.length) {
                    control.val(firstOption.val());
                    control.data("cfefDemoApplied", true);
                }
                return;
            }

            if(control.hasClass('e-form-file-upload-base')){
                const firstType = control.attr('accept').split(',')[0];
                const fileName = `${my_script_vars.pluginConstant}assets/images/placeholder.${firstType}`;
                const defaultImage = new File([], fileName, { type: 'image/png' });
                const container = new DataTransfer();
                container.items.add(defaultImage);
                control[0].files = container.files;
                return;
            }

            if (typeof control.data("cfefOriginalValue") === "undefined") {
                control.data("cfefOriginalValue", control.val() || "");
            }
            control.val(getDemoValueForControl(control));
            control.data("cfefDemoApplied", true);
        });
    }

    function logixFixedRequiredShow(targetField) {
        var controls = getFieldControls(targetField);

        controls.each(function () {
            var control = $(this);

            if(control.hasClass('e-form-file-upload-base')){
                const firstType = control.attr('accept').split(',')[0];
                const fileName = `${my_script_vars.pluginConstant}assets/images/placeholder.${firstType}`;
                const inputValue=control.val(); 
                if(inputValue.indexOf(fileName) !== -1){
                    control.val('');
                }

                return;
            }

            if (control.data("cfefDemoApplied") !== true) {
                return;
            }

            var type = (control.attr("type") || "").toLowerCase();
            if (type === "checkbox" || type === "radio") {
                var groupName = control.attr("name");
                if (groupName) {
                    var groupControls = controls.filter('[type="' + type + '"][name="' + groupName + '"]');
                    var originalCheckedValue = control.data("cfefOriginalCheckedValue");
                    groupControls.prop("checked", false);
                    if (originalCheckedValue) {
                        groupControls.filter('[value="' + originalCheckedValue + '"]').first().prop("checked", true);
                    }
                } else {
                    control.prop("checked", false);
                }
                control.removeData("cfefOriginalCheckedValue");
            } else {
                var originalValue = control.data("cfefOriginalValue");
                control.val(typeof originalValue === "undefined" ? "" : originalValue);
                control.removeData("cfefOriginalValue");
            }
            control.removeData("cfefDemoApplied");
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

                value = fieldINput.val();
                let formattedDate = value.replace(/(\d{4})-(\d{2})-(\d{2})/, "$3/$2/$1");
                return formattedDate;
            }else{
                value = fieldINput.val();
                return value;
            }

        }
        return value;
        
    }

    function evaluateLogic(form, logicValue) {
        var displayMode = logicValue.display_mode || "show";
        var fireAction = logicValue.fire_action || "All";

        var logicData = Array.isArray(logicValue.logic_data) ? logicValue.logic_data : [];
        var checks = [];

        $.each(logicData, function (_idx, rule) {
            if (!rule || !rule.cfef_logic_field_id) {
                return;
            }
            var currentValue = getFieldValue(form, rule.cfef_logic_field_id);
            checks.push(checkFieldLogic(currentValue, rule.cfef_logic_field_is, rule.cfef_logic_compare_value));
        });

        if (!checks.length) {
            return false;
        }

        var result = fireAction === "All"
            ? checks.every(function (v) { return v === true; })
            : checks.some(function (v) { return v === true; });

        return displayMode === "show" ? result : !result;
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

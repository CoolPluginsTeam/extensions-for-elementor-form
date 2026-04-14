(function ($) {
    "use strict";

    function decodeHTMLEntities(text) {
        var textArea = document.createElement("textarea");
        textArea.innerHTML = text == null ? "" : String(text);
        return textArea.value;
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
            case "e":
                return valueA === "";
            case "!e":
                return valueA !== "";
            case "c":
                return valueA.indexOf(valueB) !== -1;
            case "!c":
                return valueA !== "" && valueA.indexOf(valueB) === -1;
            case "^":
                return valueA.indexOf(valueB) === 0;
            case "~":
                return valueA.slice(-valueB.length) === valueB;
            case ">":
                return parseInt(valueA, 10) > parseInt(valueB, 10);
            case "<":
                return parseInt(valueA, 10) < parseInt(valueB, 10);
            case ">=":
                return parseInt(valueA, 10) >= parseInt(valueB, 10);
            case "<=":
                return parseInt(valueA, 10) <= parseInt(valueB, 10);
            default:
                return false;
        }
    }

    function getFieldGroup(form, fieldId) {
        let targetINput= $(form).find(`#${fieldId}`);
        return targetINput;
    }

    function getFieldValue(form, fieldId) {
        var fieldINput= getFieldGroup(form, fieldId);

        if(fieldINput.length > 0) {
            let value= fieldINput.val();
            return value;
        }
        return "";
        
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
        if (evaluateLogic(form, logicValue)) {
            targetField.removeClass("cfef-hidden");
        } else {
            targetField.addClass("cfef-hidden");
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
        $.each(logicMap, function (targetFieldId, logicValue) {
            applyFieldLogic(form, targetFieldId, logicValue);
        });
    }

    function getAtomicFormContainerFromElement(el) {
        return $(el).closest(".e-form-base");
    }

    function initAtomicForms() {

        $(".e-form-base").each(function () {
            var form = (this);
            if (form.length) {
                runAtomicLogic(form);
            }
        });
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
})(jQuery);

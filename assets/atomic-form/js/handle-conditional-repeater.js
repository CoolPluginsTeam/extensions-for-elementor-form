(function ($) {
    "use strict";


    function saveCondition(textarea_logic_repeater) {
        let conditions = [];

        $(document).find('.cfef-conditional-repeater [data-repeater-item]').each(function () {
            let row = $(this);

            conditions.push({
                cfef_logic_field_id: row.find('input[name="cfef_logic_field_id"]').val() || '',
                cfef_logic_field_is: row.find('select[name="cfef_logic_field_is"]').val() || '==',
                cfef_logic_compare_value: row.find('input[name="cfef_logic_compare_value"]').val() || ''
            });
        });

        let conditionsJson = JSON.stringify(conditions);

        $('.cfef-conditional-popup').attr('data-conditions-json', conditionsJson).removeClass('is-open');
        window.cfefConditionalConditions = conditions;

        $(document).trigger('cfef:conditions:saved', [conditions, conditionsJson]);

        let textarea_logic_repeater_field = textarea_logic_repeater[0];

        const fieldPrototype = Object.getPrototypeOf(textarea_logic_repeater_field);
        const valueDescriptor = fieldPrototype
            ? Object.getOwnPropertyDescriptor(fieldPrototype, 'value')
            : null;
        const nativeSetter = valueDescriptor && valueDescriptor.set ? valueDescriptor.set : null;

        if (nativeSetter) {
            nativeSetter.call(textarea_logic_repeater_field, conditionsJson);
        }

        textarea_logic_repeater_field.dispatchEvent(new Event('input', { bubbles: true }));
    }

    function escapeAttr(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function getConditionRowMarkup(condition) {
        const item = condition || {};
        const fieldId = escapeAttr(item.cfef_logic_field_id);
        const fieldIs = item.cfef_logic_field_is || '==';
        const compareValue = escapeAttr(item.cfef_logic_compare_value);

        const isEqualSelected = fieldIs === '==' ? " selected" : "";
        const isNotEqualSelected = fieldIs === '!=' ? " selected" : "";
        const isGreaterThanSelected = fieldIs === '>' ? " selected" : "";
        const isLessThanSelected = fieldIs === '<' ? " selected" : "";

        return "<div data-repeater-item class='cfef-conditional-popup-row'>" +
            "<input type='text' class='cfef-conditional-control' name='cfef_logic_field_id' placeholder='Field ID' value=\"" + fieldId + "\">" +
            "<span class='cfef-conditional-select-wrap'>" +
            "<select class='cfef-conditional-control' name='cfef_logic_field_is'>" +
            "<option value='=='" + isEqualSelected + ">is equal ( == )</option>" +
            "<option value='!='" + isNotEqualSelected + ">is not equal (!=)</option>" +
            "<option value='>'" + isGreaterThanSelected + ">greater than (>)</option>" +
            "<option value='<'" + isLessThanSelected + ">less than (<)</option>" +
            "</select>" +
            "</span>" +
            "<input type='text' class='cfef-conditional-control' name='cfef_logic_compare_value' placeholder='Value to compare' value=\"" + compareValue + "\">" +
            "<button type='button' data-repeater-delete class='cfef-conditional-row-delete' aria-label='Remove condition'>&times;</button>" +
            "</div>";
    }


    function removeCondition(e) {
        e.preventDefault();
        let targetBtn = $(e.target);
        let targetRow = targetBtn.closest('.cfef-conditional-popup-row');
        targetRow.remove();
    }

    function addCondition(e) {
        e.preventDefault();
        let repeaterList = $(document).find('.cfef-conditional-repeater [data-repeater-list="conditions"]');
        let firstRow = repeaterList.find('[data-repeater-item]:first');

        if (!repeaterList.length) {
            return;
        }

        if (!firstRow.length) {
            repeaterList.append(getConditionRowMarkup());
            return;
        }

        let newRow = firstRow.clone();
        newRow.find('input').val('');
        newRow.find('select').prop('selectedIndex', 0);
        repeaterList.append(newRow);
    }


    $(document).on('click', '.cfef-conditional-row-delete', function (e) {
        e.preventDefault();
        removeCondition(e);
    });

    $(document).on('click', '.cfef-conditional-add-btn', function (e) {
        e.preventDefault();
        addCondition(e);
    });


    function initConditionalRepeater(isEmpty) {
        const repeaterForm = $('.cfef-conditional-repeater');

        if (!repeaterForm.length || typeof repeaterForm.repeater !== 'function') {
            return;
        }

        repeaterForm.repeater({
            initEmpty: !!isEmpty,
            show: function () {
                $(this).hide().fadeIn(120);
            },
            hide: function (deleteElement) {
                $(this).fadeOut(120, deleteElement);
            }
        });
    }

    function bindConditionalPopupEvents() {
        $(document).off('click.cfefOpenPopup', '.cfef-repeater-data-control-button');
        $(document).on('click.cfefOpenPopup', '.cfef-repeater-data-control-button', function (e) {
            e.preventDefault();
            let targetButton = $(e.target);
            let parentOfControl = targetButton.parent().parent();
            if(parentOfControl.find('span[data-type="settings-field"] label:contains("Repeater Data")').length > 0) {
                let textarea_logic_repeater = parentOfControl.find('span[data-type="settings-field"] textarea[aria-invalid="false"]');

                if ($('.cfef-conditional-popup').length) {
                    $(document).find('.cfef-conditional-popup').remove();
                }

                createConditionalPopup(textarea_logic_repeater);
                $('.cfef-conditional-popup').addClass('is-open');

              }
        });

        $(document).off('click.cfefClosePopup', '.cfef-conditional-popup-close');
        $(document).on('click.cfefClosePopup', '.cfef-conditional-popup-close', function (e) {
            e.preventDefault();
            if ($('.cfef-conditional-popup').length) {
                $(document).find('.cfef-conditional-popup').remove();
            }
        });

        $(document).off('click.cfefSavePopup', '.cfef-conditional-save-close');
        $(document).on('click.cfefSavePopup', '.cfef-conditional-save-close', function (e) {
            e.preventDefault();
            const textareaLogicRepeater = $(document).find('span[data-type="settings-field"] textarea[aria-invalid="false"]').last();
            if (textareaLogicRepeater.length) {
                saveCondition(textareaLogicRepeater);
            }
        });

        $(document).off('mousedown.cfefSelectOpen', '.cfef-conditional-select-wrap select');
        $(document).off('keydown.cfefSelectOpen', '.cfef-conditional-select-wrap select');
        $(document).off('blur.cfefSelectOpen', '.cfef-conditional-select-wrap select');
        $(document).off('change.cfefSelectOpen', '.cfef-conditional-select-wrap select');
        $(document).on('mousedown.cfefSelectOpen', '.cfef-conditional-select-wrap select', function () {
            var $wrap = $(this).closest('.cfef-conditional-select-wrap');
            /* Closing by clicking the select again does not blur or change — toggle here */
            if ($wrap.hasClass('cfef-select-menu-open')) {
                $wrap.removeClass('cfef-select-menu-open');
            } else {
                $wrap.addClass('cfef-select-menu-open');
            }
        });
        $(document).on('keydown.cfefSelectOpen', '.cfef-conditional-select-wrap select', function (e) {
            if (e.key === 'Escape') {
                $(this).closest('.cfef-conditional-select-wrap').removeClass('cfef-select-menu-open');
                return;
            }
            var openKeys = ['ArrowDown', 'ArrowUp', ' ', 'Enter'];
            if (openKeys.indexOf(e.key) !== -1) {
                $(this).closest('.cfef-conditional-select-wrap').addClass('cfef-select-menu-open');
            }
        });
        $(document).on('blur.cfefSelectOpen', '.cfef-conditional-select-wrap select', function () {
            $(this).closest('.cfef-conditional-select-wrap').removeClass('cfef-select-menu-open');
        });
        $(document).on('change.cfefSelectOpen', '.cfef-conditional-select-wrap select', function () {
            $(this).closest('.cfef-conditional-select-wrap').removeClass('cfef-select-menu-open');
        });
    }

    function createConditionalPopup(textarea_logic_repeater) {


        if ($('.cfef-conditional-popup').length) {
            return;
        }

        let parsedConditions = [];
        let repeaterRowsMarkup = "";


        try {
            const rawValue = textarea_logic_repeater.val();
            const decoded = rawValue ? JSON.parse(rawValue) : [];
            if (Array.isArray(decoded)) {
                parsedConditions = decoded.filter(function (item) {
                    return item && typeof item === 'object';
                });
            }
        } catch (error) {
            parsedConditions = [];
        }

        if (parsedConditions.length) {
            repeaterRowsMarkup = parsedConditions.map(function (condition) {
                return getConditionRowMarkup(condition);
            }).join("");
        }

        let popup = "<div class='cfef-conditional-popup'>" +
            "<div class='cfef-conditional-popup-dialog'>" +
            "<div class='cfef-conditional-popup-header'>" +
            "<span class='cfef-conditional-popup-title'>DISPLAY CONDITIONS</span>" +
            "<button type='button' class='cfef-conditional-popup-close' aria-label='Close'>&times;</button>" +
            "</div>" +
            "<div class='cfef-conditional-popup-content'>" +
            "<div class='cfef-conditional-popup-heading'>Set one or more conditions for this element</div>" +
            "<div class='cfef-conditional-popup-subheading'>It will only appear on your website when all the conditions are met.</div>" +
            "<div class='cfef-conditional-repeater'>" +
            "<div data-repeater-list='conditions'>" +
            repeaterRowsMarkup +
            "</div>" +
            "<button type='button' data-repeater-create class='cfef-conditional-add-btn'>+ Add Condition</button>" +
            "</div>" +
            "</div>" +
            "<div class='cfef-conditional-popup-footer'>" +
            "<button type='button' class='cfef-conditional-save-close'>Save & Close</button>" +
            "</div>" +
            "</div>" +
            "</div>";

        $(document).find('body').append(popup);
        initConditionalRepeater(parsedConditions.length === 0);
    }

    function handleConditionalRepeater(e) {

        setTimeout(() => {

            let lastControlOfConditionsSection = $(document).find('#elementor-editor-wrapper aside#elementor-panel #elementor-panel-inner div[aria-label="Conditions section"]').next().find('span[data-type="settings-field"]:last-child');

            if (lastControlOfConditionsSection.length !== undefined) {
                let parentOfControl = lastControlOfConditionsSection.parent();

                const enableConditionsControl = parentOfControl.find('span').filter(function() {
                    return $(this).text().trim() === 'Enable Conditions';
                });

                if (!enableConditionsControl.find('input[type="checkbox"]').is(':checked')) {

                    if(parentOfControl.find('.cfef-repeater-data-control-btn').length > 0) {
                        parentOfControl.find('.cfef-repeater-data-control-btn').remove();
                    }
                }else{
                    if(parentOfControl.find('.cfef-repeater-data-control-btn').length === 0) {
                        parentOfControl.append('<span data-type="settings-field" class="cfef-repeater-data-control-btn"><button class="cfef-repeater-data-control-button"> + Add / Edit Conditions </button></span>');
                    }
                }

            }
        }, 100);
    }


    function hideRepeaterDataControl() {
        setTimeout(() => {
            const repeaterDataControl = $(document).find('span[data-type="settings-field"]:contains("Repeater Data")');
            if(repeaterDataControl.length > 0) {
                repeaterDataControl.addClass('cfef-hide-repeater-data-control');
            }
        }, 100);
    }


    $(document).on('click', 'div[aria-label="Conditions section"]', function (e) {
        e.preventDefault();
        handleConditionalRepeater();
    });


    $(window).on('elementor/commands/run/after', function (e) {
        handleConditionalRepeater(e);
        hideRepeaterDataControl();
    });
    function initConditionalRepeaterBindings() {
        bindConditionalPopupEvents();
    }

    if (window.elementor) {
        initConditionalRepeaterBindings();
    }

    $(window).on('elementor:init', function () {
        initConditionalRepeaterBindings();
    });
})(jQuery);
/**
 * Shared conditional-fields frontend logic for Cool Form, Hello Plus, and Elementor Pro forms.
 * Platform wrappers call CFKEF.initLogicFrontend({ selectors, flags, formIdMode, logicDataMode, ... }).
 */
window.CFKEF = window.CFKEF || {};

CFKEF.initLogicFrontend = function (opts) {
  opts = opts || {};
  var $ = jQuery;

  var S = Object.assign(
    {
      form: '.cool-form',
      widgetWrap: '.elementor-widget-cool-form',
      fieldGroup: '.cool-form__field-group',
      fieldGroupClass: 'is-field-group',
      fieldGroupPrefix: 'is-field-group-',
      fieldTypePrefix: 'is-field-type-',
      fieldRequiredClass: 'is-field-required',
      acceptanceGroupClass: 'is-field-group-acceptance',
      acceptanceTypeClass: 'is-field-type-acceptance',
      buttonsContainer: '.cool-form__buttons',
      nextButtonWrapper: '.cool-form__buttons__wrapper-next',
      previousButtonWrapper: '.cool-form__buttons__wrapper-previous',
      nextButtonSelector: 'button[class^="cool-form__buttons__wrapper__button-next"]',
      submitGroup: '.cool-form__submit-group',
      submitButtonClick: 'div.cool-form__submit-group button',
      recaptcha: '.cool-form-recaptcha',
      formInputs: '.cool-form input, .cool-form select, .cool-form textarea',
      mdcSelectRoot: '.cool-form .mdc-select',
      stepHiddenClass: 'cool-hidden',
      logicDataSelector: '.cfef_logic_data_js',
      logicDataTemplate: 'template.cfef_logic_data_js',
    },
    opts.selectors || {}
  );

  var flags = Object.assign(
    {
      supportsMdcSelect: false,
      supportsConditionalSteps: false,
      supportsSimpleStepMessage: false,
      supportsMdcFieldControls: false,
      emptyCheckboxFallback: 'cool_plugins',
      extraTelTypeClass: null,
      requiredFieldMarkup: 'mdc', // mdc | elementor
      nextStepsVisibilityMode: 'fieldGroups', // fieldGroups | eFieldStepWalk
      stepResolveMode: 'fieldIsStep', // fieldIsStep | viaButtonsContainer
    },
    opts.flags || {}
  );

  var scriptVarsKey = opts.scriptVarsKey || 'my_script_vars';
  var scriptVarsElementorKey = opts.scriptVarsElementorKey || scriptVarsKey;
  var formIdMode = opts.formIdMode || 'template';
  var logicDataMode = opts.logicDataMode || 'scoped';

  function getScriptVars() {
    return window[scriptVarsKey] || {};
  }
  function getScriptVarsElementor() {
    return window[scriptVarsElementorKey] || window[scriptVarsKey] || {};
  }

  function resolveFormId(form) {
    if (typeof opts.resolveFormId === 'function') {
      return opts.resolveFormId(form, $);
    }
    if (formIdMode === 'elementDataId') {
      return form.closest('.elementor-element').attr('data-id');
    }
    if (formIdMode === 'logicDataByWidgetId') {
      var widgetId = form.attr('data-id');
      return jQuery('#cfef_logic_data_' + widgetId).attr('data-form-id');
    }
    return form.find(S.logicDataTemplate).attr('data-form-id');
  }

  function getLogicDataHtml(form, formId) {
    if (typeof opts.getLogicDataHtml === 'function') {
      return opts.getLogicDataHtml(form, formId, $);
    }
    if (logicDataMode === 'byId') {
      return jQuery('#cfef_logic_data_' + formId).html();
    }
    if (logicDataMode === 'scopedOrById') {
      var html = $(S.logicDataSelector, form).html();
      if ((!html || html === 'undefined') && formId) {
        html = jQuery('#cfef_logic_data_' + formId).html();
      }
      return html;
    }
    return $(S.logicDataSelector, form).html();
  }

  function typeClass(name) {
    return S.fieldTypePrefix + name;
  }

  function groupSel(id) {
    return '.' + S.fieldGroupPrefix + id;
  }

  $(document).ready(function () {

        function checkFieldLogic(compareFieldValue, conditionOperation, compareValue) {
            return CFKEF.checkFieldLogic(compareFieldValue, conditionOperation, compareValue);
        }

        function decodeHTMLEntities(text) {
            return window.CFKEF.decodeHtml(text);
        }
        // function to add hidden class when form load
        function addHiddenClass(form, formId) {
            var logicData = getLogicDataHtml(form, formId || resolveFormId(form));
            if (logicData && logicData !== "undefined") {
                try {
                    logicData = jQuery.parseJSON(logicData);
                    $.each(logicData, function(logic_key, logic_value) {
                        if ($(groupSel(logic_key)).hasClass(typeClass("html"))) {
                                
                            field = $(groupSel(logic_key)).closest("." + S.fieldGroupClass);
                        
                        } else {
                            var field = getFieldMainDivById(logic_key);
                        }
                        if (!field || field.length === 0) {
                            return; // Skip to the next iteration
                        }
                    
                        var displayMode= logic_value.display_mode;
                        var fireAction = logic_value.fire_action;
                        var conditionPassFail = [];
                        $.each(logic_value.logic_data, function(conditional_logic_key, conditional_logic_values) {
                            if(conditional_logic_values.cfef_logic_field_id)
                            {
                            var value_id = getFieldEnteredValue(conditional_logic_values.cfef_logic_field_id, form);
                  
                            conditionPassFail.push(checkFieldLogic(value_id, conditional_logic_values.cfef_logic_field_is, conditional_logic_values.cfef_logic_compare_value));
                        }
                    }); 
                        var conditionResult = fireAction == "ALL" ? conditionPassFail.every(function(fvalue) { return fvalue === true; }) : conditionPassFail.some(function(fvalue) { return fvalue === true; });
                
                        if (displayMode== "show") {
                            if (conditionResult) {
                            } else {
                                if(field.hasClass(typeClass('signature'))){
                                    setTimeout(()=>{
                                        field.addClass("cfef-hidden");
                                        logicFixedRequiredHidden(field, logic_key, logic_value.file_types || '');
                                    },100)
                                }else{
                                    field.addClass("cfef-hidden");
                                    if(field.hasClass(S.fieldRequiredClass)){
                                        logicFixedRequiredHidden(field, logic_key, logic_value.file_types || '');
                                    }
                                }
                            }
                        } else if (displayMode== "hide") {
                            if (conditionResult) {
                                if(field.hasClass(typeClass('signature'))){
                                    setTimeout(()=>{
                                        field.addClass("cfef-hidden");
                                        logicFixedRequiredHidden(field, logic_key, logic_value.file_types || '');
                                    },100)
                                }else{
                                    field.addClass("cfef-hidden");
                                    if(field.hasClass(S.fieldRequiredClass)){
                                        logicFixedRequiredHidden(field, logic_key, logic_value.file_types || '');
                                    }
                                }
                            } else {
                            }
                        }
                    });
                } catch (e) {
                }
            } else {
            }
        }
        
        
        // function to check all the conditions valid or not . and based on that condition show and hide the fields 
        function logicLoad(form, formId) {
            var logicData = getLogicDataHtml(form, formId || resolveFormId(form));
            if (logicData && logicData !== "undefined") {
              try {
                logicData = jQuery.parseJSON(logicData);
                $.each(logicData, function (logic_key, logic_value) {
                  if (
                    $(groupSel(logic_key), form).hasClass(
                      typeClass("html")
                    )
                  ) {
                    field = $(groupSel(logic_key), form).closest("." + S.fieldGroupClass);
                  } else {
                    if (
                      $(groupSel(logic_key), form).hasClass(
                      typeClass("step")
                    )
                    ) {
                      setTimeout(() => {
                        $(groupSel(logic_key), form).addClass("cfef-step-field");
                        var field = getFieldMainDivById(logic_key, form);
                        performFieldLogic(
                          field,
                          logic_value,
                          form,
                          logic_key,
                          formId
                        );
                      }, 500);
                    }
                    var field = getFieldMainDivById(logic_key, form);
                    performFieldLogic(field, logic_value, form, logic_key, formId);
                  }
                  performFieldLogic(field, logic_value, form, logic_key, formId);
                });
              } catch (e) {
              }
            } else {
            
            }
          }    

        function performFieldLogic(field, logic_value, form, logic_key,formId){
            var displayMode= logic_value.display_mode;
            var fireAction = logic_value.fire_action;
            var file_types = logic_value.file_types;
            var conditionPassFail = [];
            $.each(logic_value.logic_data, function(conditional_logic_key, conditional_logic_values) {
                                
                    var dependent_fi = $(groupSel(conditional_logic_values.cfef_logic_field_id), form);
                    
                    if(dependent_fi.hasClass(S.acceptanceGroupClass) || dependent_fi.hasClass(typeClass('acceptance'))){
                            dependent_fi.find('.elementor-field-subgroup .elementor-field-option input').click(()=>{
                                if(dependent_fi.find('.elementor-field-subgroup .elementor-field-option input')[0].checked === true){
                                    dependent_fi.find('.elementor-field-subgroup .elementor-field-option input').val('on') 
                                }else{
                                    dependent_fi.find('.elementor-field-subgroup .elementor-field-option input').val('')
                            }
                            })
                    }


                    var hiddenDiv = dependent_fi[0];
                    var	is_field_hidden = hiddenDiv ? hiddenDiv.classList.contains('cfef-hidden') : hiddenDiv;
                    if(conditional_logic_values.cfef_logic_field_id){               
                        var value_id = getFieldEnteredValue(conditional_logic_values.cfef_logic_field_id, form);
                        var value = is_field_hidden ? false : checkFieldLogic(value_id, conditional_logic_values.cfef_logic_field_is, conditional_logic_values.cfef_logic_compare_value);

                        conditionPassFail.push(value);
            }});
            var conditionResult = fireAction == "All" ? conditionPassFail.every(function(fvalue) { return fvalue === true; }) : conditionPassFail.some(function(fvalue) { return fvalue === true; });

            if (displayMode== "show") {
                if (conditionResult) {
                    if (field.hasClass(typeClass("signature"))) {
                        setTimeout(() => {
                            field.removeClass("cfef-hidden");
                        }, 100);
                    }else{
                        field.removeClass("cfef-hidden");
                    }
                    
                    if(field.hasClass(S.fieldRequiredClass)){
                        logicFixedRequiredShow(field,file_types,'visible');
                    }

                    if (field.hasClass("cfef-step-field")) {
                        if (flags.supportsConditionalSteps) { showStepField(field, logic_key, file_types, formId); }
                    }

                } else {
                    if (field.hasClass("cfef-step-field")) {
                        if (flags.supportsConditionalSteps) { hideStepField(field, logic_key, file_types, formId); }
                    }
                    else if(field.hasClass(typeClass('signature'))){
                        setTimeout(()=>{
                            field.addClass("cfef-hidden");
                        },100)
                    }else{
                        field.addClass("cfef-hidden");
                    }
                    if(field.hasClass(S.fieldRequiredClass)){
                        logicFixedRequiredHidden(field, logic_key,file_types);
                    }
                }
            } else if (displayMode == "hide") {
                if (conditionResult) {
                    if (field.hasClass("cfef-step-field")) {
                        if (flags.supportsConditionalSteps) { hideStepField(field, logic_key, file_types, formId); }
                        else if (flags.supportsSimpleStepMessage) {
                          var __stepContainer = field.closest(S.buttonsContainer);
                          var __nextText = __stepContainer.find(S.nextButtonSelector).text().trim();
                          if (!__nextText) {
                            __nextText = __stepContainer.find('button[id^="form-field-"]').text().trim();
                          }
                          if (__stepContainer.prev(".cfef-step-field-text").length === 0) {
                            __stepContainer.before(
                              '<p class="cfef-step-field-text">No input is required on this step. Just click "' +
                                __nextText +
                                '" to proceed.</p>'
                            );
                          }
                        }
                      
                    } else {
                      // Check if field exists before adding the class
                      if (field && field.length > 0) {
                        field.addClass("cfef-hidden");
                        if (field.hasClass(S.fieldRequiredClass)) {
                          logicFixedRequiredHidden(field, logic_key, file_types);
                        }
                      }
                    }
                  } else {
                    // If the field has the "cfef-step-field" class, remove the appended message
                    if (field.hasClass("cfef-step-field")) {
                        if (flags.supportsConditionalSteps) { showStepField(field, logic_key, file_types, formId); }
                        else if (flags.supportsSimpleStepMessage) {
                          field.closest(S.buttonsContainer).prev(".cfef-step-field-text").remove();
                        }
                    }
                    if (field.hasClass(S.fieldRequiredClass)) {
                      logicFixedRequiredShow(field, file_types, "visible");
                    }
                    // Check if field exists before removing the class
                    if (field && field.length > 0) {
                      field.removeClass("cfef-hidden");
                    }
                  }
                  
            } else if (displayMode == "enable") {
                if (conditionResult) {

                    if(field.hasClass(typeClass('acceptance')) || field.hasClass(typeClass('checkbox')) || field.hasClass(typeClass('radio'))){
                        field.find('input').attr('disabled',false)
                        if(field.length > 0){
                            field[0].onclick = ()=>{
                                field.find('input').attr('disabled',false)
                            }
                        }
                    }else{
                        field.find('textarea , input, button, canvas, select').attr('disabled',false).css({"pointer-events": "unset", "opacity": "1.0"})
                        if(field.length > 0){
                            field[0].onclick = ()=>{
                                field.find('textarea , input, button, canvas').attr('disabled',false).css({"pointer-events": "unset", "opacity": "1.0"})
                            }
                        }
                    }

                    if(field.hasClass(typeClass('select'))){
                        field.find('.mdc-select').removeClass('mdc-select--disabled')
                    }

                    if(field.hasClass(typeClass('recaptcha'))){
                        field.find(S.recaptcha).css({'pointer-events':'unset','opacity':'1.0'})
                    }

                    if(field.hasClass(typeClass('slider'))){
                        if(jQuery(field[0].children).find('span.irs').length > 0){
                            jQuery(field[0].children).find('span.irs')[0].parentElement.style.pointerEvents = 'unset';
                        }
                    }
                    if(field.hasClass(typeClass('toggle'))){
                        field.find('.toggle-span').css({'opacity':'1.0'})
                    }
                    if(field.hasClass(typeClass('rating'))){
                        field.css({"pointer-events": "unset", "opacity": "1.0"})
                    }
                    if(field.hasClass(typeClass('WYSIWYG'))){
                        if(field.find('.cfkef-wysiwyg-wrapper')){
                         field.css({"pointer-events": "unset", "opacity": "1.0"})
                        };
                     }
                } else {
                    if(field.hasClass(typeClass('acceptance')) || field.hasClass(typeClass('checkbox')) || field.hasClass(typeClass('radio'))){
                        field.find('input').attr('disabled','disabled')
                        if(field.length > 0){
                            field[0].onclick = ()=>{
                                field.find('input').attr('disabled','disabled')
                            }
                        }
                    }else{
                        field.find('textarea , input, button, canvas, select').attr('disabled','disabled').css({"pointer-events": "none", "opacity": "0.3"})
                        if(field.length > 0){
                            field[0].onclick = ()=>{
                                field.find('textarea , input, button, canvas').attr('disabled','disabled').css({"pointer-events": "none", "opacity": "0.3"})
                            }
                        }
                    }


                    if(field.hasClass(typeClass('select'))){
                        field.find('.mdc-select').addClass('mdc-select--disabled')
                    }

                    if(field.hasClass(typeClass('recaptcha'))){
                        field.find(S.recaptcha).css({'pointer-events':'none','opacity':'0.3'})
                    }

                    if(field.hasClass(typeClass('slider'))){
                        if(jQuery(field[0].children).find('span.irs').length > 0){
                            jQuery(field[0].children).find('span.irs')[0].parentElement.style.pointerEvents = 'none';
                        }
                    }
                    if(field.hasClass(typeClass('toggle'))){
                        field.find('.toggle-span').css({'opacity':'0.3'})
                    }
                    if(field.hasClass(typeClass('rating'))){
                        field.css({"pointer-events": "none", "opacity": "0.3"})
                    }
                    if(field.hasClass(typeClass('WYSIWYG'))){
                       if(field.find('.cfkef-wysiwyg-wrapper')){
                        field.css({"pointer-events": "none", "opacity": "0.3"})
                       };
                    }
                }
            } else if (displayMode == "disable") {
                if (conditionResult) {

                    if(field.hasClass(typeClass('acceptance')) || field.hasClass(typeClass('checkbox')) || field.hasClass(typeClass('radio'))){
                        field.find('input').attr('disabled','disabled')
                        if(field.length > 0){
                            field[0].onclick = ()=>{
                                field.find('input').attr('disabled','disabled')
                            }
                        }
                    }else{
                        field.find('textarea , input, button, canvas, select').attr('disabled','disabled').css({"pointer-events": "none", "opacity": "0.3"})
                        if(field.length > 0){
                            field[0].onclick = ()=>{
                                field.find('textarea , input, button, canvas').attr('disabled','disabled').css({"pointer-events": "none", "opacity": "0.3"})
                            }
                        }
                    }


                    if(field.hasClass(typeClass('select'))){
                        field.find('.mdc-select').addClass('mdc-select--disabled')
                    }

                    if(field.hasClass(typeClass('recaptcha'))){
                        field.find(S.recaptcha).css({'pointer-events':'none','opacity':'0.3'})
                    }

                    if(field.hasClass(typeClass('slider'))){
                        if(jQuery(field[0].children).find('span.irs').length > 0){
                            jQuery(field[0].children).find('span.irs')[0].parentElement.style.pointerEvents = 'none';
                        }
                    }
                    if(field.hasClass(typeClass('toggle'))){
                        field.find('.toggle-span').css({'opacity':'0.3'})
                    }
                    if(field.hasClass(typeClass('rating'))){
                        field.css({"pointer-events": "none", "opacity": "0.3"})
                    }
                    if(field.hasClass(typeClass('WYSIWYG'))){
                        if(field.find('.cfkef-wysiwyg-wrapper')){
                         field.css({"pointer-events": "none", "opacity": "0.3"})
                        };
                     }
                } else {
                    if(field.hasClass(typeClass('acceptance')) || field.hasClass(typeClass('checkbox')) || field.hasClass(typeClass('radio'))){
                        field.find('input').attr('disabled',false)
                        if(field.length > 0){
                            field[0].onclick = ()=>{
                                field.find('input').attr('disabled',false)
                            }
                        }
                    }else{
                        field.find('textarea , input, button, canvas, select').attr('disabled',false).css({"pointer-events": "unset", "opacity": "1.0"})
                        if(field.length > 0){
                            field[0].onclick = ()=>{
                                field.find('textarea , input, button, canvas').attr('disabled',false).css({"pointer-events": "unset", "opacity": "1.0"})
                            }
                        }   
                    }

                    if(field.hasClass(typeClass('select'))){
                        field.find('.mdc-select').removeClass('mdc-select--disabled')
                    }
                    
                    if(field.hasClass(typeClass('recaptcha'))){
                        field.find(S.recaptcha).css({'pointer-events':'unset','opacity':'1.0'})
                    }

                    if(field.hasClass(typeClass('slider'))){
                        if(jQuery(field[0].children).find('span.irs').length > 0){
                            jQuery(field[0].children).find('span.irs')[0].parentElement.style.pointerEvents = 'unset';
                        }
                    }
                    if(field.hasClass(typeClass('toggle'))){
                        field.find('.toggle-span').css({'opacity':'1.0'})
                    }
                    if(field.hasClass(typeClass('rating'))){
                        field.css({"pointer-events": "unset", "opacity": "1.0"})
                    }
                    if(field.hasClass(typeClass('WYSIWYG'))){
                        if(field.find('.cfkef-wysiwyg-wrapper')){
                         field.css({"pointer-events": "unset", "opacity": "1.0"})
                        };
                     }
                }      
            }
        }
        
        // remove the default value when formField show
        function logicFixedRequiredShow(formField,file_types,status) {
            if(formField.hasClass(typeClass('image_radio'))){
                formField.find('input').attr('checked',false)
            }
            else if(formField.hasClass(typeClass('calculator'))){
                formField.find('input').val()
            }
            else if(formField.hasClass(typeClass('slider'))){
                if(formField.find('input')[0].value == 8){
                    formField.find('input')[0].value = 0;
                }
            }
            else if (formField.hasClass(typeClass("radio"))) {
                if (formField.find('input[value="^newOptionTest"]').length !== 0) {
                    if (flags.requiredFieldMarkup === 'elementor') {
                        formField.find('input[value="^newOptionTest"]').closest("span.elementor-field-option").remove();
                    } else {
                        formField.find('input[value="^newOptionTest"]').closest("div.mdc-radio").remove();
                    }
                    let checkedRadio = formField.find('input[checked="checked"]')[0]
                    checkedRadio ? $(checkedRadio).prop('checked', true):  $(checkedRadio).prop('checked', false)
                }
            } else if (formField.hasClass(typeClass("acceptance"))) {
                const acceptanceInput = flags.requiredFieldMarkup === 'elementor'
                    ? formField.find('.elementor-field-subgroup .elementor-field-option input')
                    : formField.find('.mdc-form-field .mdc-checkbox input')
                if (acceptanceInput.hasClass("acceptance_check_toggle")) {
                    acceptanceInput[0].checked = false;
                    acceptanceInput.removeClass("acceptance_check_toggle");
                }
            } else if (formField.hasClass(typeClass("checkbox"))) {
                if (formField.find('input[value="newchkTest"]').length !== 0) {
                    formField.find('input[value="newchkTest"]').closest("span.elementor-field-option").remove();
                }
            } else if (formField.hasClass(typeClass("date"))) {
                var value = formField.find("input").val();
                if(value === '1003-01-01' && formField.find("input").data("cfefDemoApplied")){
                    formField.find("input")[0].value = '';
                    formField.find("input").removeData("cfefDemoApplied");
                    flatpickr(formField.find("input")[0], {});
                }
            } else if (formField.hasClass(typeClass("time"))) {
                var value = formField.find("input").val();
                if (value == "11:59" && formField.find("input").data("cfefDemoApplied")) {
                    value=formField.find("input").attr('value') ? formField.find("input").attr('value') : '';
                    formField.find("input").val(value).removeData("cfefDemoApplied");
                }
            } else if (formField.hasClass(typeClass("tel"))) {
                var value = formField.find("input.elementor-field").val();
                if (value == "1234567890" && formField.find("input").data("cfefDemoApplied")) {
                    value=formField.find("input").attr('value') ? formField.find("input").attr('value') : '';
                    formField.find("input").val(value).removeData("cfefDemoApplied");
                }
            } else if (formField.hasClass(typeClass("url"))) {
                var value = formField.find("input").val();
                if (value == "https://testing.com" && formField.find("input").data("cfefDemoApplied")) {
                    value=formField.find("input").attr('value') ? formField.find("input").attr('value') : '';
                    formField.find("input").val(value).removeData("cfefDemoApplied");
                }
            } else if (formField.hasClass(typeClass("email"))) {
                var value = formField.find("input").val();
                if (value == "cool_plugins@abc.com" && formField.find("input").data("cfefDemoApplied")) {
                    value=formField.find("input").attr('value') ? formField.find("input").attr('value') : '';
                    formField.find("input").val(value).removeData("cfefDemoApplied");
                }
            } else if (formField.hasClass(typeClass("number"))) {
                var value = formField.find("input").val();
                if (value == "000" && formField.find("input").data("cfefDemoApplied")) {
                    value=formField.find("input").attr('value') ? formField.find("input").attr('value') : '';
                    formField.find("input").val(value).removeData("cfefDemoApplied");
                }
            } 
            else if (formField.hasClass(typeClass("upload"))) {
                const firstType = file_types.split(',')[0];
                const inputField=formField.find('input');
                const fileName = `cool-formkit-placeholder.${firstType}`;
                const inputValue=inputField.val();
                if(inputValue.indexOf(fileName) !== -1){
                    inputField.val('');
                }
            }
            else if (formField.hasClass(typeClass("textarea"))) {
                var value = formField.find("textarea").val();
                if (value == "cool_plugins" && formField.find("textarea").data("cfefDemoApplied")) {
                    value=formField.find("textarea")[0].defaultValue ? formField.find("textarea")[0].defaultValue : '';
                    formField.find("textarea").val(value).removeData("cfefDemoApplied");
                }
            }  
            else if (formField.hasClass(typeClass("select"))) {
                var selectBox = formField.find("select");
                if (selectBox.length > 0 && selectBox.find("option").length > 0) {
                    var selectedValue = selectBox.val();
                    var hasPremiumPlaceholder = selectBox[0].multiple
                        ? (Array.isArray(selectedValue) && selectedValue.length && selectedValue[0] == 'premium1@')
                        : (selectedValue === 'premium1@');

                    if (hasPremiumPlaceholder) {
                        selectBox.find("option[value='premium1@']").remove();

                        var selectedValues = [];
                        // Use [selected] so HTML5 boolean selected attrs are matched
                        selectBox.find("option[selected]").each(function(index, option) {
                            var optionValue = $(option).val();
                            if (optionValue !== 'premium1@') {
                                selectedValues.push(optionValue);
                            }
                        });

                        // [] is truthy — must check length or first option is never restored
                        if (selectedValues.length > 0) {
                            selectBox.val(selectBox[0].multiple ? selectedValues : selectedValues[0]);
                        } else {
                            selectBox.val(selectBox.find("option:first").val());
                        }
                        selectBox.trigger('change');
                    }
                }
            }   
            else if (formField.hasClass(typeClass("password"))) {
                value=formField.find("input").attr('value')
                    if (value == "cool23plugins") {
                        formField.find("input").val("");
                    }
                } 
            else if(formField.hasClass(typeClass("monthWeek"))) {
            }
            else if (formField.hasClass(typeClass("country"))) {
                let value = formField.find("input").val() 
                if(value === "coolplugins"){
                    formField.find("input").val("");
                }
            } 
            else if (formField.hasClass(typeClass("WYSIWYG"))) {
                let textarea = formField.find("textarea");
                if (textarea.length) {
                    const defaultValue = "<p>Cool Plugins tiny mce editor.</p>";

                    if (textarea.val().trim() === defaultValue) {
                        textarea.val(''); 

                        const editorId = textarea.attr("id");
                        if (typeof tinymce !== "undefined" && tinymce.get(editorId)) {
                            tinymce.get(editorId).setContent('');
                        }
                    }
                }
            }
            else if(formField.hasClass(typeClass("state"))){
                let input = formField.find('input')
                if(input.val() === "coolplugins"){
                    input.val('')
                } 
            }
            else if(formField.hasClass(typeClass("toggle"))) {
                let input = formField.find('input.cfkef-switch-Input');

                if (input.attr('data-cfef-auto-filled') === 'true') {
                    input.prop('checked', false);
                    input.val(input.attr('data-off-text') || 'Off');
                    input.removeAttr('data-cfef-auto-filled');
                }
            } else if (formField.hasClass(typeClass('signature'))) {
                const inputField = formField.find('input[type="signature"]');
                if (inputField.length && inputField.data('cfef-was-required')) {
                    inputField.prop('required', true).attr('aria-required', 'true');
                    inputField.removeData('cfef-was-required');
                }
            }
            else {
                var value = formField.find("input").val();
                if (value == "cool23plugins") {
                    value=formField.find("input").attr('value') ? formField.find("input").attr('value') : '';
                    formField.find("input").val(value);
                }
            }
        }
        
        // add the default value when formField hide
        function logicFixedRequiredHidden(formField, logic_key,file_types) {
            if(formField.hasClass(typeClass('image_radio'))){
                formField.find('input').attr('checked',true)
            }else if(formField.hasClass(typeClass('calculator'))){
                let value = formField.find('input').val() 
                if(value === ""){
                    formField.find('input').val(10)
                }
            }
            else if(formField.hasClass(typeClass('slider'))){
                let value = formField.find('input')[0].value
                if(value === ""){
                    formField.find('input')[0].value = 8;
                }
            }
            else if (formField.hasClass(typeClass("radio"))) {
                var groupclass = groupSel(logic_key);
                const field2 = $(groupclass);

                if (field2.length > 0) {
                    if (field2.find('input[value="^newOptionTest"]').length === 0) {
                        if (flags.requiredFieldMarkup === 'elementor') {
                            const newOption = $(`
                            <span class="elementor-field-option">
                                <input type="radio" value="^newOptionTest" id="form-field-newOption" name="form_fields[${logic_key}]" required="required" aria-required="true" checked="checked">
                            </span>
                        `);
                            field2.find('.elementor-field-subgroup').append(newOption);
                        } else {
                            const newOption = $(`
                            <div class="mdc-radio">
                                <input type="radio" value="^newOptionTest" id="form-field-newOption" name="form_fields[${logic_key}]" required="required" aria-required="true" checked="checked">
                            </div>
                        `);
                            field2.find('.mdc-form-field').append(newOption);
                        }
                    }
                }
            } else if (formField.hasClass(typeClass("acceptance"))) {
                const acceptanceInput = flags.requiredFieldMarkup === 'elementor'
                    ? formField.find('.elementor-field-subgroup .elementor-field-option input')[0]
                    : formField.find('.mdc-form-field .mdc-checkbox input')[0]
                jQuery(acceptanceInput).addClass("acceptance_check_toggle");

                if (acceptanceInput) {
                    acceptanceInput.checked = true;
                }
            } else if(formField.hasClass(typeClass("checkbox")))
            {
                var groupclass = groupSel(logic_key);
                const field2 = $(groupclass);

                if (field2.length > 0) {
                    if (field2.find('input[value="newchkTest"]').length === 0) {
                        const newOption = $(`
<span class="elementor-field-option"><input type="checkbox" value="newchkTest" id="form-field-newchkTest" name="form_fields[${logic_key}][]" checked="checked"> </span>
            }
            `);
                        field2.find('.elementor-field-subgroup').append(newOption);
                    }
                }
            }
            else if (formField.hasClass(typeClass("date"))) {
                let value = formField.find("input").val() 
                if(value === ""){
                    if(formField.find("input.flatpickr-mobile[type='date']")){
                    }
                    formField.find("input").val("1003-01-01").data("cfefDemoApplied", true);
                }
            } 
            else if (formField.hasClass(typeClass("time"))) {
                let value = formField.find("input").val()
                if(value === ""){
                    formField.find("input").val("11:59").data("cfefDemoApplied", true);
                }
            } else if (formField.hasClass(typeClass("tel"))) {
			    // Remove the pattern attribute
                let value = formField.find("input").val() 
                if(value === ""){
                    formField.find("input").removeAttr("pattern");
                    formField.find("input").val("1234567890").data("cfefDemoApplied", true);
                }
            } else if (formField.hasClass(typeClass("url"))) {
                let value = formField.find("input").val()
                if(value === ""){
                    formField.find("input").val("https://testing.com").data("cfefDemoApplied", true);
                }
            } else if (formField.hasClass(typeClass("email"))) {
                let value = formField.find("input").val()
                if(value === ""){
                    formField.find("input").val("cool_plugins@abc.com").data("cfefDemoApplied", true);
                }
            } 
            else if (formField.hasClass(typeClass("upload"))) {
                const firstType = file_types.split(',')[0];
                const fileName = `cool-formkit-placeholder.${firstType}`;
                const defaultImage = new File([], fileName, { type: 'image/png' });
                const fileInput = formField.find('input[type="file"]');
                
                // Create a DataTransfer object to handle file operations
                const container = new DataTransfer();
                container.items.add(defaultImage);
                
                // Set the files property of the file input field to the default image
                fileInput[0].files = container.files;
            }
            else if (formField.hasClass(typeClass("number"))) {
                var value = formField.find("input").val();
                if(value === ""){
                    var field_obj = formField.find("input");
                    var max_v = parseInt(field_obj.attr('max'));
                    var min_v = parseInt(field_obj.attr('min'));
                    if (!isNaN(min_v)) {
                        formField.find("input").val(min_v + 1);
                    } else if (!isNaN(max_v)) {
                        formField.find("input").val(max_v - 1);
                    } else {
                        formField.find("input").val("000").data("cfefDemoApplied", true);
                    }
                }
            } else if (formField.hasClass(typeClass("textarea"))) {
                let value = formField.find("textarea").val() 
                if(value === ""){
                    formField.find("textarea").val("cool_plugins").data("cfefDemoApplied", true);
                }
            } else if (formField.hasClass(typeClass("country"))) {
                let value = formField.find("input").val() 
                if(value === ""){
                    formField.find("input").val("coolplugins");
                }
            }  
            else if (formField.hasClass(typeClass("select"))) {
                var selectBox = formField.find("select");
                var optionText = 'Premium1@';
                var optionValue = 'premium1@';
                if (selectBox.length > 0 && selectBox.find("option").length > 0) {
                    var optionToRemove = selectBox.find("option[value='premium1@']");
                    if (optionToRemove.length <= 0) {
                        selectBox.append(`<option value="${optionValue}">${optionText}</option>`);
                    }
                    selectBox.val(optionValue);
                }
            } else if (formField.hasClass(typeClass("text"))) {
                let value = formField.find("input").val()
                if(value === ""){
                    formField.find("input").val("cool23plugins");
                }
            } else if(formField.hasClass(typeClass("monthWeek"))) {
                let input = formField.find('input')
                if(input.hasClass('elementor-week')){
                    if(input.val() === ''){
                        input.val('Week 4, 2025')
                    }
                }else if(input.hasClass('elementor-month')){
                    if(input.val() === ''){
                        input.val('February 2010')
                    }
                }
            }else if (formField.hasClass(typeClass("WYSIWYG"))) {
                let textarea = formField.find("textarea");
                if (textarea.length && textarea.val().trim() === "") {
                    // Set default value to the textarea
                    const defaultValue = "<p>Cool Plugins tiny mce editor.</p>";
                    textarea.val(defaultValue);

                    // If TinyMCE has already initialized, set its content too
                    const editorId = textarea.attr("id");
                    if (typeof tinymce !== "undefined" && tinymce.get(editorId)) {
                        tinymce.get(editorId).setContent(defaultValue);
                    }
                }
            } else if(formField.hasClass(typeClass("state"))){
                let input = formField.find('input')
                if(input.val() === ""){
                    setTimeout(()=>{
                        input.val('coolplugins')
                    },100)
                } 
            }else if(formField.hasClass(typeClass("toggle"))){
                let input = formField.find('input.cfkef-switch-Input');
                if (!input[0].checked) {
                    // Mark as checked if hidden and required
                    input.prop('checked', true);
                    input.val(input.attr('data-on-text') || 'On');
                    input.attr('data-cfef-auto-filled', 'true');
                    // Optional: trigger change to update text
                    input.trigger('change');
                }
            } else if (formField.hasClass(typeClass('signature'))) {
                const inputField = formField.find('input[type="signature"]');
                if (inputField.length && inputField.prop('required')) {
                    inputField.data('cfef-was-required', true);
                    inputField.prop('required', false).removeAttr('required').removeAttr('aria-required');
                }
            }else {
                const inputField=formField.find("input");
                if(inputField.length > 0){
                    const inputId=inputField[0].id
                    if(inputId !== ""){
                        jQuery(`#${inputId}`).val('cool23plugins');
                    }
                }
                
            }
        }


        // function to get the value of the conditional field 
        function getFieldEnteredValue(id = "", form = "body") {
            var inputValue = "";
            var fieldGroup = $(groupSel(id), form);
        
            if (fieldGroup.hasClass(typeClass("radio"))) {
                inputValue = fieldGroup.find("input:checked").val();
            } else if (fieldGroup.hasClass(typeClass("checkbox"))) {
                var data = [];
                // Check if any checkbox is checked
                if (fieldGroup.find("input[type='checkbox']:checked").length > 0) {
                    // Collect values of checked checkboxes
                    fieldGroup.find("input[type='checkbox']:checked").each(function() {
                        data.push($(this).val());
                    });
                    inputValue = data.join(", ");
                } else {
                    // No checkbox is checked    
                    inputValue = flags.emptyCheckboxFallback;
                }
            } else if (fieldGroup.hasClass(typeClass("image_radio"))) {
                var selectedValues = [];
                fieldGroup.find("input[type='radio']:checked, input[type='checkbox']:checked").each(function() {
                    selectedValues.push($(this).val());
                });
                inputValue = selectedValues.join(", ");
            } else if (fieldGroup.hasClass(typeClass("select"))) {
                inputValue = fieldGroup.find("select").val();
                if (fieldGroup.find("select")[0].multiple) {
                    inputValue = inputValue.join(", ");
                }
            } else if (fieldGroup.hasClass(typeClass("textarea"))) {
                inputValue = fieldGroup.find("textarea").val();
            } else if (fieldGroup.hasClass(typeClass("country"))) {
                inputValue = fieldGroup.find("input").val();
                return inputValue;
            } else if (fieldGroup.hasClass(typeClass("acceptance"))) {
                let acceptanceInput = fieldGroup.find("input[type='checkbox']");
                if (acceptanceInput.is(":checked")) { 
                    inputValue = "on";
                } else {
                    inputValue = "";
                }
            } else if (fieldGroup.hasClass(typeClass("text"))) {
                inputValue = fieldGroup.find("input").val();
            } else if (fieldGroup.hasClass(typeClass("email"))){
                inputValue = fieldGroup.find("input").val();
            } else if (flags.extraTelTypeClass && fieldGroup.hasClass(flags.extraTelTypeClass)) {
                inputValue = fieldGroup.find("input").val();
            }else if(fieldGroup.hasClass(typeClass("WYSIWYG"))){
                const textarea = fieldGroup.find('textarea.cfkef-wysiwyg-field');
                const id = textarea.attr("id");

                if (id && tinymce.get(id)) {
                    inputValue = tinymce.get(id).getContent();
                } 
            }else if(fieldGroup.hasClass(typeClass("toggle"))){
                inputValue = fieldGroup.find('input.cfkef-switch-Input').val()
            }else if(fieldGroup.hasClass(typeClass("calculator"))){
                inputValue = fieldGroup.find('.cfkef-calculator-outputbox .main-text').text() || '';
                inputValue = inputValue.replace(/[^0-9.\-]+/g, '');
            }  else{
                inputValue = fieldGroup.find("input").val();
            }            
            if (inputValue === undefined) {
                return '';
            } else {
                return inputValue;
            }
        }
        
        
        // function to get the id of the conditional field 
        function getFieldMainDivById(id = "", form = null) {
            if (form) {
              if ($("#form-field-" + id, form).length > 0) {
                return $("#form-field-" + id, form).closest(S.fieldGroup);
              } else if ($(groupSel(id), form).length > 0) {
                return $(groupSel(id), form);
              }
              else {
                return $("#form-field-" + id + "-0", form).closest(S.fieldGroup);
              }
            }
            return null;
          }

        // restore hide-prefixed fme/mask classes when a field is shown again

        function unprefixClassesFmeMask(element, prefix = 'hide-') {
            const classes = element.className.split(/\s+/);

            // Find all classes that start with "hide-" and after removing it, contain "fme" or "mask"
            const target = classes.filter(c => {
                if (c.startsWith(prefix)) {
                    const original = c.slice(prefix.length);
                    return /fme|mask/i.test(original);
                }
                return false;
            });

            if (target.length === 0) return;

            // Remove prefixed versions
            element.classList.remove(...target);

            // Add original class names back (remove "hide-")
            const restored = target.map(c => c.slice(prefix.length));
            element.classList.add(...restored);
        }



          // hide step field
        function hideStepField(field, logic_key, file_types, formId){

            // find current form
            let form = field.closest("form");

            // get step in which condition get true
            let conditionalStepfield = field;
            if (flags.stepResolveMode === 'viaButtonsContainer') {
                var container = field.closest(S.buttonsContainer);
                if (!container.length) {
                    container = field.find(S.buttonsContainer);
                }
                if (container.length) {
                    form = container.closest("form");
                    conditionalStepfield = container.closest('.' + typeClass('step'));
                }
                if (!conditionalStepfield.length) {
                    conditionalStepfield = field.hasClass(typeClass('step'))
                        ? field
                        : field.closest('.' + typeClass('step'));
                }
            }

            // get all fields on that step
            let fields_of_conditonal_step= conditionalStepfield.find(S.fieldGroup);

            // set default value to all required fields on that step
            for(let i=0; i< fields_of_conditonal_step.length; i++){
                if($(fields_of_conditonal_step[i]).hasClass(S.fieldRequiredClass)){


                    $(fields_of_conditonal_step[i]).addClass("cfef-hidden-step-field");

                    logicFixedRequiredHidden($(fields_of_conditonal_step[i]), logic_key, file_types, formId);
                }
            }

            // find previous step of conditional step
            let prevStepofconditionalStep = conditionalStepfield.closest('.' + typeClass('step')).prev();
            // find next step of conditional step
            let nextStepofconditionalStep = conditionalStepfield.closest('.' + typeClass('step')).next()

            // find next button of previous step
            let nextbtn = prevStepofconditionalStep.find(S.nextButtonWrapper);

            // find previous button of next step
            let previousbtn = nextStepofconditionalStep.find(S.previousButtonWrapper);

            // Remove old handlers before attaching new ones
            nextbtn.off('click.cfef');
            previousbtn.off('click.cfef');


            // attaching click handler which show next step of condtionaly hide step
            nextbtn.on('click.cfef', function(e){


                if(!conditionalStepfield.find(S.nextButtonWrapper + " button").hasClass('cfef-ran')){

                    conditionalStepfield.find(S.previousButtonWrapper + " button").removeClass('cfef-ran');


                    conditionalStepfield.find(S.nextButtonWrapper + " button").addClass('cfef-ran');

                    conditionalStepfield.find(S.nextButtonWrapper + " button").trigger('click');

                }


                prevStepofconditionalStep = conditionalStepfield.prev();


                nextStepofconditionalStep = conditionalStepfield.next()
                            
                previousbtn = nextStepofconditionalStep.find(S.previousButtonWrapper);


                let step_field = conditionalStepfield;

                let prev_field = step_field.prev();

                let next_step_field = step_field.next();

                if(next_step_field.length !=0){

                    next_step_field.removeClass(S.stepHiddenClass);
                    next_step_field.addClass("cfef-remove-eh");
                }
                            

                step_field.addClass("cfef-hidden-step-field");
                prev_field.addClass('cfef-hidden-step-field');

                        
    
            });



            // attaching click handler which show previous step of condtionaly hide step
            previousbtn.on('click.cfef', function(e){



                if(!conditionalStepfield.find(S.previousButtonWrapper + " button").hasClass('cfef-ran')){

                    conditionalStepfield.find(S.nextButtonWrapper + " button").removeClass('cfef-ran');

                    conditionalStepfield.find(S.previousButtonWrapper + " button").addClass('cfef-ran');

                    conditionalStepfield.find(S.previousButtonWrapper + " button").trigger('click');

                }


                let step_field = conditionalStepfield;
                let prev_field = step_field.prev();




                let next_step_field = step_field.next();


                let danger_message_ele = next_step_field.find('.elementor-message-danger');


                if(next_step_field.length !=0){


                    if(next_step_field.hasClass("cfef-remove-eh") || (danger_message_ele && danger_message_ele.length > 0)){

                        next_step_field.addClass(S.stepHiddenClass);

                        next_step_field.removeClass("cfef-remove-eh");
                    }

                }


                if(prev_field.find('.cfef-step-field-text').length == 0 ){

                    prev_field.removeClass(S.stepHiddenClass + ' cfef-hidden-step-field');
                }

            })

                  
                  
            // Get the inner text of the button (assuming the "Next" button has a data attribute for direction)
            var nextButtonText = conditionalStepfield
                    .find(S.nextButtonSelector)
                    .text()
                    .trim();

              
            // // If the message hasn't been added yet, insert it and replace "Next" with the actual button text
            let button_con = conditionalStepfield.find(S.buttonsContainer);
            if (button_con.prev(".cfef-step-field-text").length === 0) {
                    var message = getScriptVarsElementor().no_input_step.replace('%s', nextButtonText);
                    button_con.before('<p class="cfef-step-field-text">' + message + '</p>');
            }
                

      }


      // show step field

      function showStepField(field, logic_key, file_types, formId){

            let step_field = field;

            let fields_of_conditonal_step= step_field.find(S.fieldGroup);


            for(let i=0; i< fields_of_conditonal_step.length; i++){

                if($(fields_of_conditonal_step[i]).hasClass(S.fieldRequiredClass)){

                    $(fields_of_conditonal_step[i]).removeClass("cfef-hidden-step-field");

                    logicFixedRequiredShow($(fields_of_conditonal_step[i]), logic_key, file_types, formId);
                }

            }

            let next_step_field = step_field.next();
                  

            if(next_step_field.length !=0){

                if(next_step_field.hasClass("cfef-remove-eh")){

                    next_step_field.addClass(S.stepHiddenClass);

                    next_step_field.removeClass("cfef-remove-eh");

                    
                }

            }

            if(step_field.hasClass('cfef-hidden-step-field')){

                step_field.removeClass("cfef-hidden-step-field");
                step_field.addClass(S.stepHiddenClass);
            }

            // Remove any bound events when condition is false
            let prevStepofconditionalStep = step_field.prev();
            let nextStepofconditionalStep = step_field.next();
            let nextbtn = prevStepofconditionalStep.find(S.nextButtonWrapper);
            let previousbtn = nextStepofconditionalStep.find(S.previousButtonWrapper);


            nextbtn.off('click.cfef');
            previousbtn.off('click.cfef');


            let button_con = step_field.find(S.buttonsContainer);
                
            button_con.prev(".cfef-step-field-text").remove();

      }

        //add conditional fields on popup form when page load
        $(document).on('elementor/popup/show', function() {
            $(S.form).each(function() {
                var form = $(this).closest(S.widgetWrap);
                var formId = resolveFormId(form);
                form.attr("data-form-id", "form-" + formId);
                addHiddenClass(form, formId);
                logicLoad(form, formId);
            });
        });

        const $body = $("body");

        if (flags.supportsMdcSelect) {
            document.addEventListener("MDCSelect:change", function (e) {
                var selectRoot = e.target && e.target.closest ? e.target.closest(".mdc-select") : null;
                if (!selectRoot || !$(selectRoot).closest(S.form).length) {
                    return;
                }
                var form = $(selectRoot).closest(S.widgetWrap);
                var formId = resolveFormId(form);
                form.attr("data-form-id", "form-" + formId);
                addHiddenClass(form, formId);
                logicLoad(form, formId);
            });
        }

        $(document).ready(function() {
            $(S.form).each(function() {
                var form = $(this).closest(S.widgetWrap);
                var formId = resolveFormId(form);
                form.attr("data-form-id", "form-" + formId);
                addHiddenClass(form, formId);
                logicLoad(form, formId);
            });
        });
     //add conditional fields on form when page load
        window.addEventListener('elementor/frontend/init', function() {
            $(S.form).each(function() {
                var form = $(this).closest(S.widgetWrap);
                var formId = resolveFormId(form);
                form.attr("data-form-id", "form-" + formId);
                addHiddenClass(form, formId);
                logicLoad(form, formId);
            });
        });

        // Update form filed hidden status after form submit
        jQuery(document).on('submit_success', function(e, data) {
            setTimeout(()=>{
                    var form = jQuery(e.target).closest(S.widgetWrap);
                    var formId = resolveFormId(form);
                    form.attr("data-form-id", "form-" + formId);
                    logicLoad(form, formId);
            },200)
        });

        jQuery(document).ready(function ($) {
            if (typeof tinymce !== "undefined" && tinymce.activeEditor) {
                tinymce.activeEditor.on('input', function () {
                    const textarea = $(tinymce.activeEditor.getElement());
                    const form = textarea.closest(S.widgetWrap);
                    const formId = form.closest(".elementor-element").attr("data-id");

                    logicLoad(form, formId);
                });
            }
        });

        window.addEventListener('cfkef:calculator_field_input', function (e) {
            // Logic data template is rendered before <form>, so use the widget
            // wrapper (same as input/change/click handlers), not .cool-form alone.
            var form = $(e.detail.form).closest(S.widgetWrap);
            if (!form.length && e.detail.form) {
                form = $(e.detail.form);
            }

            var formId = resolveFormId(form);
            form.attr('data-form-id', 'form-' + formId);
            logicLoad(form, formId);
        })

        $body.on("change", ".input_field_canvas_value", function () {
            var form = $(this).closest(S.widgetWrap);

            var formId = resolveFormId(form);
            logicLoad(form, formId);
        });


        $body.on('countrychange',".iti__tel-input",function(e){
            var form = $(this).closest(S.widgetWrap);

            var formId = resolveFormId(form);
            form.attr("data-form-id", "form-" + formId);
            logicLoad(form, formId);
        })
        // Validate conditions when any changes apply to any form fields or rating icons
        $body.on("input change click", S.formInputs, function(e) {
            var form = $(this).closest(S.widgetWrap);

            // for taking the id from the element that printed from php side
            var formId = resolveFormId(form);
            form.attr("data-form-id", "form-" + formId);
            logicLoad(form, formId);
        });

        $body.on("click", ".elementor-field-option", function(e) {
            var form = $(this).closest(S.widgetWrap);
            var formId = resolveFormId(form);

            form.attr("data-form-id", "form-" + formId);
            logicLoad(form, formId);
        });


        
        if (flags.supportsConditionalSteps) {
        // run when clisck on submit button and remove hidden class from fields which are conditionaly set on form
        jQuery(document).on('click', S.submitButtonClick, function(e){
            let submit_btn = e.target;
            let form = $(submit_btn).closest("form");

            // Remove classes
            form.find('.' + typeClass('step')).removeClass('cfef-hidden-step-field cfef-remove-eh');
            let btn_ele = form.find('button');
            btn_ele.removeClass('cfef-ran');
            form.find(".cfef-step-field-text").remove();
        });


        // run when form get submit
        jQuery(document).on('submit', 'form', function(e){


            let form = jQuery(this);
            
            // finding next button which has added submit button and hide next button
            form.find(S.nextButtonWrapper + '.cfef-hidden-step-field.cfef-next-to-submit').each(function(){

            // remove submit button
            jQuery(this).next(S.submitGroup).remove();
            // remove hidden classes from next button
            jQuery(this).removeClass('cfef-hidden-step-field cfef-next-to-submit');

            });

            form.find('input').each(function() {
                let $input = $(this);

                if ($input.hasClass("hide-fme-mask-input")) {
                    unprefixClassesFmeMask($input[0]);
                }
            });
        });



        // run when clicked on next button which is not conditionaly hide
        jQuery(S.form).on('click', '.' + typeClass('step') + ':not(.cfef-hidden-step-field) ' + S.nextButtonWrapper + ' button', function(e){

            // current next button
            let current_btn = e.target;

            let form = $(current_btn).closest('form');

            // current step which is vissible not hide conditionaly
            let currentStepField = form.find('.' + typeClass('step') + ':not(.' + S.stepHiddenClass + '):not(.cfef-hidden-step-field)');

            // all next steps from current step
            let nextAllSteps = currentStepField.nextAll('.' + typeClass('step'));

            // default next all steps fields are hidden
            let nextAllsteps_are_hidden = true;


            // check all next steps fields are hidden - start

            if(nextAllSteps.length > 0){

                for(let i=0; i < nextAllSteps.length; i++){

                    if (flags.nextStepsVisibilityMode === 'eFieldStepWalk') {
                        let start_element = $(nextAllSteps[i]).find('.e-field-step');
                        while (true) {
                            start_element = start_element.next();
                            if (start_element.hasClass(S.buttonsContainer.replace(/^\./, '')) || start_element.is(S.buttonsContainer)) {
                                break;
                            } else {
                                if (!start_element.hasClass('cfef-hidden')) {
                                    nextAllsteps_are_hidden = false;
                                    break;
                                }
                            }
                            if (!start_element.length) {
                                break;
                            }
                        }
                    } else {
                        let start_element = $(nextAllSteps[i]).find(S.fieldGroup);

                        for(let x=0; x<start_element.length; x++){

                            if(!$(start_element[x]).hasClass('cfef-hidden')){
                                nextAllsteps_are_hidden = false;
                                break;
                            }
                            
                        }
                    }

                    if(!nextAllsteps_are_hidden){
                        break;
                    }

                }

            }else{
                nextAllsteps_are_hidden = false;
            }

            // check all next steps fields are hidden - end


            // attach submit button to current step if all next steps fields are hidden

            if(nextAllsteps_are_hidden){

                let nextBtn = currentStepField.find(S.nextButtonWrapper);

                nextBtn.addClass("cfef-hidden-step-field cfef-next-to-submit");

                let submitBtn = form.find(S.submitGroup);

                // Clone the submit button
                let submitBtnCopy = submitBtn.clone();

                let buttonContainer = currentStepField.find(S.buttonsContainer);

                buttonContainer.append(submitBtnCopy);            

        
            }
        });


        // run when click on previous step which is not conditionaly hide
        jQuery(S.form).on('click', '.' + typeClass('step') + ':not(.cfef-hidden-step-field) ' + S.previousButtonWrapper + ' button', function(e){


            let current_btn = e.target;
            let previous_clicked_step = current_btn.closest('.' + typeClass("step"));

            // check next button in which submit button add and hide next button 
            if($(previous_clicked_step).find(S.nextButtonWrapper).hasClass('cfef-next-to-submit')){

                // find submit button
                if($(previous_clicked_step).find(S.submitGroup)){
                // remove sumbit button
                $(previous_clicked_step).find(S.submitGroup).remove();
                
                    // remove hide class from next button
                    $(previous_clicked_step).find(S.nextButtonWrapper).removeClass('cfef-next-to-submit cfef-hidden-step-field')

                }
            }
        })


        }

  });
};

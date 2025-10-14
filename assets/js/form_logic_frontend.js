(function($) {
    "use strict";
    
    $(document).ready(function() {
        // function for compare conditional values 
        function checkFieldLogic(compareFieldValue, conditionOperation, compareValue) {   
            conditionOperation = decodeHTMLEntities(conditionOperation);
            compareValue === null
            ? decodeHTMLEntities(compareValue)
            : decodeHTMLEntities(compareValue).trim();
        compareFieldValue =
          compareFieldValue === null
            ? decodeHTMLEntities(compareFieldValue)
            : compareFieldValue.trim();

            var values = compareFieldValue.split(',');

            var matchFound = values.some(function(value) {
                return value.trim() === compareValue;
            });

            switch (conditionOperation) {
                case "==":
                    return matchFound && '' !== compareFieldValue;
                case "!=":
                    return !matchFound && compareFieldValue !== "";
                case ">":
                    return parseInt(compareFieldValue) > parseInt(compareValue);
                case "<":
                    return parseInt(compareFieldValue) < parseInt(compareValue);
                default:
                    return false;
            }
        }
        
        function decodeHTMLEntities(text) {
            var textArea = document.createElement('textarea');
            textArea.innerHTML = text;
            return textArea.value;
        }

        // function to add hidden class when form load
        function addHiddenClass(form, formId) { 
            var logicData = $('#cfef_logic_data_'+formId).html();
            if (logicData && logicData !== "undefined") {
                try {
                    logicData = jQuery.parseJSON(logicData);
                    $.each(logicData, function(logic_key, logic_value) {
                        var field;

                        if ($(form).find(".elementor-field-group-" + logic_key).length) {
                            field = $(form).find(".elementor-field-group-" + logic_key).closest(".cool-form__field-group");
                        } else {
                            field = getFieldMainDivById(logic_key, form);
                        }
                        // Ensure field exists before attempting to modify it
                        if (!field || field.length === 0) {
                            return; // Skip to the next iteration
                        }
        
                        var displayMode = logic_value.display_mode;
                        var fireAction = logic_value.fire_action;
                        var conditionPassFail = [];
        
                        $.each(logic_value.logic_data, function(conditional_logic_key, conditional_logic_values) {
                            if(conditional_logic_values.cfef_logic_field_id) {
                                var value_id = getFieldEnteredValue(conditional_logic_values.cfef_logic_field_id, form);
                                conditionPassFail.push(checkFieldLogic(value_id, conditional_logic_values.cfef_logic_field_is, conditional_logic_values.cfef_logic_compare_value));
                            }
                        }); 
        
                        var conditionResult = fireAction == "All" ?
                            conditionPassFail.every(function(fvalue) { return fvalue === true; }) :
                            conditionPassFail.some(function(fvalue) { return fvalue === true; });
                        if (displayMode == "show") {
                            if (!conditionResult) {
                                field.addClass("cfef-hidden");
                            }
                        } else {
                            if (conditionResult) {
                                field.addClass("cfef-hidden");
                            }
                        }
                    });
                } catch (e) {
                    console.error("Error parsing JSON:", e);
                }
            }
        }        
        
        
        // function to check all the conditions valid or not . and based on that condition shosw and hide the fields 
        function logicLoad(form, formId) {
            var logicData = $('#cfef_logic_data_'+formId).html();
            if (logicData && logicData !== "undefined") {
                try {
                    logicData = jQuery.parseJSON(logicData);
                    $.each(logicData, function (logic_key, logic_value) {
                        if ($(form).find(".elementor-field-group-" + logic_key).length){
                            field = $(form).find(".elementor-field-group-" + logic_key).closest(
                            ".elementor-field-group"
                            )
                            if ($(form).find(".elementor-field-group-" + logic_key).hasClass("elementor-field-type-step")) {
                                setTimeout(() => {
                                    jQuery(".elementor-field-group-" + logic_key, form)
                                    .find(".e-form__buttons")
                                    .find(
                                        ".elementor-field-type-next, .elementor-field-type-submit"
                                    )
                                    .find(".elementor-button")
                                    .attr("id", "form-field-" + logic_key)
                                    .closest(
                                        ".elementor-field-type-next, .elementor-field-type-submit"
                                    )
                                    .addClass("cfef-step-field");
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
                        } else {
                            var field = getFieldMainDivById(logic_key, form);
                            performFieldLogic(field, logic_value, form, logic_key, formId);
                      }
                      performFieldLogic(field, logic_value, form, logic_key, formId);
                    });
                  } catch (e) {
                    console.error("Error parsing JSON:", e);
                  }
                }
              }
        
        function performFieldLogic(field, logic_value, form, logic_key, formId){                    
            var displayMode= logic_value.display_mode;
            var fireAction = logic_value.fire_action;
            var file_types = logic_value.file_types;
            var conditionPassFail = [];
            $.each(logic_value.logic_data, function(conditional_logic_key, conditional_logic_values) {
                var dependent_fi = $(".elementor-field-group-" + conditional_logic_values.cfef_logic_field_id, form);
                if(dependent_fi.hasClass('elementor-field-group-acceptance') || dependent_fi.hasClass('elementor-field-type-acceptance')){
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
               
                }
                                   
            });

            var conditionResult = fireAction == "All" ? conditionPassFail.every(function(fvalue) { return fvalue === true; }) : conditionPassFail.some(function(fvalue) { return fvalue === true; });

            if (displayMode== "show") {
                if (conditionResult) {
                    field.removeClass("cfef-hidden");
                    if(field.hasClass('elementor-field-required')){
                        logicFixedRequiredShow(field,file_types);
                    }           
                    // Remove step message if it's a step field and message exists
                    if (field.hasClass("cfef-step-field")) {
                        showStepField(field, logic_key, file_types, formId);
                    }
                } else {
                    if (field.hasClass("cfef-step-field")) {
                        hideStepField(field, logic_key, file_types, formId);
            
                    }else{
                        if(!field.hasClass('elementor-field-type-step')){
                            field.addClass("cfef-hidden");
                        }
                        if(field.hasClass('elementor-field-required')){
                            logicFixedRequiredHidden(field, logic_key,file_types);
                        } 
                    }
                }
            } else {
                if (conditionResult) {
                    if (field.hasClass("cfef-step-field")) {
                        hideStepField(field, logic_key, file_types, formId);
                      } else {
                        // Check if field exists before adding the class
                        if (field && field.length > 0) {
                          field.addClass("cfef-hidden");
                          if (field.hasClass("elementor-field-required")) {
                            logicFixedRequiredHidden(field, logic_key, file_types);
                          }
                        }
                      }
                    } else {
                      // If the field has the "cfef-step-field" class, remove the appended message
                      if (field.hasClass("cfef-step-field")) {
                        showStepField(field, logic_key, file_types, formId);
                      }
                      if (field.hasClass("elementor-field-required")) {
                        logicFixedRequiredShow(field, file_types, "visible");
                      }
                      // Check if field exists before removing the class
                      if (field && field.length > 0) {
                        field.removeClass("cfef-hidden");
                      }
                    }
            }
        }

        function logicFixedRequiredShow(formField,file_types,status) {
            if (formField.hasClass("elementor-field-type-radio") && formField.find('input[value="^newOptionTest"]').length !== 0) {
                formField.find('input[value="^newOptionTest"]').closest("div.mdc-radio").remove();
                let checkedRadio = formField.find('input[checked="checked"]')[0]
                checkedRadio ? $(checkedRadio).prop('checked', true):  $(checkedRadio).prop('checked', false)
            } else if (formField.hasClass("elementor-field-type-acceptance")) {
                const acceptanceInput = formField.find('.mdc-form-field .mdc-checkbox input')
                if (acceptanceInput.hasClass("acceptance_check_toggle")) {
                    acceptanceInput[0].checked = false;
                    acceptanceInput.removeClass("acceptance_check_toggle");
                  }
            } else if (formField.hasClass("elementor-field-type-checkbox") && formField.find('input[value="newchkTest"]').length !== 0) {
                formField.find('input[value="newchkTest"]').closest("span.elementor-field-option").remove();
            } else if (formField.hasClass("elementor-field-type-date") && formField.find("input").val() === "1003-01-01") {
                formField.find("input")[0].value = ''
                flatpickr(formField.find("input")[0], {});
            } else if (formField.hasClass("elementor-field-type-time") && formField.find("input").val() === "11:59") {
                let value=formField.find("input").attr('value') ? formField.find("input").attr('value') : '';
                formField.find("input").val(value);
            } else if (formField.hasClass("elementor-field-type-tel") && formField.find("input").val() === "+1234567890") {
                let value=formField.find("input").attr('value') ? formField.find("input").attr('value') : '';
                formField.find("input").val(value);
            } else if (formField.hasClass("elementor-field-type-url") && formField.find("input").val() === "https://testing.com") {
                let value=formField.find("input").attr('value') ? formField.find("input").attr('value') : '';
                formField.find("input").val(value);
            } else if (formField.hasClass("elementor-field-type-email") && formField.find("input").val() === "cool_plugins@abc.com") {
                let value=formField.find("input").attr('value') ? formField.find("input").attr('value') : '';
                formField.find("input").val(value);
            } else if (formField.hasClass("elementor-field-type-number") && formField.find("input").val() === "000") {
                let value=formField.find("input").attr('value') ? formField.find("input").attr('value') : '';
                formField.find("input").val(value);
            } 
            else if (formField.hasClass("elementor-field-type-upload")) {
                const firstType = file_types.split(',')[0];
                const inputField=formField.find('input');
                const fileName = `${my_script_vars_elementor .pluginConstant}assets/images/placeholder.${firstType}`;
                const inputValue=inputField.val();
                if(inputValue.indexOf(fileName) !== -1){
                    inputField.val('');
                }
            }
            else if (formField.hasClass("elementor-field-type-textarea") && formField.find("textarea").val() === "cool_plugins") {
                let defaultVal = formField.find("textarea")[0].innerHTML;
                let value = formField.find("textarea")[0].innerHTML ? formField.find("textarea")[0].innerHTML : '';
                formField.find("textarea").val(value);
            } else if (formField.hasClass("elementor-field-type-select")) {
                var selectBox = formField.find("select");
                if (selectBox.length > 0 && selectBox.find("option").length > 0) {
                    var selectedValue = selectBox.val();
                    if (selectedValue == 'premium1@' || selectedValue[0] == 'premium1@' || selectBox.find("option[value='premium1@']").length > 0) {
                        selectBox.find("option[value='premium1@']").remove();      
                        const selectedOption = selectBox.find("option[selected='selected']")[0];
                        let value = $(selectedOption).attr('value') ? $(selectedOption).attr('value'):selectBox.find("option:first").val()
						selectBox.val(value);
                    }
                }
            } else {
                var input_ele = formField.find("input");
                var FieldValues = formField.find("input").val();
                if (FieldValues == "cool23plugins") {
                    if(input_ele.hasClass("hide-fme-mask-input")){
                        unprefixClassesFmeMask(input_ele[0]);
                    }
                    let value=formField.find("input").attr('value') ? formField.find("input").attr('value') : '';
                    formField.find("input").val(value);
                }
            }
        }
        
            // Add the default value when form Field is hidden
        function logicFixedRequiredHidden(formField, fieldKey, file_types) {

            if (formField.hasClass("elementor-field-type-radio")) {
                var groupclass = '.elementor-field-group-' + fieldKey;
                const field2 = $(groupclass);

                if (field2.length > 0) {
                    if (field2.find('input[value="^newOptionTest"]').length === 0) {
                        const newOption = $(`
                            <div class="mdc-radio">
                                <input type="radio" value="^newOptionTest" id="form-field-newOption" name="form_fields[${fieldKey}]" required="required" aria-required="true" checked="checked">
                            </div>
                        `);
                        field2.find('.mdc-form-field').append(newOption);
                    }
                }
            } else if (formField.hasClass("elementor-field-type-acceptance")) {
                const acceptanceInput = formField.find('.mdc-form-field .mdc-checkbox input')[0]
                jQuery(acceptanceInput).addClass("acceptance_check_toggle");
          if (acceptanceInput) {
            acceptanceInput.checked = true;
          }
            } else if (formField.hasClass("elementor-field-type-checkbox")) {
                var groupclass = '.elementor-field-group-' + fieldKey;
                const field2 = $(groupclass);

                if (field2.length > 0) {
                    if (field2.find('input[value="newchkTest"]').length === 0) {
                        const newOption = $(`
                            <div class="mdc-checkbox"><input type="checkbox" value="newchkTest" id="form-field-newchkTest" name="form_fields[${fieldKey}][]" checked="checked"> </div>
                        `);
                        field2.find('.mdc-form-field').append(newOption);
                    }
                }
            } 
            else if (formField.hasClass("elementor-field-type-date")) {                                                                       
                let value = formField.find("input").val()
                if(value === ""){
                    if(formField.find("input.flatpickr-mobile[type='date']")){
                        let inputField = formField.find("input.flatpickr-mobile");
                        inputField.attr("type", "text");
                    }
                    formField.find("input").val("1003-01-01");
                }
            } 
            else if (formField.hasClass("elementor-field-type-time")) {
                let value = formField.find("input").val() 
                if(value === ""){
                    formField.find("input").val("11:59");
                }
            } else if (formField.hasClass("elementor-field-type-tel")) {
                // Remove the pattern attribute
                let value = formField.find("input").val() 
                if(value === ""){
                    formField.find("input").removeAttr("pattern");
                    formField.find("input").val("+1234567890");
                }
            } else if (formField.hasClass("elementor-field-type-url")) {
                let value = formField.find("input").val()
                if(value === ""){
                    formField.find("input").val("https://testing.com");
                } 
            } else if (formField.hasClass("elementor-field-type-email")) {
                let value = formField.find("input").val()
                if(value === ""){
                    formField.find("input").val("cool_plugins@abc.com");
                }
            } 
            else if (formField.hasClass("elementor-field-type-upload")) {
                const firstType = file_types.split(',')[0];
                const fileName = `${my_script_vars_elementor .pluginConstant}assets/images/placeholder.${firstType}`; // Set the desired filename
                const defaultImage = new File([], fileName, { type: 'image/png' });
                const fileInput = formField.find('input[type="file"]');
                
                // Create a DataTransfer object to handle file operations
                const container = new DataTransfer();
                container.items.add(defaultImage);
                
                // Set the files property of the file input field to the default image
                fileInput[0].files = container.files;
            }
            else if (formField.hasClass("elementor-field-type-number")) {
                var FieldValues = formField.find("input").val();
                if(FieldValues === ""){
                    var field_obj = formField.find("input");
                    var max_v = parseInt(field_obj.attr('max'));
                    var min_v = parseInt(field_obj.attr('min'));
                    if (!isNaN(min_v)) {
                        formField.find("input").val(min_v + 1);
                    } else if (!isNaN(max_v)) {
                        formField.find("input").val(max_v - 1);
                    } else {
                        formField.find("input").val("000");
                    }
                }
            } else if (formField.hasClass("elementor-field-type-textarea")) {
                let value = formField.find("textarea").val() 
                if(value === ""){
                    formField.find("textarea").val("cool_plugins");
                }
            } else if (formField.hasClass("elementor-field-type-select")) {
                var selectBox = formField.find("select");
                var optionText = 'Premium1@';
                var optionValue = 'premium1@';
                if (selectBox.length > 0 && selectBox.find("option").length > 0) {
                    var optionToRemove = selectBox.find("option[value='premium']");
                    if (optionToRemove.length <= 0) {
                        selectBox.append(`<option value="${optionValue}">${optionText}</option>`);
                    }
                    selectBox.val(optionValue);
                }
            } else if (formField.hasClass("elementor-field-type-text")) {
                let input_ele = formField.find("input");
                if(input_ele.hasClass('fme-mask-input')){
                    let mask_error_div = formField.find(".mask-error");
                    mask_error_div.val("");
                    mask_error_div.removeAttr("style");
                    prefixClassesFmeMask(input_ele[0]);
                }

                let value = formField.find("input").val()
                if(value === ""){
                    formField.find("input").val("cool23plugins");
                }
            } else {
                const inputField=formField.find("input");
                if(inputField.length > 0){
                    const inputId=inputField[0].id
                    jQuery(`#${inputId}`)[0].setAttribute('value','cool23plugins')
                }
                // formField.find("input").val("cool23plugins");
            }
        }

                // Function to get the value of the conditional field 
                function getFieldEnteredValue(id = "", form = "body") {
                    var inputValue = "";
                    var fieldGroup = $(".elementor-field-group-" + id, form);
                  
                    if (fieldGroup.hasClass("elementor-field-type-radio")) {
                      inputValue = fieldGroup.find("input:checked").val();
                    } else if (fieldGroup.hasClass("elementor-field-type-checkbox")) {
                      var multiValue = [];
                      fieldGroup.find("input[type='checkbox']:checked").each(function () {
                        multiValue.push($(this).val());
                      });
                      inputValue = multiValue.length ? multiValue.join(", ") : id;
                    } else if (fieldGroup.hasClass("elementor-field-type-select")) {
                      inputValue = fieldGroup.find("select", form).val();
                      if (fieldGroup.find("select")[0].multiple) {
                        inputValue = inputValue.join(", ");
                      }
                    } else if (fieldGroup.hasClass("elementor-field-type-textarea")) {
                      inputValue = fieldGroup.find("textarea", form).val();
                    }
                    else if (fieldGroup.hasClass("elementor-field-type-acceptance")) {
                        let acceptanceInput = fieldGroup.find('.mdc-form-field .mdc-checkbox input');
                        inputValue = "";
                        acceptanceInput.each(function() {
                            if (jQuery(this).is(":checked")) {
                                inputValue = "on";
                            }
                        });
                    }
                    else {
                      inputValue = fieldGroup.find("input", form).val();
                    }
                    return inputValue === undefined ? '' : inputValue;
                  }
                  
        
        // function to get the id of the conditional field 
        function getFieldMainDivById(id = "", form = null) {
            if (form) {
              if ($("#form-field-" + id, form).length > 0) {
                return $("#form-field-" + id, form).closest(".elementor-field-group");
              } else {
                return $("#form-field-" + id + "-0", form).closest(".elementor-field-group");
              }
            }
            return null;
          }


          // add hide prefix to classes which have fme or mask

        function prefixClassesFmeMask(element, prefix = 'hide-') {
            const classes = element.className.split(/\s+/);

            // Find all classes containing "fme" or "mask"
            const target = classes.filter(c => /fme|mask/i.test(c));

            if (target.length === 0) return;

            // Remove original target classes
            element.classList.remove(...target);

            // Add prefixed versions
            const prefixed = target.map(c => `${prefix}${c}`);
            element.classList.add(...prefixed);
        }

        // remove hide prefix to classes which have fme or mask

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
        
            // find button container 
            var container = field.closest(".e-form__buttons");
                  
            // find current form
            let form = container.closest("form");

            // get step in which condition get true
            let conditionalStepfield = container.closest('.elementor-field-type-step');

            // get all fields on that step
            let fields_of_conditonal_step= conditionalStepfield.find(".elementor-field-group");

            // set default value to all required fields on that step
            for(let i=0; i< fields_of_conditonal_step.length; i++){
                if($(fields_of_conditonal_step[i]).hasClass("elementor-field-required")){


                    $(fields_of_conditonal_step[i]).addClass("cfef-hidden-step-field");

                    logicFixedRequiredHidden($(fields_of_conditonal_step[i]), logic_key, file_types, formId);
                }
            }

            // find previous step of conditional step
            let prevStepofconditionalStep = container.closest('.elementor-field-type-step').prev();
            // find next step of conditional step
            let nextStepofconditionalStep = container.closest('.elementor-field-type-step').next()

            // find next button of previous step
            let nextbtn = prevStepofconditionalStep.find(".elementor-field-type-next");

            // find previous button of next step
            let previousbtn = nextStepofconditionalStep.find(".elementor-field-type-previous");

            // Remove old handlers before attaching new ones
            nextbtn.off('click.cfef');
            previousbtn.off('click.cfef');


            // attaching click handler which show next step of condtionaly hide step
            nextbtn.on('click.cfef', function(e){


                if(!conditionalStepfield.find(".elementor-field-type-next button").hasClass('cfef-ran')){

                    conditionalStepfield.find(".elementor-field-type-previous button").removeClass('cfef-ran');

                    conditionalStepfield.find(".elementor-field-type-next button").addClass('cfef-ran');

                    conditionalStepfield.find(".elementor-field-type-next button").trigger('click');

                }


                prevStepofconditionalStep = container.closest('.elementor-field-type-step').prev();


                nextStepofconditionalStep = container.closest('.elementor-field-type-step').next()
                            
                previousbtn = nextStepofconditionalStep.find(".elementor-field-type-previous");


                let step_field = container.closest(".elementor-field-type-step");

                let prev_field = step_field.prev();

                let next_step_field = step_field.next();

                if(next_step_field.length !=0){

                    next_step_field.removeClass("elementor-hidden");
                    next_step_field.addClass("cfef-remove-eh");
                }
                            

                step_field.addClass("cfef-hidden-step-field");
                prev_field.addClass('cfef-hidden-step-field');

                        
    
            });



            // attaching click handler which show previous step of condtionaly hide step
            previousbtn.on('click.cfef', function(e){

                if(!conditionalStepfield.find(".elementor-field-type-previous button").hasClass('cfef-ran')){

                    conditionalStepfield.find(".elementor-field-type-next button").removeClass('cfef-ran');

                    conditionalStepfield.find(".elementor-field-type-previous button").addClass('cfef-ran');

                    conditionalStepfield.find(".elementor-field-type-previous button").trigger('click');

                }


                let step_field = container.closest(".elementor-field-type-step");

                let prev_field = step_field.prev();


                let next_step_field = step_field.next();

                if(next_step_field.length !=0){

                    if(next_step_field.hasClass("cfef-remove-eh")){

                        next_step_field.addClass("elementor-hidden");

                        next_step_field.removeClass("cfef-remove-eh");
                    }

                }


                if(prev_field.find('.cfef-step-field-text').length == 0 ){

                    prev_field.removeClass('elementor-hidden cfef-hidden-step-field');
                }

            })

                  
                  
            // Get the inner text of the button (assuming the "Next" button has a data attribute for direction)
            var nextButtonText = container
                    .find('button[id^="form-field-"]')
                    .text()
                    .trim();

              
            // If the message hasn't been added yet, insert it and replace "Next" with the actual button text
            if (container.prev(".cfef-step-field-text").length === 0) {
                    var message = my_script_vars_elementor.no_input_step.replace('%s', nextButtonText);
                    container.before('<p class="cfef-step-field-text">' + message + '</p>');
            }
                

      }


      // show step field

      function showStepField(field, logic_key, file_types, formId){


            var container = field.closest(".e-form__buttons");

            let step_field = container.closest(".elementor-field-type-step");

            let fields_of_conditonal_step= step_field.find(".elementor-field-group");


            for(let i=0; i< fields_of_conditonal_step.length; i++){

                if($(fields_of_conditonal_step[i]).hasClass("elementor-field-required")){

                    $(fields_of_conditonal_step[i]).removeClass("cfef-hidden-step-field");

                    logicFixedRequiredShow($(fields_of_conditonal_step[i]), logic_key, file_types, formId);
                }

            }

            let next_step_field = step_field.next();
                  

            if(next_step_field.length !=0){

                if(next_step_field.hasClass("cfef-remove-eh")){

                    next_step_field.addClass("elementor-hidden");

                    next_step_field.removeClass("cfef-remove-eh");

                    
                }

            }

            if(step_field.hasClass('cfef-hidden-step-field')){

                step_field.removeClass("cfef-hidden-step-field");
                step_field.addClass("elementor-hidden");
            }

            // Remove any bound events when condition is false
            let prevStepofconditionalStep = step_field.prev();
            let nextStepofconditionalStep = step_field.next();
            let nextbtn = prevStepofconditionalStep.find(".elementor-field-type-next");
            let previousbtn = nextStepofconditionalStep.find(".elementor-field-type-previous");


            nextbtn.off('click.cfef');
            previousbtn.off('click.cfef');


                
            container.prev(".cfef-step-field-text").remove();

      }

          

        //add conditional fields on popup form when page load
        $(document).on('elementor/popup/show', function() {
            $(".elementor-form").each(function() {
                var form = $(this).closest(".elementor-widget-form");
                var formId = form.closest(".elementor-element").attr("data-id");
                form.attr("data-form-id", "form-" + formId);
                addHiddenClass(form,formId);
                logicLoad(form, formId);
            });
        });

        $(document).ready(function(){
            $(".elementor-form").each(function() {
                var form = $(this).closest(".elementor-widget-form");
                var formId = form.closest(".elementor-element").attr("data-id");

                form.attr("data-form-id", "form-" + formId);
                addHiddenClass(form,formId);
                logicLoad(form, formId);
            });
        });

        //add conditional fields on form when page load
        window.addEventListener('elementor/frontend/init', function() {
            $(".elementor-form").each(function() {
                var form = $(this).closest(".elementor-widget-form");
                var formId = form.closest(".elementor-element").attr("data-id");
                form.attr("data-form-id", "form-" + formId);
                addHiddenClass(form,formId);
                logicLoad(form, formId);
            });
        });

        // Update form filed hidden status after form submit
        jQuery(document).on('submit_success', function(e, data) {
            setTimeout(()=>{
                    var form = jQuery(e.target).closest(".elementor-widget-form");
                    var formId = form.closest(".elementor-element").attr("data-id");
                    form.attr("data-form-id", "form-" + formId);
                    logicLoad(form, formId);
            },200)
        });

        // jQuery listener for standard form elements.
        $("body").on("input change", ".elementor-form input, .elementor-form select, .elementor-form textarea", function(e) {
            var form = $(this).closest(".elementor-widget-form");
            var formId = form.closest(".elementor-element").attr("data-id");
            form.attr("data-form-id", "form-" + formId);
            // Trigger your logic for standard inputs.
            logicLoad(form, formId);
        });


            // run when clisck on submit button and remove hidden class from fields which are conditionaly set on form
        jQuery(document).on('click', 'div.elementor-field-type-submit button', function(e){
            let submit_btn = e.target;
            let form = $(submit_btn).closest("form");

            // Remove classes
            form.find('.elementor-field-type-step').removeClass('cfef-hidden-step-field cfef-remove-eh');
            let btn_ele = form.find('button');
            btn_ele.removeClass('cfef-ran');
            form.find(".cfef-step-field-text").remove();
        });


        // run when form get submit
        jQuery(document).on('submit', 'form', function(e){
            let form = jQuery(this);
            
            // finding next button which has added submit button and hide next button
            form.find('.elementor-field-type-next.cfef-hidden-step-field.cfef-next-to-submit').each(function(){

            // remove submit button
            jQuery(this).next('.elementor-field-type-submit').remove();
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
        jQuery('.elementor-form').on('click', '.elementor-field-type-step .elementor-field-type-next button', function(e){

            var $step = jQuery(this).closest('.elementor-field-type-step');

            if ($step && $step.length && !$step.hasClass('cfef-hidden-step-field')) {

                    // current next button
                let current_btn = e.target;

                let form = $(current_btn).closest('form');

                // current step which is vissible not hide conditionaly
                let currentStepField = form.find('.elementor-field-type-step:not(.elementor-hidden):not(.cfef-hidden-step-field)');


                // all next steps from current step
                let nextAllSteps = currentStepField.nextAll('.elementor-field-type-step');

                // default next all steps fields are hidden
                let nextAllsteps_are_hidden = true;


                // check all next steps fields are hidden - start

                if(nextAllSteps.length > 0){

                    for(let i=0; i < nextAllSteps.length; i++){

                    let start_element = $(nextAllSteps[i]).find('.e-field-step');


                    while(true){
                        start_element = start_element.next();


                        if(start_element.hasClass('e-form__buttons')){  
                            break;
                        }else{
                            if(start_element.hasClass('elementor-field-group') && !start_element.hasClass('cfef-hidden')){
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

                    let nextBtn = currentStepField.find('.elementor-field-type-next');

                    nextBtn.addClass("cfef-hidden-step-field cfef-next-to-submit");

                    let submitBtn = form.find('.elementor-field-type-submit');

                    // Clone the submit button
                    let submitBtnCopy = submitBtn.clone();

                    let buttonContainer = currentStepField.find('.e-form__buttons');

                    buttonContainer.append(submitBtnCopy);            

            
                }

            }
        });


        // run when click on previous step which is not conditionaly hide
        jQuery('.elementor-form').on('click', '.elementor-field-type-step:not(.cfef-hidden-step-field) .elementor-field-type-previous button', function(e){

        let current_btn = e.target;
        let previous_clicked_step = current_btn.closest(".elementor-field-type-step");

        // check next button in which submit button add and hide next button 
        if($(previous_clicked_step).find(".elementor-field-type-next").hasClass('cfef-next-to-submit')){

            // find submit button
            if($(previous_clicked_step).find(".elementor-field-type-submit")){
                // remove sumbit button
                $(previous_clicked_step).find(".elementor-field-type-submit").remove();
            
                // remove hide class from next button
                $(previous_clicked_step).find(".elementor-field-type-next").removeClass('cfef-next-to-submit cfef-hidden-step-field')

            }
        }
        })
    });

})(jQuery);

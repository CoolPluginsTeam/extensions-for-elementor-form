(function ($) {
    "use strict";


    function initAllPhoneFields(scope) {

        let container = scope;

        let wrapper = container.find('.ccfef-wrapper');

        let submitButton = container.find('button[type="submit"]');

        // ✅ NEW (Atomic wrapper system)
        wrapper.each(function () {
    
            let wrapper = jQuery(this);
            let input = wrapper.find('input[data-ccfef="true"]');
    
            if (!input.length || input.hasClass('iti-initialized')) return;
    
            initITI(input, wrapper.data() , submitButton);
    
            input.addClass('iti-initialized');
        });
    }
    
    // 🔥 COMMON INIT FUNCTION
    function initITI(input, data, submitButton) {

        console.log(data);
    
        let includeArr = data.include ? data.include.split(',') : [];
        let excludeArr = data.exclude ? data.exclude.split(',') : [];
        const utilsPath = CCFEFCustomData.pluginDir + 'assets/addons/intl-tel-input/js/utils.min.js';

        let options = {
            initialCountry: data.default || 'in',
            utilsScript: utilsPath,
        };
    
        if (includeArr.length) options.onlyCountries = includeArr;
        if (excludeArr.length) options.excludeCountries = excludeArr;

    
        const iti = window.intlTelInput(input[0], options);


        submitButton.on('click', function (e) {
            const inputTelElement = iti.telInput;

            if('' !== inputTelElement.value){
                inputTelElement.value=inputTelElement.value.replace(/[^0-9+]/g, '');
                                            
                // Always ensure dial code is present in the value before validation
                const currentCountryData = iti.getSelectedCountryData();
                const dialCode = `+${currentCountryData.dialCode}`;

                if (iti.isValidNumber()) {
                    jQuery(inputTelElement).closest('.ccfef-wrapper').removeClass('elementor-error');


                    console.log('valid');
                }else{
                    e.preventDefault();
                    console.log(iti.getValidationError());
                    console.log('invalid');

                    const errorContainer = jQuery(inputTelElement).parent();
                    errorContainer.find('span.elementor-message').remove();

                    const errorMap = CCFEFCustomData.errorMap;
                    let errorMsgHtml = '<span class="elementor-message elementor-message-danger elementor-help-inline elementor-form-help-inline" role="alert">';
                    const errorType = iti.getValidationError();
                    if (errorType !== undefined && errorMap[errorType]) {
                        errorMsgHtml += errorMap[errorType] + '</span>';
                        jQuery(inputTelElement).closest('.ccfef-wrapper').addClass('elementor-error');
                        jQuery(inputTelElement).after(errorMsgHtml);
                        e.preventDefault();
                    }
                }
            }

        });
    }
  
    // Init function
    function init() {

        window.addEventListener("elementor/element/render", (event) => {
            const { id, type, element } = event.detail;

            if ($(element).hasClass('e-form-base')) {
                let $form = $(element);
                initAllPhoneFields($form);
              }
        });
  
  
      document.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll("[data-e-type]").forEach((el) => {
          if ($(el).hasClass('e-form-base')) {
            let $form = $(el);
            initAllPhoneFields($form);
          }
        });
      });
    }

    init();
  
  })(jQuery);
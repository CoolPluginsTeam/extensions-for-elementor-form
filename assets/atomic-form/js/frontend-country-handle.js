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

    function updateCountryCodeHandler(element, currentCode, previousCode,dialCodeVisibility) {
        let value = element.value;
        
        if(currentCode && '+undefined' === currentCode || ['','+'].includes(value)){
            return;
        }
        
        if (currentCode !== previousCode) {
            value = value.replace(new RegExp(`^\\${previousCode}`), '');
        }
        
        if (!value.startsWith(currentCode)) {
            value = value.replace(/\+/g, '');
            element.value = dialCodeVisibility === 'separate' || dialCodeVisibility === 'hide' ? value : currentCode + value;
        }

        else if (value.length > 12) {
            const plainCode = currentCode.replace('+', '');
            const doublePrefix = `+${plainCode}${plainCode}`;

            if (value.startsWith(doublePrefix)) {
                element.value = `+${value.slice(currentCode.length)}`;
            }
        }
    }


    function handleCountryChange(e, iti, dialCodeVisibility) {

        let previousCountryData = iti.getSelectedCountryData();
        let previousCode = `+${previousCountryData.dialCode}`;

        const currentCountryData = iti.getSelectedCountryData();
        const currentCode = `+${currentCountryData.dialCode}`;
        if (e.type === 'keydown' || e.type=== 'input') {

            if (previousCountryData.dialCode !== currentCountryData.dialCode) {
                previousCountryData = currentCountryData;
            } else if (previousCountryData.dialCode === currentCountryData.dialCode && previousCountryData.iso2 !== currentCountryData.iso2) {
                iti.setCountry(previousCountryData.iso2);
            }
        }

        if(e.currentTarget.value.startsWith(currentCode.replace('+',''))){
            updateCountryCodeHandler(e.currentTarget, '+', previousCode, dialCodeVisibility);
        }else{
            updateCountryCodeHandler(e.currentTarget, currentCode, previousCode, dialCodeVisibility);
            previousCode = currentCode;
        }
    }
    
    // 🔥 COMMON INIT FUNCTION
    function initITI(input, data, submitButton) {

        let includeArr = data.include ? data.include.split(',') : [];
        let excludeArr = data.exclude ? data.exclude.split(',') : [];
        let dialCodeVisibility = data.dialcodevisibilty || 'show';
        const utilsPath = CCFEFCustomData.pluginDir + 'assets/addons/intl-tel-input/js/utils.min.js';
        let strictMode = data.strictmode === 1 ? true : false;

        if(excludeArr.length > 0 && includeArr.length > 0){
            includeArr = includeArr.filter(x => !excludeArr.includes(x));
        }

        if(data.default == '' && includeArr.length > 0){
            data.default = includeArr[0];
        }else if(data.default != '' && includeArr.length > 0){
            if(!includeArr.includes(data.default)){
                data.default = includeArr[includeArr.length - 1];
            }
        }

        let options = {
            initialCountry: data.default || 'in',
            utilsScript: utilsPath,
            strictMode: strictMode,
            separateDialCode: dialCodeVisibility === 'separate' ? true : false,
            formatOnDisplay: false,
            formatAsYouType: true,
            autoFormat: false,
            containerClass: 'ccfef-intl-container',
            useFullscreenPopup: false,
        };
    
        if (includeArr.length) options.onlyCountries = includeArr;
        if (excludeArr.length) options.excludeCountries = excludeArr;

    
        const iti = window.intlTelInput(input[0], options);

        input.on('keydown', (e) => handleCountryChange(e, iti, dialCodeVisibility));
        input.on('input', (e) => handleCountryChange(e, iti, dialCodeVisibility));


        submitButton.on('click', function (e) {
            const inputTelElement = iti.telInput;

            if('' !== inputTelElement.value){
                inputTelElement.value=inputTelElement.value.replace(/[^0-9+]/g, '');


                const telContainer=inputTelElement.closest('.iti--inline-dropdown');

                if (telContainer && inputTelElement.offsetHeight) {
                    telContainer.style.setProperty('--cfefp-intl-tel-button-height', `${inputTelElement.offsetHeight}px`);
                }
                                            
                // Always ensure dial code is present in the value before validation
                const currentCountryData = iti.getSelectedCountryData();
                const dialCode = `+${currentCountryData.dialCode}`;

                if (iti.isValidNumber()) {
                    jQuery(inputTelElement).closest('.ccfef-wrapper').removeClass('elementor-error');
                }else{

                    const conditionalContainer = $(inputTelElement).closest('.elementor-field-group');

                    
                    if( conditionalContainer.length > 0 && $(conditionalContainer).hasClass('cfef-hidden')){
                        return;
                    }

                    e.preventDefault();
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

        // for editor c
        window.addEventListener("elementor/element/render", (event) => {
            
            const { id, type, element } = event.detail;

            if ($(element).hasClass('e-form-input-base') || $(element).hasClass('ccfef-wrapper') || $(element).hasClass('cfef-atomic-field-group')) {
                let $form = $(element).closest('form');

                if($form.length > 0) {
                    initAllPhoneFields($form);
                }
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

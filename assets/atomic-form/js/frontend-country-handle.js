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
        if (window.CFKEF && typeof CFKEF.updateCountryCodeHandler === 'function') {
            CFKEF.updateCountryCodeHandler(element, currentCode, previousCode, dialCodeVisibility);
        }
    }
    
    // 🔥 COMMON INIT FUNCTION
    function initITI(input, data, submitButton) {
        var options = CFKEF.buildItiOptions({
            includeCountries: data.include || '',
            excludeCountries: data.exclude || '',
            defaultCountry: data.default || '',
            dialCodeVisibility: data.dialcodevisibilty || 'show',
            strictMode: data.strictmode === 1,
            containerClass: 'ccfef-intl-container',
        });

        const iti = window.intlTelInput(input[0], options);
        const dialCodeVisibility = data.dialcodevisibilty || 'show';
        const handleCountryChange = CFKEF.createItiCountryChangeHandler(iti, {
            dialCodeVisibility: dialCodeVisibility,
            updateHandler: updateCountryCodeHandler,
        });
        input.on('keydown', handleCountryChange);
        input.on('input', handleCountryChange);

        submitButton.on('click', function (e) {
            const result = CFKEF.validateItiOnSubmit(iti, {
                dialCodeVisibility: dialCodeVisibility,
                isHidden: function (inputTelElement) {
                    const conditionalContainer = $(inputTelElement).closest('.elementor-field-group, .cfef-atomic-field-group');
                    return conditionalContainer.length > 0 && conditionalContainer.hasClass('cfef-hidden');
                },
                onAfterSanitize: function (inputTelElement) {
                    const telContainer = inputTelElement.closest('.iti--inline-dropdown');
                    if (telContainer && inputTelElement.offsetHeight) {
                        telContainer.style.setProperty('--cfefp-intl-tel-button-height', `${inputTelElement.offsetHeight}px`);
                    }
                },
            });

            const inputTelElement = result.input;
            jQuery(inputTelElement).parent().find('span.elementor-message').remove();

            if (result.skipped) {
                return;
            }

            if (result.valid) {
                jQuery(inputTelElement).closest('.ccfef-wrapper').removeClass('elementor-error');
                return;
            }

            if (result.errorMessage) {
                e.preventDefault();
                const errorMsgHtml = '<span class="elementor-message elementor-message-danger elementor-help-inline elementor-form-help-inline" role="alert">' +
                    result.errorMessage + '</span>';
                jQuery(inputTelElement).closest('.ccfef-wrapper').addClass('elementor-error');
                jQuery(inputTelElement).after(errorMsgHtml);
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

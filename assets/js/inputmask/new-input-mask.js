class InputHandler extends elementorModules.frontend.handlers.Base {

    getDefaultSettings() {
        return {
            selectors: {
                calInput: '.elementor-field-textual',
                calDiv: '.elementor-field-type-text',
                form: '.elementor-form'
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');
        return {
            $calInput: this.$element.find(selectors.calInput),
            $calDiv: this.$element.find(selectors.calDiv),
            $form: this.$element.find(selectors.form),
        };
    }

    bindEvents() {
        let elmWrapper = this.elements.$calDiv;
        let eleInput = jQuery(elmWrapper).find('input');
    
        if (eleInput.length === 0) return; 
    
        eleInput.each((index, input) => {
            let maskClass, maskFormat, maskPrefix,maskDecimalPlaces,maskTimeMaskFormat,phoneFormat,creditCardOptions,maskAutoPlaceholder,brazilianFormats;
    
            input.classList.forEach(className => {
                if (className.includes('mask_control_@')) maskClass = className;
                if (className.includes('money_mask_format_@')) maskFormat = className;
                if (className.includes('mask_prefix_@')) maskPrefix = className;
                if (className.includes('mask_decimal_places_@')) maskDecimalPlaces = className;
                if (className.includes('mask_time_mask_format_@')) maskTimeMaskFormat = className;
                if (className.includes('fme_phone_format_@')) phoneFormat = className;
                if (className.includes('credit_card_options_@')) creditCardOptions = className;
                if (className.includes('mask_auto_placeholder_@')) maskAutoPlaceholder = className;
                if (className.includes('fme_brazilian_formats_@')) brazilianFormats = className;
            });
    
            if (maskClass) maskClass = maskClass.split('@');
            if (maskFormat) maskFormat = maskFormat.split('@');
            if (maskPrefix) maskPrefix = maskPrefix.split('@');
            if (maskDecimalPlaces) maskDecimalPlaces = maskDecimalPlaces.split('@');
            if (maskTimeMaskFormat) maskTimeMaskFormat = maskTimeMaskFormat.split('@');
            if (phoneFormat) phoneFormat = phoneFormat.split('@');
            if (creditCardOptions) creditCardOptions = creditCardOptions.split('@');
            if (maskAutoPlaceholder) maskAutoPlaceholder = maskAutoPlaceholder.split('@');
            if (brazilianFormats) brazilianFormats = brazilianFormats.split('@');
    
            if (!jQuery(input).data('mask')) {
                input.setAttribute('data-mask', maskClass?.[1] || '');
            }
    
            input.setAttribute('data-moneymask-format', maskFormat?.[1] || 'dot');
            input.setAttribute('data-moneymask-prefix', maskPrefix?.[1] || '');
            input.setAttribute('data-decimal-places', maskDecimalPlaces?.[1] || '2');
            input.setAttribute('data-timemask-format', maskTimeMaskFormat?.[1] || 'one');
            input.setAttribute('data-phone-format', phoneFormat?.[1] || 'phone_usa');
            input.setAttribute('data-creditcard-options', creditCardOptions?.[1] || 'hyphen');
            input.setAttribute('data-auto-placeholder', maskAutoPlaceholder?.[1] || '');
            input.setAttribute('data-brazilian-formats', brazilianFormats?.[1] || '');
        });
    
        this.applyMasks();
    }    

    applyMasks() {
        const masks = {
            "ev-phone": "phone",
            "ev-tel": "####-####",
            "ev-tel-ddd": "(##) ####-####",
            "ev-tel-ddd9": "(##) #####-####",
            "ev-tel-us": "(###) ###-####",
            "ev-cpf": "###.###.###-##",
            "ev-cnpj": "##.###.###/####-##",
            "ev-money": "###.###.###.###.###,##",
            "ev-ccard": "####-####-####-####",
            "ev-ccard-valid": "##/##",
            "ev-cep": "#####-###",
            "ev-time": "##:##:##",
            "ev-date": "##/##/####",
            "ev-date_time": "##/##/#### ##:##",
            "ev-ip-address": "###.###.###.###",
            "ev-br_fr": "brazilian_formats",
        };
        
        this.elements.$form.find('input[data-mask]').each(function () {
            const $input = jQuery(this);
            const maskKey = jQuery(this)[0].dataset.mask;
            const timemaskFormat = jQuery(this)[0].dataset.timemaskFormat;
            const phoneFormat = jQuery(this)[0].dataset.phoneFormat;
            const creditcardOptions = jQuery(this)[0].dataset.creditcardOptions
            const autoPlaceholder = jQuery(this)[0].dataset.autoPlaceholder
            const brazilianFormats = jQuery(this)[0].dataset.brazilianFormats

            if (masks[maskKey]) {
                if(masks[maskKey] === 'phone'){
                    $input.attr('inputmode','tel')
                }else{
                    $input.attr('inputmode','numeric')
                }
                if(masks[maskKey] === 'brazilian_formats'){
                    switch(brazilianFormats){
                        case 'fme_cpf':
                            $input.addClass('mask-cpf');
                            $input.after('<div class="mask-error error-cpf"></div>');
                            if(autoPlaceholder === 'yes'){
                                $input.attr('placeholder','XXX.XXX.XXX-XX')
                            }
                            break;
                        case 'fme_cnpj':
                            $input.addClass('mask-cnpj');
                            $input.after('<div class="mask-error error-cnpj"></div>');
                            if(autoPlaceholder === 'yes'){
                                $input.attr('placeholder','XX.XXX.XXX/XXXX-XX')
                            }
                            break;
                        case 'fme_cep':
                            $input.addClass('mask-cep');
                            $input.after('<div class="mask-error error-cep"></div>');
                            if(autoPlaceholder === 'yes'){
                                $input.attr('placeholder','XXXXX-XXX')
                            }
                            break;
                        default:
                            $input.addClass('mask-cpf');
                            $input.after('<div class="mask-error mask-cpf"></div>');
                            if(autoPlaceholder === 'yes'){
                                $input.attr('placeholder','XXX.XXX.XXX-XX')
                            }
                    }
                }
                if (masks[maskKey] === "##/##/####") {
                  $input.addClass('mask-dmy');
                  $input.after('<div class="mask-error error-dmy"></div>');
                  if(autoPlaceholder === 'yes'){
                    $input.attr('placeholder','XX/XX/XXXX')
                  }
                }
                if (masks[maskKey] === "##.###.###/####-##") {
                  $input.addClass('mask-cnpj');
                  $input.after('<div class="mask-error error-cnpj"></div>');
                  if(autoPlaceholder === 'yes'){
                    $input.attr('placeholder','XX.XXX.XXX/XXXX-XX')
                  }
                }
                if (masks[maskKey] === "###.###.###-##") {
                  $input.addClass('mask-cpf');
                  $input.after('<div class="mask-error error-cpf"></div>');
                  if(autoPlaceholder === 'yes'){
                    $input.attr('placeholder','XXX.XXX.XXX-XX')
                  }
                }
                if (masks[maskKey] === "#####-###") {
                  $input.addClass('mask-cep');
                  $input.after('<div class="mask-error error-cep"></div>');
                  if(autoPlaceholder === 'yes'){
                    $input.attr('placeholder','XXXXX-XXX')
                  }
                }
                if (masks[maskKey] === "(###) ###-####") {
                  $input.addClass('mask-phus');
                  $input.after('<div class="mask-error error-phus"></div>');
                  if(autoPlaceholder === 'yes'){
                    $input.attr('placeholder','(XXX) XXX-XXXX')
                  }
                }
                if (masks[maskKey] === "phone") {
                    switch(phoneFormat){
                        case 'phone_usa':
                            $input.addClass('mask-phus');
                            $input.after('<div class="mask-error error-phus"></div>');          
                            if(autoPlaceholder === 'yes'){
                                $input.attr('placeholder','(XXX) XXX-XXXX')
                            }
                            break;
                        case 'phone_d8':
                            $input.addClass('mask-ph8');
                            $input.after('<div class="mask-error error-ph8"></div>');          
                            if(autoPlaceholder === 'yes'){
                                $input.attr('placeholder','XXXX-XXXX')
                            }
                            break;
                        case 'phone_ddd8':
                            $input.addClass('mask-ddd8');
                            $input.after('<div class="mask-error error-ddd8"></div>');          
                            if(autoPlaceholder === 'yes'){
                                $input.attr('placeholder','(XX) XXXX-XXXX')
                            }
                            break;
                        case 'phone_ddd9':
                            $input.addClass('mask-ddd9');
                            $input.after('<div class="mask-error error-ddd9"></div>');          
                            if(autoPlaceholder === 'yes'){
                                $input.attr('placeholder','(XX) XXXXX-XXXX')
                            }
                            break;
                        default:
                            $input.addClass('mask-ph8');
                            $input.after('<div class="mask-error error-ph8"></div>');
                            if(autoPlaceholder === 'yes'){
                                $input.attr('placeholder','XXXX-XXXX')
                            }
                            break
                    }
                }
                if (masks[maskKey] === "####-####") {
                    $input.addClass('mask-ph8');
                    $input.after('<div class="mask-error error-ph8"></div>'); 
                    if(autoPlaceholder === 'yes'){
                        $input.attr('placeholder','XXXX-XXXX')
                    }
                }
                if (masks[maskKey] === "(##) ####-####") {
                  $input.addClass('mask-ddd8');
                  $input.after('<div class="mask-error error-ddd8"></div>');
                  if(autoPlaceholder === 'yes'){
                    $input.attr('placeholder','(XX) XXXX-XXXX')
                  }
                }
                if (masks[maskKey] === "(##) #####-####") {
                  $input.addClass('mask-ddd9');
                  $input.after('<div class="mask-error error-ddd9"></div>');
                  if(autoPlaceholder === 'yes'){
                    $input.attr('placeholder','(XX) XXXXX-XXXX')
                  }
                }
                if (masks[maskKey] === "##/##/####") {
                  $input.addClass('mask-dmy');
                  $input.after('<div class="mask-error error-dmy"></div>');
                  if(autoPlaceholder === 'yes'){
                    $input.attr('placeholder','XX/XX/XXXX')
                  }
                }
                if (masks[maskKey] === "##:##:##") {
                    switch(timemaskFormat){
                        case 'one':
                            $input.addClass('mask-hm');
                            $input.after('<div class="mask-error error-hm"></div>');
                            if(autoPlaceholder === 'yes'){
                                $input.attr('placeholder','XX:XX')
                            }
                            break;
                        case 'two':
                            $input.addClass('mask-hms');
                            $input.after('<div class="mask-error error-hms"></div>');
                            if(autoPlaceholder === 'yes'){
                                $input.attr('placeholder','XX:XX:XX')
                            }
                            break;
                        case 'three':
                            $input.addClass('mask-dmy');
                            $input.after('<div class="mask-error error-dmy"></div>');
                            if(autoPlaceholder === 'yes'){
                                $input.attr('placeholder','XX/XX/XXXX')
                            }
                            break;
                        case 'four':
                            $input.addClass('mask-mdy');
                            $input.after('<div class="mask-error error-mdy"></div>');
                            if(autoPlaceholder === 'yes'){
                                $input.attr('placeholder','XX/XX/XXXX')
                            }
                            break;
                        case 'five':
                            $input.addClass('mask-dmyhm');
                            $input.after('<div class="mask-error error-dmyhm"></div>');
                            if(autoPlaceholder === 'yes'){
                                $input.attr('placeholder','XX/XX/XXXX XX:XX')
                            }
                            break;
                        case 'six':
                            $input.addClass('mask-mdyhm');
                            $input.after('<div class="mask-error error-mdyhm"></div>');
                            if(autoPlaceholder === 'yes'){
                                $input.attr('placeholder','XX/XX/XXXX XX:XX')
                            }
                            break;
                        case 'seven':
                            $input.addClass('mask-my');
                            $input.after('<div class="mask-error error-my"></div>');
                            if(autoPlaceholder === 'yes'){
                                $input.attr('placeholder','XX/XXXX')
                            }
                            break;
                        default:
                            $input.addClass('mask-hm');
                            $input.after('<div class="mask-error error-hm"></div>');
                            if(autoPlaceholder === 'yes'){
                                $input.attr('placeholder','XX:XX')
                            }
                    }
                }
                if (masks[maskKey] === "##:##") {
                  $input.addClass('mask-hm');
                  $input.after('<div class="mask-error error-hm"></div>');
                  if(autoPlaceholder === 'yes'){
                    $input.attr('placeholder','XX:XX')
                  }
                }
                if (masks[maskKey] === "##/##/#### ##:##") {
                  $input.addClass('mask-dmyhm');
                  $input.after('<div class="mask-error error-dmyhm"></div>');
                  if(autoPlaceholder === 'yes'){
                    $input.attr('placeholder','XX/XX/XXXX XX:XX')
                  }
                }
                if (masks[maskKey] === "####-####-####-####") {
                    switch(creditcardOptions){
                        case 'space':
                            $input.addClass('mask-ccs');
                            $input.after('<div class="mask-error error-ccs"></div>');
                            const $errorDivCCS = $input.next('.error-ccs'); 
                            if ($errorDivCCS.next('.card-logo').length === 0) { 
                                $errorDivCCS.after('<img id="card-logo" class="card-logo" src="" alt="Card Logo">');
                            }
                            if (autoPlaceholder === 'yes') {
                                $input.attr('placeholder', 'XXXX XXXX XXXX XXXX');
                            }
                            break;
                        case 'hyphen':
                            $input.addClass('mask-cch');
                            $input.after('<div class="mask-error error-cch"></div>');
                            const $errorDiv = $input.next('.error-cch'); 
                            if ($errorDiv.next('#card-logo').length === 0) { 
                                $errorDiv.after('<img id="card-logo" class="card-logo" src="" alt="Card Logo">');
                            }
                            if (autoPlaceholder === 'yes') {
                                $input.attr('placeholder', 'XXXX-XXXX-XXXX-XXXX');
                            }                         
                            break;
                        case 'credit_card_date':
                            $input.addClass('mask-ccmy');
                            $input.after('<div class="mask-error error-ccmy"></div>');
                            if(autoPlaceholder === 'yes'){
                                $input.attr('placeholder','XX/XX')
                            }
                            break;
                        case 'credit_card_expiry_date':
                            $input.addClass('mask-ccmyy');
                            $input.after('<div class="mask-error error-ccmyy"></div>');
                            if(autoPlaceholder === 'yes'){
                                $input.attr('placeholder','XX/XXXX')
                            }
                            break;
                        default:
                            $input.addClass('mask-ccs');
                            $input.after('<div class="mask-error error-ccs"></div>');
                            jQuery('.mask-error').after('<img id="card-logo" class="card-logo" src="" alt="Card Logo">')
                            if(autoPlaceholder === 'yes'){
                                $input.attr('placeholder','XXXX XXXX XXXX XXXX')
                            }
                    }
                }
                if (masks[maskKey] === "##/##") {
                  $input.addClass('mask-ccmy');
                  $input.after('<div class="mask-error error-ccmy"></div>');
                  if(autoPlaceholder === 'yes'){
                    $input.attr('placeholder','XX/XX')
                  }
                }
                if (masks[maskKey] === "##/####") {
                  $input.addClass('mask-my');
                  $input.after('<div class="mask-error error-my"></div>');
                  if(autoPlaceholder === 'yes'){
                    $input.attr('placeholder','XX/XXXX')
                  }
                }
                if (masks[maskKey] === "###.###.###.###") {
                  $input.addClass('mask-ipv4');
                  $input.after('<div class="mask-error error-ipv4"></div>');
                  if(autoPlaceholder === 'yes'){
                    $input.attr('placeholder','XXX.XXX.XXX.XXX')
                  }
                }
                if (masks[maskKey] === "###.###.###.###.###,##") {
                  $input.addClass('mask-moneyc');
                  $input.after('<div class="mask-error error-moneyc"></div>');
                  if(autoPlaceholder === 'yes'){
                    let moneyPrefix = ($input.data('moneymask-prefix') !== "") ? $input.data('moneymask-prefix'): '$';
                    let format = ($input.data('moneymask-format') === 'dot' ? ',':'.')
                    $input.attr('placeholder',moneyPrefix+`0${format}00`)
                  }
                }
            }
        });
    }
}

jQuery(window).on('elementor/frontend/init', () => {
    const calHandler = ($element) => {
        elementorFrontend.elementsHandler.addHandler(InputHandler, {
            $element,
        });
    };
  
    elementorFrontend.hooks.addAction('frontend/element_ready/form.default', calHandler);
}); 





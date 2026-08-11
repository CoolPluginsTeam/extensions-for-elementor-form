/**
 * Shared country-code (CCFEF) handler.
 * Wrappers: CFKEF.initCountryCode({ readyHook, selectors, ... }).
 */
window.CFKEF = window.CFKEF || {};

CFKEF.initCountryCode = function (opts) {
  opts = opts || {};
  var readyHook = opts.readyHook || 'frontend/element_ready/form.default';
  var fieldGroupSelector = opts.fieldGroupSelector || '.elementor-field-group';
  var submitGroupClass = opts.submitGroupClass || 'elementor-field-type-submit';
  var telTypeSelector = opts.telTypeSelector || '.elementor-field-type-tel';
  var telInputType = opts.telInputType || 'tel';
  var widgetSelector = opts.widgetSelector || '.elementor-widget.elementor-widget-form';
  var formWidgetSelector = opts.formWidgetSelector || '.elementor-element.elementor-widget-form';
  var telFieldLookup = opts.telFieldLookup || null;
  var enableMdcHandling = !!opts.enableMdcHandling;
  var includeNextButton = !!opts.includeNextButton;
  var selectors = Object.assign({
    inputTelTextArea: 'textarea.ccfef_country_code_data_js',
    intlInputSpan: '.ccfef-editor-intl-input',
    submitButton: 'div.elementor-field-type-submit button',
    form: '.elementor-form',
    nextButton: 'div.elementor-field-type-next button.e-form__buttons__wrapper__button-next'
  }, opts.selectors || {});

  var CountryCodeHandler = class extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
      return { selectors: selectors };
    }
    getDefaultElements() {
      var sel = this.getSettings('selectors');
      var els = {
        $textArea: this.$element.find(sel.inputTelTextArea),
        $intlSpanElement: this.$element.find(sel.intlInputSpan),
        $submitButton: this.$element.find(sel.submitButton),
        $form: this.$element.find(sel.form)
      };
      if (includeNextButton && sel.nextButton) {
        els.$nextButton = this.$element.find(sel.nextButton);
      }
      return els;
    }


    handleTelWithMdcFields(iti) {
        const input = iti.telInput;
        const parentFieldGroup = input.closest(fieldGroupSelector);
        const $parent = jQuery(parentFieldGroup);
    
        // Cache common elements
        const $floatingLabel = $parent.find('.mdc-floating-label');
        const $inputs = $parent.find('input');
        const $notchedOutlineNotch = $parent.find('.mdc-notched-outline__notch');
        const $notchedOutlineLeading = $parent.find('.mdc-notched-outline__leading');
    
        let selectedDialCode = $inputs.eq(1).prev('.iti__country-container').find('.iti__selected-dial-code')
        selectedDialCode.css('visibility','hidden')
        if($inputs.eq(1).val() !== ""){
            selectedDialCode.css('visibility','visible')
        }else{
            selectedDialCode.css('visibility','hidden')
        }
        if ($parent.nextAll().length > 0) {
            const $nextAll = $parent.nextAll();
            if ($nextAll.length > 0) {
                const first = $nextAll[0];
                const second = $nextAll[1];

                const isSubmitGroup = (el) => el && el.classList && el.classList.contains(submitGroupClass);

                const conditionMatched = !$parent.hasClass('has-width-100')
                    ? (isSubmitGroup(first) || isSubmitGroup(second))
                    : isSubmitGroup(first);

                if (conditionMatched) {
                    $parent.css({ 'margin-bottom': '25px' });
                    $parent.find('.iti__country-list').css({ 'max-height': '100px' });
                }
            }
        }

        // Set initial floating label style
        $floatingLabel.css('left', '50px');
    
        // Input focus event for the second input element
        $inputs.eq(1).on('blur',()=>{
            if($inputs.eq(1).val() !== ""){
            selectedDialCode.css('visibility','visible')
            }else{
                selectedDialCode.css('visibility','hidden')
            }
        })
        $inputs.eq(1).on('focus', () => {
            selectedDialCode.css('visibility','visible')
            $floatingLabel.css({
                "left": "50px",
                "background-color": "white"
            });
            const borderTop = getComputedStyle($notchedOutlineNotch[0]).getPropertyValue('border-bottom');
            $notchedOutlineNotch.css({ 'border-top': borderTop });
        });
    
        // Bind a click event on the parent container
        $parent.on('click', (e) => {
            handleMainLogic();
        });
    
        // Mouseover on the first input element
        $inputs.eq(0).on('mouseover', () => {
            const borderWidth = getComputedStyle($notchedOutlineLeading[0]).getPropertyValue('border-bottom-width');
            $notchedOutlineNotch.css({ 'border-top-width': borderWidth, 'border-top-color': 'black' });
        });
    
        // Parent mouseover event
        $parent.on('mouseover', (e) => {
            const borderWidth = getComputedStyle($notchedOutlineLeading[0]).getPropertyValue('border-bottom-width');
            $notchedOutlineNotch.css({ 'border-top-width': borderWidth, 'border-top-color': 'black' });
            handleMainLogic();
        });
    
        // Mouse leave event on the parent container
        $parent.on('mouseleave', (e) => {
            const borderWidth = getComputedStyle($notchedOutlineLeading[0]).getPropertyValue('border-bottom-width');
            const borderColor = getComputedStyle($notchedOutlineLeading[0]).getPropertyValue('border-right-color');
            $notchedOutlineNotch.css({ 'border-top-width': borderWidth, 'border-top-color': borderColor });
            handleMainLogic();
        });
    
        // Inner function to handle the main logic
        function handleMainLogic() {
            const $dropdown = $parent.find('.iti__dropdown-content');
            $parent.nextAll(fieldGroupSelector).each(function() {
                if (!$dropdown.hasClass('iti__hide')) {
                    this.style.zIndex = '-1';
                } else {
                    this.style.zIndex = 'initial';
                }
            });
        
        }
    }


    /**
     * Retrieves the default settings for the country code functionality.
     * @returns {Object} An object containing selector configurations.
     */


    /**
     * Retrieves the default elements based on the settings defined.
     * @returns {Object} An object containing jQuery elements for the text area and editor span.
     */


    /**
     * Binds events to the elements. This method is intended to be overridden by subclasses to add specific event handlers.
     */
    bindEvents() {
        this.telId = new Array();

        this.includeCountries = {};

        this.excludeCountries = {};

        this.defaultCountry = {};

        this.commonCountries = {};

        this.iti = {};
        
        this.dialCodeVisibility = {};

        this.countryStrictMode = {}; 
        
        this.getIntlUserData(); // Retrieves international telephone input data from the DOM and stores them for further processing.

        this.appendCountryCodeHandler(); // Appends a country code handler to each telephone input field to manage country code functionality.

        this.addCountryCodeInputHandler(); // Adds a country code input handler that initializes the international telephone input functionality.

        this.customFlags() // custom load svg flags

        this.removeInputTelSpanEle(); // Removes the telephone input span element from the DOM, typically used to clean up after modifications.

        this.intlInputValidation(); // Validates the international input fields to ensure they meet specific criteria.

        this.setCountryFieldsLabelTyprography();

    }

    setCountryFieldsLabelTyprography() {
        setTimeout(() => {
            // Get typography values from a normal field label (direct child of .elementor-field-group)
            let fieldLabel = jQuery('.elementor-field-group > label');
            if (fieldLabel.length > 0) {
                let styleData = getComputedStyle(fieldLabel[0]);
                let fieldFontFamily = styleData.getPropertyValue('font-family');
                let fieldFontSize = styleData.getPropertyValue('font-size');
                let fieldFontStyle = styleData.getPropertyValue('font-style');
                let fieldFontWeight = styleData.getPropertyValue('font-weight');
                let fieldLineHeight = styleData.getPropertyValue('line-height');
                let fieldLetterSpacing = styleData.getPropertyValue('letter-spacing');
                let fieldTextTransform = styleData.getPropertyValue('text-transform');
                let fieldTextDecoration = styleData.getPropertyValue('text-decoration');
                let fieldTextColor = styleData.getPropertyValue('color');
  
                // Apply the typography values to the custom country code field labels.
                // Adjust the selector '.iti .elementor-field-label' if your markup differs.
                jQuery('.elementor-field-group .iti .elementor-field-label').css({
                    'font-family': fieldFontFamily,
                    'font-size': fieldFontSize,
                    'font-style': fieldFontStyle,
                    'font-weight': fieldFontWeight,
                    'line-height': fieldLineHeight,
                    'letter-spacing': fieldLetterSpacing,
                    'text-transform': fieldTextTransform,
                    'text-decoration': fieldTextDecoration,
                    'color': fieldTextColor,
                });
            }
        }, 100);
    }
    /**
     * Method to handle appending country code.
     */
    appendCountryCodeHandler() {
        this.telId.forEach(data => {
            this.addCountryCodeIconHandler(data.formId, data.fieldId, data.customId);
        });
    }


    /**
     * Method to handle country code input.
     */
    addCountryCodeInputHandler() {
        const itiArr = this.iti;

        Object.keys(itiArr).forEach(key => {
            const iti = itiArr[key];
            if (enableMdcHandling) { this.handleTelWithMdcFields(iti); }

            const inputElement = iti.telInput;

            let previousCountryData = iti.getSelectedCountryData();
            let previousCode = `+${previousCountryData.dialCode}`;
            let keyUpEvent = false;

            const resetKeyUpEventStatus = () => {
                keyUpEvent = false;
            };

            const handleCountryChange = (e) => {
                if (enableMdcHandling) { this.handleTelWithMdcFields(iti); }
                this.customFlags();
                this.TelFieldInputEventHandler(inputElement)
                const currentCountryData = iti.getSelectedCountryData();
                const currentCode = `+${currentCountryData.dialCode}`;
                if (e.type === 'keydown' || e.type=== 'input') {
                    keyUpEvent = true;
                    clearTimeout(resetKeyUpEventStatus);
                    setTimeout(resetKeyUpEventStatus, 400);

                    if (previousCountryData.dialCode !== currentCountryData.dialCode) {
                        previousCountryData = currentCountryData;
                    } else if (previousCountryData.dialCode === currentCountryData.dialCode && previousCountryData.iso2 !== currentCountryData.iso2) {
                        iti.setCountry(previousCountryData.iso2);
                    }
                } else if (e.type === "countrychange") {
                    if (keyUpEvent) {
                        return;
                    }

                    previousCountryData = currentCountryData;
                }

                if(e.currentTarget.value.startsWith(currentCode.replace('+',''))){
                    this.updateCountryCodeHandler(e.currentTarget, '+', previousCode, this.dialCodeVisibility[key]);
                }else{
                    this.updateCountryCodeHandler(e.currentTarget, currentCode, previousCode, this.dialCodeVisibility[key]);
                    previousCode = currentCode;
                }
            };

            // Attach event listeners for both keyup and country change events
            this.TelFieldInputEventHandler(inputElement)
            inputElement.addEventListener('keydown', handleCountryChange);
            inputElement.addEventListener('input', handleCountryChange);
            inputElement.addEventListener('countrychange', handleCountryChange);
        });
    }

    TelFieldInputEventHandler(inputEl){
        inputEl = jQuery(inputEl);
        const itiEl = inputEl.closest('.iti');
        inputEl.on('focus', () => {
            itiEl.addClass('input-focus');
        });
        inputEl.on('blur', () => {
            itiEl.removeClass('input-focus');
            checkValue();
        });

        inputEl.on('input', checkValue);

        // Initial value check
        checkValue();
        function checkValue() {
            if (inputEl.val().trim() === '') {
                itiEl.addClass('input-no-val');
            } else {
                itiEl.removeClass('input-no-val');
            }
        }
    }
     /**
     * Method to handle adding country code icon.
     * @param {string} id - The ID of the element.
     * @param {string} widgetId - The widget ID.
     */
     addCountryCodeIconHandler(formId, widgetId, inputId) {
        const utilsPath = CCFEFCustomData.pluginDir + 'assets/js/utils.js';
        const telFIeld = (typeof telFieldLookup === "function") ? telFieldLookup(formId, inputId) : jQuery(`${widgetSelector}[data-id="${formId}"] ${telTypeSelector}${fieldGroupSelector} input[type="${telInputType}"]#${inputId}`)[0];
        
        if (undefined !== telFIeld) {
            let includeCountries = [];
            let excludeCountries = [];
            let defaultCountry = 'in';
            const defaultCoutiresArr = ['in','us','gb','ru','fr','de','br','cn','jp','it'];
            const uniqueId = `${formId}${widgetId}`;
        
            if (this.includeCountries.hasOwnProperty(uniqueId) && this.includeCountries[uniqueId].length > 0) {
                defaultCountry = this.includeCountries[uniqueId][0];
                includeCountries = [...this.includeCountries[uniqueId]];
            }
        
            if (this.excludeCountries.hasOwnProperty(uniqueId) && this.excludeCountries[uniqueId].length > 0) {
                let uniqueValue = defaultCoutiresArr.filter((value) => !this.excludeCountries[uniqueId].includes(value));
                defaultCountry = uniqueValue[0];
                excludeCountries = [...this.excludeCountries[uniqueId]];
            }
        
            if (this.defaultCountry[uniqueId] && '' !== this.defaultCountry[uniqueId]) {
                defaultCountry = this.defaultCountry[uniqueId];
            }
            
            // Initialize the international telephone input.
            const iti = window.intlTelInput(telFIeld, {
                initialCountry: defaultCountry,
                utilsScript: utilsPath,
                dialCodeVisibility: this.dialCodeVisibility[uniqueId],
                strictMode: (this.countryStrictMode[uniqueId] === 'yes') ? true : false, 
                separateDialCode: this.dialCodeVisibility[uniqueId] === 'separate' ? true : false,
                formatOnDisplay: false,
                formatAsYouType: true,
                autoFormat: false,
                containerClass: 'cfefp-intl-container',
                useFullscreenPopup: false,
                onlyCountries: includeCountries,
                excludeCountries: excludeCountries,
                customPlaceholder: (selectedCountryPlaceholder, selectedCountryData) => {
                    
                    // If the commonAttr flag is 'same', return a simple placeholder.
                    if (this.commonCountries[uniqueId]) {
                        return "No country found";
                    }
                    
                    if (!selectedCountryData || !selectedCountryPlaceholder || !selectedCountryData.dialCode) {
                        return "No country found";
                    }
                    
                    let placeHolder = selectedCountryPlaceholder;
                    if ('in' === selectedCountryData.iso2) {
                        placeHolder = selectedCountryPlaceholder.replace(/^0+/, '');
                    }
                    
                    const placeholderText = this.dialCodeVisibility[uniqueId] === 'separate' || this.dialCodeVisibility[uniqueId] === 'hide' ? `${placeHolder}` : `+${selectedCountryData.dialCode} ${placeHolder}`;
                    return placeholderText;
                },            
            });
            
            // Add styling for separate dial code
            if (this.dialCodeVisibility[uniqueId] === 'separate') {
                const style = document.createElement('style');
                style.textContent = `
                    .cfefp-intl-container .iti__selected-dial-code,
                    .cfefp-intl-container .iti__selected-flag {
                        color: var(--e-form-field-text-color, #7a7a7a) !important;
                    }
                    .cfefp-intl-container .iti__selected-dial-code {
                        font-size: inherit !important;
                        font-family: inherit !important;
                        line-height: inherit !important;
                    }
                `;
                document.head.appendChild(style);
            }
            jQuery(telFIeld).attr('data-uniqueid',uniqueId)
            // Retrieve commonAttr from the hidden span to decide whether to hide the country list.
            const intlSpan = document.querySelector(`${widgetSelector}[data-id="${formId}"] .ccfef-editor-intl-input[data-field-id="${widgetId}"]`);
            const commonAttr = intlSpan ? intlSpan.getAttribute('data-common-countries') : '';
            if ('same' === commonAttr && this.commonCountries[uniqueId] && '' !== includeCountries && '' !== excludeCountries) {
                const countryList = iti.countryList;
                if (countryList && countryList.classList.contains('iti__country-list')) {
                    countryList.style.display = 'none';
                }
            } else {
                // Filter the country list: show only the countries that are in includeCountries and not in excludeCountries.
                const countryList = iti.countryList;
                if (countryList && countryList.classList.contains('iti__country-list')) {
                    // Select all individual country items.
                    const countryItems = countryList.querySelectorAll('.iti__country');
                    
                    // Hide items if they are in the excludeCountries list.
                    countryItems.forEach(function(item) {
                        const countryCode = item.getAttribute('data-country-code');
                        if (excludeCountries.includes(countryCode)) {
                            item.style.display = 'none';
                        }
                    });
                    
                    // Get the remaining visible country items.
                    const visibleCountries = Array.from(countryItems).filter(item => item.style.display !== 'none');
                    
                    // Filter those visible items that are present in includeCountries.
                    const includedVisibleCountries = visibleCountries.filter(item => {
                        const countryCode = item.getAttribute('data-country-code');
                        return includeCountries.includes(countryCode);
                    });
                    
                    // If there are any visible items in the include list, select the first one.
                    if (includedVisibleCountries.length > 0) {
                        const selectedItem = includedVisibleCountries.find(item => item.getAttribute('aria-selected') === 'true');
                        if (!selectedItem) {
                            const firstItem = includedVisibleCountries[0];
                            firstItem.setAttribute('aria-selected', 'true');
                            // Update the intlTelInput instance so that the country selection is reflected in the field.
                            const newCountryCode = firstItem.getAttribute('data-country-code');
                            iti.setCountry(newCountryCode);
                        }
                    }
                }
            }
            
            telFIeld.removeAttribute('pattern');
            this.iti[formId + widgetId] = iti;
            this.setInitialCountry(iti, excludeCountries, uniqueId, telFIeld );

        }
    }    
    

    /**
     * Sets the initial selected country in the dropdown.
     * @param {Object} itiInstance - The intl-tel-input instance.
     * @param {string} autoDetectCountry - Auto-detect country setting.
     * @param {string} defaultCountry - Default country code.
     * @param {string} apiKey - API key for geo-location services.
     * @param {Array} excludeCountries - List of countries to exclude.
     */
    setInitialCountry(itiInstance, excludeCountries, uniqueId, telField) {       
        const defaultCountry = this.defaultCountry[uniqueId] || "";
        const defaultCountries = ['in', 'us', 'gb', 'ru', 'fr', 'de', 'br', 'cn', 'jp', 'it'];
        const itiCountriesList = itiInstance.countries.map(data => data.iso2);
  
        if (jQuery(telField).closest(telTypeSelector).hasClass('elementor-field-required') &&
            jQuery(telField).closest(telTypeSelector).hasClass('cfef-hidden')) {
            if (defaultCountry === "") {
                itiInstance.setCountry("us");
                jQuery(telField).val("United States");    
                jQuery(telField).focus()  
                jQuery(telField).trigger('change');
            } else {
                itiInstance.setCountry(defaultCountry);
                jQuery(telField).val(defaultCountry);
                jQuery(telField).focus()    
                jQuery(telField).trigger('change');
            }
        } 
     
        const setCountry = (countryCode) => {
            if (itiCountriesList.length <= 0) {
                return;
            }
            const normalizedCountryCode = isNaN(countryCode) && countryCode ? countryCode.toLowerCase() : '';
            if (normalizedCountryCode && itiCountriesList.includes(normalizedCountryCode)) {
                itiInstance.setCountry(normalizedCountryCode);
            } else if (defaultCountry && itiCountriesList.includes(defaultCountry)) {
                itiInstance.setCountry(defaultCountry);
            } else {
                const availableCountries = defaultCountries.filter(country =>
                    !excludeCountries.includes(country) && itiCountriesList.includes(country)
                );
                const fallbackCountry = availableCountries.length > 0 ? availableCountries[0] : itiCountriesList[0];
                itiInstance.setCountry(fallbackCountry);
            }
        };
  
        if (defaultCountry) {
            setCountry(defaultCountry);
        }
    }

    /**
     * Method to update country code.
     * @param {Element} element - The input element.
     * @param {string} countryCode - The country code.
     * @param {string} previousCode - The previous country code.
     */
    updateCountryCodeHandler(element, currentCode, previousCode,dialCodeVisibility) {
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

    customFlags() {
        const selectedCountries = this.$element.find('.cfefp-intl-container .iti__country-container .iti__flag:not(.iti__globe)');
    
        // Loop through each flag element
        selectedCountries.each(function() {
            const selectedCountry = this;  // 'this' refers to the current element in the loop
            const classList = selectedCountry.className.split(' '); 
            
            if (classList[1]) {
                const selectedCountryFlag = classList[1].split('__')[1]; 
                const svgFlagPath = CCFEFCustomData.pluginDir + `assets/flags/${selectedCountryFlag}.svg`;

                // Apply the styles dynamically to the current flag
                selectedCountry.style.backgroundImage = `url('${svgFlagPath}')`;
            } 
        });
    }
        
    /**
     * Removes the span element with class 'ccfef-editor-intl-input' from the DOM.
     */
    removeInputTelSpanEle() {
    }

    /**
     * Retrieves and stores unique telephone input IDs from the Elementor editor span elements.
     */
    getIntlUserData() {
        const intelInputElement = this.elements.$intlSpanElement;
        const previousIds = [];
    
        if(intelInputElement.length > 0){
            intelInputElement.closest(telTypeSelector).addClass('country-code-enabled')
        }

        intelInputElement.each((_, ele) => {
            const element = jQuery(ele);
            const includeCountries = element.data('include-countries');
            const excludeCountries = element.data('exclude-countries');
            const defaultCountry = element.data('defaultCountry');
            const commonAttr = element.data('common-countries');
            const inputId = element.data('id');
            const fieldId = element.data('field-id');
            const dialCodeVisibility=element.data('dial-code-visibility');
            const countryStrictMode = element.data('strict-mode');


            const formId = element.closest(formWidgetSelector).data('id');
            const currentId = `${formId}${fieldId}`;
    
            if ('same' === commonAttr && '' === includeCountries && '' !== excludeCountries) {
                // NEW: Store flag for use in the custom placeholder function.
                this.commonCountries[currentId] = true;
            } else {
                if ('' !== includeCountries) {
                    if (isNaN(includeCountries)) {
                        const splitIncludeCountries = includeCountries.split(',');
                        this.includeCountries[currentId] = splitIncludeCountries;
                    }
                }
    
                if ('' !== excludeCountries) {
                    if (isNaN(excludeCountries)) {
                        const splitExcludeCountries = excludeCountries.split(',');
                        this.excludeCountries[currentId] = splitExcludeCountries;
                    }
                }
    
                // NEW: If commonAttr is not 'same' but all values in includeCountries are also present in excludeCountries, set commonCountries flag.
                if ('same' !== commonAttr && '' !== includeCountries && '' !== excludeCountries) {
                    if (isNaN(includeCountries)) {
                        const includeArray = includeCountries.split(',').map(item => item.trim());
                        if (isNaN(excludeCountries)) {
                            const excludeArray = excludeCountries.split(',').map(item => item.trim());
                            const allIncludedPresent = includeArray.every(country => excludeArray.includes(country));
                            if (allIncludedPresent) {
                                this.commonCountries[currentId] = true;
                            }
                        }
                    }
                }
       
                if('' !== dialCodeVisibility){
                    this.dialCodeVisibility[currentId] = dialCodeVisibility;
                }

                if('' !== countryStrictMode){
                    this.countryStrictMode[currentId] = countryStrictMode
                }

                if ('' !== defaultCountry) {
                    this.defaultCountry[currentId] = defaultCountry;
                }
            }
    
            if (!previousIds.includes(currentId)) {
                this.telId.push({ formId, fieldId, customId: inputId });
                previousIds.push(currentId);
            }
        });
    }    

    /**
     * Show or clear Cool Form (MDC) tel validation UI.
     * @param {Element} inputTelElement
     * @param {string} message Empty string clears the error.
     * @returns {boolean} Whether MDC UI was handled.
     */
    setMdcTelValidationState(inputTelElement, message) {
        if (!enableMdcHandling || !inputTelElement) {
            return false;
        }

        const mdcRoot = inputTelElement.closest('.mdc-text-field');
        const parentWrp = inputTelElement.closest(fieldGroupSelector);
        if (!mdcRoot || !parentWrp) {
            return false;
        }

        const errorMessage = message || '';
        const hasError = '' !== errorMessage;
        const trailingIcon = parentWrp.querySelector('.cool-tel-error-icon, .mdc-text-field__icon--trailing');
        const helperText = parentWrp.querySelector('.mdc-text-field-helper-text');

        // Keep helper text visible after submit steals focus from the input.
        if (helperText) {
            helperText.textContent = errorMessage;
            helperText.setAttribute('aria-hidden', hasError ? 'false' : 'true');
            helperText.classList.toggle('mdc-text-field-helper-text--validation-msg', hasError);
            helperText.classList.toggle('mdc-text-field-helper-text--persistent', hasError);
        }

        if (trailingIcon) {
            trailingIcon.style.display = hasError ? 'initial' : 'none';
        }

        if (hasError) {
            mdcRoot.classList.add('mdc-text-field--invalid');
        } else {
            mdcRoot.classList.remove('mdc-text-field--invalid');
        }

        if (typeof mdc !== 'undefined' && mdc.textfield && mdc.textfield.MDCTextField) {
            try {
                let mdcField = null;
                if (typeof mdc.textfield.MDCTextField.getInstance === 'function') {
                    mdcField = mdc.textfield.MDCTextField.getInstance(mdcRoot);
                }
                if (!mdcField) {
                    mdcField = mdc.textfield.MDCTextField.attachTo(mdcRoot);
                }
                mdcField.valid = !hasError;
                if (mdcField.trailingIcon && mdcField.trailingIcon.root) {
                    mdcField.trailingIcon.root.style.display = hasError ? 'initial' : 'none';
                }
                if (mdcField.helperText && mdcField.helperText.foundation) {
                    const foundation = mdcField.helperText.foundation;
                    if (typeof foundation.setValidation === 'function') {
                        foundation.setValidation(hasError);
                    }
                    if (typeof foundation.setPersistent === 'function') {
                        foundation.setPersistent(hasError);
                    }
                    if (foundation.adapter && typeof foundation.adapter.setContent === 'function') {
                        foundation.adapter.setContent(errorMessage);
                    }
                    if (typeof foundation.setValidity === 'function') {
                        foundation.setValidity(!hasError);
                    }
                }
            } catch (err) {
                // DOM updates above are enough as fallback.
            }
        }

        return true;
    }

    /**
     * Validates the international telephone input fields when the submit button is clicked.
     * It checks if the number is valid and displays appropriate error messages next to the input field.
     */
    intlInputValidation() {
        this.elements.$submitButton.each((index, button) => {
          button.addEventListener('click', (e) => {
            const itiArr = this.iti;
            let firstInvalidInput = null;

            if (Object.keys(itiArr).length > 0) {
                Object.keys(itiArr).forEach(data => {
                    const iti = itiArr[data];
              
                    const inputTelElement = iti.telInput;                    

                    if('' !== inputTelElement.value){
                        inputTelElement.value=inputTelElement.value.replace(/[^0-9+]/g, '');
                                                                        
                        // Always ensure dial code is present in the value before validation
                        const currentCountryData = iti.getSelectedCountryData();
                        const dialCode = `+${currentCountryData.dialCode}`;
                        
                        // If using separate or hide mode, ensure dial code is in the value
                        if (this.dialCodeVisibility[data] === 'separate' || this.dialCodeVisibility[data] === 'hide') {
                            if (!inputTelElement.value.startsWith('+')) {
                                inputTelElement.value = dialCode + inputTelElement.value;
                            }
                        }
                    }

                    const parentWrp = inputTelElement.closest(fieldGroupSelector);
                    const telContainer=parentWrp.querySelector('.cfefp-intl-container');

                    if (telContainer && inputTelElement.offsetHeight) {
                        telContainer.style.setProperty('--cfefp-intl-tel-button-height', `${inputTelElement.offsetHeight}px`);
                    }

                    const errorContainer = jQuery(inputTelElement).parent();
                    errorContainer.find('span.elementor-message').remove();

                    const errorMap = CCFEFCustomData.errorMap;
                    let errorMsgHtml = '<span class="elementor-message elementor-message-danger elementor-help-inline elementor-form-help-inline" role="alert">';
                    if('' === inputTelElement.value){
                        return;
                    };
                    if (iti.isValidNumber()) {
                        jQuery(inputTelElement).closest('.cfefp-intl-container').removeClass('elementor-error');
                        this.setMdcTelValidationState(inputTelElement, '');
                    } else {


                        const currentCountryData = iti.getSelectedCountryData();
                        const inputVal = inputTelElement.value;

                        // Special validation for Israeli landline numbers
                        if(currentCountryData.dialCode === '972' && currentCountryData.iso2 === 'il'){

                            // Get the full number (with country code)
                            const fullNumber = inputVal.startsWith('+') ? inputVal : `+${currentCountryData.dialCode}${inputVal}`;

                            // Extract number after +972 (remove country code and any non-digits)
                            let numberAfterCountryCode = fullNumber.replace(/^\+972/, '').replace(/\D/g, '');

                            if (numberAfterCountryCode.startsWith('0')) {
                                numberAfterCountryCode = numberAfterCountryCode.substring(1);
                            }

                            // Valid landline area codes (without leading 0): 2, 3, 4, 8, 9
                            const validLandlinePrefixes = ['2', '3', '4', '8', '9'];
                            
                            // Israeli landline format: exactly 8 digits after +972 (without leading 0)
                            // Format: +972 + [2|3|4|8|9] + 7 more digits = 8 digits total
                            // Handles both: +97221234567 (8 digits) and +972021234567 (9 digits, we remove the 0)
                            if (numberAfterCountryCode.length === 8) {
                                const firstDigit = numberAfterCountryCode.charAt(0);
                                
                                if (validLandlinePrefixes.includes(firstDigit)) {
                                    // Valid Israeli landline number - allow it even if general validation failed
                                    jQuery(inputTelElement).closest('.cfefp-intl-container').removeClass('elementor-error');
                                    this.setMdcTelValidationState(inputTelElement, '');
                                    
                                    return; // Exit early, don't show error
                                }
                            }
                            // If not a valid landline, continue with normal error handling below
                        }

                        const errorType = iti.getValidationError();
                        if (errorType !== undefined && errorMap[errorType]) {
                            // Remove dial code from input field if validation fails
                            if (this.dialCodeVisibility[data] === 'separate' || this.dialCodeVisibility[data] === 'hide') {
                                const currentCountryData = iti.getSelectedCountryData();
                                const dialCode = `+${currentCountryData.dialCode}`;
                                if (inputTelElement.value.startsWith(dialCode)) {
                                    inputTelElement.value = inputTelElement.value.substring(dialCode.length);
                                }
                            }
                            
                            const errorMessage = errorMap[errorType];
                            jQuery(inputTelElement).closest('.cfefp-intl-container').addClass('elementor-error');

                            // Cool Form uses MDC helper text + trailing icon (not Elementor messages).
                            if (!this.setMdcTelValidationState(inputTelElement, errorMessage)) {
                                errorMsgHtml += errorMessage + '</span>';
                                jQuery(inputTelElement).after(errorMsgHtml);
                            }

                            if (!firstInvalidInput) {
                                firstInvalidInput = inputTelElement;
                            }

                            e.preventDefault();
                            e.stopImmediatePropagation();
                        }
                    }
                });
            }

            // Restore focus so MDC invalid styles paint immediately after submit.
            if (firstInvalidInput) {
                setTimeout(() => {
                    firstInvalidInput.focus();
                }, 0);
            }
          }, true);
        });
    }
    

  };

  jQuery(window).on('elementor/frontend/init', function () {
    var addHandler = function ($element) {
      elementorFrontend.elementsHandler.addHandler(CountryCodeHandler, { $element: $element });
    };
    elementorFrontend.hooks.addAction(readyHook, addHandler);
  });
};

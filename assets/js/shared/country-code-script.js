/**
 * Shared country-code (CCFEF) handler.
 * Wrappers: CFKEF.initCountryCode({ readyHook, selectors, ... }).
 */
window.CFKEF = window.CFKEF || {};

/**
 * Shared dial-code prefix updater used by Elementor / Cool Form / Hello Plus / Atomic.
 *
 * @param {HTMLInputElement} element
 * @param {string} currentCode
 * @param {string} previousCode
 * @param {string} dialCodeVisibility
 */
CFKEF.updateCountryCodeHandler = function (element, currentCode, previousCode, dialCodeVisibility) {
	var value = element.value;

	if ((currentCode && '+undefined' === currentCode) || ['', '+'].includes(value)) {
		return;
	}

	if (currentCode !== previousCode) {
		value = value.replace(new RegExp('^\\' + previousCode), '');
	}

	if (!value.startsWith(currentCode)) {
		value = value.replace(/\+/g, '');
		element.value = dialCodeVisibility === 'separate' || dialCodeVisibility === 'hide' ? value : currentCode + value;
	} else if (value.length > 12) {
		var plainCode = currentCode.replace('+', '');
		var doublePrefix = '+' + plainCode + plainCode;

		if (value.startsWith(doublePrefix)) {
			element.value = '+' + value.slice(currentCode.length);
		}
	}
};

/**
 * Build intlTelInput options from wrapper data attributes.
 *
 * @param {Object} config
 * @return {Object}
 */
CFKEF.buildItiOptions = function (config) {
	config = config || {};

	var includeArr = config.includeCountries;
	var excludeArr = config.excludeCountries;

	if (typeof includeArr === 'string') {
		includeArr = includeArr ? includeArr.split(',') : [];
	}
	includeArr = includeArr || [];

	if (typeof excludeArr === 'string') {
		excludeArr = excludeArr ? excludeArr.split(',') : [];
	}
	excludeArr = excludeArr || [];

	var dialCodeVisibility = config.dialCodeVisibility || 'show';
	var defaultCountry = config.defaultCountry || '';
	var strictMode = !!config.strictMode;
	var utilsPath = config.utilsScript || (
		window.CCFEFCustomData && CCFEFCustomData.pluginDir
			? CCFEFCustomData.pluginDir + 'assets/js/utils.js'
			: ''
	);
	var containerClass = config.containerClass || 'cfefp-intl-container';
	var defaultCountriesArr = ['in', 'us', 'gb', 'ru', 'fr', 'de', 'br', 'cn', 'jp', 'it'];

	if (excludeArr.length > 0 && includeArr.length > 0) {
		includeArr = includeArr.filter(function (code) {
			return excludeArr.indexOf(code) === -1;
		});
	}

	if (!defaultCountry && includeArr.length > 0) {
		defaultCountry = includeArr[0];
	} else if (defaultCountry && includeArr.length > 0 && includeArr.indexOf(defaultCountry) === -1) {
		defaultCountry = includeArr[0];
	}

	if (!defaultCountry && excludeArr.length > 0 && includeArr.length === 0) {
		var uniqueValue = defaultCountriesArr.filter(function (code) {
			return excludeArr.indexOf(code) === -1;
		});
		defaultCountry = uniqueValue[0] || 'in';
	}

	if (!defaultCountry || defaultCountry === 'NAN') {
		defaultCountry = 'in';
	}

	var options = {
		initialCountry: defaultCountry,
		utilsScript: utilsPath,
		strictMode: strictMode,
		separateDialCode: dialCodeVisibility === 'separate',
		formatOnDisplay: false,
		formatAsYouType: true,
		autoFormat: false,
		containerClass: containerClass,
		useFullscreenPopup: false,
	};

	if (includeArr.length) {
		options.onlyCountries = includeArr;
	}
	if (excludeArr.length) {
		options.excludeCountries = excludeArr;
	}

	return options;
};

/**
 * Israeli landline numbers that intlTelInput rejects but should still pass.
 *
 * @param {Object} iti
 * @param {string} inputVal
 * @return {boolean}
 */
CFKEF.isIsraeliLandlineException = function (iti, inputVal) {
	var currentCountryData = iti.getSelectedCountryData();
	if (currentCountryData.dialCode !== '972' || currentCountryData.iso2 !== 'il') {
		return false;
	}

	var fullNumber = inputVal.startsWith('+') ? inputVal : '+' + currentCountryData.dialCode + inputVal;
	var numberAfterCountryCode = fullNumber.replace(/^\+972/, '').replace(/\D/g, '');
	if (numberAfterCountryCode.charAt(0) === '0') {
		numberAfterCountryCode = numberAfterCountryCode.substring(1);
	}

	var validLandlinePrefixes = ['2', '3', '4', '8', '9'];
	if (numberAfterCountryCode.length !== 8) {
		return false;
	}
	return validLandlinePrefixes.indexOf(numberAfterCountryCode.charAt(0)) !== -1;
};

/**
 * Validate one intlTelInput instance for submit.
 *
 * @param {Object} iti
 * @param {Object} [opts]
 * @param {string} [opts.dialCodeVisibility]
 * @param {Array|Object} [opts.errorMap]
 * @param {Function} [opts.isHidden] (inputEl) => boolean
 * @param {Function} [opts.onAfterSanitize] (inputEl) => void
 * @return {{ valid: boolean, skipped: boolean, input: HTMLElement, errorMessage: string|null }}
 */
CFKEF.validateItiOnSubmit = function (iti, opts) {
	opts = opts || {};
	var dialCodeVisibility = opts.dialCodeVisibility || 'show';
	var errorMap = opts.errorMap || (window.CCFEFCustomData && CCFEFCustomData.errorMap) || [];
	var inputTelElement = iti.telInput;

	if ('' !== inputTelElement.value) {
		inputTelElement.value = inputTelElement.value.replace(/[^0-9+]/g, '');

		var currentCountryData = iti.getSelectedCountryData();
		var dialCode = '+' + currentCountryData.dialCode;

		if (dialCodeVisibility === 'separate' || dialCodeVisibility === 'hide') {
			if (!inputTelElement.value.startsWith('+')) {
				inputTelElement.value = dialCode + inputTelElement.value;
			}
		}
	}

	if (typeof opts.onAfterSanitize === 'function') {
		opts.onAfterSanitize(inputTelElement);
	}

	if ('' === inputTelElement.value) {
		return { valid: true, skipped: true, input: inputTelElement, errorMessage: null };
	}

	if (typeof opts.isHidden === 'function' && opts.isHidden(inputTelElement)) {
		return { valid: true, skipped: true, input: inputTelElement, errorMessage: null };
	}

	if (iti.isValidNumber()) {
		return { valid: true, skipped: false, input: inputTelElement, errorMessage: null };
	}

	if (CFKEF.isIsraeliLandlineException(iti, inputTelElement.value)) {
		return { valid: true, skipped: false, input: inputTelElement, errorMessage: null };
	}

	var errorType = iti.getValidationError();
	var errorMessage =
		errorType !== undefined && errorMap[errorType] ? errorMap[errorType] : null;

	if (errorMessage && (dialCodeVisibility === 'separate' || dialCodeVisibility === 'hide')) {
		var failCountryData = iti.getSelectedCountryData();
		var failDialCode = '+' + failCountryData.dialCode;
		if (inputTelElement.value.startsWith(failDialCode)) {
			inputTelElement.value = inputTelElement.value.substring(failDialCode.length);
		}
	}

	return {
		valid: !errorMessage,
		skipped: false,
		input: inputTelElement,
		errorMessage: errorMessage,
	};
};

/**
 * Create a dial-code / iso2 sync handler for an intlTelInput instance.
 *
 * @param {Object} iti intlTelInput instance
 * @param {Object} [opts]
 * @param {string} [opts.dialCodeVisibility]
 * @param {boolean} [opts.enableCountryChangeGuard]
 * @param {number} [opts.debounceMs]
 * @param {Function} [opts.onBeforeUpdate]
 * @param {Function} [opts.updateHandler]
 * @return {Function}
 */
CFKEF.createItiCountryChangeHandler = function (iti, opts) {
	opts = opts || {};
	var previousCountryData = iti.getSelectedCountryData();
	var previousCode = '+' + previousCountryData.dialCode;
	var keyUpEvent = false;
	var dialCodeVisibility = opts.dialCodeVisibility || 'show';
	var enableGuard = !!opts.enableCountryChangeGuard;
	var debounceMs = opts.debounceMs || 400;
	var onBeforeUpdate = typeof opts.onBeforeUpdate === 'function' ? opts.onBeforeUpdate : function () {};
	var updateHandler =
		typeof opts.updateHandler === 'function' ? opts.updateHandler : CFKEF.updateCountryCodeHandler;

	var resetKeyUpEventStatus = function () {
		keyUpEvent = false;
	};

	return function handleCountryChange(e) {
		onBeforeUpdate(e);

		var currentCountryData = iti.getSelectedCountryData();
		var currentCode = '+' + currentCountryData.dialCode;

		if (e.type === 'keydown' || e.type === 'input') {
			if (enableGuard) {
				keyUpEvent = true;
				clearTimeout(resetKeyUpEventStatus);
				setTimeout(resetKeyUpEventStatus, debounceMs);
			}

			if (previousCountryData.dialCode !== currentCountryData.dialCode) {
				previousCountryData = currentCountryData;
			} else if (
				previousCountryData.dialCode === currentCountryData.dialCode &&
				previousCountryData.iso2 !== currentCountryData.iso2
			) {
				iti.setCountry(previousCountryData.iso2);
			}
		} else if (e.type === 'countrychange') {
			if (enableGuard && keyUpEvent) {
				return;
			}
			previousCountryData = currentCountryData;
		}

		if (e.currentTarget.value.startsWith(currentCode.replace('+', ''))) {
			updateHandler(e.currentTarget, '+', previousCode, dialCodeVisibility);
		} else {
			updateHandler(e.currentTarget, currentCode, previousCode, dialCodeVisibility);
			previousCode = currentCode;
		}
	};
};

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
        if (!input) {
            return;
        }
        const parentFieldGroup = input.closest(fieldGroupSelector);
        if (!parentFieldGroup) {
            return;
        }
        const $parent = jQuery(parentFieldGroup);
        const $telInput = jQuery(input);
        const $iti = $telInput.closest('.iti');

        // Avoid stacking duplicate handlers when countrychange re-runs this.
        if ($parent.data('ccfefMdcBound')) {
            return;
        }
        $parent.data('ccfefMdcBound', true);

        // Cache common elements
        const $floatingLabel = $parent.find('.mdc-floating-label');
        const $searchInput = $iti.find('input.iti__search-input');
        const $notchedOutlineNotch = $parent.find('.mdc-notched-outline__notch');
        const $notchedOutlineLeading = $parent.find('.mdc-notched-outline__leading');
        const $selectedDialCode = $iti.find('.iti__country-container .iti__selected-dial-code');

        const syncDialCodeVisibility = () => {
            if (!$selectedDialCode.length) {
                return;
            }
            $selectedDialCode.css('visibility', $telInput.val() !== '' ? 'visible' : 'hidden');
        };
        syncDialCodeVisibility();

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

        // Make room for the flag button inside the outlined field.
        $floatingLabel.css('left', '50px');

        // Match dropdown width/left to the outer MDC outline (not the padded tel input).
        const syncDropdownToField = () => {
            const field = input.closest('.mdc-text-field') || input.closest('.cool-form-text');
            const countryContainer = $iti[0] && $iti[0].querySelector('.iti__country-container');
            const dropdown = $iti[0] && $iti[0].querySelector('.iti__dropdown-content');
            if (!field || !countryContainer || !dropdown) {
                return;
            }
            const fieldRect = field.getBoundingClientRect();
            const containerRect = countryContainer.getBoundingClientRect();
            const width = Math.round(fieldRect.width);
            const left = Math.round(fieldRect.left - containerRect.left);
            dropdown.style.setProperty('width', width + 'px', 'important');
            dropdown.style.setProperty('max-width', width + 'px', 'important');
            dropdown.style.setProperty('left', left + 'px', 'important');
            dropdown.style.setProperty('right', 'auto', 'important');
            dropdown.style.setProperty('margin-left', '0', 'important');
        };
        input.addEventListener('open:countrydropdown', () => {
            requestAnimationFrame(syncDropdownToField);
        });
        jQuery(window).on('resize.ccfefMdcDropdown', () => {
            if ($iti.find('.iti__dropdown-content:not(.iti__hide)').length) {
                syncDropdownToField();
            }
        });

        $telInput.on('blur.ccfefMdc', () => {
            syncDialCodeVisibility();
        });
        $telInput.on('focus.ccfefMdc', () => {
            $selectedDialCode.css('visibility', 'visible');
            $floatingLabel.css({
                left: '50px',
                'background-color': 'white'
            });
            if ($notchedOutlineNotch[0]) {
                const borderTop = getComputedStyle($notchedOutlineNotch[0]).getPropertyValue('border-bottom');
                $notchedOutlineNotch.css({ 'border-top': borderTop });
            }
        });

        $parent.on('click.ccfefMdc', () => {
            handleMainLogic();
        });

        $searchInput.on('mouseover.ccfefMdc', () => {
            if (!$notchedOutlineLeading[0] || !$notchedOutlineNotch[0]) {
                return;
            }
            const borderWidth = getComputedStyle($notchedOutlineLeading[0]).getPropertyValue('border-bottom-width');
            $notchedOutlineNotch.css({ 'border-top-width': borderWidth, 'border-top-color': 'black' });
        });

        $parent.on('mouseover.ccfefMdc', () => {
            if (!$notchedOutlineLeading[0] || !$notchedOutlineNotch[0]) {
                return;
            }
            const borderWidth = getComputedStyle($notchedOutlineLeading[0]).getPropertyValue('border-bottom-width');
            $notchedOutlineNotch.css({ 'border-top-width': borderWidth, 'border-top-color': 'black' });
            handleMainLogic();
        });

        $parent.on('mouseleave.ccfefMdc', () => {
            if (!$notchedOutlineLeading[0] || !$notchedOutlineNotch[0]) {
                return;
            }
            const borderWidth = getComputedStyle($notchedOutlineLeading[0]).getPropertyValue('border-bottom-width');
            const borderColor = getComputedStyle($notchedOutlineLeading[0]).getPropertyValue('border-right-color');
            $notchedOutlineNotch.css({ 'border-top-width': borderWidth, 'border-top-color': borderColor });
            handleMainLogic();
        });

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
            const handleCountryChange = CFKEF.createItiCountryChangeHandler(iti, {
                dialCodeVisibility: this.dialCodeVisibility[key],
                enableCountryChangeGuard: true,
                debounceMs: 400,
                onBeforeUpdate: () => {
                    this.customFlags();
                    this.TelFieldInputEventHandler(inputElement);
                },
                updateHandler: (element, currentCode, previousCode, dialCodeVisibility) => {
                    this.updateCountryCodeHandler(element, currentCode, previousCode, dialCodeVisibility);
                },
            });

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
        
            if (this.defaultCountry[uniqueId] && '' !== this.defaultCountry[uniqueId] && 'NAN' !== String(this.defaultCountry[uniqueId]).toUpperCase()) {
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
        CFKEF.updateCountryCodeHandler(element, currentCode, previousCode, dialCodeVisibility);
    }

    customFlags() {
        // Only the selected-country button — not dropdown list flags (those use the CSS sprite).
        const selectedCountries = this.$element.find('.cfefp-intl-container .iti__country-container .iti__selected-country .iti__flag:not(.iti__globe), .cfefp-intl-container .iti__country-container .iti__selected-flag .iti__flag:not(.iti__globe)');
    
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

                if ('' !== defaultCountry && 'NAN' !== String(defaultCountry).toUpperCase()) {
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
                    const result = CFKEF.validateItiOnSubmit(iti, {
                        dialCodeVisibility: this.dialCodeVisibility[data],
                        onAfterSanitize: (inputTelElement) => {
                            const parentWrp = inputTelElement.closest(fieldGroupSelector);
                            if (!parentWrp) {
                                return;
                            }
                            const telContainer = parentWrp.querySelector('.cfefp-intl-container');
                            if (telContainer && inputTelElement.offsetHeight) {
                                telContainer.style.setProperty('--cfefp-intl-tel-button-height', `${inputTelElement.offsetHeight}px`);
                            }
                        },
                    });

                    const inputTelElement = result.input;
                    const errorContainer = jQuery(inputTelElement).parent();
                    errorContainer.find('span.elementor-message').remove();

                    if (result.skipped) {
                        return;
                    }

                    if (result.valid) {
                        jQuery(inputTelElement).closest('.cfefp-intl-container').removeClass('elementor-error');
                        this.setMdcTelValidationState(inputTelElement, '');
                        return;
                    }

                    if (result.errorMessage) {
                        jQuery(inputTelElement).closest('.cfefp-intl-container').addClass('elementor-error');

                        if (!this.setMdcTelValidationState(inputTelElement, result.errorMessage)) {
                            const errorMsgHtml = '<span class="elementor-message elementor-message-danger elementor-help-inline elementor-form-help-inline" role="alert">' +
                                result.errorMessage + '</span>';
                            jQuery(inputTelElement).after(errorMsgHtml);
                        }

                        if (!firstInvalidInput) {
                            firstInvalidInput = inputTelElement;
                        }

                        e.preventDefault();
                        e.stopImmediatePropagation();
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

/**
 * Shared input mask handler for Elementor Pro, Cool Form, and Hello Plus.
 * Platform wrappers call CFKEF.initInputMask({ readyHook, selectors, flags }).
 */
window.CFKEF = window.CFKEF || {};

CFKEF.prepareMaskInputAttributes = function (input) {
	if (!input || !input.classList) {
		return;
	}

	var maskClass, maskFormat, maskPrefix, maskDecimalPlaces, maskTimeMaskFormat, phoneFormat, creditCardOptions, maskAutoPlaceholder, brazilianFormats;

	input.classList.forEach(function (className) {
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
		input.setAttribute('data-mask', maskClass && maskClass[1] ? maskClass[1] : '');
	}

	input.setAttribute('data-moneymask-format', maskFormat && maskFormat[1] ? maskFormat[1] : 'dot');
	input.setAttribute('data-moneymask-prefix', maskPrefix && maskPrefix[1] ? maskPrefix[1] : '');
	input.setAttribute('data-decimal-places', maskDecimalPlaces && maskDecimalPlaces[1] ? maskDecimalPlaces[1] : '2');
	input.setAttribute('data-timemask-format', maskTimeMaskFormat && maskTimeMaskFormat[1] ? maskTimeMaskFormat[1] : 'one');
	input.setAttribute('data-phone-format', phoneFormat && phoneFormat[1] ? phoneFormat[1] : 'phone_usa');
	input.setAttribute('data-creditcard-options', creditCardOptions && creditCardOptions[1] ? creditCardOptions[1] : 'hyphen');
	input.setAttribute('data-auto-placeholder', maskAutoPlaceholder && maskAutoPlaceholder[1] ? maskAutoPlaceholder[1] : '');
	input.setAttribute('data-brazilian-formats', brazilianFormats && brazilianFormats[1] ? brazilianFormats[1] : '');
};

CFKEF.initInputMask = function (opts) {
  opts = opts || {};
  var readyHook = opts.readyHook || 'frontend/element_ready/form.default';
  var selectors = Object.assign(
    {
      calInput: '.elementor-field-textual',
      calDiv: '.elementor-field-type-text',
      form: '.elementor-form',
      fieldGroup: '.elementor-field-group',
    },
    opts.selectors || {}
  );
  var flags = Object.assign({ creditCardRelativeGroup: false }, opts.flags || {});

  class InputHandler extends elementorModules.frontend.handlers.Base {


    getDefaultSettings() {
        return {
            selectors: selectors,
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
            CFKEF.prepareMaskInputAttributes(input);
        });
    
        this.applyMasks();
    }    

    applyMasks() {
        this.elements.$form.find('input[data-mask]').each(function () {
            const $input = jQuery(this);
            if (flags.creditCardRelativeGroup && $input.hasClass('mask_control_@ev-ccard')) {
                $input.closest(selectors.fieldGroup).css({ position: 'relative' });
            }
            CFKEF.applyMaskUi($input);
        });
    }
  }

  jQuery(window).on('elementor/frontend/init', () => {
    const calHandler = ($element) => {
      elementorFrontend.elementsHandler.addHandler(InputHandler, {
        $element,
      });
    };

    elementorFrontend.hooks.addAction(readyHook, calHandler);
  });
};

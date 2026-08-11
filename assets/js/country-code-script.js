(function ($) {
  CFKEF.initCountryCode({
    readyHook: 'frontend/element_ready/form.default',
    enableMdcHandling: false,
    includeNextButton: true,
    fieldGroupSelector: '.elementor-field-group',
    submitGroupClass: 'elementor-field-type-submit',
    telTypeSelector: '.elementor-field-type-tel',
    telInputType: 'tel',
    widgetSelector: '.elementor-widget.elementor-widget-form',
    formWidgetSelector: '.elementor-element.elementor-widget-form',
    selectors: {
      submitButton: 'div.elementor-field-type-submit button',
      nextButton:
        'div.elementor-field-type-next button.e-form__buttons__wrapper__button-next',
      form: '.elementor-form',
    },
    telFieldLookup: function (formId, inputId) {
      return jQuery(
        '.elementor-widget.elementor-widget-form[data-id="' +
          formId +
          '"] .elementor-field-type-tel.elementor-field-group input[type="tel"]#' +
          inputId
      )[0];
    },
  });
})(jQuery);

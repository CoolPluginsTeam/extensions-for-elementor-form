(function ($) {
  CFKEF.initCountryCode({
    readyHook: 'frontend/element_ready/cool-form.default',
    enableMdcHandling: true,
    includeNextButton: false,
    fieldGroupSelector: '.cool-form__field-group',
    submitGroupClass: 'cool-form__submit-group',
    telTypeSelector: '.is-field-type-tel',
    telInputType: 'tel',
    widgetSelector: '.elementor-widget.elementor-widget-cool-form',
    formWidgetSelector: '.elementor-element.elementor-widget-cool-form',
    selectors: {
      submitButton: 'div.cool-form__submit-group button',
      form: '.cool-form',
    },
    telFieldLookup: function (formId, inputId) {
      return jQuery(
        '.elementor-widget.elementor-widget-cool-form[data-id="' +
          formId +
          '"] .is-field-type-tel.cool-form__field-group input[type="tel"]#' +
          inputId
      )[0];
    },
  });
})(jQuery);

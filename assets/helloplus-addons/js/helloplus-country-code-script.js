(function ($) {
  CFKEF.initCountryCode({
    readyHook: 'frontend/element_ready/ehp-form.default',
    enableMdcHandling: false,
    includeNextButton: false,
    fieldGroupSelector: '.ehp-form__field-group',
    submitGroupClass: 'ehp-form__submit-group',
    telTypeSelector: '.is-field-type-ehp-tel',
    telInputType: 'ehp-tel',
    widgetSelector: '.elementor-widget.elementor-widget-ehp-form',
    formWidgetSelector: '.elementor-element.elementor-widget-ehp-form',
    selectors: {
      submitButton: 'div.ehp-form__submit-group button',
      form: '.ehp-form',
    },
    telFieldLookup: function (formId, inputId) {
      return jQuery(
        '.elementor-widget.elementor-widget-ehp-form[data-id="' +
          formId +
          '"] .is-field-type-ehp-tel.ehp-form__field-group input[type="ehp-tel"]#' +
          inputId
      )[0];
    },
  });
})(jQuery);

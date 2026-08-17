(function ($) {
  CFKEF.initInputMask({
    readyHook: 'frontend/element_ready/ehp-form.default',
    selectors: {
      calInput: '.ehp-form__field',
      calDiv: '.is-field-type-text',
      form: '.ehp-form',
      fieldGroup: '.ehp-form__field-group',
    },
    flags: {
      creditCardRelativeGroup: true,
    },
  });
})(jQuery);

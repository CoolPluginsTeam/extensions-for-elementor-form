(function ($) {
  CFKEF.initInputMask({
    readyHook: 'frontend/element_ready/cool-form.default',
    selectors: {
      calInput: '.cool-form__field',
      calDiv: '.is-field-type-text',
      form: '.cool-form',
      fieldGroup: '.cool-form__field-group',
    },
  });
})(jQuery);

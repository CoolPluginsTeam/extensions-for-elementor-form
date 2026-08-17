(function ($) {
  CFKEF.initInputMask({
    readyHook: 'frontend/element_ready/form.default',
    selectors: {
      calInput: '.elementor-field-textual',
      calDiv: '.elementor-field-type-text',
      form: '.elementor-form',
      fieldGroup: '.elementor-field-group',
    },
  });
})(jQuery);

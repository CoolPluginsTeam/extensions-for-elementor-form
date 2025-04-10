"use strict";

// Add repeater functionality to conditional field repeater
window.addEventListener('elementor/init', function () {
  const originalRepeater = elementor.modules.controls.Repeater;

  const RepeaterControlItemView = originalRepeater.extend({
    className() {
      return `${originalRepeater.prototype.className.apply(this, arguments)} cfef-repeater`;
    },
    onButtonAddRowClick(event) {
      originalRepeater.prototype.onButtonAddRowClick.apply(this, arguments);
      event.stopPropagation();
    }
  });

  elementor.addControlView('field_condition_repeater', RepeaterControlItemView);
});

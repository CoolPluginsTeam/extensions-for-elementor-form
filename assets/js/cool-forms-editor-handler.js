(function ($) {
    "use strict";
    $(document).ready(function () {
        const elementorPanel = $('#elementor-panel')[0];

        if (!elementorPanel) return;

        const bgColor = getComputedStyle(elementorPanel).getPropertyValue('background-color');

        function isDark(color) {
            const match = color.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)/i);
            if (!match) return false;

            const r = parseInt(match[1], 10);
            const g = parseInt(match[2], 10);
            const b = parseInt(match[3], 10);

            const brightness = (r * 299 + g * 587 + b * 114) / 1000;
            return brightness < 128;
        }

        if (isDark(bgColor)) {
            $('body').addClass('dark-mode-enabled').removeClass('light-mode-enabled');
        } else {
            $('body').addClass('light-mode-enabled').removeClass('dark-mode-enabled');
        }
    });
})(jQuery);

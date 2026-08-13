(function ($) {
    "use strict";

    const fieldModes = {
        hide: { repeater: 'Hide fields if', title: 'Hide Fields' },
        show: { repeater: 'Show fields if', title: 'Show Fields' },
        enable: { repeater: 'Enable fields if', title: 'Enable Fields' },
        disable: { repeater: 'Disable fields if', title: 'Disable Fields' },
    };

    CFKEF.initConditionalEditorLabels({
        modes: fieldModes,
        hideRawHtml: true,
    });

    CFKEF.initConditionalSubmitEditorLabels();

    $(document).ready(function () {
        $(document).mouseup(function (e) {
            var container = $(".cfef-dynamic-tag");

            if (!container.is(e.target) && container.has(e.target).length === 0) {
                container.hide();
            }
        });
    })

})(jQuery);

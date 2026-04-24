(function ($) {
    "use strict";

    const SELECTOR = 'div[aria-label="WhatsApp Redirect section"]';
    const EMPTY_CLASS = 'cfef-whatsapp-redirect-section-empty';

    function handleWhatsappRedirectEditor() {
        const $section = $(SELECTOR);

        if (!$section.length) return;

        const $next = $section.next();
        const hasInput = $next.find('input').length > 0;

        $section.toggleClass(EMPTY_CLASS, !hasInput);
        $next.toggleClass(EMPTY_CLASS, !hasInput);
    }

    $(window).on('elementor/commands/run/after', function () {
        setTimeout(handleWhatsappRedirectEditor, 100);
    });

})(jQuery);
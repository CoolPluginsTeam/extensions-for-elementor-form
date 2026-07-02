(function ($) {
    "use strict";

    function addCountryDescriptions(e){
        const controlDescriptions = (window.cflCountryEditorData && window.cflCountryEditorData.controlDescriptions) || {};

        Object.entries(controlDescriptions).forEach(([labelText, descriptionText]) => {
            const controlNode = $(document).find(`span[data-type="settings-field"] > div:contains("${labelText}")`);
            const hasCountryDescription = controlNode.find(".country-description").length > 0;

            if (controlNode.length > 0 && !hasCountryDescription && descriptionText) {
                controlNode.append( $( '<p/>', { class: 'country-description' } ).text( descriptionText ) );
            }
        });
    }
    
    
    $(window).on('elementor/commands/run/after', function (e) {
        setTimeout(() => {
            addCountryDescriptions(e);
        }, 100);
    });

})(jQuery);
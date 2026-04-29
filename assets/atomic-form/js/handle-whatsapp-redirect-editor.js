(function ($) {
    "use strict";
    
    $(document).on('click', 'div[aria-label="WhatsApp Redirect section"]', function (e) {
        e.preventDefault();
        hideInputOfWarning(e);
    });

    function hideInputOfWarning(){
        setTimeout(()=>{

            const waring_input_div = jQuery('span[data-type="settings-field"]:contains("Add WhatsApp redirect action to use this action.")').find('input').closest('span');
            
            if(waring_input_div.length > 0){
                waring_input_div.addClass('hide-warning-container');
            }
        }, 100)
    }

    $(window).on('elementor/commands/run/after', function (e) {
        hideInputOfWarning(e);
    });

})(jQuery);
(function($) {
    "use strict";

    // function for all dynamic tag to show the Id's of all elements
    $( document ).ready( function () { 

        // Elementor Review Notice Start
        jQuery(document).on('click','#cfl_elementor_review_dismiss',(event)=>{
            jQuery(".cfl_elementor_review_notice").hide();
            const btn=jQuery(event.target);
            const nonce=btn.data('nonce');
            const url=btn.data('url');

            jQuery.ajax({
                type: 'POST',
                // eslint-disable-next-line no-undef
                url: url, // Set this using wp_localize_script
                data: {
                    action: 'cfl_dismiss_notice',
                    cfl_notice_dismiss: true,
                    nonce: nonce
                },
                success: (response) => {
                    btn.closest('.elementor-control').remove();
                },
                error: (xhr, status, error) => {
                    console.log(xhr.responseText);
                    console.log(error);
                    console.log(status);
                }
            });
        });
        // Elementor Review Notice End
    })
})(jQuery);
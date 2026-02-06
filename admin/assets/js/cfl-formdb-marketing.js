(function ($) {

    $(document).on('click', '.cfl-dismiss-notice, .cfl-dismiss-cross, .cfl-tec-notice .notice-dismiss, [data-notice_id="formdb-marketing-elementor-form-submissions"] .e-notice__dismiss', function(e) {

        e.preventDefault();
        var noticeType = cflFormDBMarketing.formdb_type;
        var nonce = cflFormDBMarketing.formdb_dismiss_nonce;

        $.post(ajaxurl, {

            action: 'cfl_mkt_dismiss_notice',
            notice_type: noticeType,
            nonce: nonce

        }, function(response) {

            if (response.success) {

            }
        });

    });


   function installPlugin(btn, slugg){

       let button = $(btn);
        var $wrapper = button.closest('.cool-form-wrp');
        button.next('.cfl-error-message').remove();

        const slug = getPluginSlug(slugg);
        if (!slug) return;

        const allowedSlugs = [
            'loop-grid-extender-for-elementor-pro',
            'events-widgets-for-elementor-and-the-events-calendar',
            'conditional-fields-for-elementor-form-pro',
            'sb-elementor-contact-form-db'
        ];
        if (!slug || !allowedSlugs.includes(slug)) return;

        // Get the nonce from the button data attribute
        let nonce = button.data('nonce') || cflFormDBMarketing.nonce;

        button.text('Installing...').prop('disabled', true);

        $.post(ajaxurl, {

                action: 'cfl_install_plugin',
                slug: slug,
                _wpnonce:  nonce
            },

            function(response) {
                const pluginSlug = slug;
                const responseString = JSON.stringify(response);
                const responseContainsPlugin = responseString.includes(pluginSlug);

                if (responseContainsPlugin) {
                    handlePluginActivation(button, slug, $wrapper);

                    if(cflFormDBMarketing.redirect_to_formdb){

                        window.location.href = 'admin.php?page=formsdb';
                    }

                } else if (!responseContainsPlugin) {
                    showNotActivatedMessage($wrapper);
                } else {
                    let errorMessage = 'Please try again or download plugin manually from WordPress.org</a>';
                    $wrapper.find('.elementor-button-warning').remove();
                    if (slug === 'events-widget') {
                        jQuery('.ect-notice-widget').text(errorMessage)
                    } else {
                        $wrapper.find('.elementor-control-notice-main-actions').after(
                            '<div class="elementor-control-notice elementor-button-warning">' +
                            '<div class="elementor-control-notice-content">' +
                            errorMessage +
                            '</div></div>'
                        );
                    }
                }
            }
        );
    }


    // function for activation success
    function handlePluginActivation(button, slug, $wrapper) { 
        button.text('Activated')
            .removeClass('e-btn e-info e-btn-1 elementor-button-success')
            .addClass('elementor-disabled')
            .prop('disabled', true);

        let successMessage = 'Save & reload the page to start using the feature.';

        if (slug === 'events-widgets-for-elementor-and-the-events-calendar') {
            successMessage = 'Events Widget is now active! Design your Events page with Elementor to access powerful new features.';
            jQuery('.cfl-tec-notice .ect-notice-widget').text(successMessage);
        } else {
            $wrapper.find('.elementor-control-notice-success').remove();
            $wrapper.find('.elementor-control-notice-main-actions').after(
                '<div class="elementor-control-notice elementor-control-notice-success">' +
                '<div class="elementor-control-notice-content">' +
                successMessage +
                '</div></div>'
            );
        }
    }

    // function for "not activated" notice
    function showNotActivatedMessage($wrapper) {
        $wrapper.find('.elementor-control-notice-success').remove();
        $wrapper.find('.elementor-control-notice-main-actions').after(
            '<div class="elementor-control-notice elementor-control-notice-success">' +
            '<div class="elementor-control-notice-content">' +
            'The plugin is installed but not yet activated. Please go to the Plugins menu and activate it.' +
            '</div></div>'
        );
    }

    function getPluginSlug(plugin) {

        const slugs = {
            'form-db': 'sb-elementor-contact-form-db'
        };
        return slugs[plugin];
    }


    if(typeof elementor !== 'undefined' && elementor) {
        const callbackfunction = elementor.modules.controls.BaseData.extend({
            onRender:(data)=>{

                if(!data.el) return;

                const customNotice=data.el.querySelector('.cool-form-wrp');

                if(!customNotice) return;

                const installBtns=data.el.querySelectorAll('button.cfl-install-plugin');

                if(installBtns.length === 0) return;


                installBtns.forEach(btn=>{
                    const installSlug=btn.dataset.plugin;

                    btn.addEventListener('click',()=>{
                        installPlugin(jQuery(btn),installSlug)
                    });
                });
            },
        });

        // Initialize when Elementor is ready
        $(window).on('elementor:init', function () { 
            elementor.addControlView('raw_html', callbackfunction);
        });
    }else{


        $(document).ready(function ($) {

            const customNotice = $('.cool-form-wrp, .cfl-tec-notice, [data-notice_id="formdb-marketing-elementor-form-submissions"], .e-form-submissions-search');

            if(customNotice.length === 0) return;

            const installBtns = customNotice.find('button.cfl-install-plugin, a.cfl-install-plugin');

            if(installBtns.length === 0) return;  
            

            installBtns.each(function(){
                const btn = this;
                const installSlug = btn.dataset.plugin;

                $(btn).on('click', function(){
                    if(installSlug) {
                        installPlugin($(btn), installSlug);
                    } else {
                        installPlugin($(btn), cflFormDBMarketing.plugin);
                    }
                });
            });
        })
    }

})(jQuery);
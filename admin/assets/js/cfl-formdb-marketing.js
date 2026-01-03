jQuery(document).ready(function($) {

    $(document).on('click', '.cfl-dismiss-notice, .cfl-dismiss-cross, .cfl-tec-notice .notice-dismiss, .e-notice__dismiss', function(e) {

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


    $(document).on('click', '.cfl-install-plugin', function(e) {

        e.preventDefault();

        var $form = $(this);
        var $wrapper = $form.closest('.cool-form-wrp');
        let button = $(this);
        let plugin =  button.data('plugin') || cflFormDBMarketing.plugin;
        button.next('.cfl-error-message').remove();

        const slug = getPluginSlug(plugin);
        if (!slug) return;
        // Get the nonce from the button data attribute
        let nonce = button.data('nonce');

        button.text('Installing...').prop('disabled', true);

        $.post(ajaxurl, {

                action: 'cfl_install_plugin',
                slug: slug,
                _wpnonce:  nonce || cflFormDBMarketing.nonce
            },

            function(response) {
                const pluginSlug = slug;
                const responseString = JSON.stringify(response);
                const responseContainsPlugin = responseString.includes(pluginSlug);

                if (responseContainsPlugin) {
                    handlePluginActivation(button, slug, $wrapper);
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
    });


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

});
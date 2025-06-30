jQuery(document).ready(function () {
    function handleEntriesSubmenu(){
        var $entriesItem = jQuery('.wp-submenu a[href="admin.php?page=cfkef-entries"]').closest('li');

        if(!$entriesItem.length > 0){
            return;
        }

        var $entriesClone = $entriesItem.clone();
        $entriesItem.remove();

        var $formKitItem = jQuery('.wp-submenu a[href="admin.php?page=cool-formkit"]').closest('li');

        $formKitItem.after($entriesClone);

        jQuery('.wp-submenu a[href="admin.php?page=cfkef-entries"]').css({
            'padding-left': '10px',
            'font-style': 'italic',
            'opacity': '0.85'
        });
    }

    handleEntriesSubmenu();

    jQuery(".site-key-show-hide-icon img").on("click", function () {

        if (jQuery("#cfefp_cloudflare_site_key").attr("type") == 'text') {
            jQuery("#cfefp_cloudflare_site_key").attr("type", "password");

            let src_val = jQuery(".site-key-show-hide-icon img").attr("src");
            let regex = /\/images\/(.*)$/;
            let match = src_val.match(regex);

            let new_src = src_val.replace(match[0], "/images/hide.svg");
            jQuery(".site-key-show-hide-icon img").attr("src", new_src);

        } else {
            jQuery("#cfefp_cloudflare_site_key").attr("type", "text");

            let src_val = jQuery(".site-key-show-hide-icon img").attr("src");
            let regex = /\/images\/(.*)$/;
            let match = src_val.match(regex);

            let new_src = src_val.replace(match[0], "/images/show.svg");
            jQuery(".site-key-show-hide-icon img").attr("src", new_src);
        }
    });

    jQuery(".secret-key-show-hide-icon img").on("click", function () {

        if (jQuery("#cfefp_cloudflare_secret_key").attr("type") == 'text') {
            jQuery("#cfefp_cloudflare_secret_key").attr("type", "password");

            let src_val = jQuery(".secret-key-show-hide-icon img").attr("src");
            let regex = /\/images\/(.*)$/;
            let match = src_val.match(regex);

            let new_src = src_val.replace(match[0], "/images/hide.svg");
            jQuery(".secret-key-show-hide-icon img").attr("src", new_src);

        } else {
            jQuery("#cfefp_cloudflare_secret_key").attr("type", "text");

            let src_val = jQuery(".secret-key-show-hide-icon img").attr("src");
            let regex = /\/images\/(.*)$/;
            let match = src_val.match(regex);

            let new_src = src_val.replace(match[0], "/images/show.svg");
            jQuery(".secret-key-show-hide-icon img").attr("src", new_src);
        }
    });



    jQuery(".site-key-show-hide-icon-h-captcha img").on("click", function () {

        if (jQuery("#cfefp_h_site_key").attr("type") == 'text') {
            jQuery("#cfefp_h_site_key").attr("type", "password");

            let src_val = jQuery(".site-key-show-hide-icon-h-captcha img").attr("src");
            let regex = /\/images\/(.*)$/;
            let match = src_val.match(regex);

            let new_src = src_val.replace(match[0], "/images/hide.svg");
            jQuery(".site-key-show-hide-icon-h-captcha img").attr("src", new_src);

        } else {
            jQuery("#cfefp_h_site_key").attr("type", "text");

            let src_val = jQuery(".site-key-show-hide-icon-h-captcha img").attr("src");
            let regex = /\/images\/(.*)$/;
            let match = src_val.match(regex);

            let new_src = src_val.replace(match[0], "/images/show.svg");
            jQuery(".site-key-show-hide-icon-h-captcha img").attr("src", new_src);
        }
    });

    jQuery(".secret-key-show-hide-icon-h-captcha img").on("click", function () {

        if (jQuery("#cfefp_h_secret_key").attr("type") == 'text') {
            jQuery("#cfefp_h_secret_key").attr("type", "password");

            let src_val = jQuery(".secret-key-show-hide-icon-h-captcha img").attr("src");
            let regex = /\/images\/(.*)$/;
            let match = src_val.match(regex);

            let new_src = src_val.replace(match[0], "/images/hide.svg");
            jQuery(".secret-key-show-hide-icon-h-captcha img").attr("src", new_src);

        } else {
            jQuery("#cfefp_h_secret_key").attr("type", "text");

            let src_val = jQuery(".secret-key-show-hide-icon-h-captcha img").attr("src");
            let regex = /\/images\/(.*)$/;
            let match = src_val.match(regex);

            let new_src = src_val.replace(match[0], "/images/show.svg");
            jQuery(".secret-key-show-hide-icon-h-captcha img").attr("src", new_src);
        }
    });

    // recaptcha js
    jQuery(".site-key-show-hide-icon-recaptcha img").on("click", function () {

        if (jQuery("#cfl_site_key_v2").attr("type") == 'text') {
            jQuery("#cfl_site_key_v2").attr("type", "password");

            let src_val = jQuery(".site-key-show-hide-icon-recaptcha img").attr("src");
            let regex = /\/images\/(.*)$/;
            let match = src_val.match(regex);

            let new_src = src_val.replace(match[0], "/images/hide.svg");
            jQuery(".site-key-show-hide-icon-recaptcha img").attr("src", new_src);

        } else {
            jQuery("#cfl_site_key_v2").attr("type", "text");

            let src_val = jQuery(".site-key-show-hide-icon-recaptcha img").attr("src");
            let regex = /\/images\/(.*)$/;
            let match = src_val.match(regex);

            let new_src = src_val.replace(match[0], "/images/show.svg");
            jQuery(".site-key-show-hide-icon-recaptcha img").attr("src", new_src);
        }
    });

    jQuery(".secret-key-show-hide-icon-recaptcha img").on("click", function () {

        if (jQuery("#cfl_secret_key_v2").attr("type") == 'text') {
            jQuery("#cfl_secret_key_v2").attr("type", "password");

            let src_val = jQuery(".secret-key-show-hide-icon-recaptcha img").attr("src");
            let regex = /\/images\/(.*)$/;
            let match = src_val.match(regex);

            let new_src = src_val.replace(match[0], "/images/hide.svg");
            jQuery(".secret-key-show-hide-icon-recaptcha img").attr("src", new_src);

        } else {
            jQuery("#cfl_secret_key_v2").attr("type", "text");

            let src_val = jQuery(".secret-key-show-hide-icon-recaptcha img").attr("src");
            let regex = /\/images\/(.*)$/;
            let match = src_val.match(regex);

            let new_src = src_val.replace(match[0], "/images/show.svg");
            jQuery(".secret-key-show-hide-icon-recaptcha img").attr("src", new_src);
        }
    });

    // recaptcha v3 js
    jQuery(".site-key-show-hide-icon-recaptcha_v3 img").on("click", function () {

        if (jQuery("#cfl_site_key_v3").attr("type") == 'text') {
            jQuery("#cfl_site_key_v3").attr("type", "password");

            let src_val = jQuery(".site-key-show-hide-icon-recaptcha_v3 img").attr("src");
            let regex = /\/images\/(.*)$/;
            let match = src_val.match(regex);

            let new_src = src_val.replace(match[0], "/images/hide.svg");
            jQuery(".site-key-show-hide-icon-recaptcha_v3 img").attr("src", new_src);

        } else {
            jQuery("#cfl_site_key_v3").attr("type", "text");

            let src_val = jQuery(".site-key-show-hide-icon-recaptcha_v3 img").attr("src");
            let regex = /\/images\/(.*)$/;
            let match = src_val.match(regex);

            let new_src = src_val.replace(match[0], "/images/show.svg");
            jQuery(".site-key-show-hide-icon-recaptcha_v3 img").attr("src", new_src);
        }
    });

    jQuery(".secret-key-show-hide-icon-recaptcha_v3 img").on("click", function () {

        if (jQuery("#cfl_secret_key_v3").attr("type") == 'text') {
            jQuery("#cfl_secret_key_v3").attr("type", "password");

            let src_val = jQuery(".secret-key-show-hide-icon-recaptcha_v3 img").attr("src");
            let regex = /\/images\/(.*)$/;
            let match = src_val.match(regex);

            let new_src = src_val.replace(match[0], "/images/hide.svg");
            jQuery(".secret-key-show-hide-icon-recaptcha_v3 img").attr("src", new_src);

        } else {
            jQuery("#cfl_secret_key_v3").attr("type", "text");

            let src_val = jQuery(".secret-key-show-hide-icon-recaptcha_v3 img").attr("src");
            let regex = /\/images\/(.*)$/;
            let match = src_val.match(regex);

            let new_src = src_val.replace(match[0], "/images/show.svg");
            jQuery(".secret-key-show-hide-icon-recaptcha_v3 img").attr("src", new_src);
        }
    });
});

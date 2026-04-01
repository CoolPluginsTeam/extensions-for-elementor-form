jQuery(document).ready(function () {
    function addCoolformAdmingPageToElementor() {
        let $elementorEditorPage = jQuery('.wp-submenu a[href="admin.php?page=elementor"]').closest('li');
        if (!$elementorEditorPage.length) {
            return;
        }

        let $submenu = $elementorEditorPage.closest('ul.wp-submenu');
        if (!$submenu.length) {
            return;
        }

        $submenu.find('.cool-formkit-page-list').remove();
        $submenu.find('.cfkef-entries-page-list').remove();

        let $coolFormkitItem = jQuery('<li class="cool-formkit-page-list"><a href="admin.php?page=cool-formkit">Cool Formkit</a></li>');
        let $coolFormEntriesItem = jQuery('<li class="cfkef-entries-page-list"><a href="admin.php?page=cfkef-entries">↳ Entries</a></li>');


        if($submenu.find('a[href="admin.php?page=elementor-one-upgrade"]').length > 0){

            if(localStorage.getItem('cfkef_enable_hello_plus') == 1 || localStorage.getItem('cfkef_enable_formkit_builder') == 1){
                $elementorEditorPage.after($coolFormEntriesItem)            
            }

            $elementorEditorPage.after($coolFormkitItem)            
        }else{

            $submenu.append($coolFormkitItem);

            if(localStorage.getItem('cfkef_enable_hello_plus') == 1 || localStorage.getItem('cfkef_enable_formkit_builder') == 1){
                console.log($submenu);
                $submenu.append($coolFormEntriesItem);
            }
        }

    }

    addCoolformAdmingPageToElementor();

    document.addEventListener('cfkef_dashboard_toggle:settings:changed', function (e) {
        addCoolformAdmingPageToElementor()
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

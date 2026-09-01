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

            if(localStorage.getItem('cfkef_enable_hello_plus') == 1 || localStorage.getItem('cfkef_enable_formkit_builder') == 1 || localStorage.getItem('cfkef_enable_atomic_form') == 1){
                $elementorEditorPage.after($coolFormEntriesItem)            
            }

            $elementorEditorPage.after($coolFormkitItem)            
        }else{

            $submenu.append($coolFormkitItem);

            if(localStorage.getItem('cfkef_enable_hello_plus') == 1 || localStorage.getItem('cfkef_enable_formkit_builder') == 1 || localStorage.getItem('cfkef_enable_atomic_form') == 1){
                $submenu.append($coolFormEntriesItem);
            }
        }

    }

    addCoolformAdmingPageToElementor();

    document.addEventListener('cfkef_dashboard_toggle:settings:changed', function (e) {
        addCoolformAdmingPageToElementor()
    });

    function bindSecretToggle(triggerSelector, inputSelector) {
        jQuery(triggerSelector).on('click', function () {
            var $input = jQuery(inputSelector);
            var $icon = jQuery(triggerSelector);
            var srcVal = $icon.attr('src');
            var match = srcVal && srcVal.match(/\/images\/(.*)$/);
            if (!match) {
                return;
            }

            if ($input.attr('type') === 'text') {
                $input.attr('type', 'password');
                $icon.attr('src', srcVal.replace(match[0], '/images/hide.svg'));
            } else {
                $input.attr('type', 'text');
                $icon.attr('src', srcVal.replace(match[0], '/images/show.svg'));
            }
        });
    }

    bindSecretToggle('.site-key-show-hide-icon-recaptcha img', '#cfl_site_key_v2');
    bindSecretToggle('.secret-key-show-hide-icon-recaptcha img', '#cfl_secret_key_v2');
    bindSecretToggle('.site-key-show-hide-icon-recaptcha_v3 img', '#cfl_site_key_v3');
    bindSecretToggle('.secret-key-show-hide-icon-recaptcha_v3 img', '#cfl_secret_key_v3');
});

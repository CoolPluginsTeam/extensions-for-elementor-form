<?php

//phpcs:disable WordPress.Security.NonceVerification.Missing
//phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash
//phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
//phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound	

// Ensure the file is being accessed through the WordPress admin area
if (!defined('ABSPATH')) {
    die;
}


function cfkef_block_sql_patterns($input) {
    $sql_keywords = [
        'SELECT', 'INSERT', 'UPDATE', 'DELETE', 'DROP', 'UNION', 'OUTFILE', 'OR ', 'AND ', '--', '#', '/*', '*/'
    ];

    foreach ($sql_keywords as $keyword) {
        if (stripos($input, $keyword) !== false) {
            return ''; // If SQL pattern is detected, return an empty string
        }
    }

    return $input;
}

function cfkef_sanitize_sql_input($input) {
    $input = preg_replace('/[\'"=;#()\-]/', '', $input); // Remove SQL special characters
    return cfkef_block_sql_patterns($input);
}


function cfkef_handle_unchecked_checkbox() {
        $choice  = get_option('cpfm_opt_in_choice_cool_forms');
        $options = get_option('cfef_usage_share_data');

        if (!empty($choice)) {

            // If the checkbox is unchecked (value is empty, false, or null)
            if (empty($options)) {
                wp_clear_scheduled_hook('cfl_extra_data_update');

                if(method_exists('cfefp_cronjob', 'cfefp_send_data')){
                    wp_clear_scheduled_hook('cfefp_extra_data_update');
                }
            }

            // If checkbox is checked (value is 'on' or any non-empty value)
            else {
                if (!wp_next_scheduled('cfl_extra_data_update')) {
                    if (class_exists('CFL_cronjob') && method_exists('CFL_cronjob', 'cfl_send_data')) {
                        CFL_cronjob::cfl_send_data();
                    }
                    wp_schedule_event(time(), 'every_30_days', 'cfl_extra_data_update');
                }


                if(method_exists('cfefp_cronjob', 'cfefp_send_data')){


                    if (!wp_next_scheduled('cfefp_extra_data_update')) {
                        wp_schedule_event(time(), 'every_30_days', 'cfefp_extra_data_update');
                            cfefp_cronjob::cfefp_send_data();
                    }

                }
            }
        }
}


// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
function handle_form_submit() {

    // Security check
    $pattern = "/(<script|<\/script>|onerror=|onload=|eval\(|javascript:|SELECT |INSERT |DELETE |DROP |UPDATE |UNION )/i";

    if(isset($_POST['cfefp_cdn_image'])){

        if (preg_match($pattern, $_POST['cfefp_cdn_image'])) {

            return false;
        }
    }


    if(isset($_POST['cfefp_email_conditionally'])){

        if (preg_match($pattern, $_POST['cfefp_email_conditionally'])) {

            return false;
        }
    }


    if(isset($_POST['cfefp_redirect_conditionally'])){

        if (preg_match($pattern, $_POST['cfefp_redirect_conditionally'])) {

            return false;
        }
    }

    if(isset($_POST['cfefp_mailchimp_conditionally'])){

        if (preg_match($pattern, $_POST['cfefp_mailchimp_conditionally'])) {

            return false;
        }
    }

    if(isset($_POST['cfefp_webhook_conditionally'])){

        if (preg_match($pattern, $_POST['cfefp_webhook_conditionally'])) {

            return false;
        }
    }

    if(isset($_POST['cfefp_whatsapp_conditionally'])){

        if (preg_match($pattern, $_POST['cfefp_whatsapp_conditionally'])) {

            return false;
        }
    }

    if(isset($_POST['cfefp_getresponse_conditionally'])){

        if (preg_match($pattern, $_POST['cfefp_getresponse_conditionally'])) {

            return false;
        }
    }

    if(isset($_POST['cfkef_country_code_api_key'])){

        if (preg_match($pattern, $_POST['cfkef_country_code_api_key'])) {

            return false;
        }
    }

    if(isset($_POST['cfefp_cloudflare_site_key'])){

        if (preg_match($pattern, $_POST['cfefp_cloudflare_site_key'])) {

            return false;
        }
    }

    if(isset($_POST['cfefp_cloudflare_secret_key'])){

        if (preg_match($pattern, $_POST['cfefp_cloudflare_secret_key'])) {


            return false;
        }
    }


    if(isset($_POST['cfefp_h_site_key'])){

        if (preg_match($pattern, $_POST['cfefp_h_site_key'])) {

            return false;
        }
    }

    if(isset($_POST['cfefp_h_secret_key'])){

        if (preg_match($pattern, $_POST['cfefp_h_secret_key'])) {


            return false;
        }
    }

    if(isset($_POST['cfl_site_key_v2'])){

        if (preg_match($pattern, $_POST['cfl_site_key_v2'])) {


            return false;
        }
    }

    if(isset($_POST['cfl_secret_key_v2'])){

        if (preg_match($pattern, $_POST['cfl_secret_key_v2'])) {


            return false;
        }
    }

    if(isset($_POST['cfl_site_key_v3'])){

        if (preg_match($pattern, $_POST['cfl_site_key_v3'])) {


            return false;
        }
    }

    if(isset($_POST['cfl_secret_key_v3'])){

        if (preg_match($pattern, $_POST['cfl_secret_key_v3'])) {


            return false;
        }
    }

    if(isset($_POST['cfl_threshold_v3'])){

        if (preg_match($pattern, $_POST['cfl_threshold_v3'])) {


            return false;
        }
    }


    return true;


}

// Save API keys when the form is submitted
if (isset($_POST['cfl_site_key_v2']) || isset($_POST['cfl_secret_key_v2']) || isset($_POST['cfl_site_key_v3']) || isset($_POST['cfl_secret_key_v3']) || isset($_POST['cfl_threshold_v3']) || isset($_POST['cfef_usage_share_data']) || isset($_POST['cfefp_redirect_conditionally']) || isset($_POST['cfefp_email_conditionally'])) {

    check_admin_referer('cool_formkit_save_api_keys', 'cool_formkit_nonce');

    if(handle_form_submit() == false){
        echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('Invalid Input.', 'extensions-for-elementor-form') . '</p></div>';

    }else{

    $redirect_conditionally = isset($_POST['cfefp_redirect_conditionally']) ?  sanitize_text_field($_POST['cfefp_redirect_conditionally']) : '';
    
    $email_conditionally = isset($_POST['cfefp_email_conditionally']) ? sanitize_text_field($_POST['cfefp_email_conditionally']) : '';

    // Mailchimp conditionally
    $cfefp_mailchimp_conditionally = isset($_POST['cfefp_mailchimp_conditionally']) ? sanitize_text_field($_POST['cfefp_mailchimp_conditionally']) : '';

    // GetResponse conditionally
    $cfefp_getresponse_conditionally = isset($_POST['cfefp_getresponse_conditionally']) ? sanitize_text_field($_POST['cfefp_getresponse_conditionally']) : '';

    // Webhook conditionally
    $cfefp_webhook_conditionally = isset($_POST['cfefp_webhook_conditionally']) ? sanitize_text_field($_POST['cfefp_webhook_conditionally']) : '';

    // WhatsApp redirect conditionally
    $cfefp_whatsapp_conditionally = isset($_POST['cfefp_whatsapp_conditionally']) ? sanitize_text_field($_POST['cfefp_whatsapp_conditionally']) : '';



    $recaptcha_site_key  = isset($_POST['cfl_site_key_v2']) ? sanitize_text_field($_POST['cfl_site_key_v2']) : '';
    $recaptcha_secret_key = isset($_POST['cfl_secret_key_v2']) ? sanitize_text_field($_POST['cfl_secret_key_v2']) : '';

    $recaptcha_v3_site_key  = isset($_POST['cfl_site_key_v3']) ? sanitize_text_field($_POST['cfl_site_key_v3']) : '';
    $recaptcha_v3_secret_key = isset($_POST['cfl_secret_key_v3']) ? sanitize_text_field($_POST['cfl_secret_key_v3']) : '';
    $recaptcha_v3_threshold = isset($_POST['cfl_threshold_v3']) ? sanitize_text_field($_POST['cfl_threshold_v3']) : '';
    $cfef_usage_share_data = isset($_POST['cfef_usage_share_data']) ? sanitize_text_field($_POST['cfef_usage_share_data']) : '';

    update_option('cfl_site_key_v2', $recaptcha_site_key);
    update_option('cfl_secret_key_v2', $recaptcha_secret_key);

    update_option('cfl_site_key_v3', $recaptcha_v3_site_key);
    update_option('cfl_secret_key_v3', $recaptcha_v3_secret_key);
    update_option('cfl_threshold_v3', $recaptcha_v3_threshold);
    update_option( "cfef_usage_share_data",  $cfef_usage_share_data);
    update_option('cfefp_redirect_conditionally', $redirect_conditionally);
    update_option('cfefp_email_conditionally', $email_conditionally);
    update_option( "cfefp_mailchimp_conditionally",  $cfefp_mailchimp_conditionally);
    update_option( "cfefp_getresponse_conditionally",  $cfefp_getresponse_conditionally);
    update_option( "cfefp_webhook_conditionally",  $cfefp_webhook_conditionally);
    update_option( "cfefp_whatsapp_conditionally",  $cfefp_whatsapp_conditionally);


    cfkef_handle_unchecked_checkbox();
    
    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Settings saved.', 'extensions-for-elementor-form') . '</p></div>';

    }

}

// Get the current API key values
$geo_provider          = get_option('cfkef_geo_provider', 'ipapi');

$api_key_one = get_option('cfkef_country_code_api_key', '');

$non_ipapi_api_key = get_option('cfkef_country_code_non_ipapi_api_key', '');

// Get the Conditional Redirection key values
$redirect_conditionally = get_option('cfefp_redirect_conditionally', 5);

// Get Conditional Email key values
$email_conditionally = get_option('cfefp_email_conditionally', 5);

// Get Conditional Mailchimp key values
$mailchimp_conditionally = get_option('cfefp_mailchimp_conditionally', 5);

// Get Conditional GetResponse key values
$getresponse_conditionally = get_option('cfefp_getresponse_conditionally', 5);

// Get Conditional Webhook key values
$webhook_conditionally = get_option('cfefp_webhook_conditionally', 5);

// Get Conditional Whatsapp Redirect key values
$whatsapp_redirect_conditionally = get_option('cfefp_whatsapp_conditionally', 5);


$conditional_pro_install = is_plugin_active('conditional-fields-for-elementor-form-pro/class-conditional-fields-for-elementor-form-pro.php');

$cool_formkit_pro_install = is_plugin_active('cool-formkit-for-elementor-forms/cool-formkit-for-elementor-forms.php');

// Get CDN Image key values
$cdn_image = get_option('cfefp_cdn_image', '');
?>

<div class="cfkef-settings-box">

    <div>
        <form method="post" action="" class="cool-formkit-form">
            <div class="wrapper-header">
                <div class="cfkef-save-all">
                    <div class="cfkef-title-desc">
                        <h2><?php esc_html_e('Cool FormKit Settings', 'extensions-for-elementor-form'); ?></h2>
                    </div>
                    <div class="cfkef-save-controls">
                        <button type="submit" class="button button-primary"><?php esc_html_e('Save Changes', 'extensions-for-elementor-form'); ?></button>
                    </div>
                </div>
            </div>
            <div class="wrapper-body">
                <?php wp_nonce_field('cool_formkit_save_api_keys', 'cool_formkit_nonce'); ?>


                <p class="highlight-description"><?php esc_html_e('reCAPTCHA and reCAPTCHA V3 for Cool Form builder', 'extensions-for-elementor-form'); ?></p>
                <h3><?php esc_html_e('reCAPTCHA Settings', 'extensions-for-elementor-form'); ?></h3>
                <table class="form-table cool-formkit-table recaptcha-table">
                    <tr>
                        <th scope="row" class="cool-formkit-table-th">
                            <label for="cfl_site_key_v2" class="cool-formkit-label"><?php esc_html_e('Site Key', 'extensions-for-elementor-form'); ?></label>
                        </th>
                        <td class="cool-formkit-table-td site-key-td">
                            <input type="password" id="cfl_site_key_v2" name="cfl_site_key_v2" min="4" value="<?php echo esc_attr(get_option('cfl_site_key_v2')); ?>" class="regular-text cool-formkit-input"/>
                            
                            <span class="site-key-show-hide-icon-recaptcha">
                                <img src="<?php echo esc_url(CFL_PLUGIN_URL . 'assets/images/hide.svg'); ?>" alt="show">
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="cool-formkit-table-th">
                            <label for="cfl_secret_key_v2" class="cool-formkit-label"><?php esc_html_e('Secret Key', 'extensions-for-elementor-form'); ?></label>
                        </th>
                        <td class="cool-formkit-table-td secret-key-td">
                            <input type="password" id="cfl_secret_key_v2" name="cfl_secret_key_v2" min="4" value="<?php echo esc_attr(get_option('cfl_secret_key_v2')); ?>" class="regular-text cool-formkit-input" />
        
                            <span class="secret-key-show-hide-icon-recaptcha">
                                <img src="<?php echo esc_url(CFL_PLUGIN_URL . 'assets/images/hide.svg'); ?>" alt="show">
                            </span>
        
                        </td>
                    </tr>
                </table>

                <h3><?php esc_html_e('reCAPTCHA V3 Settings', 'extensions-for-elementor-form'); ?></h3>
                <table class="form-table cool-formkit-table recaptcha-table">
                    <tr>
                        <th scope="row" class="cool-formkit-table-th">
                            <label for="cfl_site_key_v3" class="cool-formkit-label"><?php esc_html_e('Site Key', 'extensions-for-elementor-form'); ?></label>
                        </th>
                        <td class="cool-formkit-table-td site-key-td">
                            <input type="password" id="cfl_site_key_v3" name="cfl_site_key_v3" min="4" value="<?php echo esc_attr(get_option('cfl_site_key_v3')); ?>" class="regular-text cool-formkit-input"/>
                            
                            <span class="site-key-show-hide-icon-recaptcha_v3">
                                <img src="<?php echo esc_url(CFL_PLUGIN_URL . 'assets/images/hide.svg'); ?>" alt="show">
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="cool-formkit-table-th">
                            <label for="cfl_secret_key_v3" class="cool-formkit-label"><?php esc_html_e('Secret Key', 'extensions-for-elementor-form'); ?></label>
                        </th>
                        <td class="cool-formkit-table-td secret-key-td">
                            <input type="password" id="cfl_secret_key_v3" name="cfl_secret_key_v3" min="4" value="<?php echo esc_attr(get_option('cfl_secret_key_v3')); ?>" class="regular-text cool-formkit-input" />
        
                            <span class="secret-key-show-hide-icon-recaptcha_v3">
                                <img src="<?php echo esc_url(CFL_PLUGIN_URL . 'assets/images/hide.svg'); ?>" alt="show">
                            </span>
        
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="cool-formkit-table-th">
                            <label for="threshold_v3" class="cool-formkit-label"><?php esc_html_e('Score Threshold', 'extensions-for-elementor-form'); ?></label>
                        </th>
                        <td class="cool-formkit-table-td">
                            <input type="number" id="threshold_v3" name="cfl_threshold_v3" class="regular-text cool-formkit-input" value="<?php echo esc_attr(get_option('cfl_threshold_v3'))?>" min="0" max="1"  step="0.1"/>
                            <p class="description cool-formkit-description"><?php esc_html_e('Score threshold should be a value between 0 and 1, default: 0.5', 'extensions-for-elementor-form'); ?></p>
                        </td>
                    </tr>
                </table>
                <hr>

                <p class="cool-formkit-description highlight-description"><?php esc_html_e('Configure the settings for conditional fields\' action after submit.', 'extensions-for-elementor-form'); ?></p>
                <table class="form-table cool-formkit-table">
                    <tr>
                        <th scope="row" class="cool-formkit-table-th">
                            <label for="cfefp_email_conditionally" class="cool-formkit-label"><?php esc_html_e('Number of Conditional Emails', 'extensions-for-elementor-form'); ?>
                                <span class="cfkef-pro-feature">
                                    <a href="https://coolformkit.com/pricing/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=get-pro&utm_content=settings_dashboard" target="_blank">
                                    <?php echo $conditional_pro_install ? '' : '(Pro)'?>
                                    </a>
                                </span>
                            </label>
                        </th>
                        <td class="cool-formkit-table-td">
                            <input type="number" id="cfefp_email_conditionally" name="cfefp_email_conditionally" min="4" value="<?php echo esc_attr($email_conditionally); ?>" class="regular-text cool-formkit-input" 
                            <?php echo !$conditional_pro_install ? 'disabled' : ''; ?>/>
                            <p class="description cool-formkit-description"><?php esc_html_e('Set the no. of conditional emails for the Elementor form.', 'extensions-for-elementor-form'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="cool-formkit-table-th">
                            <label for="cfefp_redirect_conditionally" class="cool-formkit-label"><?php esc_html_e('Number of Conditional Redirections', 'extensions-for-elementor-form'); ?>
                                <span class="cfkef-pro-feature">
                                    <a href="https://coolformkit.com/pricing/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=get_pro&utm_content=settings_dashboard" target="_blank">
                                    <?php echo $conditional_pro_install ? '' : '(Pro)'?>
                                    </a>
                                </span>
                            </label>
                        </th>
                        <td class="cool-formkit-table-td">
                            <input type="number" id="cfefp_redirect_conditionally" name="cfefp_redirect_conditionally" min="4" value="<?php echo esc_attr($redirect_conditionally); ?>" class="regular-text cool-formkit-input" <?php echo !$conditional_pro_install ? 'disabled' : ''; ?>/>
                            <p class="description cool-formkit-description"><?php esc_html_e('Set the no. of conditional redirects for the Elementor form.', 'extensions-for-elementor-form'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row" class="cool-formkit-table-th">
                            <label for="cfefp_mailchimp_conditionally" class="cool-formkit-label"><?php esc_html_e('Number of Conditional Mailchimp', 'extensions-for-elementor-form'); ?>
                                <span class="cfkef-pro-feature">
                                    <a href="https://coolformkit.com/pricing/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=get_pro&utm_content=settings_dashboard" target="_blank">
                                    <?php echo $conditional_pro_install ? '' : '(Pro)'?>
                                    </a>
                                </span>
                            </label>
                        </th>
                        <td class="cool-formkit-table-td">
                            <input type="number" id="cfefp_mailchimp_conditionally" name="cfefp_mailchimp_conditionally" min="4" value="<?php echo esc_attr($mailchimp_conditionally); ?>" class="regular-text cool-formkit-input" 
                            <?php echo !$conditional_pro_install ? 'disabled' : ''; ?>/>
                            <p class="description cool-formkit-description"><?php esc_html_e('Set the no. of conditional mailchimp for the Elementor form.', 'extensions-for-elementor-form'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row" class="cool-formkit-table-th">
                            <label for="cfefp_getresponse_conditionally" class="cool-formkit-label"><?php esc_html_e('Number of Conditional Getresponse', 'extensions-for-elementor-form'); ?>
                                <span class="cfkef-pro-feature">
                                    <a href="https://coolformkit.com/pricing/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=get_pro&utm_content=settings_dashboard" target="_blank">
                                    <?php echo $conditional_pro_install ? '' : '(Pro)'?>
                                    </a>
                                </span>
                            </label>
                        </th>
                        <td class="cool-formkit-table-td">
                            <input type="number" id="cfefp_getresponse_conditionally" name="cfefp_getresponse_conditionally" min="4" value="<?php echo esc_attr($getresponse_conditionally); ?>" class="regular-text cool-formkit-input" 
                            <?php echo !$conditional_pro_install ? 'disabled' : ''; ?>/>
                            <p class="description cool-formkit-description"><?php esc_html_e('Set the no. of conditional getresponse for the Elementor form.', 'extensions-for-elementor-form'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row" class="cool-formkit-table-th">
                            <label for="cfefp_webhook_conditionally" class="cool-formkit-label"><?php esc_html_e('Number of Conditional Webhook', 'extensions-for-elementor-form'); ?>
                                <span class="cfkef-pro-feature">
                                    <a href="https://coolformkit.com/pricing/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=get_pro&utm_content=settings_dashboard" target="_blank">
                                    <?php echo $conditional_pro_install ? '' : '(Pro)'?>
                                    </a>
                                </span>
                            </label>
                        </th>
                        <td class="cool-formkit-table-td">
                            <input type="number" id="cfefp_webhook_conditionally" name="cfefp_webhook_conditionally" min="4" value="<?php echo esc_attr($webhook_conditionally); ?>" class="regular-text cool-formkit-input" 
                            <?php echo !$conditional_pro_install ? 'disabled' : ''; ?>/>
                            <p class="description cool-formkit-description"><?php esc_html_e('Set the no. of conditional webhook for the Elementor form.', 'extensions-for-elementor-form'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row" class="cool-formkit-table-th">
                            <label for="cfefp_whatsapp_conditionally" class="cool-formkit-label"><?php esc_html_e('Number of Conditional Whatsapp Redirect', 'extensions-for-elementor-form'); ?>
                                <span class="cfkef-pro-feature">
                                    <a href="https://coolformkit.com/pricing/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=get_pro&utm_content=settings_dashboard" target="_blank">
                                    <?php echo $conditional_pro_install ? '' : '(Pro)'?>
                                    </a>
                                </span>
                            </label>
                        </th>
                        <td class="cool-formkit-table-td">
                            <input type="number" id="cfefp_whatsapp_conditionally" name="cfefp_whatsapp_conditionally" min="4" value="<?php echo esc_attr($whatsapp_redirect_conditionally); ?>" class="regular-text cool-formkit-input" 
                            <?php echo !$conditional_pro_install ? 'disabled' : ''; ?>/>
                            <p class="description cool-formkit-description"><?php esc_html_e('Set the no. of conditional whatsapp redirect for the Elementor form.', 'extensions-for-elementor-form'); ?></p>
                        </td>
                    </tr>

                </table>
                <hr>

                <p class="cool-formkit-description highlight-description"><?php esc_html_e('Configure the settings for country code and country field.', 'extensions-for-elementor-form'); ?></p>
                <table class="form-table cool-formkit-table">
        
                    <tr id="api-selector">
                        <th scope="row" class="cool-formkit-table-th">
                            <label for="cfkef_geo_provider" class="cool-formkit-label"><?php esc_html_e('Geo-IP Provider', 'extensions-for-elementor-form'); ?>
                                <span class="cfkef-pro-feature">
                                    <a href="https://coolformkit.com/pricing/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=get_pro&utm_content=settings_dashboard" target="_blank">
                                    (Pro)
                                    </a>
                                </span>
                            </label>
                        </th>
                        <td class="cool-formkit-table-td">
                            <select id="cfkef_geo_provider" name="cfkef_geo_provider" class="regular-text cool-formkit-input" disabled="disabled">
                                <option value="ipapi"  <?php selected($geo_provider, 'ipapi'); ?> >ipapi.co</option>
                                <option value="ipstack" <?php selected($geo_provider, 'ipstack'); ?>>ipstack.com</option>
                                <option value="ipinfo" <?php selected($geo_provider, 'ipinfo'); ?>>ipinfo.io</option>
                                <option value="geojs"  <?php selected($geo_provider, 'geojs');  ?>>geojs.io</option>
                                <option value="ip-api"  <?php selected($geo_provider, 'ip-api');  ?>>ip-api.com</option>
                            </select>
                            <p class="description cool-formkit-description"><?php esc_html_e('Choose the Geo-IP service to use for auto-detecting country by IP.', 'extensions-for-elementor-form'); ?></p>
                        </td>
                    </tr>
        
                    <tr id="ipapi-row">
                        <th scope="row" class="cool-formkit-table-th">
                            <label for="cfkef_country_code_api_key" class="cool-formkit-label"><?php esc_html_e('Enter ipapi.co API Key', 'extensions-for-elementor-form'); ?>
                                <span class="cfkef-pro-feature">
                                    <a href="https://coolformkit.com/pricing/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=get_pro&utm_content=settings_dashboard" target="_blank">
                                    (Pro)
                                    </a>
                                </span>
                            </label>
                        </th>
                        <td class="cool-formkit-table-td">
                                <input type="text" id="cfkef_country_code_api_key" name="cfkef_country_code_api_key" value="<?php echo esc_attr($api_key_one); ?>" class="regular-text cool-formkit-input" disabled="disabled"/>
                                <p class="description cool-formkit-description"><?php esc_html_e('Auto-detect country code in the Tel field via IP address.', 'extensions-for-elementor-form'); ?></p>
                                <p class="description cool-formkit-description">
                                    <?php
                                    echo wp_kses_post(
                                        __(
                                            'We use <a href="https://ipapi.co/#pricing" target="_blank">ipapi.co</a> to auto-detect the country code in the telephone field using the IP address. It offers 1000 free IP lookups per day. No API key is needed for low requests or if you are not using the auto-detect feature. However, please add an API key if you have a lot of users or purchase a premium plan.',
                                            'extensions-for-elementor-form'
                                        )
                                    );
                                    ?>
                                </p>
                        </td>
                    </tr>
                    <tr id="other-api-row">
                        <th scope="row" class="cool-formkit-table-th">
                            <label for="cfkef_country_code_non_ipapi_api_key" class="cool-formkit-label"><?php esc_html_e('Enter Geo API Key', 'extensions-for-elementor-form'); ?>
                                <span class="cfkef-pro-feature">
                                    <a href="https://coolformkit.com/pricing/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=get_pro&utm_content=settings_dashboard" target="_blank">
                                    (Pro)
                                    </a>
                                </span>
                            </label>
                        </th>
                        <td class="cool-formkit-table-td">
                                <input type="text" id="cfkef_country_code_non_ipapi_api_key" name="cfkef_country_code_non_ipapi_api_key" value="<?php echo esc_attr($non_ipapi_api_key); ?>" class="regular-text cool-formkit-input" disabled="disabled"/>
                                <p class="description cool-formkit-description"><a href="" target="_blank" class="api-infromation"><?php esc_html_e('Read More', 'extensions-for-elementor-form')?></a> <?php esc_html_e('About API', 'extensions-for-elementor-form')?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="cool-formkit-table-th">
                            <label class="cool-formkit-label"><?php esc_html_e('CDN Image', 'extensions-for-elementor-form'); ?>
                                    <span class="cfkef-pro-feature">
                                        <a href="https://coolformkit.com/pricing/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=get_pro&utm_content=settings_dashboard" target="_blank">
                                        (Pro)
                                    </a>
                                </span>
                            </label>
                        </th>
                        <td class="cool-formkit-table-td">
                        <label class="cfkef-toggle-switch">
                            <input type="checkbox" name="cfefp_cdn_image" class="cfkef-element-toggle" value="1" <?php checked($cdn_image); ?>
                            disabled="disabled">
                            <span class="cfkef-slider round"></span>
                        
                        </label>
                        <p class="description cool-formkit-description"><?php esc_html_e("In case the flags appear blurry, enable the option to load flag images directly from the CDN.", 'extensions-for-elementor-form'); ?></p>
                        </td>
                    </tr>
                </table>
                <hr>

                <h3><?php esc_html_e('Cloudflare Turnstile Settings', 'extensions-for-elementor-form'); ?></h3>
                <p class="description cool-formkit-description">
                    <?php
                    echo wp_kses_post(
                        __(
                            'You can get your site key and secret key from here: <a href="https://www.cloudflare.com/en-au/application-services/products/turnstile/" target="_blank">https://www.cloudflare.com/en-au/application-services/products/turnstile/</a>',
                            'extensions-for-elementor-form'
                        )
                    );
                    ?>
                </p>

                <table class="form-table cool-formkit-table turnstile-table">
                    <tr>
                        <th scope="row" class="cool-formkit-table-th">
                            <label for="cfefp_cloudflare_site_key" class="cool-formkit-label"><?php esc_html_e('Site Key', 'extensions-for-elementor-form'); ?>
                                <span class="cfkef-pro-feature">
                                    <a href="https://coolformkit.com/pricing/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=get_pro&utm_content=settings_dashboard" target="_blank">
                                    (Pro)
                                    </a>
                                </span>
                            </label>
                        </th>
                        <td class="cool-formkit-table-td site-key-td">
                            <input type="password" id="cfefp_cloudflare_site_key" name="cfefp_cloudflare_site_key" min="4" value="<?php echo esc_attr(get_option('cfefp_cloudflare_site_key')); ?>" class="regular-text cool-formkit-input" disabled="disabled"/>    
                            <span class="site-key-show-hide-icon">
                                <img src="<?php echo esc_url(CFL_PLUGIN_URL . 'assets/images/hide.svg'); ?>" alt="show">
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="cool-formkit-table-th">
                            <label for="cfefp_cloudflare_secret_key" class="cool-formkit-label"><?php esc_html_e('Secret Key', 'extensions-for-elementor-form'); ?>
                                <span class="cfkef-pro-feature">
                                    <a href="https://coolformkit.com/pricing/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=get_pro&utm_content=settings_dashboard" target="_blank">
                                    (Pro)
                                    </a>
                                </span>
                            </label>
                        </th>
                        <td class="cool-formkit-table-td secret-key-td">
                            <input type="password" id="cfefp_cloudflare_secret_key" name="cfefp_cloudflare_secret_key" min="4" value="<?php echo esc_attr(get_option('cfefp_cloudflare_secret_key')); ?>" class="regular-text cool-formkit-input" disabled="disabled"/>
                            <span class="secret-key-show-hide-icon">
                                <img src="<?php echo esc_url(CFL_PLUGIN_URL . 'assets/images/hide.svg'); ?>" alt="show">
                            </span>
                        </td>
                    </tr>
                </table>
                <hr>
                <h3><?php esc_html_e('hCAPTCHA Settings', 'extensions-for-elementor-form'); ?></h3>
                <p class="description cool-formkit-description">
                    <?php
                    echo wp_kses_post(
                        __(
                            'To use <a href="https://www.hcaptcha.com/" target="_blank">hCaptcha</a>, please register <a href="https://www.hcaptcha.com/signup-interstitial" target="_blank">here</a> to get your site and secret keys.',
                            'extensions-for-elementor-form'
                        )
                    );
                    ?>
                </p>

                <table class="form-table cool-formkit-table hcaptcha-table">
                    <tr>
                        <th scope="row" class="cool-formkit-table-th">
                            <label for="cfefp_h_site_key" class="cool-formkit-label"><?php esc_html_e('Site Key', 'extensions-for-elementor-form'); ?>
                                <span class="cfkef-pro-feature">
                                    <a href="https://coolformkit.com/pricing/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=get_pro&utm_content=settings_dashboard" target="_blank">
                                    (Pro)
                                    </a>
                                </span>
                            </label>
                        </th>
                        <td class="cool-formkit-table-td site-key-td">
                            <input type="password" id="cfefp_h_site_key" name="cfefp_h_site_key" min="4" value="<?php echo  esc_attr(get_option('cfefp_h_site_key')); ?>" class="regular-text cool-formkit-input" disabled="disabled"/>
                                
                            <span class="site-key-show-hide-icon-h-captcha">
                                <img src="<?php echo esc_url(CFL_PLUGIN_URL . 'assets/images/hide.svg'); ?>" alt="show">
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="cool-formkit-table-th">
                            <label for="cfefp_h_secret_key" class="cool-formkit-label"><?php esc_html_e('Secret Key', 'extensions-for-elementor-form'); ?>
                                <span class="cfkef-pro-feature">
                                    <a href="https://coolformkit.com/pricing/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=get_pro&utm_content=settings_dashboard" target="_blank">
                                    (Pro)
                                    </a>
                                </span>
                            </label>
                        </th>
                        <td class="cool-formkit-table-td secret-key-td">
                            <input type="password" id="cfefp_h_secret_key" name="cfefp_h_secret_key" min="4" value="<?php echo esc_attr(get_option('cfefp_h_secret_key')); ?>" class="regular-text cool-formkit-input" 
                            disabled="disabled"/>
                            <span class="secret-key-show-hide-icon-h-captcha">
                                <img src="<?php echo esc_url(CFL_PLUGIN_URL . 'assets/images/hide.svg'); ?>" alt="show">
                            </span>
                        </td>
                    </tr>
                </table>
                <hr>

                
                <div class="cfkef-mailchimp-head-con">
                    <h3 class="mailchimp-h3"><?php esc_html_e('MailChimp Settings', 'extensions-for-elementor-form'); ?></h3>
                    <span class="cfkef-pro-feature">
                        <a href="https://coolformkit.com/pricing/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=get-pro&utm_content=settings_dashboard" target="_blank">
                        <?php echo $cool_formkit_pro_install ? '' : '(Pro)'?>
                        </a>
                    </span>
                </div>
                

                <?php if ( $conditional_pro_install ) : ?>
                    <p class="description cool-formkit-description">
                        <?php
                        echo wp_kses_post(
                            sprintf(
                                /* translators: %s: URL to Elementor settings page */
                                __('If you have activated Conditional Pro, go to the Elementor settings to use the MailChimp API: <a href="%s" target="_blank">Click here</a>', 'extensions-for-elementor-form'),
                                esc_url('admin.php?page=elementor-settings#tab-integrations')
                            )
                        );
                        ?>
                    </p>
                <?php endif; ?>

                <table class="form-table cool-formkit-table mailchimp-table">
                    <tr>
                        <th scope="row" class="cool-formkit-table-th">
                            <label for="cfl_mailchimp_api_key" class="cool-formkit-label"><?php esc_html_e('API Key', 'extensions-for-elementor-form'); ?></label>
                        </th>
                        <td>

                            <div class="cool-formkit-table-td site-key-td">
                                <input type="password" id="cfl_mailchimp_api_key" name="cfl_mailchimp_api_key" min="4" value="" class="regular-text cool-formkit-input" <?php echo !$cool_formkit_pro_install ? 'disabled' : ''; ?>/>
                                
                                <span class="mailchimp-show-hide-icon">
                                    <img src="<?php echo esc_url(CFL_PLUGIN_URL . 'assets/images/hide.svg'); ?>" alt="show">
                                </span>
                            </div>
                            <div class="cool-formkit-table-td api-div">
                                <p class="description cool-formkit-description">
                                    <?php
                                    echo wp_kses_post(
                                        __(
                                            'To integrate with our forms you need an <a href="https://mailchimp.com/help/about-api-keys/" target="_blank">API Key</a>.',
                                            'extensions-for-elementor-form'
                                        )
                                    );
                                    ?>
                                </p>
                            </div>
                        </td>
                    </tr>
                </table>
                
                <hr>

                <div class="cfkef-getresponse-head-con">
                    <h3 class="getresponse-h3"><?php esc_html_e('GetResponse Settings', 'extensions-for-elementor-form'); ?></h3>
                    <span class="cfkef-pro-feature">
                            <a href="https://coolformkit.com/pricing/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=get-pro&utm_content=settings_dashboard" target="_blank">
                            <?php echo $cool_formkit_pro_install ? '' : '(Pro)'?>
                            </a>
                    </span>
                </div>

                <?php if ( $conditional_pro_install ) : ?>
                    <p class="description cool-formkit-description">
                        <?php
                        echo wp_kses_post(
                            sprintf(
                                /* translators: %s: URL to Elementor settings page */
                                __('If you have activated Conditional Pro, go to the Elementor settings to use the GetResponse API: <a href="%s" target="_blank">Click here</a>', 'extensions-for-elementor-form'),
                                esc_url('admin.php?page=elementor-settings#tab-integrations')
                            )
                        );
                        ?>
                    </p>
                <?php endif; ?>


                <table class="form-table cool-formkit-table getresponse-table">
                    <tr>
                        <th scope="row" class="cool-formkit-table-th">
                            <label for="cfl_getresponse_api_key" class="cool-formkit-label"><?php esc_html_e('API Key', 'extensions-for-elementor-form'); ?>
                            </label>
                        </th>

                        <td >
                            <div class="cool-formkit-table-td site-key-td">
                                <input type="password" id="cfl_getresponse_api_key" name="cfl_getresponse_api_key" min="4" value="" class="regular-text cool-formkit-input" <?php echo !$cool_formkit_pro_install ? 'disabled' : ''; ?>/>
                                
                                <span class="getresponse-show-hide-icon">
                                    <img src="<?php echo esc_url(CFL_PLUGIN_URL . 'assets/images/hide.svg'); ?>" alt="show">
                                </span>
                            </div>
                            <div class="cool-formkit-table-td api-div">
                                <p class="description cool-formkit-description">
                                    <?php
                                    echo wp_kses_post(
                                        __(
                                            'To integrate with our forms you need an <a href="https://app.getresponse.com/api" target="_blank">API Key</a>.',
                                            'extensions-for-elementor-form'
                                        )
                                    );
                                    ?>

                                </p>
                            </div>
                        </td>
                    </tr>
                </table>
                
                    
                <hr>
                            
                <table class="form-table cool-formkit-table">
                    <?php $cpfm_opt_in = get_option('cpfm_opt_in_choice_cool_forms','');
                                     if ($cpfm_opt_in) {
        
                                      $check_option =  get_option( 'cfef_usage_share_data','');
                                    
                                    if($check_option == 'on'){
                                        $checked = 'checked';
                                    }else{
                                        $checked = '';
                                    }
        
                                    ?>
                                    
                                    <tr>
                                        <th scope="row" class="cool-formkit-table-th">
                                            <label for="cfef_usage_share_data" class="usage-share-data-label"><?php esc_html_e('Usage Share Data', 'extensions-for-elementor-form'); ?></label>
                                        </th>
                                        <td class="cool-formkit-table-td usage-share-data">
                                            <input type="checkbox" id="cfef_usage_share_data" name="cfef_usage_share_data" value="on" <?php echo esc_attr($checked) ?>  class="regular-text cool-formkit-input"  />
                                            <div class="description cool-formkit-description">
                                            <?php esc_html_e('Help us make this plugin more compatible with your site by sharing non-sensitive site data.', 'extensions-for-elementor-form'); ?>
                                            <a href="#" class="ccpw-see-terms">[<?php esc_html_e('See terms', 'extensions-for-elementor-form'); ?>]</a>
        
                                            <div id="termsBox" style="display: none; padding-left: 20px; margin-top: 10px; font-size: 12px; color: #999;">
                                                <p>
                                                    <?php esc_html_e('Opt in to receive email updates about security improvements, new features, helpful tutorials, and occasional special offers. We\'ll collect:', 'extensions-for-elementor-form'); ?>
                                                    <a href="https://my.coolplugins.net/terms/usage-tracking/" target="_blank">Click Here</a>

                                                </p>
                                                <ul style="list-style-type: auto;">
                                                    <li><?php esc_html_e('Your website home URL and WordPress admin email.', 'extensions-for-elementor-form'); ?></li>
                                                    <li><?php esc_html_e('To check plugin compatibility, we will collect the following: list of active plugins and themes, server type, MySQL version, WordPress version, memory limit, site language and database prefix.', 'extensions-for-elementor-form'); ?></li>
                                                </ul>
                                            </div>
                                        </div>
        
        
                                        </td>
                                    </tr>
                                    <?php }?>
                </table>
                <div class="cool-formkit-submit">
                    <?php submit_button(); ?>
                </div>
            </div>
        </form>
    </div>
</div>

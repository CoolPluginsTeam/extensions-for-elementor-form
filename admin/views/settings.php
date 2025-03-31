<?php
// Ensure the file is being accessed through the WordPress admin area
if (!defined('ABSPATH')) {
    die;
}


// Save API keys when the form is submitted
if (isset($_POST['cfkef_country_code_api_key']) || isset($_POST['cfefp_redirect_conditionally']) || isset($_POST['cfefp_email_conditionally']) || isset($_POST['cfefp_cdn_image'])) {
    check_admin_referer('cool_formkit_save_api_keys', 'cool_formkit_nonce');
    $api_key_one = sanitize_text_field($_POST['cfkef_country_code_api_key']);
    $redirect_conditionally = sanitize_text_field($_POST['cfefp_redirect_conditionally']);
    $email_conditionally = sanitize_text_field($_POST['cfefp_email_conditionally']);
    $cdn_image = isset($_POST['cfefp_cdn_image']) ? '1' : '0';
    update_option('cfkef_country_code_api_key', $api_key_one);
    update_option('cfefp_redirect_conditionally', $redirect_conditionally);
    update_option('cfefp_email_conditionally', $email_conditionally);
    update_option('cfefp_cdn_image', $cdn_image);
    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Settings saved.', 'cool-formkit') . '</p></div>';
}

// Get the current API key values
$api_key_one = get_option('cfkef_country_code_api_key', '');

// Get the Conditional Redirection key values
$redirect_conditionally = get_option('cfefp_redirect_conditionally', 5);

// Get Conditional Email key values
$email_conditionally = get_option('cfefp_email_conditionally', 5);

// Get CDN Image key values
$cdn_image = get_option('cfefp_cdn_image', '');
?>

<div class="cfkef-settings-box">
    <h3><?php esc_html_e('Cool FormKit Settings', 'cool-formkit'); ?></h3>
    <p class="cool-formkit-description"><?php esc_html_e('Configure the settings for Cool FormKit.', 'cool-formkit'); ?></p>

    <form method="post" action="" class="cool-formkit-form">
        <?php wp_nonce_field('cool_formkit_save_api_keys', 'cool_formkit_nonce'); ?>
        <table class="form-table cool-formkit-table">
            <tr>
                <th scope="row" class="cool-formkit-table-th">
                    <label for="cfkef_country_code_api_key" class="cool-formkit-label"><?php esc_html_e('Enter ipapi.co API Key', 'cool-formkit'); ?></label>
                </th>
                <td class="cool-formkit-table-td">
                    <input type="text" id="cfkef_country_code_api_key" name="cfkef_country_code_api_key" value="<?php echo esc_attr($api_key_one); ?>" class="regular-text cool-formkit-input" />
                    <p class="description cool-formkit-description"><?php esc_html_e('Auto-detect country code in the Tel field via IP address.', 'cool-formkit'); ?></p>
                    <p class="description cool-formkit-description"><?php _e('We use <a href="https://ipapi.co/#pricing" target="_blank">ipapi.co</a> to auto-detect the country code in the telephone field using the IP address. It offers 1000 free IP lookups per day. No API key is needed for low requests or if you are not using the auto-detect feature. However, please add an API key if you have a lot of users or purchase a premium plan.', 'cool-formkit'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row" class="cool-formkit-table-th">
                    <label for="cfefp_email_conditionally" class="cool-formkit-label"><?php esc_html_e('Number of Conditional Emails', 'cool-formkit'); ?></label>
                </th>
                <td class="cool-formkit-table-td">
                    <input type="number" id="cfefp_email_conditionally" name="cfefp_email_conditionally" min="4" value="<?php echo esc_attr($email_conditionally); ?>" class="regular-text cool-formkit-input" />
                    <p class="description cool-formkit-description"><?php esc_html_e('Set the no. of conditional emails for the Elementor form.', 'cool-formkit'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row" class="cool-formkit-table-th">
                    <label for="cfefp_redirect_conditionally" class="cool-formkit-label"><?php esc_html_e('Number of Conditional Redirections', 'cool-formkit'); ?></label>
                </th>
                <td class="cool-formkit-table-td">
                    <input type="number" id="cfefp_redirect_conditionally" name="cfefp_redirect_conditionally" min="4" value="<?php echo esc_attr($redirect_conditionally); ?>" class="regular-text cool-formkit-input" />
                    <p class="description cool-formkit-description"><?php esc_html_e('Set the no. of conditional redirects for the Elementor form.', 'cool-formkit'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row" class="cool-formkit-table-th">
                    <label class="cool-formkit-label"><?php esc_html_e('CDN Image', 'cool-formkit'); ?></label>
                </th>
                <td class="cool-formkit-table-td">
                <label class="cfkef-toggle-switch">
                    <input type="checkbox" name="cfefp_cdn_image" class="cfkef-element-toggle" value="1" <?php checked($cdn_image); ?>>
                    <span class="cfkef-slider round"></span>
                
                </label>
                <p class="description cool-formkit-description"><?php _e("In case the flags appear blurry, enable the option to load flag images directly from the CDN.", 'cool-formkit'); ?></p>
                </td>
            </tr>
        </table>
        <div class="cool-formkit-submit">
            <?php submit_button(); ?>
        </div>
    </form>
</div>

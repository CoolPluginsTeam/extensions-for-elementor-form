<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
// Ensure the file is being accessed through the WordPress admin area
if (!defined('ABSPATH')) {
    die;
}

if ( ! function_exists( 'get_plugins' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}
// Get the saved options
$enabled_elements = get_option('cfkef_enabled_elements', array());

// Check if the default plugin option is set to true
$default_plugin_enabled = get_option('cfkef-defaultPlugin', false);

// If the default plugin option is true and conditional_logic is not in enabled_elements, add it
if ($default_plugin_enabled && !in_array('conditional_logic', $enabled_elements)) {
    $enabled_elements[] = 'conditional_logic';
    $enabled_elements[] = 'country_code';
    update_option('cfkef-defaultPlugin',false);
}
$conditional_pro_install = is_plugin_active('conditional-fields-for-elementor-form-pro/class-conditional-fields-for-elementor-form-pro.php');

$form_elements = array(
    'conditional_logic' => array(
        'label' => __('Conditional Logic', 'extensions-for-elementor-form'),
        'how_to' => 'https://docs.coolplugins.net/doc/elementor-form-conditional-fields/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=docs&utm_content=dashboard',
        'demo' => 'https://coolplugins.net/product/conditional-fields-for-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard/#view-demo-forms',
        'icon' => CFL_PLUGIN_URL . 'admin/assets/icons/conditional-logic-1-min.svg',
        'pro' => false,
        'pro_link' => 'https://coolplugins.net/cool-formkit-for-elementor-forms/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=get-pro&utm_content=dashboard/#pricing'
    ),
    'country_code' => array(
        'label' => __('Country Code for Tel Field', 'extensions-for-elementor-form'),
        'how_to' => 'https://docs.coolplugins.net/doc/country-code-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=docs&utm_content=dashboard',
        'demo' => 'https://coolplugins.net/cool-formkit-for-elementor-forms/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard/#country-code',
        'icon' => CFL_PLUGIN_URL . 'admin/assets/icons/country-code-min.svg',
        'pro' => false,
        'pro_link' => 'https://coolplugins.net/cool-formkit-for-elementor-forms/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=get-pro&utm_content=dashboard/#pricing'
    ),
    'whatsapp_redirect' => array(
        'label' => __('Whatsapp Redirect', 'extensions-for-elementor-form'),
        'how_to' => 'https://docs.coolplugins.net/doc/whatsapp-redirection-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=docs&utm_content=dashboard',
        'demo' => 'https://coolplugins.net/cool-formkit-for-elementor-forms/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard',
        'icon' => CFL_PLUGIN_URL . 'admin/assets/icons/whatsapp-icon-min.svg',
        'pro' => false,
        'pro_link' => 'https://coolplugins.net/cool-formkit-for-elementor-forms/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=get-pro&utm_content=dashboard/#pricing'
    ),
    'form_input_mask' => array(
        'label' => __('Form Input Mask', 'extensions-for-elementor-form'),
        'how_to' => 'https://docs.coolplugins.net/doc/input-masks-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=docs&utm_content=dashboard',
        'demo' => 'https://coolplugins.net/add-input-masks-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard/#input-masks-demos',
        'icon' => CFL_PLUGIN_URL . 'admin/assets/icons/input-mask-min.svg',
        'pro' => false,
        'pro_link' => 'https://coolplugins.net/cool-formkit-for-elementor-forms/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=get-pro&utm_content=dashboard/#pricing'
    ),
    'conditional_redirect' => array(
        'label' => __('Redirect Conditionally After Submit', 'extensions-for-elementor-form'),
        'how_to' => 'https://docs.coolplugins.net/doc/conditional-redirect-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=docs&utm_content=dashboard',
        'demo' => 'https://coolplugins.net/product/conditional-fields-for-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard/#conditional-redirect',
        'icon' => CFL_PLUGIN_URL . 'admin/assets/icons/redirect-conditionally-min.svg',
        'pro' => !$conditional_pro_install,
        'pro_link' => 'https://coolformkit.com/features/conditional-redirect-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard'
    ),
    'conditional_email' => array(
        'label' => __('Conditional Email After Submit', 'extensions-for-elementor-form'),
        'how_to' => 'https://docs.coolplugins.net/doc/conditional-email-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=docs&utm_content=dashboard',
        'demo' => 'https://coolplugins.net/product/conditional-fields-for-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard/#conditional-email',
        'popular' => true,
        'icon' => CFL_PLUGIN_URL . 'admin/assets/icons/conditional-email-1-min.svg',
        'pro' => !$conditional_pro_install,
        'pro_link' => 'https://coolformkit.com/features/conditional-emails-on-form-submission/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard'
    ),
    'conditional_submit_button' => array(
        'label' => __('Conditional Logic For Submit Button', 'extensions-for-elementor-form'),
        'how_to' => 'https://docs.coolplugins.net/doc/submit-button-conditions-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=docs&utm_content=dashboard',
        'demo' => 'https://coolplugins.net/product/conditional-fields-for-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard/#view-demo-forms',
        'popular' => true,
        'icon' => CFL_PLUGIN_URL . 'admin/assets/icons/conditional-button-min.svg',
        'pro' => !$conditional_pro_install,
        'pro_link' => 'https://coolformkit.com/features/conditional-submit-button-for-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard'
    ),

    'conditional_mailchimp' => array(
        'label' => __('Conditional MailChimp', 'extensions-for-elementor-form'),
        'how_to' => 'https://docs.coolplugins.net/doc/conditional-mailchimp-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=docs&utm_content=dashboard',
        'demo' => 'https://coolplugins.net/cool-formkit-for-elementor-forms/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard',
        'icon' => CFL_PLUGIN_URL . 'admin/assets/icons/mailchimp-logo.svg',
        'pro' => !$conditional_pro_install,
        'pro_link' => 'https://coolformkit.com/features/conditional-mailchimp-for-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard'
    ),

    'conditional_getresponse' => array(
        'label' => __('Conditional GetResponse', 'extensions-for-elementor-form'),
        'how_to' => 'https://docs.coolplugins.net/doc/conditional-getresponse-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=docs&utm_content=dashboard',
        'demo' => 'https://coolplugins.net/cool-formkit-for-elementor-forms/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard',
        'icon' => CFL_PLUGIN_URL . 'admin/assets/icons/getresponse-icon.svg',
        'pro' => !$conditional_pro_install,
        'pro_link' => ''
    ),
    'conditional_webhook' => array(
        'label' => __('Conditional Webhook', 'extensions-for-elementor-form'),
        'how_to' => 'https://docs.coolplugins.net/doc/conditional-webhook-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=docs&utm_content=dashboard',
        'demo' => 'https://coolplugins.net/cool-formkit-for-elementor-forms/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard',
        'icon' => CFL_PLUGIN_URL . 'admin/assets/icons/webhooks-logo.svg',
        'pro' => !$conditional_pro_install,
        'pro_link' => ''
    ),
    'conditional_whatsapp_redirect' => array(
        'label' => __('Conditional Whatsapp Redirect', 'extensions-for-elementor-form'),
        'how_to' => 'https://docs.coolplugins.net/doc/conditional-whatsapp-redirect-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=docs&utm_content=dashboard',
        'demo' => 'https://coolplugins.net/cool-formkit-for-elementor-forms/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard',
        'icon' => CFL_PLUGIN_URL . 'admin/assets/icons/conditional-whatsapp-redirect.svg',
        'pro' => !$conditional_pro_install,
        'pro_link' => 'https://coolformkit.com/features/whatsapp-redirect-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard'
    ),

    'range_slider' => array(
        'label' => __('Range Slider', 'extensions-for-elementor-form'),
        'how_to' => 'https://docs.coolplugins.net/doc/range-slider-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=docs&utm_content=dashboard',
        'demo' => 'https://coolplugins.net/cool-formkit-for-elementor-forms/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard/#range-field',
        'icon' => CFL_PLUGIN_URL . 'admin/assets/icons/range-slider-min.svg',
        'pro' => true,
        'pro_link' => 'https://coolformkit.com/features/range-slider-for-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard'
    ),
    'calculator_field' => array(
        'label' => __('Calculator Field', 'extensions-for-elementor-form'),
        'how_to' => 'https://docs.coolplugins.net/doc/calculator-field-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=docs&utm_content=dashboard',
        'demo' => 'https://coolplugins.net/cool-formkit-for-elementor-forms/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard/#calculator-field',
        'icon' => CFL_PLUGIN_URL . 'admin/assets/icons/calculator-field-min.svg',
        'pro' => true,
        'pro_link' => 'https://coolformkit.com/features/calculator-for-elementor/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard'
    ),
    'rating_field' => array(
        'label' => __('Rating Field', 'extensions-for-elementor-form'),
        'how_to' => 'https://docs.coolplugins.net/doc/rating-field-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=docs&utm_content=dashboard',
        'demo' => 'https://coolplugins.net/cool-formkit-for-elementor-forms/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard/#rating-field',
        'icon' => CFL_PLUGIN_URL . 'admin/assets/icons/rating-field-min.svg',
        'pro' => true,
        'pro_link' => 'https://coolformkit.com/features/rating-field-for-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard'
    ),
    'signature_field' => array(
        'label' => __('Signature Field', 'extensions-for-elementor-form'),
        'how_to' => 'https://docs.coolplugins.net/doc/signature-field-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=docs&utm_content=dashboard',
        'demo' => 'https://coolplugins.net/cool-formkit-for-elementor-forms/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard/#signature-field',
        'icon' => CFL_PLUGIN_URL . 'admin/assets/icons/signature.svg',
        'pro' => true,
        'pro_link' => 'https://coolformkit.com/features/signature-field-for-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard'
    ),
    'image_radio' => array(
        'label' => __('Image Radio', 'extensions-for-elementor-form'),
        'how_to' => 'https://docs.coolplugins.net/doc/add-image-radio-field/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=docs&utm_content=dashboard',
        'demo' => 'https://coolplugins.net/cool-formkit-for-elementor-forms/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard/#image-radio-checkbox',
        'icon' => CFL_PLUGIN_URL . 'admin/assets/icons/image-radio-min.svg',
        'pro' => true,
        'pro_link' => 'https://coolformkit.com/features/image-radio-for-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard'
    ),
    'radio_checkbox_styler' => array(
        'label' => __('Radio & Checkbox Styler', 'extensions-for-elementor-form'),
        'how_to' => 'https://docs.coolplugins.net/doc/style-radio-checkbox-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=docs&utm_content=dashboard',
        'demo' => 'https://coolplugins.net/cool-formkit-for-elementor-forms/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard/#button-radio-checkbox',
        'icon' => CFL_PLUGIN_URL . 'admin/assets/icons/radio-styler-min.svg',
        'pro' => true,
        'pro_link' => 'https://coolformkit.com/features/checkbox-radio-styles-for-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard'
    ),
    'label_styler' => array(
        'label' => __('Label Styler', 'extensions-for-elementor-form'),
        'how_to' => 'https://docs.coolplugins.net/doc/label-styler-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=docs&utm_content=dashboard',
        'demo' => 'https://coolplugins.net/cool-formkit-for-elementor-forms/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard/#label-styler',
        'icon' => CFL_PLUGIN_URL . 'admin/assets/icons/label-style-min.svg',
        'pro' => true,
        'pro_link' => 'https://coolformkit.com/features/label-styler-for-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard'
    ),
    'select2' => array(
        'label' => __('Select2', 'extensions-for-elementor-form'),
        'how_to' => 'https://docs.coolplugins.net/doc/select-field-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=docs&utm_content=dashboard',
        'demo' => 'https://coolplugins.net/cool-formkit-for-elementor-forms/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard',
        'icon' => CFL_PLUGIN_URL . 'admin/assets/icons/select2-field-min.svg',
        'pro' => true,
        'pro_link' => 'https://coolformkit.com/features/select2-field-for-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard'
    ),
    'WYSIWYG' => array(
        'label' => __('WYSIWYG', 'extensions-for-elementor-form'),
        'how_to' => 'https://docs.coolplugins.net/doc/add-wysiwyg-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=docs&utm_content=dashboard',
        'demo' => 'https://coolplugins.net/cool-formkit-for-elementor-forms/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard',
        'icon' => CFL_PLUGIN_URL . 'admin/assets/icons/WYSIWYG-min.svg',
        'pro' => true,
        'pro_link' => 'https://coolformkit.com/features/wysiwyg-field-for-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard'
    ),
    'confirm_dialog' => array(
        'label' => __('Confirm Dialog Box', 'extensions-for-elementor-form'),
        'how_to' => 'https://docs.coolplugins.net/doc/elementor-form-confirm-dialog-popup/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=docs&utm_content=dashboard',
        'demo' => 'https://coolplugins.net/cool-formkit-for-elementor-forms/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard',
        'icon' => CFL_PLUGIN_URL . 'admin/assets/icons/dialog-box-min.svg',
        'pro' => true,
        'pro_link' => 'https://coolformkit.com/features/confirm-dialog-box-for-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard'
    ),
    'restrict_date' => array(
        'label' => __('Restrict Date', 'extensions-for-elementor-form'),
        'how_to' => 'https://docs.coolplugins.net/doc/restrict-date-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=docs&utm_content=dashboard',
        'demo' => 'https://coolplugins.net/cool-formkit-for-elementor-forms/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard',
        'icon' => CFL_PLUGIN_URL . 'admin/assets/icons/restrict-date-min.svg',
        'pro' => true,
        'pro_link' => 'https://coolformkit.com/features/restrict-date-field-for-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard'
    ),
    'currency_field' => array(
        'label' => __('Currency Field', 'extensions-for-elementor-form'),
        'how_to' => 'https://docs.coolplugins.net/doc/add-currency-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=docs&utm_content=dashboard',
        'demo' => 'https://coolplugins.net/cool-formkit-for-elementor-forms/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard',
        'icon' => CFL_PLUGIN_URL . 'admin/assets/icons/currency-field-min.svg',
        'pro' => true,
        'pro_link' => 'https://coolformkit.com/features/currency-field-for-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard'
    ),
    'month_week_field' => array(
        'label' => __('Month/Week Field', 'extensions-for-elementor-form'),
        'how_to' => 'https://docs.coolplugins.net/doc/add-month-week/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=docs&utm_content=dashboard',
        'demo' => 'https://coolplugins.net/cool-formkit-for-elementor-forms/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard',
        'icon' => CFL_PLUGIN_URL . 'admin/assets/icons/month-week-field-min.svg',
        'pro' => true,
        'pro_link' => 'https://coolformkit.com/features/month-week-field-for-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard'
    ),
    'cloudflare_recaptcha' => array(
        'label' => __('Cloudflare Turnstile', 'extensions-for-elementor-form'),
        'how_to' => 'https://docs.coolplugins.net/doc/add-cloudflare-turnstile-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=docs&utm_content=dashboard',
        'demo' => 'https://coolplugins.net/cool-formkit-for-elementor-forms/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard',
        'icon' => CFL_PLUGIN_URL . 'admin/assets/icons/cloudflare-icon-min.svg',
        'pro' => true,
        'pro_link' => 'https://coolformkit.com/features/cloudflare-turnstile-for-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard'
    ),

    'h_recaptcha' => array(
        'label' => __('hCAPTCHA', 'extensions-for-elementor-form'),
        'how_to' => 'https://docs.coolplugins.net/doc/add-hcaptcha-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=docs&utm_content=dashboard',
        'demo' => 'https://coolplugins.net/cool-formkit-for-elementor-forms/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard',
        'icon' => CFL_PLUGIN_URL . 'admin/assets/icons/hcaptcha-icon-min.svg',
        'pro' => true,
        'pro_link' => 'https://coolformkit.com/features/hcaptcha-for-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard'
    ),
    'toggle_field' => array(
        'label' => __('Toggle Field', 'extensions-for-elementor-form'),
        'how_to' => 'https://docs.coolplugins.net/doc/toggle-field-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=docs&utm_content=dashboard',
        'demo' => 'https://coolplugins.net/cool-formkit-for-elementor-forms/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard',
        'icon' => CFL_PLUGIN_URL . 'admin/assets/icons/toggle-field.svg',
        'pro' => true,
        'pro_link' => 'https://coolformkit.com/features/toggle-field-for-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard'
    ),
);

$popular_elements = array('');
$updated_elements = array('');
?>

<div id="cfkef-loader" style="display: none;">
  <div class="cfkef-loader-overlay"></div>
  <div class="cfkef-loader-spinner"></div>
</div>
<form method="post" action="options.php">
    <?php settings_fields('cfkef_form_elements_group'); ?>
    <?php do_settings_sections('cfkef_form_elements_group'); ?>

    <div class="cfkef-main-content">
        <div class="cfkef-form-elements-container">

            <div class="cfkef-form-element-wrapper">
                <div class="wrapper-header">
                    <div class="cfkef-save-all">
                        <div class="cfkef-title-desc">
                            <h2><?php esc_html_e('Form Builder Widgets', 'extensions-for-elementor-form'); ?></h2>
                        </div>
                        <div class="cfkef-save-controls">
                            <button type="submit" class="button button-primary"><?php esc_html_e('Save Changes', 'extensions-for-elementor-form'); ?></button>
                        </div>
                    </div>
                </div>
                <div class="wrapper-body">
                        <p><?php esc_html_e('Enable or disable Cool FormKit support for a form builder widget you want to use in elementor.', 'extensions-for-elementor-form'); ?></p>

                        <div class="cfkef-form-element-box">

                            <?php
                                $pro_elements_activate = is_plugin_active('pro-elements/pro-elements.php');

                                $plugin_file = 'elementor-pro/elementor-pro.php';

                                $is_elementor_active = defined('ELEMENTOR_PRO_VERSION');
                                $all_plugins = get_plugins();
                                $is_elementor_pro_installed = isset($all_plugins[$plugin_file]);

                                $card_class = '';
                                $data_action = '';
                                $data_slug = '';
                                $data_init = '';

                                if ( ! $is_elementor_active ) {
                                    $card_class .= ' cfkef-has-tooltip';
                                    if ( $is_elementor_pro_installed ) {
                                        $card_class .= ' need-activation';
                                        $data_action = 'activate';
                                        $data_slug   = 'elementor-pro';
                                        $data_init   = $plugin_file;
                                    } else {
                                        $card_class .= ' need-install';
                                        $data_action = 'install';
                                        $data_slug   = 'elementor-pro';
                                    }
                                }
                                ?>

                                <div class="cfkef-form-element-card<?php echo esc_attr( $card_class ); ?>"
                                    <?php if ( !$is_elementor_active ) : ?>
                                        data-action="<?php echo esc_attr( $data_action ); ?>"
                                        data-slug="<?php echo esc_attr( $data_slug ); ?>"
                                        <?php if ( $data_init ) : ?>
                                            data-init="<?php echo esc_attr( $data_init ); ?>"
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    title="<?php echo !$is_elementor_active ? 'Requires Elementor Pro plugin to be activated' : ''; ?>"
                                >
                                <div class="cfkef-form-element-info">
                                    <img src="<?php echo esc_url(CFL_PLUGIN_URL . 'admin/assets/icons/elementor-pro-form-widget-min.svg'); ?>" alt="Elementor Field">
                                    <h4>
                                        <?php if($pro_elements_activate): ?>
                                            <span><?php esc_html_e('Form Widget By', 'extensions-for-elementor-form'); ?></span><br>
                                            <?php esc_html_e('Pro Elements', 'extensions-for-elementor-form'); ?>
                                        <?php else: ?>
                                            <span><?php esc_html_e('Form Widget By', 'extensions-for-elementor-form'); ?></span><br>
                                            <?php esc_html_e('Elementor Pro', 'extensions-for-elementor-form'); ?>
                                        <?php endif; ?>
                                    </h4>
                                </div>
                                <label class="cfkef-toggle-switch"
                                    style="<?php echo !$is_elementor_active ? 'opacity: 0.5; pointer-events: none; cursor: not-allowed;' : ''; ?>">
                                    <input type="checkbox"
                                        name="cfkef_enable_elementor_pro_form"
                                        value="1"
                                        <?php checked(get_option('cfkef_enable_elementor_pro_form', true)); ?>
                                        <?php disabled(!$is_elementor_active); ?>>
                                    <span class="cfkef-slider round"></span>
                                </label>
                                <?php if (!$is_elementor_active): ?>
                                    <div class="cfkef-tooltip"><?php esc_html_e('Requires Elementor Pro plugin to be activated', 'extensions-for-elementor-form'); ?></div>
                                <?php endif; ?>
                            </div>
                   
    
                            <?php
                            $theme = wp_get_theme();                          
                            $theme_name = $theme->get('Name');

                            $plugin_file = 'hello-plus/hello-plus.php';
                            $plugin_slug = 'hello-plus';

                            $is_hello_plus_active = defined('HELLO_PLUS_VERSION');
                            $all_plugins = get_plugins();
                            $hello_plus_installed = isset($all_plugins[$plugin_file]);

                            $card_class = '';
                            $data_action = '';
                            if (!$is_hello_plus_active) {
                                $card_class .= ' cfkef-has-tooltip';
                                if ($hello_plus_installed) {
                                    $card_class .= ' need-activation';
                                    $data_action = 'activate';
                                } else {
                                    $card_class .= ' need-install';
                                    $data_action = 'install';
                                }
                            }
                            ?>
                            <div class="cfkef-form-element-card<?php echo esc_attr($card_class); ?>"
                                data-action="<?php echo esc_attr($data_action); ?>"
                                data-slug="<?php echo esc_attr($plugin_slug); ?>"
                                data-init="<?php echo esc_attr($plugin_file); ?>"
                                data-gettheme="<?php echo esc_attr($theme_name); ?>">
                                <div class="cfkef-form-element-info">
                                    <img src="<?php echo esc_url(CFL_PLUGIN_URL . 'admin/assets/icons/form-lite-min.svg')?>">
                                    <h4>
                                        <span><?php esc_html_e('Form Lite Widget by', 'extensions-for-elementor-form'); ?></span><br>
                                        <?php esc_html_e('Hello Plus','extensions-for-elementor-form'); ?>
                                    </h4>
                                </div>
                                <label class="cfkef-toggle-switch"
                                style="<?php echo !$is_hello_plus_active ? 'opacity: 0.5; pointer-events: none; cursor: not-allowed;' : ''; ?>"
                                >
                                    <input type="checkbox"
                                        name="cfkef_enable_hello_plus"
                                        value="1"
                                        <?php checked(get_option('cfkef_enable_hello_plus', true)); ?>
                                        <?php disabled(!$is_hello_plus_active); ?>>
                                    <span class="cfkef-slider round"></span>
                                </label>
                                <?php if (!$is_hello_plus_active): ?>
                                    <div class="cfkef-tooltip"><?php esc_html_e('Requires Hello Plus plugin to be activated', 'extensions-for-elementor-form'); ?></div>
                                <?php endif; ?>
                            </div>
                       
    
                            <div class="cfkef-form-element-card">
                                <div class="cfkef-form-element-info">
                                    <img src="<?php echo esc_url(CFL_PLUGIN_URL . 'admin/assets/icons/cool-form-widget-min.svg')?>">
                                    <h4>
                                        <span><?php esc_html_e('Cool Form Widget by', 'extensions-for-elementor-form'); ?></span><br>
                                        <?php esc_html_e('Cool Formkit','extensions-for-elementor-form'); ?>
                                    </h4>
                                </div>
                                <label class="cfkef-toggle-switch">
                                    <input type="checkbox" name="cfkef_enable_formkit_builder" value="1" <?php checked(get_option('cfkef_enable_formkit_builder', true)); ?>>
                                    <span class="cfkef-slider round"></span>
                                </label>
                            </div>                    
                        </div>
                </div>
            </div>

            <div class="cfkef-form-element-wrapper">

                <div class="wrapper-header">
                    <div class="cfkef-save-all">
                        <div class="cfkef-title-desc">
                            <h2><?php esc_html_e('Form Elements', 'extensions-for-elementor-form'); ?></h2>
                        </div>
                        <div class="cfkef-save-controls">
                            <div class="cfkef-toggle-all-wrapper">
                                <span class="cfkef-toggle-label"><?php esc_html_e('Disable All', 'extensions-for-elementor-form'); ?></span>
                                <label class="cfkef-toggle-switch">
                                <input type="checkbox" 
                                name="cfkef_toggle_all" 
                                id="cfkef-toggle-all" 
                                value="1" 
                                <?php checked( get_option('cfkef_toggle_all', false) ); ?>>
                                <span class="cfkef-slider round"></span>
                                </label>
                                <span class="cfkef-toggle-label"><?php esc_html_e('Enable All', 'extensions-for-elementor-form'); ?></span>
                            </div>
                            <button type="submit" class="button button-primary"><?php esc_html_e('Save Changes', 'extensions-for-elementor-form'); ?></button>
                        </div>
                    </div>
                </div>
                
                <div class="wrapper-body">
                    <div>
                        <p><?php esc_html_e('Enable or disable a form element that you are using in your form widget.', 'extensions-for-elementor-form'); ?></p>
                        <p><?php esc_html_e('After enabling or disabling any element make sure to click the ', 'extensions-for-elementor-form'); ?><strong><?php esc_html_e('Save Changes', 'extensions-for-elementor-form'); ?></strong> <?php esc_html_e(' button.', 'extensions-for-elementor-form'); ?></p>
                    </div>

                    <div class="cfkef-form-element-box">
                        <?php foreach ($form_elements as $key => $element): ?>
                        <div class="cfkef-form-element-card">
                            <div class="cfkef-form-element-info">
                                <img src="<?php echo esc_url($element['icon'])?>" alt="Color Field">
                                <h4>
                                    <?php echo esc_html($element['label']); ?>
                                    <?php if (!empty($element['pro'])): ?>
                                        <span class="cfkef-label-popular"><a href="<?php echo esc_url($element['pro_link']) ?>" target="_blank"><?php esc_html_e('Pro', 'extensions-for-elementor-form'); ?></a></span>
                                    <?php endif; ?>

                                    <?php if (in_array($key, $popular_elements)): ?>
                                        <span class="cfkef-label-popular">Popular</span>
                                    <?php endif; ?>

                                    <?php if (in_array($key, $updated_elements)): ?>
                                        <span class="cfkef-label-updated">Updated</span>
                                    <?php endif; ?>
                                </h4>
                                <div>
                                    <a href="<?php echo esc_url($element['how_to']) ?>" title="Documentation" target="_blank" rel="noreferrer">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="#000" d="M21 11V3h-8v2h4v2h-2v2h-2v2h-2v2H9v2h2v-2h2v-2h2V9h2V7h2v4zM11 5H3v16h16v-8h-2v6H5V7h6z"/></svg>
                                    </a>
                                </div>
                            </div>
                            <label class="cfkef-toggle-switch">
                                <input type="checkbox" name="cfkef_enabled_elements[]" value="<?php echo esc_attr($key); ?>" <?php checked(in_array($key, $enabled_elements)); ?> class="cfkef-element-toggle"
                                <?php disabled(!empty($element['pro'])); ?>
                                >
                                <?php if(!empty($element['pro'])): ?>
                                    <a href="<?php echo esc_url($element['pro_link']) ?>" target="_blank">
                                        <span class="cfkef-slider round"></span>
                                    </a>
                                <?php else:?>
                                    <span class="cfkef-slider round"></span>
                                <?php endif;?>
                                
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>

                </div>
            </div>

            <div class="cfkef-save-bottom">
                <?php submit_button(__('Save Changes', 'extensions-for-elementor-form')); ?>
            </div>

            <div class="cfkef-review-request">
                <div class="cfkef-review-left">
                    <h3><?php esc_html_e('Enjoying Cool FormKit?', 'extensions-for-elementor-form'); ?></h3>
                    <p><?php esc_html_e('Please consider leaving us a review. It helps us a lot!', 'extensions-for-elementor-form'); ?></p>
                </div>
                <div class="cfkef-review-right">
                    <div class="cfkef-stars">
                    ★★★★★
                    </div>
                    <a href="https://wordpress.org/support/plugin/extensions-for-elementor-form/reviews/#new-post" class="button button-primary" target="_blank"><?php esc_html_e('Leave a Review', 'extensions-for-elementor-form'); ?></a>
                </div>
            </div>
        </div>
        <div class="cfkef-sidebar">
            <div class="cfkef-sidebar-block">
                <h3><?php esc_html_e('Watch Video Tutorial', 'extensions-for-elementor-form'); ?></h3>
                <div class="cfkef-sidebar-link-group">
                    <a href="https://coolplugins.net/video/cool-formkit-pro" target="_blank">
                        <img src="<?php echo esc_url(CFL_PLUGIN_URL . 'admin/assets/icons/youtube-logo.svg')?>" class="youtube-logo">
                        <picture>
                            <img src="<?php echo esc_url(CFL_PLUGIN_URL . 'admin/assets/images/cool formkit-dashboard.png')?>">
                        </picture>
                    </a>
                </div>
            </div>
            <div class="cfkef-sidebar-block">
                <h3><?php esc_html_e('Important Links', 'extensions-for-elementor-form'); ?></h3>
                <div class="cfkef-sidebar-link-group">
                    <a href="https://coolformkit.com/support/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=support&utm_content=setting_page_sidebar" class="button" target="_blank"><?php esc_html_e('Contact Support', 'extensions-for-elementor-form'); ?></a>
                    <a href="https://coolplugins.net/about-us/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=about_us&utm_content=setting_page_sidebar" class="button button-secondary" target="_blank"><?php esc_html_e('Meet Cool Plugins Developers', 'extensions-for-elementor-form'); ?></a>
                    <a href="https://x.com/cool_plugins" class="button button-secondary" target="_blank"><?php esc_html_e('Follow On X', 'extensions-for-elementor-form'); ?></a>
                    <a href="https://coolplugins.net/products-list/elementor-form-addons/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=view_plugins&utm_content=setting_page_sidebar" class="button button-secondary" target="_blank"><?php esc_html_e('View Plugins', 'extensions-for-elementor-form'); ?></a>

                </div>
            </div>
            <div class="cfkef-sidebar-block">
                <h3><?php esc_html_e('Enjoying Cool FormKit?', 'extensions-for-elementor-form'); ?></h3>
                <p><?php esc_html_e('Please consider leaving us a review. It helps us a lot!', 'extensions-for-elementor-form'); ?></p>
                <div class="cfkef-sidebar-link-group">
                    <div class="cfkef-review-right">
                        <div class="cfkef-stars">
                        ★★★★★
                        </div>
                        <a href="https://wordpress.org/support/plugin/extensions-for-elementor-form/reviews/#new-post" class="button button-primary" target="_blank"><?php esc_html_e('Leave a Review', 'extensions-for-elementor-form'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

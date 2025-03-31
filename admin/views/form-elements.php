<?php
// Ensure the file is being accessed through the WordPress admin area
if (!defined('ABSPATH')) {
    die;
}

// Get the saved options
$enabled_elements = get_option('cfkef_enabled_elements', array());

// Check if the default plugin option is set to true
$default_plugin_enabled = get_option('cfkef-defaultPlugin', false);

// If the default plugin option is true and conditional_logic is not in enabled_elements, add it
if ($default_plugin_enabled && !in_array('conditional_logic', $enabled_elements)) {
    $enabled_elements[] = 'conditional_logic';
    $enabled_elements[] = 'conditional_redirect';
    $enabled_elements[] = 'conditional_email';
    $enabled_elements[] = 'conditional_submit_button';
    update_option('cfkef-defaultPlugin',false);
}

$form_elements = array(
    'custom_success_message' => array(
        'label' => __('Custom Success Message', 'cool-formkit'),
        'how_to' => 'https://site.com/conditional-logic-how-to',
        'demo' => 'https://site.com/conditional-logic-demo'
    ),
    'register_post_after_submit' => array(
        'label' => __('Register Post/Custom Post', 'cool-formkit'),
        'how_to' => 'https://site.com/conditional-logic-how-to',
        'demo' => 'https://site.com/conditional-logic-demo'
    ),
    'whatsapp_redirect' => array(
        'label' => __('What\'s App Redirect', 'cool-formkit'),
        'how_to' => 'https://site.com/conditional-logic-how-to',
        'demo' => 'https://site.com/conditional-logic-demo'
    ),
    'conditional_logic' => array(
        'label' => __('Conditional Logic', 'cool-formkit'),
        'how_to' => 'https://site.com/conditional-logic-how-to',
        'demo' => 'https://site.com/conditional-logic-demo'
    ),
    'conditional_redirect' => array(
        'label' => __('Redirect Conditionally After Submit', 'cool-formkit'),
        'how_to' => 'https://site.com/conditional-redirect-how-to',
        'demo' => 'https://site.com/conditional-redirect-demo'
    ),
    'conditional_email' => array(
        'label' => __('Conditional Email After Submit', 'cool-formkit'),
        'how_to' => 'https://site.com/conditional-email-how-to',
        'demo' => 'https://site.com/conditional-email-demo',
        'popular' => true
    ),
    'conditional_submit_button' => array(
        'label' => __('Conditional Logic For Submit Button', 'cool-formkit'),
        'how_to' => 'https://site.com/conditional-email-how-to',
        'demo' => 'https://site.com/conditional-email-demo',
        'popular' => true
    ),
    'range_slider' => array(
        'label' => __('Range Slider', 'cool-formkit'),
        'how_to' => 'https://site.com/range-slider-how-to',
        'demo' => 'https://site.com/range-slider-demo'
    ),
    'country_code' => array(
        'label' => __('Country Code for Tel Field', 'cool-formkit'),
        'how_to' => 'https://site.com/country-code-how-to',
        'demo' => 'https://site.com/country-code-demo'
    ),
    'calculator_field' => array(
        'label' => __('Calculator Field', 'cool-formkit'),
        'how_to' => 'https://site.com/calculator-field-how-to',
        'demo' => 'https://site.com/calculator-field-demo'
    ),
    'rating_field' => array(
        'label' => __('Rating Field', 'cool-formkit'),
        'how_to' => 'https://site.com/rating-field-how-to',
        'demo' => 'https://site.com/rating-field-demo'
    ),
    'signature_field' => array(
        'label' => __('Signature Field', 'cool-formkit'),
        'how_to' => 'https://site.com/rating-field-how-to',
        'demo' => 'https://site.com/rating-field-demo'
    ),
    'image_radio' => array(
        'label' => __('Image Radio', 'cool-formkit'),
        'how_to' => 'https://site.com/rating-field-how-to',
        'demo' => 'https://site.com/rating-field-demo'
    ),
    'radio_checkbox_styler' => array(
        'label' => __('Radio & Checkbox Styler', 'cool-formkit'),
        'how_to' => 'https://site.com/rating-field-how-to',
        'demo' => 'https://site.com/rating-field-demo'
    ),
    'label_styler' => array(
        'label' => __('Label Styler', 'cool-formkit'),
        'how_to' => 'https://site.com/rating-field-how-to',
        'demo' => 'https://site.com/rating-field-demo'
    ),
    'select2' => array(
        'label' => __('Select2', 'cool-formkit'),
        'how_to' => '',
        'demo' => ''
    ),
    'WYSIWYG' => array(
        'label' => __('WYSIWYG', 'cool-formkit'),
        'how_to' => '',
        'demo' => ''
    ),
    'confirm_dialog' => array(
        'label' => __('Confirm Dialog Box', 'cool-formkit'),
        'how_to' => '',
        'demo' => ''
    ),
    'restrict_date' => array(
        'label' => __('Restrict Date', 'cool-formkit'),
        'how_to' => '',
        'demo' => ''
    ),
    'currency_field' => array(
        'label' => __('Currency Field', 'cool-formkit'),
        'how_to' => '',
        'demo' => ''
    ),
    'month_week_field' => array(
        'label' => __('Month/Week Field', 'cool-formkit'),
        'how_to' => '',
        'demo' => ''
    )
);

$popular_elements = array('');
$updated_elements = array('');
$pro_elements = array('conditional_logic', 'conditional_redirect', 'conditional_email', 'conditional_submit_button', 'range_slider', 'country_code', 'calculator_field', 'rating_field', 'signature_field', 'image_radio', 'radio_checkbox_styler', 'label_styler', 'select2','WYSIWYG','confirm_dialog','restrict_date','currency_field','month_week_field');
?>

<form method="post" action="options.php">
    <?php settings_fields('cfkef_form_elements_group'); ?>
    <?php do_settings_sections('cfkef_form_elements_group'); ?>

    <div class="cfkef-main-content">
        <div class="cfkef-form-elements-container">
            <div class="cfkef-save-all">
                <div class="cfkef-title-desc">
                    <h2><?php esc_html_e('Form Elements', 'cool-formkit'); ?></h2>
                    <p><?php esc_html_e('Manage the form elements and functionalities provided by Cool FormKit.', 'cool-formkit'); ?></p>
                </div>
                <div class="cfkef-save-controls">
                    <div class="cfkef-toggle-all-wrapper">
                        <span class="cfkef-toggle-label"><?php esc_html_e('Disable All', 'cool-formkit'); ?></span>
                        <label class="cfkef-toggle-switch">
                            <input type="checkbox" id="cfkef-toggle-all">
                            <span class="cfkef-slider round"></span>
                        </label>
                        <span class="cfkef-toggle-label"><?php esc_html_e('Enable All', 'cool-formkit'); ?></span>
                    </div>
                    <button type="submit" class="button button-primary"><?php esc_html_e('Save Changes', 'cool-formkit'); ?></button>
                </div>
            </div>

            <?php foreach ($form_elements as $key => $element): ?>
            <div class="cfkef-form-element-card">
                <div class="cfkef-form-element-info">
                    <h2>
                        <?php echo esc_html($element['label']); ?>
                        <?php if (in_array($key, $popular_elements)): ?>
                            <span class="cfkef-label-popular">Popular</span>
                        <?php endif; ?>
                        <?php if (in_array($key, $updated_elements)): ?>
                            <span class="cfkef-label-updated">Updated</span>
                        <?php endif; ?>
                        <?php if (in_array($key, $pro_elements)): ?>
                            <span class="cfkef-label-pro">Pro</span>
                        <?php endif; ?>
                    </h2>
                    <!-- <div class="cfkef-form-element-icons">
                        <a href="<?php // echo esc_url($element['how_to']); ?>" target="_blank"><i class="dashicons dashicons-editor-help"></i><span>How To?</span></a>
                        <a href="<?php // echo esc_url($element['demo']); ?>" target="_blank"><i class="dashicons dashicons-visibility"></i><span>Demo</span></a>
                    </div> -->
                </div>
                <label class="cfkef-toggle-switch">
                    <input type="checkbox" name="cfkef_enabled_elements[]" value="<?php echo esc_attr($key); ?>" <?php checked(in_array($key, $enabled_elements) && !in_array($key, $pro_elements)); ?>  
                    <?php echo in_array($key, $pro_elements) ? 'disabled' : ''; ?> 
                    class="cfkef-element-toggle">
                    <span class="cfkef-slider round"></span>
                </label>
            </div>
            <?php endforeach; ?>

            <div class="cfkef-save-bottom">
                <?php submit_button(__('Save Changes', 'cool-formkit')); ?>
            </div>

            <div class="cfkef-review-request">
                <div class="cfkef-review-left">
                    <h3><?php esc_html_e('Enjoying Cool FormKit?', 'cool-formkit'); ?></h3>
                    <p><?php esc_html_e('Please consider leaving us a review. It helps us a lot!', 'cool-formkit'); ?></p>
                </div>
                <div class="cfkef-review-right">
                    <div class="cfkef-stars">
                    ★★★★★
                    </div>
                    <a href="https://coolplugins.net/reviews/submit-review/?utm_source=cfkef_plugin&utm_medium=inside&utm_campaign=review&utm_content=cfkef-footer" class="button button-primary" target="_blank"><?php esc_html_e('Leave a Review', 'cool-formkit'); ?></a>
                </div>
            </div>
        </div>
        <div class="cfkef-sidebar">
            <div class="cfkef-sidebar-block">
                <h3><?php esc_html_e('Important Links', 'cool-formkit'); ?></h3>
                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    <a href="https://coolplugins.net/support/?utm_source=cfkef_plugin&utm_medium=inside&utm_campaign=get-support&utm_content=setting-page-sidebar" class="button" target="_blank"><?php esc_html_e('Contact Support', 'cool-formkit'); ?></a>
                    <a href="https://coolplugins.net/video/cool-formkit-pro/" class="button button-secondary" target="_blank"><?php esc_html_e('Watch Video Tutorial', 'cool-formkit'); ?></a>
                    <a href="https://coolplugins.net/about-us/?utm_source=cfkef_plugin&utm_medium=inside&utm_campaign=about-us&utm_content=setting-page-sidebar" class="button button-secondary" target="_blank"><?php esc_html_e('Meet Cool Plugins Developers', 'cool-formkit'); ?></a>
                    <a href="https://x.com/cool_plugins" class="button button-secondary" target="_blank"><?php esc_html_e('Follow On X', 'cool-formkit'); ?></a>
                </div>
            </div>
            <div class="cfkef-sidebar-block">
                <h3><?php esc_html_e('More Plugins by Author', 'cool-formkit'); ?></h3>
                <p><?php esc_html_e('Explore other plugins developed by us to enhance your WordPress experience.', 'cool-formkit'); ?></p>
                <a href="https://coolplugins.net/products/?utm_source=cfkef_plugin&utm_medium=inside&utm_campaign=view-plugin&utm_content=setting-page-sidebar" class="button button-secondary" target="_blank"><?php esc_html_e('View Plugins', 'cool-formkit'); ?></a>
            </div>
        </div>
    </div>
</form>

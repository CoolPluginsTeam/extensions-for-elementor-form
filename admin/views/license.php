<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Ensure the file is being accessed through the WordPress admin area
if (!defined('ABSPATH')) {
    die;
}
?>
<div class="cfkef-license-box">
    <div class="wrapper-header">
        <div class="cfkef-save-all">
            <div class="cfkef-title-desc">
                <h2><?php esc_html_e('License Key', 'cool-formkit'); ?></h2>
            </div>
            <div class="cfkef-save-controls">
                <span><?php esc_html_e('Free', 'cool-formkit'); ?></span>
                <a class="button button-primary upgrade-pro-btn" target="_blank" href="https://coolplugins.net/cool-formkit-for-elementor-forms/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=get_pro&utm_content=license_dashboard#pricing">
                    <img class="crown-diamond-pro" src="<?php echo esc_url(CFL_PLUGIN_URL . 'admin/assets/images/crown-diamond-pro.png'); ?>" alt="Cool FormKit Logo">
                    <?php esc_html_e('Upgrade To Pro', 'cool-formkit'); ?>
                </a>
            </div>
        </div>
    </div>
    <div class="wrapper-body">
        <p><?php esc_html_e('Your license key provides access to pro version updates and support.', 'cool-formkit'); ?></p>
        <p><?php esc_html_e('You\'re using ', 'cool-formkit'); ?><strong><?php esc_html_e('Cool Formkit Lite (Free) ', 'cool-formkit'); ?></strong><?php esc_html_e('- no license needed. Enjoy! 😊', 'cool-formkit'); ?></p>
        <div class="cfkef-license-upgrade-box">
            <p><?php esc_html_e('To unlock more features, consider ', 'cool-formkit'); ?><a href="https://coolplugins.net/cool-formkit-for-elementor-forms/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=get_pro&utm_content=license_dashboard#pricing" target="_blank"><?php esc_html_e('upgrading to Pro', 'cool-formkit'); ?></a>.
            </p>
            <em><?php esc_html_e('As a valued user, you automatically receive an exclusive discount on the Annual License and an even greater discount on the POPULAR Lifetime License at checkout!', 'cool-formkit'); ?></em>
        </div>


        <div class="pro-plugin-buy cfkef-license-upgrade-box">

            <p><?php esc_html_e('Have you purchased pro plugin?', 'cool-formkit'); ?></p>



            <div class="pro-plugin-buy-buttons-con">

                <a class="button button-primary pro-bought-btn" target="_blank" href="https://my.coolplugins.net/account/downloads">
                    <?php esc_html_e('Yes', 'cool-formkit'); ?>
                </a>


                <a class="button button-primary no-pro-buy-btn" target="_blank" href="https://coolformkit.com/pricing/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=get-pro&utm_content=license_dashboard#pricing">
                    <?php esc_html_e('No', 'cool-formkit'); ?>
                </a>
            </div>

            <em><?php esc_html_e("If you've purchased the Pro plugin, download it from ", 'cool-formkit'); ?><a href="https://my.coolplugins.net/account/downloads" target="_blank"><?php esc_html_e('my.coolplugins.net', 'cool-formkit'); ?></a><?php esc_html_e(', deactivate the free plugin, then install and activate the Pro version along with the license.', 'cool-formkit'); ?></em>

        </div>

    </div>
</div>
<?php
do_action('cfkef_render_pro_license_fields');

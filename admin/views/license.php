<?php
// Ensure the file is being accessed through the WordPress admin area
if (!defined('ABSPATH')) {
    die;
}
?>
<div class="cfkef-license-box">
    <div class="wrapper-header">
        <div class="cfkef-save-all">
            <div class="cfkef-title-desc">
                <h2><?php esc_html_e('License Key', 'extensions-for-elementor-form'); ?></h2>
            </div>
            <div class="cfkef-save-controls">
                <span><?php esc_html_e('Free', 'extensions-for-elementor-form'); ?></span>
                <a class="button button-primary upgrade-pro-btn" target="_blank" href="https://coolformkit.com/pricing/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=get_pro&utm_content=license_dashboard">
                    <img class="crown-diamond-pro" src="<?php echo esc_url(CFL_PLUGIN_URL . 'admin/assets/images/crown-diamond-pro.png'); ?>" alt="Cool FormKit Logo">
                    <?php esc_html_e('Upgrade To Pro', 'extensions-for-elementor-form'); ?>
                </a>
            </div>
        </div>
    </div>
    <div class="wrapper-body">
        <p><?php esc_html_e('Your license key provides access to pro version updates and support.', 'extensions-for-elementor-form'); ?></p>
        <p><?php esc_html_e('You\'re using ', 'extensions-for-elementor-form'); ?><strong><?php esc_html_e('Cool Formkit Lite (Free) ', 'extensions-for-elementor-form'); ?></strong><?php esc_html_e('- no license needed. Enjoy! 😊', 'extensions-for-elementor-form'); ?></p>
        <div class="cfkef-license-upgrade-box">
            <p><?php esc_html_e('To unlock more features, consider ', 'extensions-for-elementor-form'); ?><a href="https://coolformkit.com/pricing/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=get_pro&utm_content=license_dashboard" target="_blank"><?php esc_html_e('upgrading to Pro', 'extensions-for-elementor-form'); ?></a>.
            </p>
            <em><?php esc_html_e('As a valued user, you automatically receive an exclusive discount on the Annual License and an even greater discount on the POPULAR Lifetime License at checkout!', 'extensions-for-elementor-form'); ?></em>
        </div>


        <div class="pro-plugin-buy cfkef-license-upgrade-box">

            <p><?php esc_html_e('Have you purchased pro plugin?', 'extensions-for-elementor-form'); ?></p>



            <div class="pro-plugin-buy-buttons-con">

                <a class="button button-primary pro-bought-btn" target="_blank" href="https://my.coolplugins.net/account/downloads">
                    <?php esc_html_e('Yes', 'extensions-for-elementor-form'); ?>
                </a>


                <a class="button button-primary no-pro-buy-btn" target="_blank" href="https://coolformkit.com/pricing/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=get_pro&utm_content=license_dashboard">
                    <?php esc_html_e('No', 'extensions-for-elementor-form'); ?>
                </a>
            </div>

            <em><?php esc_html_e("If you've purchased the Pro plugin, download it from ", 'extensions-for-elementor-form'); ?><a href="https://my.coolplugins.net/account/downloads" target="_blank"><?php esc_html_e('my.coolplugins.net', 'extensions-for-elementor-form'); ?></a><?php esc_html_e(', deactivate the free plugin, then install and activate the Pro version along with the license.', 'extensions-for-elementor-form'); ?></em>

        </div>

    </div>
</div>
<?php
do_action('cfkef_render_pro_license_fields');

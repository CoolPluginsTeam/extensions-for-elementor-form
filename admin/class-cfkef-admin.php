<?php
namespace Cool_formkit\admin;
use Cool_FormKit\Includes\Cron\CFL_cronjob;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Cool_FormKit
 * @subpackage Cool_FormKit/admin
 */

if (!defined('ABSPATH')) {
    die;
}

/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 * @package    Cool_FormKit
 * @subpackage Cool_FormKit/admin
 */
if(!class_exists('CFKEF_Admin')) { 
class CFKEF_Admin {

    /**
     * The instance of this class.
     *
     * @since    1.0.0
     * @access   private
     * @var      CFKEF_Admin    $instance    The instance of this class.
     */
    private static $instance = null;

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Constructor to initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    string    $plugin_name       The name of this plugin.
     * @param    string    $version    The version of this plugin.
     */
    private function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        add_action('admin_menu', array($this, 'add_plugin_admin_menu'),999);
        add_action('admin_init', array($this, 'register_form_elements_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));

        add_action('cpfm_register_notice', function () {
            
            if (!class_exists('\CPFM_Feedback_Notice') || !current_user_can('manage_options')) {
                return;
            }

            $notice = [

                'title' => __('Elementor Form Addons by Cool Plugins', 'extensions-for-elementor-form'),
                'message' => __('Help us make this plugin more compatible with your site by sharing non-sensitive site data.', 'cool-plugins-feedback'),
                'pages' => ['cool-formkit','cfkef-entries','cool-formkit&tab=recaptcha-settings'],
                'always_show_on' => ['cool-formkit','cfkef-entries','cool-formkit&tab=recaptcha-settings'], // This enables auto-show
                'plugin_name'=>'cool_forms'
            ];

            \CPFM_Feedback_Notice::cpfm_register_notice('cool_forms', $notice);

                if (!isset($GLOBALS['cool_plugins_feedback'])) {
                    $GLOBALS['cool_plugins_feedback'] = [];
                }
                
                $GLOBALS['cool_plugins_feedback']['cool_forms'][] = $notice;
           
        });
        
        add_action('cpfm_after_opt_in_cool_forms', function($category) {                
            if ($category === 'cool_forms') {

                CFL_cronjob::cfl_send_data();
                update_option( 'cfef_usage_share_data','on' );   
            } 
        });

        add_action( 'wp_ajax_cfkef_plugin_install', 'wp_ajax_install_plugin' );
        add_action( 'wp_ajax_cfkef_plugin_activate', array($this,'cfkef_plugin_activate') );
    }

    public function cfkef_plugin_activate(){
        check_ajax_referer( 'cfkef_plugin_nonce', 'security' );
        if ( ! current_user_can( 'activate_plugins' ) ) {
            wp_send_json_error( [ 'message' => 'Permission denied' ] );
        }

        if ( empty( $_POST['init'] ) ) {
            wp_send_json_error( [ 'message' => 'Plugin init file missing' ] );
        }

        include_once ABSPATH . 'wp-admin/includes/plugin.php';

        $init_file = sanitize_text_field( wp_unslash($_POST['init']) );

        $activate = activate_plugin( $init_file );

        if ( is_wp_error( $activate ) ) {
            wp_send_json_error( [ 'message' => $activate->get_error_message() ] );
        }

        wp_send_json_success( [ 'message' => 'Plugin activated successfully' ] );
    } 
    /**
     * Get the instance of this class.
     *
     * @since    1.0.0
     * @param    string    $plugin_name       The name of this plugin.
     * @param    string    $version    The version of this plugin.
     * @return   CFKEF_Admin    The instance of this class.
     */
    public static function get_instance($plugin_name, $version) {
        if (null == self::$instance) {
            self::$instance = new self($plugin_name, $version);
        }
        return self::$instance;
    }

    
    /**
     * Add a menu item under Settings.
     *
     * @since    1.0.0
     */
    public function add_plugin_admin_menu() {
        add_submenu_page(
            'elementor',
            __('Cool FormKit', 'cool-formkit'),
            __('Cool FormKit', 'cool-formkit'),
            'manage_options',
            'cool-formkit',
            array($this, 'display_plugin_admin_page')
        );
    }

    
    /**
     * Display the plugin admin page with tabs.
     *
     * @since    1.0.0
     */
    public function display_plugin_admin_page() {
        $tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'form-elements';
        ?>
        <div class="cfkef-wrapper">
            <div class="cfkef-header">
                <div class="cfkef-header-logo">
                    <a href="?page=cool-formkit">
                        <img src="<?php echo esc_url(CFL_PLUGIN_URL . 'assets/images/logo-cool-formkit.png'); ?>" alt="Cool FormKit Logo">
                    </a>
                    <span>Lite</span>
                    <a class="button button-primary upgrade-pro-btn" target="_blank" href="https://coolformkit.com/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=get_pro&utm_content=dashboard">
                        <img class="crown-diamond-pro" src="<?php echo esc_url(CFL_PLUGIN_URL . 'admin/assets/images/crown-diamond-pro.png'); ?>" alt="Cool FormKit Logo">
                        <?php esc_html_e('Upgrade To Pro', 'cool-formkit'); ?>
                    </a>
                </div>
                <div class="cfkef-header-buttons">
                    <p><?php esc_html_e('Advanced Elementor Form Builder.', 'cool-formkit'); ?></p>
                    <a href="https://docs.coolplugins.net/plugin/cool-formkit-for-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=docs&utm_content=setting_page_header" class="button" target="_blank"><?php esc_html_e('Check Docs', 'cool-formkit'); ?></a>
                    <a href="https://coolformkit.com/features/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=demo&utm_content=setting_page_header" class="button button-secondary" target="_blank"><?php esc_html_e('View Form Demos', 'cool-formkit'); ?></a>
                </div>
            </div>
            <h2 class="nav-tab-wrapper">
                <a href="?page=cool-formkit&tab=form-elements" class="nav-tab <?php echo $tab == 'form-elements' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Form Elements', 'cool-formkit'); ?></a>
                <a href="?page=cfkef-entries" class="nav-tab <?php echo $tab == 'cfkef-entries' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Entries', 'cool-formkit'); ?></a>
                <a href="?page=cool-formkit&tab=settings" class="nav-tab <?php echo $tab == 'settings' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Settings', 'cool-formkit'); ?></a>
                <a href="?page=cool-formkit&tab=license" class="nav-tab <?php echo $tab == 'license' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('License', 'cool-formkit'); ?></a>
            </h2>
            <div class="tab-content">
                <?php
                switch ($tab) {
                    case 'form-elements':
                        include_once 'views/form-elements.php';
                        break;
                    case 'settings':
                        include_once 'views/settings.php';
                        break;
                    case 'license':
                        include_once 'views/license.php';
                        break;
                }
                ?>
            </div>
        </div>
        <?php
    }
    /**
     * Register the settings for form elements.
     *
     * @since    1.0.0
     */
    public function register_form_elements_settings() {
        register_setting('cfkef_form_elements_group', 'cfkef_enabled_elements', array(
            'type' => 'array',
            'description' => 'Enabled Form Elements',
            'sanitize_callback' => array($this, 'sanitize_form_elements'),
            'default' => array('conditional_logic','country_code')
        ));
        register_setting( 'cfkef_form_elements_group', 'cfkef_toggle_all' );

        register_setting( 'cfkef_form_elements_group', 'cfkef_enable_elementor_pro_form' );
        register_setting( 'cfkef_form_elements_group', 'cfkef_enable_hello_plus' );
        register_setting( 'cfkef_form_elements_group', 'cfkef_enable_formkit_builder' );

        if (!get_option('cfl_plugin_initialized')) {
            // Get current enabled elements or empty array
            $enabled_elements = get_option('cfkef_enabled_elements', array());

            // Only update if it's empty (first-time install)
            if (empty($enabled_elements)) {
                $default_elements = array(
                    'conditional_logic',
                    'country_code',
                    'form_input_mask',
                    'whatsapp_redirect'
                );

                update_option('cfkef_enabled_elements', $default_elements);
            }
            // Set initialization flag to avoid repeating
            update_option('cfl_plugin_initialized', true);
        }
    }

    /**
     * Sanitize form elements input.
     *
     * @param array $input The input array.
     * @return array The sanitized array.
     */
    public function sanitize_form_elements($input) {
        $valid = array();

        $form_elements = array('conditional_logic', 'conditional_redirect', 'conditional_email', 'conditional_submit_button', 'conditional_mailchimp','conditional_getresponse','conditional_webhook', 'conditional_whatsapp_redirect','range_slider', 'country_code', 'calculator_field', 'rating_field', 'signature_field', 'image_radio', 'radio_checkbox_styler', 'label_styler', 'select2','WYSIWYG','confirm_dialog','restrict_date','currency_field','month_week_field','custom_success_message','register_post_after_submit','whatsapp_redirect','form_input_mask');

        if (is_array($input)) {
            foreach ($input as $element) {
                if (in_array($element, $form_elements)) {
                    $valid[] = $element;
                }
            }
        } 
        return $valid;
    }

    /**
     * Enqueue admin styles and scripts.
     *
     * @since    1.0.0
     */
    public function enqueue_admin_styles() {
        wp_enqueue_script('cfkef-global-admin', CFL_PLUGIN_URL . 'assets/js/global-admin.js', array('jquery'), $this->version, true);

        if (isset($_GET['page']) &&(strpos($_GET['page'], 'cool-formkit') !== false || strpos($_GET['page'], 'cfkef-entries') !== false)) {
            wp_enqueue_style('cfkef-admin-style', CFL_PLUGIN_URL . 'assets/css/admin-style.css', array(), $this->version, 'all');
            wp_enqueue_style('dashicons');
            wp_enqueue_script('cfkef-admin-script', CFL_PLUGIN_URL . 'assets/js/admin-script.js', array('jquery'), $this->version, true);

            wp_localize_script( 'cfkef-admin-script', 'cfkef_plugin_vars', [
                'nonce' => wp_create_nonce( 'cfkef_plugin_nonce' ),
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'installNonce' => wp_create_nonce( 'updates' ),
            ] );
        }
    }

}
}

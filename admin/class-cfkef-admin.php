<?php
namespace Cool_formkit\admin;

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
if(!class_exists('CFL_Admin')) { 
class CFL_Admin {

    /**
     * The instance of this class.
     *
     * @since    1.0.0
     * @access   private
     * @var      CFL_Admin    $instance    The instance of this class.
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
        // add_action('admin_menu', array($this, 'add_plugin_admin_menu'),999);
        add_action('admin_init', array($this, 'register_form_elements_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
    }

    /**
     * Get the instance of this class.
     *
     * @since    1.0.0
     * @param    string    $plugin_name       The name of this plugin.
     * @param    string    $version    The version of this plugin.
     * @return   CFL_Admin    The instance of this class.
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
        $tab = isset($_GET['tab']) ? $_GET['tab'] : 'form-elements';
        ?>
        <div class="cfkef-wrapper">
            <div class="cfkef-header">
                <div class="cfkef-header-logo">
                    <img src="<?php echo esc_url(CFL_PLUGIN_URL . 'assets/images/cool-formkit-logo.png'); ?>" alt="Cool FormKit Logo">
                </div>
                <div class="cfkef-header-buttons">
                    <p>Upgrade your Elementor form with advanced fields and features.</p>
                    <a href="https://docs.coolplugins.net/docs/cool-formkit/?utm_source=cfkef_plugin&utm_medium=inside&utm_campaign=doc&utm_content=setting-page-header" class="button" target="_blank"><?php esc_html_e('Check Docs', 'cool-formkit'); ?></a>
                    <a href="https://coolplugins.net/cool-formkit-for-elementor-forms/?utm_source=cfkef_plugin&utm_medium=inside&utm_campaign=view-demo&utm_content=setting-page-header" class="button button-secondary" target="_blank"><?php esc_html_e('View Form Demos', 'cool-formkit'); ?></a>
                </div>
            </div>
            <h2 class="nav-tab-wrapper">
                <a href="?page=cool-formkit&tab=form-elements" class="nav-tab <?php echo $tab == 'form-elements' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Form Elements', 'cool-formkit'); ?></a>
                <a href="?page=cool-formkit&tab=settings" class="nav-tab <?php echo $tab == 'settings' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Settings', 'cool-formkit'); ?></a>
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
            'default' => array()
        ));
    }

    /**
     * Sanitize form elements input.
     *
     * @param array $input The input array.
     * @return array The sanitized array.
     */
    public function sanitize_form_elements($input) {
        $valid = array();

        $form_elements = array('conditional_logic', 'conditional_redirect', 'conditional_email', 'conditional_submit_button', 'range_slider', 'country_code', 'calculator_field', 'rating_field', 'signature_field', 'image_radio', 'radio_checkbox_styler', 'label_styler', 'select2','WYSIWYG','confirm_dialog','restrict_date','currency_field','month_week_field','custom_success_message','register_post_after_submit','whatsapp_redirect');

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

        if (isset($_GET['page']) && strpos($_GET['page'], 'cool-formkit') !== false) {
            wp_enqueue_style('cfkef-admin-style', CFL_PLUGIN_URL . 'assets/css/admin-style.css', array(), $this->version, 'all');
            wp_enqueue_style('dashicons');
            wp_enqueue_script('cfkef-admin-script', CFL_PLUGIN_URL . 'assets/js/admin-script.js', array('jquery'), $this->version, true);
        }
    }

}
}

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
        add_action('admin_init', array($this, 'register_form_elements_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
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

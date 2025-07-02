<?php

namespace Cool_FormKit\Includes;

use Cool_Formkit\admin\CFKEF_Admin;

use Cool_FormKit\Admin\Register_Menu_Dashboard\CFKEF_Dashboard;
use Cool_FormKit\Admin\Entries\CFKEF_Entries_Posts;
use Cool_FormKit\Includes\Frontend\CFKEF_Frontend;

use Cool_FormKit\Includes\Frontend\Widget\Custom_Success_Message;


/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * admin-facing side of the site and the public-facing side.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Cool_FormKit
 * @subpackage Cool_FormKit/includes
 */

if (!defined('ABSPATH')) {
    die;
}

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Cool_FormKit
 * @subpackage Cool_FormKit/includes
 */
if(!class_exists('CFL_Loader')) { 
class CFL_Loader {

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * The loader instance.
     *
     * @since    1.0.0
     * @access   private
     * @var      CFL_Loader    $instance    The loader instance.
     */
    private static $instance = null;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    private function __construct() {
        $this->plugin_name = 'extensions-for-elementor-form';
        $this->version = CFL_VERSION;

        $this->admin_menu_dashboard();

        do_action( 'extensions_for_elementor_form_load' );
		add_action( 'elementor/init', array( $this, 'init' ), 5 );

        $this->load_dependencies();
    }

    /**
     * Get the instance of this class.
     *
     * @since    1.0.0
     * @return   CFL_Loader    The instance of this class.
     */


    public static function get_instance() {
        if (null == self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function is_field_enabled($field_key) {
        $enabled_elements = get_option('cfkef_enabled_elements', array());
        return in_array(sanitize_key($field_key), array_map('sanitize_key', $enabled_elements));

    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - CFKEF_i18n. Defines internationalization functionality.
     * - CFL_Admin. Defines all hooks for the admin area.
     * - CFKEF_Public. Defines all hooks for the public side of the site.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        require_once CFL_PLUGIN_PATH . 'admin/class-cfkef-admin.php';
        $plugin_admin = CFKEF_Admin::get_instance($this->get_plugin_name(), $this->get_version());
        if(get_option('cfkef_enable_elementor_pro_form', true)){
            require_once CFL_PLUGIN_PATH . 'includes/frontend/class-cfl-frontend.php';
            $plugin_public = new CFKEF_Frontend($this->get_plugin_name(), $this->get_version());
        }
    }


    private function admin_menu_dashboard() {
        if(class_exists(CFKEF_Dashboard::class)){
            $menu_pages = CFKEF_Dashboard::get_instance($this->get_plugin_name(), $this->get_version());
        }

        if(class_exists(CFKEF_Entries_Posts::class)){
            $entries_posts = CFKEF_Entries_Posts::get_instance();
        }


        // if(class_exists(Recaptcha_settings::class)){
        //     $entries_posts = Recaptcha_settings::get_instance();
        // }
    }


    
    /**
	 * Init plugin
	 */
	public function init() : void {
		do_action( 'extensions_for_elementor_form_init' );
	}
    
    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since    1.0.0
     * @return   string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since    1.0.0
     * @return   string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}
}
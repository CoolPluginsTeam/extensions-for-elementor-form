<?php

namespace Cool_FormKit\Includes;

use Cool_FormKit\Includes\Custom_Success_Message;
use Cool_FormKit\Includes\Actions\Register_Actions;
use Cool_Formkit\admin\CFKEF_Admin;
use Cool_Formkit\Admin\Submission_Posts\CFKEF_Submission_Posts;

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
if(!class_exists('CFKEF_Loader')) { 
class CFKEF_Loader {

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
     * @var      CFKEF_Loader    $instance    The loader instance.
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

        if ( ! is_plugin_active( 'elementor-pro/elementor-pro.php' ) ) {
            return false;
		}

        do_action( 'extensions_for_elementor_form_load' );
		add_action( 'elementor/init', array( $this, 'init' ), 5 );

        $this->load_dependencies();
        $this->include_addons();
        $this->load_submission_posts();
    }

    /**
     * Get the instance of this class.
     *
     * @since    1.0.0
     * @return   CFKEF_Loader    The instance of this class.
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

    public function include_addons(){
        include_once CFL_PLUGIN_PATH . '/includes/actions/class-register-actions.php';
        // if($this->is_field_enabled('custom_success_message')){
    		include_once CFL_PLUGIN_PATH . 'includes/widget/class-custom-success-message.php';
            $custom_success_message = new Custom_Success_Message();
            $custom_success_message->set_hooks();
        // }
        // if($this->is_field_enabled('register_post_after_submit')){
            $actions = array(
                'register_post' => array(
                    'relative_path' => '/includes/actions/class-register-post.php',
                    'class_name' => 'Register_Post',
                ),
            );
            $regiser_actions = new Register_Actions( $actions );
            $regiser_actions->set_hooks();
        // }
        // if($this->is_field_enabled('whatsapp_redirect')){
            $actions = array(
                'whatsapp_redirect' => array(
                    'relative_path' => '/includes/actions/class-whatsapp-redirect.php',
                    'class_name' => 'Whatsapp_Redirect',
                )
            );
            $regiser_actions = new Register_Actions( $actions );
            $regiser_actions->set_hooks();
        // }
    }
    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - CFKEF_i18n. Defines internationalization functionality.
     * - CFKEF_Admin. Defines all hooks for the admin area.
     * - CFKEF_Public. Defines all hooks for the public side of the site.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        require_once CFL_PLUGIN_PATH . 'admin/class-cfkef-admin.php';
        $plugin_admin = CFKEF_Admin::get_instance($this->get_plugin_name(), $this->get_version());
    }

    private function load_submission_posts() {
        require_once CFL_PLUGIN_PATH . 'admin/submission-posts/class-cfkef-submission-posts.php';
        $submission_posts = CFKEF_Submission_Posts::get_instance();
    }
    
    /**
	 * Init plugin
	 */
	public function init() : void {
		do_action( 'extensions_for_elementor_form_init' );

		add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'register_editor_scripts') );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frondend_scripts' ) );
	}

	/**
	 * Enqueue front end styles/scripts
	 */
	public function enqueue_frondend_scripts() : void {
		wp_enqueue_script( 'eef-frontend-script', CFL_PLUGIN_URL . 'assets/js/frontend-scripts.min.js', array( 'jquery' ), CFL_VERSION );
		wp_enqueue_style( 'eef-frontend-style',  CFL_PLUGIN_URL . 'assets/css/style.min.css', array(), CFL_VERSION );
	}

	/**
	 * Register custom scritps on Elementor editor
	 *
	 * @since 2.0
	 */
	function register_editor_scripts() : void {
		wp_register_script( 'eef-editor-scripts', CFL_PLUGIN_URL . 'assets/js/admin/editor-scripts.min.js', array(), CFL_VERSION );
		wp_enqueue_script( 'eef-editor-scripts' );
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
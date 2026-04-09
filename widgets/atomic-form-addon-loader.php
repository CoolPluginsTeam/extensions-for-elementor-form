<?php

namespace Cool_FormKit\Widgets;
use Cool_FormKit\Widgets\AtomicForm\Input\Input;
use Elementor\Widgets_Manager;

class Atomic_Form_Addon_Loader {

    private static $instance = null;

    protected $version;

    protected $error_map;

    public static function get_instance() {
        if (null == self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {

        $this->version = CFL_VERSION;

        $this->error_map =[
            __("The phone number you entered is not valid. Please check the format and try again.", "extensions-for-elementor-form"),
            __("The country code you entered is not recognized. Please ensure it is correct and try again.", "extensions-for-elementor-form"),
            __("The phone number you entered is too short. Please enter a complete phone number, including the country code.", "extensions-for-elementor-form"),
            __("The phone number you entered is too long. Please ensure it is in the correct format and try again.", "extensions-for-elementor-form"),
            __("The phone number you entered is not valid. Please check the format and try again.", "extensions-for-elementor-form")
        ];

        add_filter('elementor/widgets/register', [$this, 'register_widgets'], 999);
        add_action('elementor/frontend/before_enqueue_scripts', [$this, 'enqueue_frontend_scripts']);
    }

    public function register_widgets( Widgets_Manager $widgets_manager ) {
		$widgets_manager->unregister('e-form-input');

		require_once CFL_PLUGIN_PATH . 'widgets/atomic-form/input/input.php';
		$widgets_manager->register( new Input() );
	}

    public function enqueue_frontend_scripts() {

        wp_register_script('sample-frontend-country-handle-js', CFL_PLUGIN_URL . 'assets/js/sample-frontend-country-handle.js', array('jquery'), $this->version, true);
        wp_enqueue_script('sample-frontend-country-handle-js');

        wp_localize_script(
			'sample-frontend-country-handle-js',
			'CCFEFCustomData',
			array(
				'pluginDir' => CFL_PLUGIN_URL,
				'errorMap'  => $this->error_map, 
			)	
		);

        wp_register_script('cfl-country-code-library-script', CFL_PLUGIN_URL . 'assets/addons/intl-tel-input/js/intlTelInput.js', array(), CFL_VERSION, true);
        wp_register_style('cfl-country-code-library-style', CFL_PLUGIN_URL . 'assets/addons/intl-tel-input/css/intlTelInput.min.css', array(), CFL_VERSION, 'all');
        wp_register_style('cfl-country-code-style', CFL_PLUGIN_URL . 'assets/addons/css/country-code-style.min.css', array(), CFL_VERSION, 'all');


        if (! wp_script_is('cfl-country-code-library-script', 'enqueued') && ! wp_script_is('cfl-country-code-library-script', 'done')) {
            wp_enqueue_script('cfl-country-code-library-script');
        }

        if (! wp_style_is('cfl-country-code-library-style', 'enqueued') && ! wp_style_is('cfl-country-code-library-style', 'done')) {
            wp_enqueue_style('cfl-country-code-library-style');
        }

        if (! wp_style_is('cfl-country-code-style', 'enqueued') && ! wp_style_is('cfl-country-code-style', 'done')) {
            wp_enqueue_style('cfl-country-code-style');
        }
    }

    public function get_version() {
        return $this->version;
    }

}

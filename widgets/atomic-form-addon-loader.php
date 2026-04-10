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

    /**
     * Mask scripts are normally registered by FME_Plugin when "form input mask" is enabled.
     * Atomic form masks still need them, so register here if missing.
     */
    private function ensure_fme_mask_assets_registered() {
        if ( ! wp_script_is( 'fme-custom-mask-script', 'registered' ) ) {
            wp_register_script( 'fme-custom-mask-script', CFL_PLUGIN_URL . 'assets/js/inputmask/custom-mask-script.js', array( 'jquery' ), $this->version, true );

            $error_messages = array(
                'mask-cnpj'  => __( 'Invalid CNPJ.', 'extensions-for-elementor-form' ),
                'mask-cpf'   => __( 'Invalid CPF.', 'extensions-for-elementor-form' ),
                'mask-cep'   => __( 'Invalid CEP (XXXXX-XXX).', 'extensions-for-elementor-form' ),
                'mask-phus'  => __( 'Invalid number: (123) 456-7890', 'extensions-for-elementor-form' ),
                'mask-ph8'   => __( 'Invalid number: 1234-5678', 'extensions-for-elementor-form' ),
                'mask-ddd8'  => __( 'Invalid number: (DDD) 1234-5678', 'extensions-for-elementor-form' ),
                'mask-ddd9'  => __( 'Invalid number: (DDD) 91234-5678', 'extensions-for-elementor-form' ),
                'mask-dmy'   => __( 'Invalid date: dd/mm/yyyy', 'extensions-for-elementor-form' ),
                'mask-mdy'   => __( 'Invalid date: mm/dd/yyyy', 'extensions-for-elementor-form' ),
                'mask-hms'   => __( 'Invalid time: hh:mm:ss', 'extensions-for-elementor-form' ),
                'mask-hm'    => __( 'Invalid time: hh:mm', 'extensions-for-elementor-form' ),
                'mask-dmyhm' => __( 'Invalid date: dd/mm/yyyy hh:mm', 'extensions-for-elementor-form' ),
                'mask-mdyhm' => __( 'Invalid date: mm/dd/yyyy hh:mm', 'extensions-for-elementor-form' ),
                'mask-my'    => __( 'Invalid date: mm/yyyy', 'extensions-for-elementor-form' ),
                'mask-ccs'   => __( 'Invalid credit card number.', 'extensions-for-elementor-form' ),
                'mask-cch'   => __( 'Invalid credit card number.', 'extensions-for-elementor-form' ),
                'mask-ccmy'  => __( 'Invalid date.', 'extensions-for-elementor-form' ),
                'mask-ccmyy' => __( 'Invalid date.', 'extensions-for-elementor-form' ),
                'mask-ipv4'  => __( 'Invalid IPv4 address.', 'extensions-for-elementor-form' ),
            );

            wp_localize_script(
                'fme-custom-mask-script',
                'fmeData',
                array(
                    'pluginUrl'     => CFL_PLUGIN_URL,
                    'errorMessages' => $error_messages,
                )
            );
        }

        if ( ! wp_style_is( 'fme-frontend-css', 'registered' ) ) {
            wp_register_style( 'fme-frontend-css', CFL_PLUGIN_URL . 'assets/css/inputmask/mask-frontend.css', array(), $this->version, 'all' );
        }
    }

    public function enqueue_frontend_scripts() {

        $this->ensure_fme_mask_assets_registered();

        wp_register_script('sample-frontend-country-handle-js', CFL_PLUGIN_URL . 'assets/atomic-form/js/sample-frontend-country-handle.js', array('jquery'), $this->version, true);
        wp_enqueue_script('sample-frontend-country-handle-js');

        wp_register_script(
            'cfl-atomic-form-mask-init',
            CFL_PLUGIN_URL . 'assets/atomic-form/js/atomic-form-mask-init.js',
            array( 'jquery', 'elementor-frontend', 'fme-custom-mask-script' ),
            $this->version,
            true
        );
        wp_enqueue_script( 'cfl-atomic-form-mask-init' );

        wp_enqueue_style( 'fme-frontend-css' );

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
        wp_register_style('cfl-atomic-form-country-code-style', CFL_PLUGIN_URL . 'assets/atomic-form/css/atomic-form-country-code-style.min.css', array(), CFL_VERSION, 'all');


        if (! wp_script_is('cfl-country-code-library-script', 'enqueued') && ! wp_script_is('cfl-country-code-library-script', 'done')) {
            wp_enqueue_script('cfl-country-code-library-script');
        }

        if (! wp_style_is('cfl-country-code-library-style', 'enqueued') && ! wp_style_is('cfl-country-code-library-style', 'done')) {
            wp_enqueue_style('cfl-country-code-library-style');
        }

        if (! wp_style_is('cfl-atomic-form-country-code-style', 'enqueued') && ! wp_style_is('cfl-atomic-form-country-code-style', 'done')) {
            wp_enqueue_style('cfl-atomic-form-country-code-style');
        }
    }

    public function get_version() {
        return $this->version;
    }

}

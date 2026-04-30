<?php

namespace Cool_FormKit\Widgets;
use Cool_FormKit\Widgets\AtomicForm\Atomic_Form;
use Cool_FormKit\Widgets\AtomicForm\Checkbox\Checkbox;
use Cool_FormKit\Widgets\AtomicForm\Input\Input;
use Cool_FormKit\Widgets\AtomicForm\Textarea\Textarea;
use Elementor\Elements_Manager;
use Elementor\Plugin as Elementor_Plugin;
use Elementor\Utils as Elementor_Utils;
use Elementor\Widgets_Manager;
use ElementorPro\Modules\AtomicForm\Actions\Action_Runner;
use Cool_FormKit\Widgets\AtomicForm\Actions\AtomicForm_Whatsapp_Redirect;
use Cool_FormKit\Widgets\AtomicForm\Handle_Atomic_Form_Submission;
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

        add_filter('elementor/widgets/register', [$this, 'register_widgets'], 999);
        add_action( 'elementor/elements/elements_registered', [ $this, 'register_extended_atomic_form' ], 20 );
        add_action('elementor/frontend/before_enqueue_scripts', [$this, 'enqueue_frontend_scripts']);

        add_action('elementor/editor/after_enqueue_scripts', [$this, 'enqueue_editor_scripts']);

        add_action('elementor_pro/atomic_forms/actions/register', [$this, 'register_new_form_actions']);

		if ( $this->is_field_enabled( 'whatsapp_redirect' ) || $this->is_field_enabled( 'conditional_logic' ) ) {
			new Handle_Atomic_Form_Submission();
		}
    }

    private function is_field_enabled($field_key) {
        $enabled_elements = get_option('cfkef_enabled_elements', array());
        return in_array(sanitize_key($field_key), array_map('sanitize_key', $enabled_elements));
    }

    public function enqueue_editor_scripts() {

        if($this->is_field_enabled('conditional_logic')){

            wp_register_script('cfl-atomic-form-handle-conditional-repeater', CFL_PLUGIN_URL . 'assets/atomic-form/js/handle-conditional-repeater.js', array( 'jquery', 'elementor-editor'), $this->version, true);

            if (! wp_script_is('cfl-atomic-form-handle-conditional-repeater', 'enqueued') && ! wp_script_is('cfl-atomic-form-handle-conditional-repeater', 'done')) {
                wp_enqueue_script( 'cfl-atomic-form-handle-conditional-repeater' );
            }
        }

        wp_register_style('cfl-atomic-form-conditional-repeater-style', CFL_PLUGIN_URL . 'assets/atomic-form/css/atomic-form-conditional-repeater.min.css', array(), CFL_VERSION, 'all');
        if (! wp_style_is('cfl-atomic-form-conditional-repeater-style', 'enqueued') && ! wp_style_is('cfl-atomic-form-conditional-repeater-style', 'done')) {
            wp_enqueue_style('cfl-atomic-form-conditional-repeater-style');
        }


        if($this->is_field_enabled('whatsapp_redirect')){

            wp_register_script('cfl-atomic-form-handle-whatsapp-redirect-editor', CFL_PLUGIN_URL . 'assets/atomic-form/js/handle-whatsapp-redirect-editor.js', array( 'jquery', 'elementor-editor'), $this->version, true);
            if (! wp_script_is('cfl-atomic-form-handle-whatsapp-redirect-editor', 'enqueued') && ! wp_script_is('cfl-atomic-form-handle-whatsapp-redirect-editor', 'done')) {
                wp_enqueue_script('cfl-atomic-form-handle-whatsapp-redirect-editor');
            }
        }
    }

    

    public function register_new_form_actions($action_runner_class){
        if($this->is_field_enabled('whatsapp_redirect')){
            
            require_once CFL_PLUGIN_PATH . 'widgets/atomic-form/actions/atomic-form-whatsapp-redirect.php';
    
            if (is_string($action_runner_class) && method_exists($action_runner_class, 'register_action')) {
                $action_runner_class::register_action(new AtomicForm_Whatsapp_Redirect());
                return;
            }
    
            Action_Runner::register_action(new AtomicForm_Whatsapp_Redirect());
        }
    }

    public function register_widgets( Widgets_Manager $widgets_manager ) {

        if(get_option('cfkef_enable_atomic_form', true)){

            $widgets_manager->unregister('e-form-input');
            $widgets_manager->unregister('e-form-textarea');
            $widgets_manager->unregister('e-form-checkbox');
    
            require_once CFL_PLUGIN_PATH . 'widgets/atomic-form/input/input.php';
            require_once CFL_PLUGIN_PATH . 'widgets/atomic-form/textarea/textarea.php';
            require_once CFL_PLUGIN_PATH . 'widgets/atomic-form/checkbox/checkbox.php';
            $widgets_manager->register( new Input() );
            $widgets_manager->register( new Textarea() );
            $widgets_manager->register( new Checkbox() );
        }

    }

	/**
	 * Replace core e-form with an extended Atomic Form (extra controls / props).
	 */
	public function register_extended_atomic_form( Elements_Manager $elements_manager ) {

        if(get_option('cfkef_enable_atomic_form', true)){

            if ( ! Elementor_Utils::has_pro() ) {
                return;
            }
    
            $experiments = Elementor_Plugin::$instance->experiments;
            if ( ! $experiments || ! $experiments->is_feature_active( 'e_pro_atomic_form' ) ) {
                return;
            }
    
            if ( ! $elements_manager->get_element_types( 'e-form' ) ) {
                return;
            }
    
            require_once CFL_PLUGIN_PATH . 'widgets/atomic-form/atomic-form.php';
    
            $elements_manager->unregister_element_type( 'e-form' );
            $elements_manager->register_element_type( new Atomic_Form() );
        }

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

        wp_register_script(
            'cfl-atomic-form-mask-init',
            CFL_PLUGIN_URL . 'assets/atomic-form/js/atomic-form-mask-init.js',
            array( 'jquery', 'elementor-frontend', 'fme-custom-mask-script' ),
            $this->version,
            true
        );

        if ( ! wp_style_is( 'fme-frontend-css', 'registered' ) ) {
            wp_register_style( 'fme-frontend-css', CFL_PLUGIN_URL . 'assets/css/inputmask/mask-frontend.css', array(), $this->version, 'all' );
        }

        if ( ! wp_style_is( 'atomic-form-mask-style', 'registered' ) ) {
            wp_register_style( 'atomic-form-mask-style', CFL_PLUGIN_URL . 'assets/atomic-form/css/atomic-form-mask-style.min.css', array(), $this->version, 'all' );
        }

        if (! wp_script_is('fme-custom-mask-script', 'enqueued') && ! wp_script_is('fme-custom-mask-script', 'done')) {
            wp_enqueue_script( 'fme-custom-mask-script' );
        }

        if (! wp_script_is('cfl-atomic-form-mask-init', 'enqueued') && ! wp_script_is('cfl-atomic-form-mask-init', 'done')) {
            wp_enqueue_script( 'cfl-atomic-form-mask-init' );
        }
        if (! wp_style_is('fme-frontend-css', 'enqueued') && ! wp_style_is('fme-frontend-css', 'done')) {
            wp_enqueue_style( 'fme-frontend-css' );
        }
        if (! wp_style_is('atomic-form-mask-style', 'enqueued') && ! wp_style_is('atomic-form-mask-style', 'done')) {
            wp_enqueue_style( 'atomic-form-mask-style' );
        }
    }

    private function ensure_atomic_form_country_code_assets_registered() {

        $this->error_map =[
            __("The phone number you entered is not valid. Please check the format and try again.", "extensions-for-elementor-form"),
            __("The country code you entered is not recognized. Please ensure it is correct and try again.", "extensions-for-elementor-form"),
            __("The phone number you entered is too short. Please enter a complete phone number, including the country code.", "extensions-for-elementor-form"),
            __("The phone number you entered is too long. Please ensure it is in the correct format and try again.", "extensions-for-elementor-form"),
            __("The phone number you entered is not valid. Please check the format and try again.", "extensions-for-elementor-form")
        ];

        wp_register_script('frontend-country-handle-js', CFL_PLUGIN_URL . 'assets/atomic-form/js/frontend-country-handle.js', array('jquery'), $this->version, true);
        wp_enqueue_script('frontend-country-handle-js');

        wp_register_script('cfl-country-code-library-script', CFL_PLUGIN_URL . 'assets/addons/intl-tel-input/js/intlTelInput.js', array(), CFL_VERSION, true);
        wp_register_style('cfl-country-code-library-style', CFL_PLUGIN_URL . 'assets/addons/intl-tel-input/css/intlTelInput.min.css', array(), CFL_VERSION, 'all');
        wp_register_style('cfl-atomic-form-country-code-style', CFL_PLUGIN_URL . 'assets/atomic-form/css/atomic-form-country-code-style.min.css', array(), CFL_VERSION, 'all');

        wp_localize_script(
			'frontend-country-handle-js',
			'CCFEFCustomData',
			array(
				'pluginDir' => CFL_PLUGIN_URL,
				'errorMap'  => $this->error_map, 
			)	
		);

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

    /**
     * Follow redirect_url from atomic form action results (e.g. WhatsApp), which core JS does not handle.
     */
    public function register_atomic_form_whatsapp_redirect_script() {
        if ( ! Elementor_Utils::has_pro() ) {
            return;
        }

        $experiments = Elementor_Plugin::$instance->experiments;
        if ( ! $experiments || ! $experiments->is_feature_active( 'e_pro_atomic_form' ) ) {
            return;
        }

        wp_register_script(
            'cfl-atomic-form-whatsapp-action-redirect',
            CFL_PLUGIN_URL . 'assets/atomic-form/js/atomic-form-whatsapp-action-redirect.js',
            array( 'elementor-frontend' ),
            $this->version,
            true
        );

        if (! wp_script_is('cfl-atomic-form-whatsapp-action-redirect', 'enqueued') && ! wp_script_is('cfl-atomic-form-whatsapp-action-redirect', 'done')) {
            wp_enqueue_script( 'cfl-atomic-form-whatsapp-action-redirect' );
        }
        
    }

    /**
     * Atomic-form-only conditional logic handler.
     */
    public function register_atomic_form_condition_script() {
        if ( ! Elementor_Utils::has_pro() ) {
            return;
        }

        $experiments = Elementor_Plugin::$instance->experiments;
        if ( ! $experiments || ! $experiments->is_feature_active( 'e_pro_atomic_form' ) ) {
            return;
        }

        wp_register_script(
            'cfl-atomic-form-condition',
            CFL_PLUGIN_URL . 'assets/atomic-form/js/atomic-form-condition.js',
            array( 'jquery', 'elementor-frontend' ),
            $this->version,
            true
        );

        if (! wp_script_is('cfl-atomic-form-condition', 'enqueued') && ! wp_script_is('cfl-atomic-form-condition', 'done')) {
            wp_enqueue_script( 'cfl-atomic-form-condition' );
        }

        wp_register_style('cfl-atomic-form-conditional-style', CFL_PLUGIN_URL . 'assets/atomic-form/css/atomic-form-conditional.min.css', array(), CFL_VERSION, 'all');
        if (! wp_style_is('cfl-atomic-form-conditional-style', 'enqueued') && ! wp_style_is('cfl-atomic-form-conditional-style', 'done')) {
            wp_enqueue_style('cfl-atomic-form-conditional-style');
        }
    }

    public function enqueue_frontend_scripts() {

        if($this->is_field_enabled('whatsapp_redirect')){
            
            $this->register_atomic_form_whatsapp_redirect_script();
        }

        if($this->is_field_enabled('conditional_logic')){

            $this->register_atomic_form_condition_script();
        }

        if($this->is_field_enabled('form_input_mask')){
            $this->ensure_fme_mask_assets_registered();
        }

        if($this->is_field_enabled('country_code')){
            $this->ensure_atomic_form_country_code_assets_registered();
        }
    }

    public function get_version() {
        return $this->version;
    }
}

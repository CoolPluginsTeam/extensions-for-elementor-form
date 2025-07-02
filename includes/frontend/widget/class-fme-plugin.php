<?php

namespace Cool_FormKit\Includes\Frontend\Widget;

use Cool_FormKit\Includes\Frontend\Widget\FME_Elementor_Forms_Mask;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Form Mask Elementor Class
 *
 * Class to initialize the plugin.
 *
 * @since 1.4
 */
final class FME_Plugin {
	/**
	 * Instance
	 *
	 * @since 1.4
	 *
	 * @access private
	 * @static
	 *
	 * @var FME_Plugin The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.4
	 *
	 * @access public
	 * @static
	 *
	 * @return FME_Plugin An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * Constructor
	 *
	 * Private method for prevent instance outsite the class.
	 *
	 * @since 1.4
	 *
	 * @access private
	 */
	private function __construct() {
		add_action('wp_enqueue_scripts', array($this,'my_enqueue_scripts'));
		add_action( 'elementor/preview/init', array( $this, 'editor_inline_JS'));
		add_action( 'init', array( $this, 'init' ), 10 );
		add_action('fme_after_mask_attribute_added',array($this,'add_frontend_assets_conditionally'),10,3);
	}

	public function add_frontend_assets_conditionally($field, $field_index, $form_widget){
		if ( ! empty( $field['fme_mask_control'] ) && $field['fme_mask_control'] !== 'mask' &&  $field['field_type'] === 'text'){
			$this->handle_dynamic_assests_loading(false);
		} 
	}

	public function my_enqueue_scripts(){
		wp_register_script( 'fme-custom-mask-script', CFL_PLUGIN_URL . 'assets/js/inputmask/custom-mask-script.js', array('jquery'), time(), true );

		wp_register_style( 'fme-frontend-css', CFL_PLUGIN_URL . 'assets/css/inputmask/mask-frontend.css', CFL_VERSION, true );

		wp_register_script( 'fme-new-input-mask', CFL_PLUGIN_URL . 'assets/js/inputmask/new-input-mask.js', array('elementor-frontend','jquery'), CFL_VERSION, true );

		$error_messages = [
			'mask-cnpj'   => __("Invalid CNPJ.", "cool-formkit"),
			'mask-cpf'    => __("Invalid CPF.", "cool-formkit"),
			'mask-cep'    => __("Invalid CEP (XXXXX-XXX).", "cool-formkit"),
			'mask-phus'   => __("Invalid number: (123) 456-7890", "cool-formkit"),
			'mask-ph8'    => __("Invalid number: 1234-5678", "cool-formkit"),
			'mask-ddd8'   => __("Invalid number: (DDD) 1234-5678", "cool-formkit"),
			'mask-ddd9'   => __("Invalid number: (DDD) 91234-5678", "cool-formkit"),
			'mask-dmy'    => __("Invalid date: dd/mm/yyyy", "cool-formkit"),
			'mask-mdy'    => __("Invalid date: mm/dd/yyyy", "cool-formkit"),
			'mask-hms'    => __("Invalid time: hh:mm:ss", "cool-formkit"),
			'mask-hm'     => __("Invalid time: hh:mm", "cool-formkit"),
			'mask-dmyhm'  => __("Invalid date: dd/mm/yyyy hh:mm", "cool-formkit"),
			'mask-mdyhm'  => __("Invalid date: mm/dd/yyyy hh:mm", "cool-formkit"),
			'mask-my'     => __("Invalid date: mm/yyyy", "cool-formkit"),
			'mask-ccs'    => __("Invalid credit card number.", "cool-formkit"),
			'mask-cch'    => __("Invalid credit card number.", "cool-formkit"),
			'mask-ccmy'   => __("Invalid date.", "cool-formkit"),
			'mask-ccmyy'  => __("Invalid date.", "cool-formkit"),
			'mask-ipv4'   => __("Invalid IPv4 address.", "cool-formkit")
		];

		wp_localize_script( 'fme-custom-mask-script', 'fmeData', array(
			'pluginUrl' => CFL_PLUGIN_URL, 
			'errorMessages' => $error_messages 
		) );
	}

	public function editor_inline_JS() {
		wp_enqueue_script( 'fme-editor-template-js', CFL_PLUGIN_URL . 'assets/js/inputmask/mask-editor-template.js', array(), CFL_VERSION, true );

		$this->handle_dynamic_assests_loading(true);
	}

	public function handle_dynamic_assests_loading($editor){		
		if($editor){
			wp_register_script( 'fme-custom-mask-script', CFL_PLUGIN_URL . 'assets/js/inputmask/custom-mask-script.js', array('jquery'), time(), true );
		}

		wp_enqueue_script( 'fme-custom-mask-script' );
		wp_enqueue_script( 'fme-new-input-mask' );
		wp_enqueue_style( 'fme-frontend-css' );
	}
	/**
	 * Initialize the plugin
	 *
	 * Load the plugin and all classes after Elementor and all plugins is loaded.
	 *
	 * @since 1.4
	 *
	 * @access public
	 */
	public function init() {
		require_once CFL_PLUGIN_PATH . 'includes/frontend/widget/class-elementor-mask-control.php';
		new FME_Elementor_Forms_Mask();
	}

	/**
	 * Enqueue JS
	 *
	 * Register and enqueue JS scripts.
	 *
	 * @since 1.4
	 *
	 * @access public
	 */
	public function enqueue_plugin_js() {
		do_action( 'fme_after_enqueue_scripts' );
	}

}

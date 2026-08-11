<?php

namespace Cool_FormKit\Widgets\HelloPlusAddons;

require_once CFL_PLUGIN_PATH . 'includes/fields/country-code-addon-trait.php';

use Cool_FormKit\Includes\Fields\Country_Code_Addon_Trait;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Country code field for Hello Plus forms.
 */
if ( ! class_exists( 'HelloPlus_COUNTRY_CODE_FIELD' ) ) {
	class HelloPlus_COUNTRY_CODE_FIELD {
		use Country_Code_Addon_Trait;

		/**
		 * @var HelloPlus_COUNTRY_CODE_FIELD|null
		 */
		private static $instance = null;

		/**
		 * @return HelloPlus_COUNTRY_CODE_FIELD
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		public function __construct() {
			$this->init_country_code_addon();
		}

		protected function get_validation_hook(): string {
			return 'hello_plus/forms/validation';
		}

		protected function get_render_field_hook(): string {
			return 'hello_plus/forms/render_field/ehp-tel';
		}

		protected function get_form_fields_section_hook(): string {
			return 'elementor/element/ehp-form/section_form_fields/before_section_end';
		}

		protected function get_tel_field_type(): string {
			return 'ehp-tel';
		}

		protected function get_library_style_handle(): string {
			return 'helloplus-country-code-library-style';
		}

		protected function get_style_handle(): string {
			return 'helloplus-country-code-style';
		}

		protected function get_style_src(): string {
			return CFL_PLUGIN_URL . 'assets/helloplus-addons/css/helloplus-country-code-style.css';
		}

		protected function get_library_script_handle(): string {
			return 'helloplus-country-code-library-script';
		}

	
		protected function get_main_script_handle(): string {
			return 'helloplus-country-code-script';
		}

		protected function get_main_script_src(): string {
			return CFL_PLUGIN_URL . 'assets/helloplus-addons/js/helloplus-country-code-script.js';
		}

		protected function get_editor_script_handle(): string {
			return 'helloplus-country-code-editor-script';
		}

		protected function get_editor_script_src(): string {
			return CFL_PLUGIN_URL . 'assets/helloplus-addons/js/helloplus-ccfef-editor.js';
		}

		protected function get_elementor_plugin() {
			if ( class_exists( '\HelloPlus\Includes\Utils' ) ) {
				return \HelloPlus\Includes\Utils::elementor();
			}
			return \Elementor\Plugin::instance();
		}
	}
}

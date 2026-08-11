<?php

namespace Cool_FormKit\Widgets\Addons;

require_once CFL_PLUGIN_PATH . 'includes/fields/country-code-addon-trait.php';

use Cool_FormKit\Includes\Utils;
use Cool_FormKit\Includes\Fields\Country_Code_Addon_Trait;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Country code field for Cool Form.
 */
if ( ! class_exists( 'CoolForm_COUNTRY_CODE_FIELD' ) ) {
	class CoolForm_COUNTRY_CODE_FIELD {
		use Country_Code_Addon_Trait;

		/**
		 * @var CoolForm_COUNTRY_CODE_FIELD|null
		 */
		private static $instance = null;

		/**
		 * @return CoolForm_COUNTRY_CODE_FIELD
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
			return 'cool_formkit/forms/validation';
		}

		protected function get_render_field_hook(): string {
			return 'cool_formkit/forms/render_field/tel';
		}

		protected function get_form_fields_section_hook(): string {
			return 'elementor/element/cool-form/section_form_fields/before_section_end';
		}

		protected function get_tel_field_type(): string {
			return 'tel';
		}

		protected function get_library_style_handle(): string {
			return 'coolform-country-code-library-style';
		}

		protected function get_style_handle(): string {
			return 'coolform-country-code-style';
		}

		protected function get_style_src(): string {
			return CFL_PLUGIN_URL . 'assets/addons/css/coolform-country-code-style.css';
		}

		protected function get_library_script_handle(): string {
			return 'coolform-country-code-library-script';
		}

	
		protected function get_main_script_handle(): string {
			return 'coolform-country-code-script';
		}

		protected function get_main_script_src(): string {
			return CFL_PLUGIN_URL . 'assets/addons/js/coolform-country-code-script.js';
		}

		protected function get_editor_script_handle(): string {
			return 'coolform-country-code-editor-script';
		}

		protected function get_editor_script_src(): string {
			return CFL_PLUGIN_URL . 'assets/addons/js/coolform-ccfef-editor.js';
		}

		protected function get_elementor_plugin() {
			return Utils::elementor();
		}
	}
}

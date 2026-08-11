<?php

namespace Cool_FormKit\Includes\Frontend\Widget;

require_once CFL_PLUGIN_PATH . 'includes/fields/country-code-addon-trait.php';

use Cool_FormKit\Includes\Fields\Country_Code_Addon_Trait;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Country code field for Elementor Pro forms.
 */
if ( ! class_exists( 'CFL_COUNTRY_CODE_FIELD' ) ) {
	class CFL_COUNTRY_CODE_FIELD {
		use Country_Code_Addon_Trait;

		/**
		 * @var CFL_COUNTRY_CODE_FIELD|null
		 */
		private static $instance = null;

		/**
		 * @return CFL_COUNTRY_CODE_FIELD
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
			return 'elementor_pro/forms/validation';
		}

		protected function get_render_field_hook(): string {
			return 'elementor_pro/forms/render_field/tel';
		}

		protected function get_form_fields_section_hook(): string {
			return 'elementor/element/form/section_form_fields/before_section_end';
		}

		protected function get_tel_field_type(): string {
			return 'tel';
		}

		protected function get_library_style_handle(): string {
			return 'ccfef-country-code-library-style';
		}

		protected function get_style_handle(): string {
			return 'ccfef-country-code-style';
		}

		protected function get_style_src(): string {
			return CFL_PLUGIN_URL . 'assets/css/country-code-style.min.css';
		}

		protected function get_library_script_handle(): string {
			return 'ccfef-country-code-library-script';
		}

	
		protected function get_main_script_handle(): string {
			return 'ccfef-country-code-script';
		}

		protected function get_main_script_src(): string {
			return CFL_PLUGIN_URL . 'assets/js/country-code-script.js';
		}

		protected function get_main_script_version(): string {
			return function_exists( 'cfl_asset_version' )
				? cfl_asset_version( 'assets/js/country-code-script.js' )
				: CFL_VERSION;
		}

		protected function get_shared_script_version(): string {
			return function_exists( 'cfl_asset_version' )
				? cfl_asset_version( 'assets/js/shared/country-code-script.js' )
				: CFL_VERSION;
		}

		protected function get_editor_script_handle(): string {
			return 'ccfef-country-code-editor-script';
		}

		protected function get_editor_script_src(): string {
			return CFL_PLUGIN_URL . 'assets/js/ccfef-editor.js';
		}

		protected function get_elementor_plugin() {
			return \Elementor\Plugin::instance();
		}

		protected function should_include_review_notice(): bool {
			return true;
		}
	}
}

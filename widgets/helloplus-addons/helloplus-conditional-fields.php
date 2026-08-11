<?php
namespace Cool_FormKit\Widgets\HelloPlusAddons;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once CFL_PLUGIN_PATH . 'includes/fields/conditional-fields-trait.php';

use Cool_FormKit\Includes\Fields\Conditional_Fields_Logic_Trait;

/**
 * Conditional fields for Hello Plus forms.
 */
if ( ! class_exists( 'HelloPlus_Create_Conditional_Fields' ) ) {
	class HelloPlus_Create_Conditional_Fields {
		use Conditional_Fields_Logic_Trait;

		public function __construct() {
			$this->init_conditional_fields_logic();
		}

		protected function get_validation_hook(): string {
			return 'hello_plus/forms/validation';
		}

		protected function get_pre_render_hook(): string {
			return 'elementor/frontend/widget/before_render';
		}

		protected function get_form_widget_name(): string {
			return 'ehp-form';
		}

		protected function get_tel_field_type(): string {
			return 'ehp-tel';
		}

		protected function get_frontend_script_handle(): string {
			return 'helloplus_cfefp_logic';
		}

		protected function get_frontend_script_src(): string {
			return CFL_PLUGIN_URL . 'assets/helloplus-addons/js/helloplus-logic_frontend.js';
		}

		protected function get_editor_script_handle(): string {
			return 'helloplus_cfefp_logic_editor';
		}

		protected function get_editor_script_src(): string {
			return CFL_PLUGIN_URL . 'assets/addons/js/coolform-editor.js';
		}

		protected function get_editor_style_handle(): string {
			return 'helloplus_cfefp_logic_editor';
		}

		protected function get_editor_style_src(): string {
			return CFL_PLUGIN_URL . 'assets/addons/css/editor.css';
		}

		protected function get_elementor_plugin() {
			if ( class_exists( '\HelloPlus\Includes\Utils' ) ) {
				return \HelloPlus\Includes\Utils::elementor();
			}
			return \Elementor\Plugin::instance();
		}

		protected function get_pre_render_widget_name_guard(): string {
			return 'ehp-form';
		}
	}
}

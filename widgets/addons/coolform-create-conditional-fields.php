<?php
namespace Cool_FormKit\Widgets\Addons;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once CFL_PLUGIN_PATH . 'includes/fields/conditional-fields-trait.php';

use Cool_FormKit\Includes\Utils;
use Cool_FormKit\Includes\Fields\Conditional_Fields_Logic_Trait;

/**
 * Conditional fields for Cool Form.
 */
if ( ! class_exists( 'CoolForm_Create_Conditional_Fields' ) ) {
	class CoolForm_Create_Conditional_Fields {
		use Conditional_Fields_Logic_Trait;

		public function __construct() {
			$this->init_conditional_fields_logic();
		}

		protected function get_validation_hook(): string {
			return 'cool_formkit/forms/validation';
		}

		protected function get_pre_render_hook(): string {
			return 'cool_formkit/forms/pre_render';
		}

		protected function get_form_widget_name(): string {
			return 'cool-form';
		}

		protected function get_tel_field_type(): string {
			return 'tel';
		}

		protected function get_frontend_script_handle(): string {
			return 'coolform_cfefp_logic';
		}

		protected function get_frontend_script_src(): string {
			return CFL_PLUGIN_URL . 'assets/addons/js/coolform-logic_frontend.js';
		}

		protected function get_editor_script_handle(): string {
			return 'coolform_cfefp_logic_editor';
		}

		protected function get_editor_script_src(): string {
			return CFL_PLUGIN_URL . 'assets/addons/js/coolform-editor.js';
		}

		protected function get_editor_style_handle(): string {
			return 'coolform_cfefp_logic_editor';
		}

		protected function get_editor_style_src(): string {
			return CFL_PLUGIN_URL . 'assets/addons/css/editor.css';
		}

		protected function get_elementor_plugin() {
			return Utils::elementor();
		}

		protected function should_localize_my_script_vars(): bool {
			return true;
		}

		protected function should_localize_my_script_vars_elementor(): bool {
			return true;
		}

		protected function should_enqueue_editor_fontawesome(): bool {
			return true;
		}

		protected function logic_template_has_hidden_class(): bool {
			return true;
		}
	}
}

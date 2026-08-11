<?php
/**
 * Conditional fields for Elementor Pro forms.
 *
 * @package Cool_FormKit
 */

namespace Cool_FormKit\Includes\Frontend\Widget;

require_once CFL_PLUGIN_PATH . 'includes/fields/conditional-fields-trait.php';

use Cool_FormKit\Includes\Fields\Conditional_Fields_Logic_Trait;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'CFL_Create_Conditional_Fields' ) ) {
	class CFL_Create_Conditional_Fields {
		use Conditional_Fields_Logic_Trait;

		public function __construct() {
			$this->init_conditional_fields_logic();
		}

		protected function get_validation_hook(): string {
			return 'elementor_pro/forms/validation';
		}

		protected function get_pre_render_hook(): string {
			return 'elementor-pro/forms/pre_render';
		}

		protected function get_form_widget_name(): string {
			return 'form';
		}

		protected function get_tel_field_type(): string {
			return 'tel';
		}

		protected function get_frontend_script_handle(): string {
			return 'cfl_logic';
		}

		protected function get_frontend_script_src(): string {
			return CFL_PLUGIN_URL . 'assets/js/form_logic_frontend.js';
		}

		protected function get_editor_script_handle(): string {
			return 'cfl_logic_editor';
		}

		protected function get_editor_script_src(): string {
			return CFL_PLUGIN_URL . 'assets/addons/js/editor.js';
		}

		protected function get_editor_style_handle(): string {
			return 'cfl_logic_editor';
		}

		protected function get_editor_style_src(): string {
			return CFL_PLUGIN_URL . 'assets/addons/css/editor.min.css';
		}

		protected function get_elementor_plugin() {
			return \Elementor\Plugin::instance();
		}

		protected function should_include_review_notice(): bool {
			return true;
		}

		protected function should_localize_my_script_vars(): bool {
			return false;
		}

		protected function should_localize_my_script_vars_elementor(): bool {
			return true;
		}

		protected function should_early_enqueue_twenty_theme(): bool {
			return true;
		}

		protected function get_hidden_field_inline_css(): string {
			return '.cfef-hidden, .cfef-hidden-step-field {
				display: none !important;
			 }';
		}

		protected function logic_template_has_data_form_id(): bool {
			return false;
		}

		protected function pre_render_form_id_from_widget_arg(): bool {
			return true;
		}

		protected function should_prune_hidden_step_fields(): bool {
			return true;
		}
	}
}

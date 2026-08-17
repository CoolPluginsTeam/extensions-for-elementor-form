<?php
/**
 * Configurable conditional-fields addon (Cool Form, Hello Plus, Elementor Pro).
 *
 * @package Cool_FormKit
 */

namespace Cool_FormKit\Includes\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/conditional-fields-trait.php';
require_once __DIR__ . '/configurable-addon-trait.php';

class Conditional_Fields_Addon {
	use Conditional_Fields_Logic_Trait;
	use Configurable_Addon_Trait;

	/**
	 * @param array<string, mixed> $config
	 */
	public function __construct( array $config ) {
		$this->addon_config = $config;
		$this->init_conditional_fields_logic();
	}

	/**
	 * @return array<string, mixed>
	 */
	public static function coolform_config(): array {
		return array(
			'elementor'                         => 'coolform',
			'validation_hook'                   => 'cool_formkit/forms/validation',
			'pre_render_hook'                   => 'cool_formkit/forms/pre_render',
			'form_widget_name'                  => 'cool-form',
			'tel_field_type'                    => 'tel',
			'frontend_script_handle'            => 'coolform_cfefp_logic',
			'frontend_script_src'               => CFL_PLUGIN_URL . 'assets/addons/js/coolform-logic_frontend.js',
			'editor_script_handle'              => 'coolform_cfefp_logic_editor',
			'editor_script_src'                 => CFL_PLUGIN_URL . 'assets/addons/js/coolform-editor.js',
			'editor_style_handle'               => 'coolform_cfefp_logic_editor',
			'editor_style_src'                  => CFL_PLUGIN_URL . 'assets/addons/css/editor.min.css',
			'localize_my_script_vars'           => true,
			'localize_my_script_vars_elementor' => true,
			'enqueue_editor_fontawesome'        => true,
			'logic_template_has_hidden_class'   => true,
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	public static function helloplus_config(): array {
		return array(
			'elementor'                         => 'helloplus',
			'validation_hook'                   => 'hello_plus/forms/validation',
			'pre_render_hook'                   => 'elementor/frontend/widget/before_render',
			'form_widget_name'                  => 'ehp-form',
			'tel_field_type'                    => 'ehp-tel',
			'frontend_script_handle'            => 'helloplus_cfefp_logic',
			'frontend_script_src'               => CFL_PLUGIN_URL . 'assets/helloplus-addons/js/helloplus-logic_frontend.js',
			'editor_script_handle'              => 'helloplus_cfefp_logic_editor',
			'editor_script_src'                 => CFL_PLUGIN_URL . 'assets/helloplus-addons/js/helloplus-editor.js',
			'editor_style_handle'               => 'helloplus_cfefp_logic_editor',
			'editor_style_src'                  => CFL_PLUGIN_URL . 'assets/addons/css/editor.min.css',
			'localize_my_script_vars'           => true,
			'localize_my_script_vars_elementor' => true,
			'pre_render_widget_guard'           => 'ehp-form',
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	public static function elementor_config(): array {
		return array(
			'elementor'                         => 'elementor',
			'validation_hook'                   => 'elementor_pro/forms/validation',
			'pre_render_hook'                   => 'elementor-pro/forms/pre_render',
			'form_widget_name'                  => 'form',
			'tel_field_type'                    => 'tel',
			'frontend_script_handle'            => 'cfl_logic',
			'frontend_script_src'               => CFL_PLUGIN_URL . 'assets/js/form_logic_frontend.js',
			'editor_script_handle'              => 'cfl_logic_editor',
			'editor_script_src'                 => CFL_PLUGIN_URL . 'assets/addons/js/editor.js',
			'editor_style_handle'               => 'cfl_logic_editor',
			'editor_style_src'                  => CFL_PLUGIN_URL . 'assets/addons/css/editor.min.css',
			'review_notice'                     => true,
			'localize_my_script_vars'           => false,
			'localize_my_script_vars_elementor' => true,
			'early_enqueue_twenty_theme'        => true,
			'hidden_field_inline_css'           => '.cfef-hidden, .cfef-hidden-step-field {
				display: none !important;
			 }',
			'logic_template_has_data_form_id'   => false,
			'pre_render_form_id_from_widget'    => true,
			'prune_hidden_step_fields'          => true,
		);
	}

	protected function get_validation_hook(): string {
		return (string) $this->cfg( 'validation_hook' );
	}

	protected function get_pre_render_hook(): string {
		return (string) $this->cfg( 'pre_render_hook' );
	}

	protected function get_form_widget_name(): string {
		return (string) $this->cfg( 'form_widget_name' );
	}

	protected function get_tel_field_type(): string {
		return (string) $this->cfg( 'tel_field_type', 'tel' );
	}

	protected function get_frontend_script_handle(): string {
		return (string) $this->cfg( 'frontend_script_handle' );
	}

	protected function get_frontend_script_src(): string {
		return (string) $this->cfg( 'frontend_script_src' );
	}

	protected function get_editor_script_handle(): string {
		return (string) $this->cfg( 'editor_script_handle' );
	}

	protected function get_editor_script_src(): string {
		return (string) $this->cfg( 'editor_script_src' );
	}

	protected function get_editor_style_handle(): string {
		return (string) $this->cfg( 'editor_style_handle' );
	}

	protected function get_editor_style_src(): string {
		return (string) $this->cfg( 'editor_style_src' );
	}

	protected function get_elementor_plugin() {
		return $this->resolve_elementor_plugin();
	}

	protected function get_pre_render_widget_name_guard(): string {
		return (string) $this->cfg( 'pre_render_widget_guard', '' );
	}

	protected function should_include_review_notice(): bool {
		return (bool) $this->cfg( 'review_notice', false );
	}

	protected function should_localize_my_script_vars(): bool {
		return (bool) $this->cfg( 'localize_my_script_vars', false );
	}

	protected function should_localize_my_script_vars_elementor(): bool {
		return (bool) $this->cfg( 'localize_my_script_vars_elementor', false );
	}

	protected function should_enqueue_editor_fontawesome(): bool {
		return (bool) $this->cfg( 'enqueue_editor_fontawesome', false );
	}

	protected function should_early_enqueue_twenty_theme(): bool {
		return (bool) $this->cfg( 'early_enqueue_twenty_theme', false );
	}

	protected function get_hidden_field_inline_css(): string {
		return (string) $this->cfg( 'hidden_field_inline_css', '' );
	}

	protected function logic_template_has_hidden_class(): bool {
		return (bool) $this->cfg( 'logic_template_has_hidden_class', false );
	}

	protected function logic_template_has_data_form_id(): bool {
		return (bool) $this->cfg( 'logic_template_has_data_form_id', true );
	}

	protected function pre_render_form_id_from_widget_arg(): bool {
		return (bool) $this->cfg( 'pre_render_form_id_from_widget', false );
	}

	protected function should_prune_hidden_step_fields(): bool {
		return (bool) $this->cfg( 'prune_hidden_step_fields', false );
	}
}

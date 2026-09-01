<?php
/**
 * Configurable country-code addon (Cool Form, Hello Plus, Elementor Pro).
 *
 * @package Cool_FormKit
 */

namespace Cool_FormKit\Includes\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/country-code-addon-trait.php';
require_once __DIR__ . '/configurable-addon-trait.php';
require_once __DIR__ . '/singleton-trait.php';

class Country_Code_Addon {
	use Country_Code_Addon_Trait;
	use Configurable_Addon_Trait;

	/**
	 * @param array<string, mixed> $config
	 */
	public function __construct( array $config ) {
		$this->addon_config = $config;
		$this->init_country_code_addon();
	}

	/**
	 * @return array<string, mixed>
	 */
	public static function coolform_config(): array {
		return array(
			'elementor'              => 'coolform',
			'validation_hook'        => 'cool_formkit/forms/validation',
			'render_field_hook'      => 'cool_formkit/forms/render_field/tel',
			'form_fields_section'    => 'elementor/element/cool-form/section_form_fields/before_section_end',
			'tel_field_type'         => 'tel',
			'library_style_handle'   => 'coolform-country-code-library-style',
			'style_handle'           => 'coolform-country-code-style',
			'style_src'              => CFL_PLUGIN_URL . 'assets/addons/css/coolform-country-code-style.css',
			'library_script_handle'  => 'cfl-country-code-library-script',
			'main_script_handle'     => 'coolform-country-code-script',
			'main_script_src'        => CFL_PLUGIN_URL . 'assets/addons/js/coolform-country-code-script.js',
			'editor_script_handle'   => 'coolform-country-code-editor-script',
			'editor_script_src'      => CFL_PLUGIN_URL . 'assets/addons/js/coolform-ccfef-editor.js',
			'review_notice'          => false,
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	public static function helloplus_config(): array {
		return array(
			'elementor'              => 'helloplus',
			'validation_hook'        => 'hello_plus/forms/validation',
			'render_field_hook'      => 'hello_plus/forms/render_field/ehp-tel',
			'form_fields_section'    => 'elementor/element/ehp-form/section_form_fields/before_section_end',
			'tel_field_type'         => 'ehp-tel',
			'library_style_handle'   => 'helloplus-country-code-library-style',
			'style_handle'           => 'helloplus-country-code-style',
			'style_src'              => CFL_PLUGIN_URL . 'assets/helloplus-addons/css/helloplus-country-code-style.css',
			'library_script_handle'  => 'cfl-country-code-library-script',
			'main_script_handle'     => 'helloplus-country-code-script',
			'main_script_src'        => CFL_PLUGIN_URL . 'assets/helloplus-addons/js/helloplus-country-code-script.js',
			'editor_script_handle'   => 'helloplus-country-code-editor-script',
			'editor_script_src'      => CFL_PLUGIN_URL . 'assets/helloplus-addons/js/helloplus-ccfef-editor.js',
			'review_notice'          => false,
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	public static function elementor_config(): array {
		return array(
			'elementor'              => 'elementor',
			'validation_hook'        => 'elementor_pro/forms/validation',
			'render_field_hook'      => 'elementor_pro/forms/render_field/tel',
			'form_fields_section'    => 'elementor/element/form/section_form_fields/before_section_end',
			'tel_field_type'         => 'tel',
			'library_style_handle'   => 'ccfef-country-code-library-style',
			'style_handle'           => 'ccfef-country-code-style',
			'style_src'              => CFL_PLUGIN_URL . 'assets/css/country-code-style.min.css',
			'library_script_handle'  => 'cfl-country-code-library-script',
			'main_script_handle'     => 'ccfef-country-code-script',
			'main_script_src'        => CFL_PLUGIN_URL . 'assets/js/country-code-script.js',
			'editor_script_handle'   => 'ccfef-country-code-editor-script',
			'editor_script_src'      => CFL_PLUGIN_URL . 'assets/js/ccfef-country-editor.js',
			'review_notice'          => true,
		);
	}

	protected function get_validation_hook(): string {
		return (string) $this->cfg( 'validation_hook' );
	}

	protected function get_render_field_hook(): string {
		return (string) $this->cfg( 'render_field_hook' );
	}

	protected function get_form_fields_section_hook(): string {
		return (string) $this->cfg( 'form_fields_section' );
	}

	protected function get_tel_field_type(): string {
		return (string) $this->cfg( 'tel_field_type', 'tel' );
	}

	protected function get_library_style_handle(): string {
		return (string) $this->cfg( 'library_style_handle' );
	}

	protected function get_style_handle(): string {
		return (string) $this->cfg( 'style_handle' );
	}

	protected function get_style_src(): string {
		return (string) $this->cfg( 'style_src' );
	}

	protected function get_library_script_handle(): string {
		return (string) $this->cfg( 'library_script_handle' );
	}

	protected function get_main_script_handle(): string {
		return (string) $this->cfg( 'main_script_handle' );
	}

	protected function get_main_script_src(): string {
		return (string) $this->cfg( 'main_script_src' );
	}

	protected function get_editor_script_handle(): string {
		return (string) $this->cfg( 'editor_script_handle' );
	}

	protected function get_editor_script_src(): string {
		return (string) $this->cfg( 'editor_script_src' );
	}

	protected function get_elementor_plugin() {
		return $this->resolve_elementor_plugin();
	}

	protected function should_include_review_notice(): bool {
		return (bool) $this->cfg( 'review_notice', false );
	}
}

<?php
/**
 * Configurable FME bootstrap (Cool Form, Hello Plus, Elementor Pro).
 *
 * @package Cool_FormKit
 */

namespace Cool_FormKit\Includes\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/fme-plugin-trait.php';
require_once __DIR__ . '/configurable-addon-trait.php';
require_once __DIR__ . '/mask-control-addon.php';
require_once __DIR__ . '/singleton-trait.php';

class FME_Plugin_Addon {
	use FME_Plugin_Trait;
	use Configurable_Addon_Trait;

	/**
	 * @param array<string, mixed> $config
	 */
	public function __construct( array $config ) {
		$this->addon_config = $config;
		$this->init_fme_plugin();
	}

	/**
	 * @return array<string, mixed>
	 */
	public static function coolform_config(): array {
		return array(
			'custom_mask_script_handle'     => 'coolform-fme-custom-mask-script',
			'frontend_style_handle'         => 'coolform-fme-frontend-css',
			'input_mask_script_handle'      => 'coolform-fme-new-input-mask',
			'input_mask_script_src'         => CFL_PLUGIN_URL . 'assets/addons/js/inputmask/coolform-new-input-mask.js',
			'editor_template_script_handle' => 'coolform-fme-editor-template-js',
			'editor_template_script_src'    => CFL_PLUGIN_URL . 'assets/addons/js/inputmask/coolform-mask-editor-template.js',
			'after_mask_attribute'          => 'coolform_fme_after_mask_attribute_added',
			'mask'                          => Mask_Control_Addon::coolform_config(),
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	public static function helloplus_config(): array {
		return array(
			'custom_mask_script_handle'     => 'helloplus-fme-custom-mask-script',
			'frontend_style_handle'         => 'helloplus-fme-frontend-css',
			'input_mask_script_handle'      => 'helloplus-fme-new-input-mask',
			'input_mask_script_src'         => CFL_PLUGIN_URL . 'assets/helloplus-addons/js/helloplus-new-input-mask.js',
			'editor_template_script_handle' => 'helloplus-fme-editor-template-js',
			'editor_template_script_src'    => CFL_PLUGIN_URL . 'assets/helloplus-addons/js/helloplus-mask-editor-template.js',
			'after_mask_attribute'          => 'helloplus_after_mask_attribute_added',
			'mask'                          => Mask_Control_Addon::helloplus_config(),
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	public static function elementor_config(): array {
		return array(
			'custom_mask_script_handle'     => 'fme-custom-mask-script',
			'frontend_style_handle'         => 'fme-frontend-css',
			'input_mask_script_handle'      => 'fme-new-input-mask',
			'input_mask_script_src'         => CFL_PLUGIN_URL . 'assets/js/inputmask/new-input-mask.js',
			'editor_template_script_handle' => 'fme-editor-template-js',
			'editor_template_script_src'    => CFL_PLUGIN_URL . 'assets/js/inputmask/mask-editor-template.js',
			'after_mask_attribute'          => 'fme_after_mask_attribute_added',
			'mask'                          => Mask_Control_Addon::elementor_config(),
		);
	}

	protected function get_custom_mask_script_handle(): string {
		return (string) $this->cfg( 'custom_mask_script_handle' );
	}

	protected function get_frontend_style_handle(): string {
		return (string) $this->cfg( 'frontend_style_handle' );
	}

	protected function get_input_mask_script_handle(): string {
		return (string) $this->cfg( 'input_mask_script_handle' );
	}

	protected function get_input_mask_script_src(): string {
		return (string) $this->cfg( 'input_mask_script_src' );
	}

	protected function get_editor_template_script_handle(): string {
		return (string) $this->cfg( 'editor_template_script_handle' );
	}

	protected function get_editor_template_script_src(): string {
		return (string) $this->cfg( 'editor_template_script_src' );
	}

	protected function get_after_mask_attribute_action(): string {
		return (string) $this->cfg( 'after_mask_attribute' );
	}

	/**
	 * Instantiate the shared mask control with this platform's config.
	 *
	 * @return void
	 */
	public function init() {
		$mask_config = $this->cfg( 'mask', array() );
		if ( ! is_array( $mask_config ) || empty( $mask_config ) ) {
			return;
		}
		new Mask_Control_Addon( $mask_config );
	}
}

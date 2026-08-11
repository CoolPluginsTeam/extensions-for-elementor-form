<?php

namespace Cool_FormKit\Widgets\Addons;

require_once CFL_PLUGIN_PATH . 'includes/fields/fme-plugin-trait.php';

use Cool_FormKit\Includes\Fields\FME_Plugin_Trait;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Form Mask Elementor bootstrap for Cool Form.
 */
final class CoolForm_FME_Plugin {
	use FME_Plugin_Trait;

	/**
	 * @var CoolForm_FME_Plugin|null
	 */
	private static $_instance = null;

	/**
	 * @return CoolForm_FME_Plugin
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	private function __construct() {
		$this->init_fme_plugin();
	}

	protected function get_custom_mask_script_handle(): string {
		return 'coolform-fme-custom-mask-script';
	}

	protected function get_frontend_style_handle(): string {
		return 'coolform-fme-frontend-css';
	}

	protected function get_input_mask_script_handle(): string {
		return 'coolform-fme-new-input-mask';
	}

	protected function get_input_mask_script_src(): string {
		return CFL_PLUGIN_URL . 'assets/addons/js/inputmask/coolform-new-input-mask.js';
	}

	protected function get_editor_template_script_handle(): string {
		return 'coolform-fme-editor-template-js';
	}

	protected function get_editor_template_script_src(): string {
		return CFL_PLUGIN_URL . 'assets/addons/js/inputmask/coolform-mask-editor-template.js';
	}

	protected function get_after_mask_attribute_action(): string {
		return 'coolform_fme_after_mask_attribute_added';
	}

	protected function get_mask_control_file(): string {
		return CFL_PLUGIN_PATH . 'widgets/addons/coolform-elementor-mask-control.php';
	}

	protected function get_mask_control_class(): string {
		return FME_Elementor_Forms_Mask::class;
	}
}

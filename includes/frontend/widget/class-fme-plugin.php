<?php

namespace Cool_FormKit\Includes\Frontend\Widget;

require_once CFL_PLUGIN_PATH . 'includes/fields/fme-plugin-trait.php';

use Cool_FormKit\Includes\Fields\FME_Plugin_Trait;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Form Mask Elementor bootstrap for Elementor Pro.
 */
final class FME_Plugin {
	use FME_Plugin_Trait;

	/**
	 * @var FME_Plugin|null
	 */
	private static $_instance = null;

	/**
	 * @return FME_Plugin
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
		return 'fme-custom-mask-script';
	}

	protected function get_frontend_style_handle(): string {
		return 'fme-frontend-css';
	}

	protected function get_input_mask_script_handle(): string {
		return 'fme-new-input-mask';
	}

	protected function get_input_mask_script_src(): string {
		return CFL_PLUGIN_URL . 'assets/js/inputmask/new-input-mask.js';
	}

	protected function get_editor_template_script_handle(): string {
		return 'fme-editor-template-js';
	}

	protected function get_editor_template_script_src(): string {
		return CFL_PLUGIN_URL . 'assets/js/inputmask/mask-editor-template.js';
	}

	protected function get_after_mask_attribute_action(): string {
		return 'fme_after_mask_attribute_added';
	}

	protected function get_mask_control_file(): string {
		return CFL_PLUGIN_PATH . 'includes/frontend/widget/class-elementor-mask-control.php';
	}

	protected function get_mask_control_class(): string {
		return FME_Elementor_Forms_Mask::class;
	}
}

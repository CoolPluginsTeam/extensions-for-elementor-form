<?php

namespace Cool_FormKit\Includes\Frontend\Widget;

require_once CFL_PLUGIN_PATH . 'includes/fields/fme-plugin-addon.php';

use Cool_FormKit\Includes\Fields\FME_Plugin_Addon;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Form Mask Elementor bootstrap for Elementor Pro.
 */
final class FME_Plugin extends FME_Plugin_Addon {

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

	public function __construct() {
		parent::__construct( FME_Plugin_Addon::elementor_config() );
	}
}

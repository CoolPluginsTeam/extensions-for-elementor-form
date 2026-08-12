<?php

namespace Cool_FormKit\Widgets\HelloPlusAddons;

require_once CFL_PLUGIN_PATH . 'includes/fields/fme-plugin-addon.php';

use Cool_FormKit\Includes\Fields\FME_Plugin_Addon;
use Cool_FormKit\Includes\Fields\Singleton_Trait;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Form Mask Elementor bootstrap for Hello Plus.
 */
final class HelloPlus_FME_Plugin extends FME_Plugin_Addon {
	use Singleton_Trait;

	public function __construct() {
		parent::__construct( FME_Plugin_Addon::helloplus_config() );
	}
}

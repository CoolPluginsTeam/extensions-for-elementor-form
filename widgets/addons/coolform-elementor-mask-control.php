<?php

namespace Cool_FormKit\Widgets\Addons;

require_once CFL_PLUGIN_PATH . 'includes/fields/mask-control-addon.php';

use Cool_FormKit\Includes\Fields\Mask_Control_Addon;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cool Form Mask Control addon.
 */
class FME_Elementor_Forms_Mask extends Mask_Control_Addon {
	public function __construct() {
		parent::__construct( Mask_Control_Addon::coolform_config() );
	}
}

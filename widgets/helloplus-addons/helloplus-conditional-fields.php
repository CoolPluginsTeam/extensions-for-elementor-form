<?php
namespace Cool_FormKit\Widgets\HelloPlusAddons;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once CFL_PLUGIN_PATH . 'includes/fields/conditional-fields-addon.php';

use Cool_FormKit\Includes\Fields\Conditional_Fields_Addon;

/**
 * Conditional fields for Hello Plus forms.
 */
if ( ! class_exists( 'HelloPlus_Create_Conditional_Fields' ) ) {
	class HelloPlus_Create_Conditional_Fields extends Conditional_Fields_Addon {
		public function __construct() {
			parent::__construct( Conditional_Fields_Addon::helloplus_config() );
		}
	}
}

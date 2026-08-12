<?php
namespace Cool_FormKit\Widgets\Addons;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once CFL_PLUGIN_PATH . 'includes/fields/conditional-fields-addon.php';

use Cool_FormKit\Includes\Fields\Conditional_Fields_Addon;

/**
 * Conditional fields for Cool Form.
 */
if ( ! class_exists( 'CoolForm_Create_Conditional_Fields' ) ) {
	class CoolForm_Create_Conditional_Fields extends Conditional_Fields_Addon {
		public function __construct() {
			parent::__construct( Conditional_Fields_Addon::coolform_config() );
		}
	}
}

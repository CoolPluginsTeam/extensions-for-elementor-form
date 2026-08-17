<?php
/**
 * Conditional fields for Elementor Pro forms.
 *
 * @package Cool_FormKit
 */

namespace Cool_FormKit\Includes\Frontend\Widget;

require_once CFL_PLUGIN_PATH . 'includes/fields/conditional-fields-addon.php';

use Cool_FormKit\Includes\Fields\Conditional_Fields_Addon;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'CFL_Create_Conditional_Fields' ) ) {
	class CFL_Create_Conditional_Fields extends Conditional_Fields_Addon {
		public function __construct() {
			parent::__construct( Conditional_Fields_Addon::elementor_config() );
		}
	}
}

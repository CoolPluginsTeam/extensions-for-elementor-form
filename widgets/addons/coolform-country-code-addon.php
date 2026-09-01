<?php

namespace Cool_FormKit\Widgets\Addons;

require_once CFL_PLUGIN_PATH . 'includes/fields/country-code-addon.php';

use Cool_FormKit\Includes\Fields\Country_Code_Addon;
use Cool_FormKit\Includes\Fields\Singleton_Trait;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Country code field for Cool Form.
 */
if ( ! class_exists( 'CoolForm_COUNTRY_CODE_FIELD' ) ) {
	class CoolForm_COUNTRY_CODE_FIELD extends Country_Code_Addon {
		use Singleton_Trait;

		public function __construct() {
			parent::__construct( Country_Code_Addon::coolform_config() );
		}
	}
}

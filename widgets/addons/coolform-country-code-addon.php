<?php

namespace Cool_FormKit\Widgets\Addons;

require_once CFL_PLUGIN_PATH . 'includes/fields/country-code-addon.php';

use Cool_FormKit\Includes\Fields\Country_Code_Addon;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Country code field for Cool Form.
 */
if ( ! class_exists( 'CoolForm_COUNTRY_CODE_FIELD' ) ) {
	class CoolForm_COUNTRY_CODE_FIELD extends Country_Code_Addon {

		/**
		 * @var CoolForm_COUNTRY_CODE_FIELD|null
		 */
		private static $instance = null;

		/**
		 * @return CoolForm_COUNTRY_CODE_FIELD
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		public function __construct() {
			parent::__construct( Country_Code_Addon::coolform_config() );
		}
	}
}

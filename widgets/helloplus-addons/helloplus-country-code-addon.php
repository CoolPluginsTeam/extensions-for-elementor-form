<?php

namespace Cool_FormKit\Widgets\HelloPlusAddons;

require_once CFL_PLUGIN_PATH . 'includes/fields/country-code-addon.php';

use Cool_FormKit\Includes\Fields\Country_Code_Addon;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Country code field for Hello Plus forms.
 */
if ( ! class_exists( 'HelloPlus_COUNTRY_CODE_FIELD' ) ) {
	class HelloPlus_COUNTRY_CODE_FIELD extends Country_Code_Addon {

		/**
		 * @var HelloPlus_COUNTRY_CODE_FIELD|null
		 */
		private static $instance = null;

		/**
		 * @return HelloPlus_COUNTRY_CODE_FIELD
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		public function __construct() {
			parent::__construct( Country_Code_Addon::helloplus_config() );
		}
	}
}

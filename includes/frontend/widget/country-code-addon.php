<?php

namespace Cool_FormKit\Includes\Frontend\Widget;

require_once CFL_PLUGIN_PATH . 'includes/fields/country-code-addon.php';

use Cool_FormKit\Includes\Fields\Country_Code_Addon;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Country code field for Elementor Pro forms.
 */
if ( ! class_exists( 'CFL_COUNTRY_CODE_FIELD' ) ) {
	class CFL_COUNTRY_CODE_FIELD extends Country_Code_Addon {

		/**
		 * @var CFL_COUNTRY_CODE_FIELD|null
		 */
		private static $instance = null;

		/**
		 * @return CFL_COUNTRY_CODE_FIELD
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		public function __construct() {
			parent::__construct( Country_Code_Addon::elementor_config() );
		}
	}
}

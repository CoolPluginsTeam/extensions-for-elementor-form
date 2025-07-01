<?php
namespace Cool_FormKit\Widgets\Addons;
/**
 * Files use for creating repeater button for conditional fields
 *
 * @package cfef
 *
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use ElementorPro\Plugin;
	/**
	 * Class for creating repeater field
	 */
class CoolForm_Control_Repeater_Field extends \Elementor\Control_Repeater {
	/**
	 * Function for creating repeater field
	 */
	public function get_type() {
		return 'field_condition_repeater';
	}
	/**
	 * Function for creating default valuer of repeater field
	 */
	public function get_default_value() {
		return array();
	}
	/**
	 * Add js file so that repeater default functionality not affect
	 */
	public function enqueue() {
		wp_enqueue_script(
			'field_condition_repeater',
			CFL_PLUGIN_URL . 'assets/addons/js/logic_repeater_controler.min.js',
			array(),
			CFL_VERSION,
			true
		);
	}
}

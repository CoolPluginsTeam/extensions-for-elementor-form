<?php
namespace Cool_FormKit\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Shared utility helpers used by Cool Form / frontend modules.
 */
class Utils {

	public static function elementor(): \Elementor\Plugin {
		return \Elementor\Plugin::$instance;
	}

	public static function _unstable_get_super_global_value( $super_global, $key ) {
		if ( ! isset( $super_global[ $key ] ) ) {
			return null;
		}

		return wp_kses_post_deep( wp_unslash( $super_global[ $key ] ) );
	}

	/**
	 * Insert controls into the form fields repeater before field_value (Advanced tab).
	 *
	 * @param object $element      Elementor element.
	 * @param array  $control_data Existing form_fields control data.
	 * @param array  $pattern_field Controls to insert (from a Repeater).
	 * @return mixed
	 */
	public static function register_control_in_form_advanced_tab( $element, $control_data, $pattern_field ) {
		foreach ( $pattern_field as $key => $control ) {
			if ( '_id' === $key ) {
				continue;
			}

			$new_order = array();
			foreach ( $control_data['fields'] as $field_key => $field ) {
				if ( 'field_value' === $field['name'] ) {
					$new_order[ $key ] = $control;
				}
				$new_order[ $field_key ] = $field;
			}

			$control_data['fields'] = $new_order;
		}

		return $element->update_control( 'form_fields', $control_data );
	}

	public static function get_current_post_id(): int {
		if ( isset( self::elementor()->documents ) && self::elementor()->documents->get_current() ) {
			return self::elementor()->documents->get_current()->get_main_id();
		}

		return get_the_ID();
	}

	public static function get_client_ip(): string {
		$server_ip_keys = [
			'HTTP_CLIENT_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_X_CLUSTER_CLIENT_IP',
			'HTTP_FORWARDED_FOR',
			'HTTP_FORWARDED',
			'REMOTE_ADDR',
		];

		foreach ( $server_ip_keys as $key ) {
			$value = filter_input( INPUT_SERVER, $key, FILTER_VALIDATE_IP );

			if ( $value ) {
				return $value;
			}
		}

		return '127.0.0.1';
	}
}

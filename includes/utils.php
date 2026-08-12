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

	public static function has_pro(): bool {
		return defined( 'ELEMENTOR_PRO_VERSION' );
	}

	private static function sanitize_file_name( $file ) {
		$file['name'] = sanitize_file_name( $file['name'] );

		return $file;
	}

	private static function sanitize_multi_upload( $fields ) {
		return array_map( function( $field ) {
			return array_map( [ __CLASS__, 'sanitize_file_name' ], $field );
		}, $fields );
	}

	public static function _unstable_get_super_global_value( $super_global, $key ) {
		if ( ! isset( $super_global[ $key ] ) ) {
			return null;
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( $_FILES === $super_global ) {
			return isset( $super_global[ $key ]['name'] ) ?
				static::sanitize_file_name( $super_global[ $key ] ) :
				static::sanitize_multi_upload( $super_global[ $key ] );
		}

		return wp_kses_post_deep( wp_unslash( $super_global[ $key ] ) );
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

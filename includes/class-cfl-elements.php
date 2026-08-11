<?php
/**
 * Enabled form elements feature flags (cfkef_enabled_elements option).
 *
 * @package Cool_FormKit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Single source of truth for whether a Cool FormKit element is enabled.
 */
final class CFL_Elements {

	/**
	 * Sanitized enabled keys, or null before first read.
	 *
	 * @var string[]|null
	 */
	private static $enabled = null;

	/**
	 * Whether an element key is enabled in site settings.
	 *
	 * @param string $key Element option key.
	 * @return bool
	 */
	public static function is_enabled( $key ): bool {
		if ( null === self::$enabled ) {
			$raw = (array) get_option( 'cfkef_enabled_elements', array() );

			if ( empty( $raw ) ) {
				$raw = array(
					'conditional_logic',
					'country_code',
					'form_input_mask',
					'whatsapp_redirect',
				);
			}

			self::$enabled = array_map( 'sanitize_key', $raw );
		}

		return in_array( sanitize_key( $key ), self::$enabled, true );
	}

	/**
	 * Reset cache (e.g. after settings save in same request).
	 *
	 * @return void
	 */
	public static function flush_cache(): void {
		self::$enabled = null;
	}
}

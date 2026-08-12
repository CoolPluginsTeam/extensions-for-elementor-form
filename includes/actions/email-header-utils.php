<?php
/**
 * Shared email header utilities.
 *
 * @package Cool_FormKit
 */

namespace Cool_FormKit\Includes\Actions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Email_Header_Utils {

	/**
	 * Strip CR/LF and other control characters from a header value.
	 *
	 * @param mixed $value Raw header fragment.
	 * @return string
	 */
	public static function strip_header_breaks( $value ): string {
		if ( ! is_string( $value ) ) {
			return '';
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Return unique, valid email addresses from a comma-separated string.
	 *
	 * @param mixed $value Comma-separated email addresses.
	 * @return string[]
	 */
	public static function sanitize_email_list( $value ): array {
		$value = self::strip_header_breaks( $value );
		if ( '' === $value ) {
			return array();
		}

		$emails = array_filter(
			array_map( 'trim', explode( ',', $value ) ),
			'is_email'
		);

		return array_values( array_unique( $emails ) );
	}

	/**
	 * Return a single valid From/Reply-To address, or a fallback.
	 *
	 * @param mixed  $value    Candidate address.
	 * @param string $fallback Used when $value is not a valid email.
	 * @return string
	 */
	public static function sanitize_from_email( $value, $fallback = '' ): string {
		$value = self::strip_header_breaks( $value );
		if ( is_email( $value ) ) {
			return $value;
		}

		$fallback = self::strip_header_breaks( (string) $fallback );
		if ( is_email( $fallback ) ) {
			return $fallback;
		}

		$admin_email = (string) get_option( 'admin_email' );
		return is_email( $admin_email ) ? $admin_email : '';
	}

	/**
	 * Build a Cc header containing only valid email addresses.
	 *
	 * @param mixed $value Comma-separated email addresses.
	 * @return string
	 */
	public static function build_cc_header( $value ): string {
		$emails = self::sanitize_email_list( $value );
		if ( empty( $emails ) ) {
			return '';
		}

		return 'Cc: ' . implode( ',', $emails ) . "\r\n";
	}
}

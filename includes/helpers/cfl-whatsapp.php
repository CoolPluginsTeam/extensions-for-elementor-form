<?php
/**
 * Shared WhatsApp redirect helpers.
 *
 * @package Cool_FormKit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Replace %break% tokens in WhatsApp message text.
 *
 * @param string $message Message text.
 * @return string
 */
function cfl_replace_whatsapp_break_token( $message ) {
	return str_replace( '%break%', "\r\n", (string) $message );
}

/**
 * Replace [all-fields] and [field id="…"] shortcodes in a WhatsApp message.
 *
 * @param string $message        Message template.
 * @param array  $form_data      Map of field_id => value.
 * @param array  $field_metadata Optional map of field_id => [ 'label' => … ].
 * @return string
 */
function cfl_replace_whatsapp_message_shortcodes( $message, array $form_data, array $field_metadata = array() ) {
	$message = (string) $message;

	if ( false !== strpos( $message, '[all-fields]' ) ) {
		$all_fields_text = '';

		foreach ( $form_data as $key => $value ) {
			$meta            = $field_metadata[ $key ] ?? array();
			$formatted_key   = ! empty( $meta['label'] ) ? $meta['label'] : ucwords( str_replace( array( '_', '-' ), ' ', (string) $key ) );
			$formatted_value = is_array( $value ) ? implode( ', ', $value ) : (string) $value;
			$all_fields_text .= sprintf( "%s: %s\n", $formatted_key, $formatted_value );
		}

		$message = str_replace( '[all-fields]', trim( $all_fields_text ), $message );
	}

	$message = preg_replace_callback(
		'/\[field[^\]]*id=["\']([^"\']+)["\'][^\]]*\]/',
		static function ( $matches ) use ( $form_data ) {
			$field_id = $matches[1];
			if ( ! isset( $form_data[ $field_id ] ) ) {
				return '';
			}
			$value = $form_data[ $field_id ];
			return is_array( $value ) ? implode( ', ', $value ) : (string) $value;
		},
		$message
	);

	return is_string( $message ) ? $message : '';
}

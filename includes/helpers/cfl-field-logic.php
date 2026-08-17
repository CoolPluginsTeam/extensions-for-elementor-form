<?php
/**
 * Shared conditional field operator evaluation.
 *
 * @package Cool_FormKit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Compare a field value against a conditional logic rule.
 *
 * @param mixed  $value_id Field value(s) to compare.
 * @param string $operator Comparison operator.
 * @param string $value    Expected comparison value.
 * @return bool
 */
function cfl_check_field_logic( $value_id, $operator, $value ) {
	$value_id = esc_html( (string) $value_id );
	$value    = trim( (string) $value );
	$value    = esc_html( $value );

	$values      = array_map( 'trim', explode( ',', $value_id ) );
	$match_found = in_array( $value, $values, true );

	switch ( $operator ) {
		case '==':
			return $match_found && '' !== $value_id;
		case '!=':
			return ! $match_found && '' !== $value_id;
		case 'e':
			return empty( $value_id );
		case '!e':
			return ! empty( $value_id );
		case 'c':
			return false !== strpos( $value_id, $value );
		case '!c':
			return ! empty( $value_id ) && false === strpos( $value_id, $value );
		case '^':
			return '' !== $value && 0 === strpos( $value_id, $value );
		case '~':
			return '' !== $value && str_ends_with( $value_id, $value );
		case '>':
			return (int) $value_id > (int) $value;
		case '<':
			return (int) $value_id < (int) $value;
		case '>=':
			return (int) $value_id >= (int) $value;
		case '<=':
			return (int) $value_id <= (int) $value;
		default:
			return false;
	}
}

/**
 * Evaluate whether a field should be shown given conditional rules.
 *
 * @param array  $checks       Boolean results per rule.
 * @param string $fire_action  'All' or 'Any'.
 * @param string $display_mode 'show'|'hide'|'enable'|'disable'.
 * @return bool True when the field should remain visible/active.
 */
function cfl_evaluate_conditional_display( array $checks, $fire_action = 'All', $display_mode = 'show' ) {
	if ( empty( $checks ) ) {
		return true;
	}

	$logic_result = ( 'All' === $fire_action )
		? ! in_array( false, $checks, true )
		: in_array( true, $checks, true );

	if ( 'show' === $display_mode || 'enable' === $display_mode ) {
		return $logic_result;
	}

	if ( 'hide' === $display_mode || 'disable' === $display_mode ) {
		return ! $logic_result;
	}

	return $logic_result;
}

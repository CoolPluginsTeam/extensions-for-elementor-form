<?php
/**
 * Shared country-code (intl-tel-input) helpers.
 *
 * @package Cool_FormKit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<int, string>
 */
function cfl_get_country_code_error_map() {
	return array(
		__( 'The phone number you entered is not valid. Please check the format and try again.', 'extensions-for-elementor-form' ),
		__( 'The country code you entered is not recognized. Please ensure it is correct and try again.', 'extensions-for-elementor-form' ),
		__( 'The phone number you entered is too short. Please enter a complete phone number, including the country code.', 'extensions-for-elementor-form' ),
		__( 'The phone number you entered is too long. Please ensure it is in the correct format and try again.', 'extensions-for-elementor-form' ),
		__( 'The phone number you entered is not valid. Please check the format and try again.', 'extensions-for-elementor-form' ),
	);
}

/**
 * Register intlTelInput library once under a canonical handle.
 *
 * @param string|null $version Script version.
 * @return string Handle name.
 */
function cfl_register_intl_tel_input_script( $version = null ) {
	$handle = 'cfl-country-code-library-script';

	if ( ! wp_script_is( $handle, 'registered' ) ) {
		$intl_ver = function_exists( 'cfl_asset_version' )
			? cfl_asset_version( 'assets/js/intlTelInput.min.js' )
			: ( $version ?: CFL_VERSION );

		wp_register_script(
			$handle,
			CFL_PLUGIN_URL . 'assets/js/intlTelInput.min.js',
			array(),
			$intl_ver,
			true
		);
	}

	return $handle;
}

/**
 * Register shared country-code frontend script with full dependencies.
 *
 * @param string|null $script_version Shared script version.
 * @return void
 */
function cfl_register_shared_country_code_script( $script_version = null ) {
	if ( wp_script_is( 'cfkef-shared-country-code-script', 'registered' ) ) {
		return;
	}

	$library_handle = cfl_register_intl_tel_input_script( $script_version );
	$deps             = array( 'jquery', $library_handle );

	if ( wp_script_is( 'elementor-frontend', 'registered' ) ) {
		$deps[] = 'elementor-frontend';
	}

	$shared_ver = function_exists( 'cfl_asset_version' )
		? cfl_asset_version( 'assets/js/shared/country-code-script.js' )
		: ( $script_version ?: CFL_VERSION );

	wp_register_script(
		'cfkef-shared-country-code-script',
		CFL_PLUGIN_URL . 'assets/js/shared/country-code-script.js',
		$deps,
		$shared_ver,
		true
	);
}

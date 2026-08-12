<?php
/**
 * Global asset version helper (no namespace).
 *
 * @package Cool_FormKit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'cfl_asset_version' ) ) {
	/**
	 * Cache-busting version for a plugin-relative asset path.
	 *
	 * @param string $relative_path Path under the plugin root (e.g. assets/js/foo.js).
	 * @return string
	 */
	function cfl_asset_version( $relative_path ) {
		$base     = defined( 'CFL_PLUGIN_PATH' ) ? CFL_PLUGIN_PATH : ( dirname( __DIR__, 2 ) . '/' );
		$absolute = $base . ltrim( (string) $relative_path, '/\\' );
		if ( is_readable( $absolute ) ) {
			return (string) filemtime( $absolute );
		}
		return defined( 'CFL_VERSION' ) ? (string) CFL_VERSION : '1.0.0';
	}
}

if ( ! function_exists( 'cfl_register_review_dismiss_script' ) ) {
	/**
	 * Shared review-notice dismiss helper used by editor scripts.
	 *
	 * @return void
	 */
	function cfl_register_review_dismiss_script() {
		if ( wp_script_is( 'cfkef-review-dismiss', 'registered' ) ) {
			return;
		}

		wp_register_script(
			'cfkef-review-dismiss',
			CFL_PLUGIN_URL . 'assets/js/shared/review-dismiss.js',
			array( 'jquery' ),
			function_exists( 'cfl_asset_version' )
				? cfl_asset_version( 'assets/js/shared/review-dismiss.js' )
				: CFL_VERSION,
			true
		);
	}
}

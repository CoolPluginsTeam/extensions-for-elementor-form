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

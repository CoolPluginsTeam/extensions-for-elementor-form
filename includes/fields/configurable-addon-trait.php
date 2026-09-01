<?php
/**
 * Config bag + Elementor resolver for platform addon wrappers.
 *
 * @package Cool_FormKit
 */

namespace Cool_FormKit\Includes\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait Configurable_Addon_Trait {

	/**
	 * @var array<string, mixed>
	 */
	protected $addon_config = array();

	/**
	 * @param string $key
	 * @param mixed  $default
	 * @return mixed
	 */
	protected function cfg( $key, $default = '' ) {
		return array_key_exists( $key, $this->addon_config ) ? $this->addon_config[ $key ] : $default;
	}

	/**
	 * @return object
	 */
	protected function resolve_elementor_plugin() {
		$which = $this->cfg( 'elementor', 'elementor' );

		if ( 'coolform' === $which && class_exists( '\Cool_FormKit\Includes\Utils' ) ) {
			return \Cool_FormKit\Includes\Utils::elementor();
		}

		if ( 'helloplus' === $which && class_exists( '\HelloPlus\Includes\Utils' ) ) {
			return \HelloPlus\Includes\Utils::elementor();
		}

		return \Elementor\Plugin::instance();
	}

	/**
	 * Cache-busting version for a plugin URL under CFL_PLUGIN_URL.
	 *
	 * @param string $src Absolute asset URL.
	 * @return string
	 */
	protected function version_from_src( $src ): string {
		if ( function_exists( 'cfl_asset_version' ) && defined( 'CFL_PLUGIN_URL' ) && 0 === strpos( $src, CFL_PLUGIN_URL ) ) {
			return cfl_asset_version( substr( $src, strlen( CFL_PLUGIN_URL ) ) );
		}

		return defined( 'CFL_VERSION' ) ? CFL_VERSION : '1.0.0';
	}
}

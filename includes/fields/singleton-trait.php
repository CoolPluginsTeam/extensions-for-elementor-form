<?php
/**
 * Late-static-binding singleton for thin addon wrappers.
 *
 * @package Cool_FormKit
 */

namespace Cool_FormKit\Includes\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait Singleton_Trait {

	/**
	 * @var static|null
	 */
	private static $instance = null;

	/**
	 * @return static
	 */
	public static function get_instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Alias used by FME wrappers.
	 *
	 * @return static
	 */
	public static function instance() {
		return static::get_instance();
	}
}

<?php
/**
 * Singleton scaffolding for edition-specific addon loaders.
 *
 * @package Cool_FormKit
 */

namespace Cool_FormKit\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class Base_Addons_Loader {

	/**
	 * @var array<class-string, static>
	 */
	private static $instances = [];

	final public static function get_instance() {
		$class = static::class;

		if ( ! isset( self::$instances[ $class ] ) ) {
			self::$instances[ $class ] = new static();
		}

		return self::$instances[ $class ];
	}

	final protected function __construct() {
		$this->init();
	}

	/**
	 * Register hooks and load edition-specific addons.
	 */
	abstract protected function init(): void;
}

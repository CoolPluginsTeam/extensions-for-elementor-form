<?php

namespace Cool_FormKit\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Module Base.
 *
 * An abstract class providing the properties and methods needed to
 * manage and handle modules in inheriting classes.
 *
 * @abstract
 * @package Cool_FormKit
 * @subpackage Cool_FormKitModules
 */
abstract class Module_Base {

	/**
	 * Module instance.
	 *
	 * Holds the module instance.
	 * @access protected
	 *
	 * @var Module_Base[]
	 */
	protected static array $instances = [];

	/**
	 * Get module name.
	 *
	 * Retrieve the module name.
	 * @access public
	 * @abstract
	 *
	 * @return string Module name.
	 */
	abstract public static function get_name(): string;

	/**
	 * Singleton Instance.
	 *
	 * Ensures only one instance of the module class is loaded or can be loaded.
	 * @access public
	 * @static
	 *
	 * @return Module_Base An instance of the class.
	 */
	public static function instance(): Module_Base {
		$class_name = static::class_name();

		if ( empty( static::$instances[ $class_name ] ) ) {
			static::$instances[ $class_name ] = new static(); // @codeCoverageIgnore
		}

		return static::$instances[ $class_name ];
	}

	/**
	 * is_active
	 *
	 * @access public
	 * @static
	 *
	 * @return bool
	 */
	public static function is_active(): bool {
		/**
		 * allow enabling/disabling the module on run-time
		 *
		 * @since 1.0.0
		 *
		 * @param bool $is_active the filters value
		 */
		return apply_filters( 'cool-formkit/modules/' . static::get_name() . '/is-active', true );
	}

	/**
	 * Class name.
	 *
	 * Retrieve the name of the class.
	 * @access public
	 * @static
	 */
	public static function class_name(): string {
		return get_called_class();
	}

	/**
	 * Retrieve the namespace of the class
	 *
	 * @static
	 * @access public
	 *
	 * @return string
	 */
	public static function namespace_name(): string {
		$class_name = static::class_name();
		return substr( $class_name, 0, strrpos( $class_name, '\\' ) );
	}

	/**
	 * Registers the Module's widgets.
	 * Assumes namespace structure contains `\Widgets\`
	 *
	 * @access protected
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager
	 * @return void
	 */
	public function register_widgets( \Elementor\Widgets_Manager $widgets_manager ): void {
		$widget_ids = $this->get_widget_ids();
		$namespace = static::namespace_name();

		foreach ( $widget_ids as $widget_id ) {
			$class_name = $namespace . '\\Widgets\\' . $widget_id;
			$widgets_manager->register( new $class_name() );
		}
	}

	/**
	 * @access protected
	 *
	 * @return string[]
	 */
	protected function get_widget_ids(): array {
		return [];
	}

	/**
	 * @access protected
	 *
	 * @return void
	 */
	protected function register_hooks(): void {
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
	}

	/**
	 * Clone.
	 *
	 * Disable class cloning and throw an error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object. Therefore, we don't want the object to be cloned.
	 *
	 * @access private
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Something went wrong.', 'extensions-for-elementor-form' ), '0.0.1' ); // @codeCoverageIgnore
	}

	/**
	 * Wakeup.
	 *
	 * Disable un-serializing of the class.
	 * @access public
	 */
	public function __wakeup() {
		// Un-serializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Something went wrong.', 'extensions-for-elementor-form' ), '0.0.1' ); // @codeCoverageIgnore
	}

	/**
	 * class constructor
	 *
	 * @access protected
	 */
	protected function __construct() {
		$this->register_hooks();
	}
}

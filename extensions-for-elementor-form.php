<?php

/**
 * Plugin Name: Cool FormKit Lite
 * Plugin URI: https://coolplugins.net/
 * Description: Supercharge your Elementor forms with advanced functionality that streamlines workflows, enhances usability, and integrates seamlessly with tools like WhatsApp. Build dynamic forms directly in Elementor Free—no additional plugins required.
 * Author: Cool Plugins
 * Author URI: https://coolplugins.net/
 * Text Domain: extensions-for-elementor-form
 * Version: 2.4.0
 * Requires at least: 6.2
 * Requires PHP: 6.2
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires Plugins: elementor
 */

namespace Cool_FormKit;

use Cool_FormKit\Includes\Module_Base;
use Cool_FormKit\Includes\CFKEF_Loader;

if (! defined('ABSPATH')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

define('CFL_VERSION', '2.4.0');
define('PHP_MINIMUM_VERSION', '7.4');
define('WP_MINIMUM_VERSION', '5.5');
define('CFL_PLUGIN_MAIN_FILE', __FILE__);
define('CFL_PLUGIN_PATH', plugin_dir_path(CFL_PLUGIN_MAIN_FILE));
define('CFL_PLUGIN_URL', plugin_dir_url(CFL_PLUGIN_MAIN_FILE));
define('CFL_ASSETS_PATH', CFL_PLUGIN_PATH . 'build/');
define('CFL_ASSETS_URL', CFL_PLUGIN_URL . '/build/');
define('CFL_SCRIPTS_PATH', CFL_ASSETS_PATH . 'js/');
define('CFL_SCRIPTS_URL', CFL_ASSETS_URL . 'js/');
define('CFL_STYLE_PATH', CFL_ASSETS_PATH . 'css/');
define('CFL_STYLE_URL', CFL_ASSETS_URL . 'css/');
define('CFL_IMAGES_PATH', CFL_ASSETS_PATH . 'images/');
define('CFL_IMAGES_URL', CFL_ASSETS_URL . 'images/');
define('CFL__MIN_ELEMENTOR_VERSION', '3.26.4');



if (! function_exists('is_plugin_active')) {
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
}

class Cool_Formkit_Lite_For_Elementor_Form
{

	/**
	 * Plugin instance
	 */
	public static $instance = null;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		static $autoloader_registered = false;

		if ($this->check_requirements()) {
			if (! $autoloader_registered) {
				$autoloader_registered = spl_autoload_register([$this, 'autoload']);
			}

			$this->initialize_modules();

			$this->initialize_plugin();

			// add_action( 'activated_plugin', array( $this, 'EEF_plugin_redirection' ) );
			add_action('wp_enqueue_scripts', array($this, 'my_enqueue_scripts'));			


		}
	}


	public function my_enqueue_scripts()
	{
		wp_register_script('handle-date-pickr', CFL_PLUGIN_URL . 'assets/js/flatpickr/handle-date-pickr.js', array('elementor-frontend', 'jquery'), CFL_VERSION, true);

		wp_register_script('handle-time-pickr', CFL_PLUGIN_URL . 'assets/js/flatpickr/handle-time-pickr.js', array('elementor-frontend', 'jquery'), CFL_VERSION, true);
	}
	/**
	 * Singleton instance.
	 *
	 * @return self
	 */
	public static function instance()
	{
		if (null === self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}
	/**
	 * Add hooks for plugin initialization.
	 */
	public function initialize_plugin()
	{
		// Include main plugin class.
		require_once CFL_PLUGIN_PATH . '/includes/class-plugin.php';
		CFKEF_Loader::get_instance();
		
		require_once CFL_PLUGIN_PATH . '/includes/review-notice.php';
		new Review_notice();

		if (is_admin()) {
			require_once CFL_PLUGIN_PATH . 'admin/feedback/admin-feedback-form.php';
		}
		// add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'EEF_plugin_dashboard_link' ) );

	}

	public function EEF_plugin_redirection($plugin)
	{
		if (! is_plugin_active('elementor-pro/elementor-pro.php')) {
			return false;
		}
		if (is_plugin_active('cool-formkit-for-elementor-forms/cool-formkit-for-elementor-forms.php')) {
			return false;
		}
		if ($plugin == plugin_basename(__FILE__)) {
			exit(wp_redirect(admin_url('admin.php?page=cool-formkit')));
		}
	}
	/**
	 * Check PHP and WordPress version compatibility.
	 *
	 * @return bool
	 */
	public function check_requirements()
	{
		if (! version_compare(PHP_VERSION, PHP_MINIMUM_VERSION, '>=')) {
			add_action('admin_notices', [$this, 'admin_notice_php_version_fail']);
			return false;
		}

		if (! version_compare(get_bloginfo('version'), WP_MINIMUM_VERSION, '>=')) {
			add_action('admin_notices', [$this, 'admin_notice_wp_version_fail']);
			return false;
		}

		if (is_plugin_active('cool-formkit-for-elementor-forms/cool-formkit-for-elementor-forms.php')) {
			return false;
		}

		if (! is_plugin_active('elementor/elementor.php')) {
			add_action('admin_notices', array($this, 'admin_notice_missing_main_plugin'));
			return false;
		}


		return true;
	}

	public function EEF_plugin_dashboard_link($links)
	{
		$settings_link = '<a href="' . admin_url('admin.php?page=cool-formkit') . '">Settings</a>';
		array_unshift($links, $settings_link);
		return $links;
	}
	/**
	 * Show notice to enable elementor pro
	 */
	public function admin_notice_missing_main_plugin()
	{
		$message = sprintf(
			// translators: %1$s replace with Conditional Fields for Elementor Form & %2$s replace with Elementor Pro.
			esc_html__(
				'%1$s requires %2$s to be installed and activated.',
				'extensions-for-elementor-form'
			),
			esc_html__('Cool Formkit Lite', 'extensions-for-elementor-form'),
			esc_html__('Elementor', 'extensions-for-elementor-form'),
		);
		printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', esc_html($message));
		deactivate_plugins(plugin_basename(__FILE__));
	}

	/**
	 * Display admin notice for PHP version failure.
	 */
	public function admin_notice_php_version_fail()
	{
		$message = sprintf(
			esc_html__('%1$s requires PHP version %2$s or greater.', 'extensions-for-elementor-form'),
			'<strong>Cool Formkit Lite</strong>',
			PHP_MINIMUM_VERSION
		);

		echo wp_kses_post(sprintf('<div class="notice notice-error"><p>%1$s</p></div>', $message));
	}

	/**
	 * Display admin notice for WordPress version failure.
	 */
	public function admin_notice_wp_version_fail()
	{
		$message = sprintf(
			esc_html__('%1$s requires WordPress version %2$s or greater.', 'extensions-for-elementor-form'),
			'<strong>Cool Formkit Lite</strong>',
			WP_MINIMUM_VERSION
		);

		echo wp_kses_post(sprintf('<div class="notice notice-error"><p>%1$s</p></div>', $message));
	}

	private function initialize_modules()
	{
		$modules_list = [
			'Forms',  // Add additional module names as needed.
		];

		foreach ($modules_list as $module_name) {
			// Convert the module name to match the folder structure.
			// "Forms" becomes "Forms", but our autoloader expects lower-case folder names.
			// Therefore, if your file is in "modules/forms/module.php", adjust accordingly:
			$module_folder = strtolower($module_name);
			$class_name = __NAMESPACE__ . '\\Modules\\' . $module_name . '\\Module';

			if (class_exists($class_name) && $class_name::is_active()) {
				// Initialize the module by calling its singleton instance.
				$class_name::instance();
			} else {
				// Optional: Log or debug if the module class isn't found.
				error_log('Module class not found or not active: ' . $class_name);
			}
		}
	}

	public function autoload($class_name)
	{
		if (0 !== strpos($class_name, __NAMESPACE__)) {
			return;
		}

		$has_class_alias = isset($this->classes_aliases[$class_name]);

		// Backward Compatibility: Save old class name for set an alias after the new class is loaded
		if ($has_class_alias) {
			$class_alias_name = $this->classes_aliases[$class_name];
			$class_to_load = $class_alias_name;
		} else {
			$class_to_load = $class_name;
		}

		if (! class_exists($class_to_load)) {
			$filename = strtolower(
				preg_replace(
					['/^' . __NAMESPACE__ . '\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/'],
					['', '$1-$2', '-', DIRECTORY_SEPARATOR],
					$class_to_load
				)
			);


			$filename = trailingslashit(CFL_PLUGIN_PATH) . $filename . '.php';


			if (is_readable($filename)) {
				include $filename;
			}
		}

		if ($has_class_alias) {
			class_alias($class_alias_name, $class_name);
		}
	}

	public static function eef_activate()
	{
		update_option('eef-v', CFL_VERSION);
		update_option('eef-type', 'FREE');
		update_option('eef-installDate', gmdate('Y-m-d h:i:s'));
	}


	public static function eef_deactivate() {}
}

// Initialize the plugin.
Cool_Formkit_Lite_For_Elementor_Form::instance();

register_activation_hook(__FILE__, array('Cool_FormKit\Cool_Formkit_Lite_For_Elementor_Form', 'eef_activate'));
register_deactivation_hook(__FILE__, array('Cool_FormKit\Cool_Formkit_Lite_For_Elementor_Form', 'eef_deactivate'));

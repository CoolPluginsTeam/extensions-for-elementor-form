<?php

/**
 * Plugin Name: Cool FormKit Lite - Elementor Form Builder
 * Plugin URI: https://coolplugins.net/
 * Description: Build advanced forms in Elementor Free using Cool FormKit Lite. It also enhances Elementor Pro and Hello Plus Form Widget with conditional logic and advanced field options.
 * Author: Cool Plugins
 * Author URI: https://coolplugins.net/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=author_page&utm_content=plugins_list
 * Text Domain: extensions-for-elementor-form
 * Version: 2.5.2
 * Requires at least: 6.2
 * Requires PHP: 6.2
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires Plugins: elementor
 * Elementor tested up to: 3.31.2
 * Elementor Pro tested up to: 3.31.2
 */

namespace Cool_FormKit;

use Cool_FormKit\Includes\Module_Base;
use Cool_FormKit\Includes\CFL_Loader;

use Cool_FormKit\Widgets\CoolForm_Addons_Loader;
use Cool_FormKit\Widgets\HelloPlus_Addons_Loader;

if (! defined('ABSPATH')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

define('CFL_VERSION','2.5.2');
define('PHP_MINIMUM_VERSION','7.4');
define('WP_MINIMUM_VERSION','5.5');
define( 'CFL_PLUGIN_MAIN_FILE', __FILE__ );
define( 'CFL_PLUGIN_PATH', plugin_dir_path( CFL_PLUGIN_MAIN_FILE ) );
define( 'CFL_PLUGIN_URL', plugin_dir_url( CFL_PLUGIN_MAIN_FILE ) );
define( 'CFL_ASSETS_PATH', CFL_PLUGIN_PATH . 'build/' );
define( 'CFL_ASSETS_URL', CFL_PLUGIN_URL . '/build/' );
define( 'CFL_SCRIPTS_PATH', CFL_ASSETS_PATH . 'js/' );
define( 'CFL_SCRIPTS_URL', CFL_ASSETS_URL . 'js/' );
define( 'CFL_STYLE_PATH', CFL_ASSETS_PATH . 'css/' );
define( 'CFL_STYLE_URL', CFL_ASSETS_URL . 'css/' );
define( 'CFL_IMAGES_PATH', CFL_ASSETS_PATH . 'images/' );
define( 'CFL_IMAGES_URL', CFL_ASSETS_URL . 'images/' );
define( 'CFL__MIN_ELEMENTOR_VERSION', '3.26.4' );
define( 'CFL_FEEDBACK_URL', 'https://feedback.coolplugins.net/' );



if ( ! function_exists( 'is_plugin_active' ) ) {
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

			if(get_option('cfkef_enable_formkit_builder',true)){
				$this->initialize_modules();
			}

			$this->initialize_plugin();
			$this->deactivate_child_plugins();
			
			add_action( 'activated_plugin', array( $this, 'EEF_plugin_redirection' ) );
			add_action('wp_enqueue_scripts', array($this, 'my_enqueue_scripts'));	
			add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'add_global_editor_js' ) );		


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
		require_once CFL_PLUGIN_PATH . 'includes/class-cfl-loader.php';
		require_once CFL_PLUGIN_PATH . 'admin/feedback/cron/cfl-class-cron.php';

		CFL_Loader::get_instance();

		if(get_option('cfkef_enable_formkit_builder',true)){
			require_once CFL_PLUGIN_PATH . 'widgets/coolform-addons-loader.php';		
			CoolForm_Addons_Loader::get_instance();
		}

		if(get_option('cfkef_enable_hello_plus',true)){
			require_once CFL_PLUGIN_PATH . 'widgets/helloplus-addons-loader.php';		
			HelloPlus_Addons_Loader::get_instance();
		}
		
		if (is_admin()) {

			require_once CFL_PLUGIN_PATH . 'admin/review-notice.php';
			new Review_notice();

			require_once CFL_PLUGIN_PATH . 'admin/feedback/admin-feedback-form.php';
			if(!class_exists('CPFM_Feedback_Notice')){
				require_once CFL_PLUGIN_PATH . 'admin/feedback/cpfm-feedback-notice.php';
			}
			if (!get_option( 'CFL_initial_save_version' ) ) {
				add_option( 'CFL_initial_save_version', CFL_VERSION );
			}
		
			if(!get_option( 'cfl-install-date' ) ) {
				add_option( 'cfl-install-date', gmdate('Y-m-d h:i:s') );
        	}
		}

		add_filter( 'plugin_action_links_' . plugin_basename( CFL_PLUGIN_MAIN_FILE ), array( $this, 'EEF_plugin_dashboard_link' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( CFL_PLUGIN_MAIN_FILE ), array( $this, 
		'EEF_get_pro_link' ) );
		add_filter( 'plugin_row_meta', array( $this, 'cfkef_plugin_row_meta' ), 10, 2 );


	}

	public function cfkef_plugin_row_meta( $plugin_meta, $plugin_file ) {
		if ( plugin_basename( CFL_PLUGIN_MAIN_FILE ) === $plugin_file ) {
			$row_meta = array(
				'docs' => '<a href="' . esc_url('https://docs.coolplugins.net/plugin/cool-formkit-for-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=docs&utm_content=plugins_list') . '" aria-label="' . esc_attr(esc_html__('View CoolFomkit Documentation', 'cool-formkit')) . '" target="_blank">' . esc_html__('View Documentation', 'cool-formkit') . '</a>',
			);

			$plugin_meta = array_merge( $plugin_meta, $row_meta );
		}
		return $plugin_meta;
	}

	public function EEF_get_pro_link($links){
		$get_pro = '<a target="_blank" style="font-weight:bold;color:green;" href="https://coolformkit.com/pricing/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=get_pro&utm_content=plugins_list">Get Pro</a>';
		array_push($links, $get_pro);
		return $links;	
	}

	public function EEF_plugin_redirection($plugin)
	{
		if (is_plugin_active('cool-formkit-for-elementor-forms/cool-formkit-for-elementor-forms.php')) {
			return false;
		}
		if ($plugin == plugin_basename(CFL_PLUGIN_MAIN_FILE)) {
			exit(wp_safe_redirect(admin_url('admin.php?page=cool-formkit')));
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
			add_action('admin_notices', array($this, 'cool_formkit_active_notice'));
			return false;
		}

		if (! is_plugin_active('elementor/elementor.php')) {
			add_action('admin_notices', array($this, 'admin_notice_missing_main_plugin'));
			return false;
		}


		return true;
	}

	public function add_global_editor_js() {
		wp_enqueue_script( 'cfl-global-editor-script', CFL_PLUGIN_URL . 'assets/addons/js/global.js', array( 'jquery' ), CFL_VERSION, true );

	}

	public function EEF_plugin_dashboard_link($links)
	{
		$settings_link = '<a href="' . admin_url('admin.php?page=cool-formkit') . '">Settings</a>';
		array_unshift($links, $settings_link);
		return $links;
	}

	// Define a helper function for plugin deactivation and admin notice
	private function deactivate_plugin_with_notice( $plugin_path, $plugin_name ) {
		if ( file_exists( plugin_dir_path( __DIR__ ) . $plugin_path ) ) {
			if ( is_plugin_active( $plugin_path ) ) {
				deactivate_plugins( $plugin_path );
				add_action(
					'admin_notices',
					function() use ( $plugin_name ) {
						$this->admin_notice_deactivating_conditional_field_plugin( $plugin_name );
					}
				);
			}
		}
	}

	// Method to deactivate child plugins
	public function deactivate_child_plugins() {
		// Array of plugins to deactivate
		$plugins_to_deactivate = array(
			'conditional-fields-for-elementor-form/class-conditional-fields-for-elementor-form.php' => 'Conditional Fields for Elementor Form',
			'country-code-field-for-elementor-form/country-code-field-for-elementor-form.php' => 'Country Code Field For Elementor Form',
			'country-code-field-for-elementor-form/country-code-field-for-elementor-form-pro.php' => 'Country Code Field For Elementor Form Pro',
			'form-masks-for-elementor/form-masks-for-elementor.php' => 'Form Input Masks for Elementor Form',
			'mask-form-elementor/index.php' => 'Input Mask Elementor Form Fields',
		);

		// Loop through the plugins and deactivate them if necessary
		foreach ( $plugins_to_deactivate as $plugin_path => $plugin_name ) {
			$this->deactivate_plugin_with_notice( $plugin_path, $plugin_name );
		}
	}

	public function admin_notice_deactivating_conditional_field_plugin( $plugin_name ) {
		?>
		<div class="notice notice-error">
			<p><?php echo esc_html( "{$plugin_name} is deactivated because Cool FormKit is installed and activated." ); ?></p>
		</div>
		<?php
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
		deactivate_plugins(plugin_basename(CFL_PLUGIN_MAIN_FILE));
	}

	public function cool_formkit_active_notice()
	{
		$message = sprintf(
			esc_html__('Cool Formkit Lite for Elementor Free now you are using Elementor Pro so please deactivate Cool Formkit Lite and use Cool Formkit instead.', 'extensions-for-elementor-form'),
		);
		printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', esc_html($message));
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
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log('Module class not found or not active: ' . $class_name);
				}
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
		
		if (!get_option( 'CFL_initial_save_version' ) ) {
			add_option( 'CFL_initial_save_version', CFL_VERSION );
		}
		
		if(!get_option( 'cfl-install-date' ) ) {
			add_option( 'cfl-install-date', gmdate('Y-m-d h:i:s') );
        }

		$settings       = get_option('cfef_usage_share_data');
           
        if (!empty($settings) || $settings === 'on'){
			
			self::cfl_cron_job_init();
        }
		
	}
		
	public static function cfl_cron_job_init()
	{
		if (!wp_next_scheduled('cfl_extra_data_update')) {
			wp_schedule_event(time(), 'every_30_days', 'cfl_extra_data_update');
		}
	}

	public static function eef_deactivate() {

        if (wp_next_scheduled('cfl_extra_data_update')) {
            wp_clear_scheduled_hook('cfl_extra_data_update');
        }
	}
}

// Initialize the plugin.
Cool_Formkit_Lite_For_Elementor_Form::instance();

register_activation_hook(CFL_PLUGIN_MAIN_FILE, array('Cool_FormKit\Cool_Formkit_Lite_For_Elementor_Form', 'eef_activate'));
register_deactivation_hook(CFL_PLUGIN_MAIN_FILE, array('Cool_FormKit\Cool_Formkit_Lite_For_Elementor_Form', 'eef_deactivate'));

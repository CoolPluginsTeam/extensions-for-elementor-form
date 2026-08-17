<?php

/**
 * Plugin Name: Cool FormKit Lite - Elementor Form Builder
 * Plugin URI: https://coolplugins.net/
 * Description: Build advanced forms in Elementor Free using Cool FormKit Lite. It also enhances Elementor Pro and Hello Plus Form Widget with conditional logic and advanced field options.
 * Author: Cool Plugins
 * Author URI: https://coolplugins.net/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=author_page&utm_content=plugins_list
 * Text Domain: extensions-for-elementor-form
 * Version: 2.7.7
 * Requires at least: 6.2
 * Requires PHP: 7.4
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires Plugins: elementor
 * Elementor tested up to: 4.2.0
 * Elementor Pro tested up to: 4.2.0
 */

namespace Cool_FormKit;

use Cool_FormKit\Includes\CFL_Loader;

use Cool_FormKit\Widgets\CoolForm_Addons_Loader;
use Cool_FormKit\Widgets\HelloPlus_Addons_Loader;
use Cool_FormKit\Widgets\Atomic_Form_Addon_Loader;



if (! defined('ABSPATH')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

define('CFL_VERSION', '2.7.7');
define('PHP_MINIMUM_VERSION', '7.4');
define('WP_MINIMUM_VERSION', '6.2');
define('CFL_PLUGIN_MAIN_FILE', __FILE__);
define('CFL_PLUGIN_PATH', plugin_dir_path(CFL_PLUGIN_MAIN_FILE));
define('CFL_PLUGIN_URL', plugin_dir_url(CFL_PLUGIN_MAIN_FILE));
define('CFL_ASSETS_URL', CFL_PLUGIN_URL . 'build/');
define('CFL_SCRIPTS_URL', CFL_ASSETS_URL . 'js/');
define('CFL_STYLE_URL', CFL_ASSETS_URL . 'css/');
define('CFL_MIN_ELEMENTOR_VERSION', '3.26.4');
define('CFL_MIN_ELEMENTOR_ATOMIC_FORM_VERSION', '4.0');
define('CFL_FEEDBACK_URL', 'https://feedback.coolplugins.net/');

require_once CFL_PLUGIN_PATH . 'includes/helpers/cfl-asset-version.php';
require_once CFL_PLUGIN_PATH . 'includes/helpers/cfl-field-logic.php';
require_once CFL_PLUGIN_PATH . 'includes/helpers/cfl-country-code.php';
require_once CFL_PLUGIN_PATH . 'includes/helpers/cfl-whatsapp.php';
require_once CFL_PLUGIN_PATH . 'includes/class-cfl-elements.php';

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

			if (get_option('cfkef_enable_formkit_builder', true)) {
				$this->initialize_modules();
			}

			$this->initialize_plugin();
			$this->deactivate_child_plugins();

			add_action('activated_plugin', array($this, 'EEF_plugin_redirection'));
			add_action('wp_enqueue_scripts', array($this, 'my_enqueue_scripts'));
			add_action('elementor/editor/before_enqueue_scripts', array($this, 'add_global_editor_js'));
			add_action('elementor/preview/init', array($this, 'register_shared_editor_scripts'), 1);
			add_action('wp_head', array($this, 'stop_format_detection_in_safari'));
			add_action('elementor_pro/forms/actions/register', array($this, 'cfl_register_new_form_actions'));
			add_action( 'plugins_loaded',array($this,'formdb_elementor_marketing'));
			add_filter( 'elementor-pro/editor/v2/packages', array( $this, 'filter_elementor_pro_editor_packages' ) );
			add_action( 'elementor/editor/v2/scripts/enqueue', array( $this, 'enqueue_editor_templates_extended_stub' ), 20 );
		}
	}

	

	public function formdb_elementor_marketing() {

		if ( did_action( 'elementor/loaded' ) && class_exists( '\Elementor\Plugin' ) ) {

				require_once CFL_PLUGIN_PATH . '/admin/marketing/cfl-marketing-common.php';
			}
	}

	public function cfl_register_new_form_actions($form_actions_registrar)
	{

		if (!is_plugin_active('sb-elementor-contact-form-db/sb_elementor_contact_form_db.php') && !defined("formdb_elementor_marketing_editor")) {

			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
			define("formdb_elementor_marketing_editor", true);

			include_once(__DIR__ .  '/includes/class-form-to-sheet.php');
			$form_actions_registrar->register(new \Sheet_Action());
		}
	}

	public function stop_format_detection_in_safari()
	{
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		$ua = isset( $_SERVER['HTTP_USER_AGENT'] )
			? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) )
			: '';
		if ( '' === $ua ) {
			return;
		}
		$is_safari = strpos($ua, 'Safari') !== false
			&& strpos($ua, 'Mobile') !== false        // ensures mobile Safari
			&& (strpos($ua, 'iPhone') !== false
				|| strpos($ua, 'iPad') !== false
				|| strpos($ua, 'iPod') !== false)
			&& !preg_match('/Chrome|CriOS|Chromium|OPR|Edg/i', $ua);

		if ($is_safari) {

			echo '<meta name="format-detection" content="telephone=no">' . "\n";
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

		if (get_option('cfkef_enable_formkit_builder', true)) {
			require_once CFL_PLUGIN_PATH . 'widgets/base-addons-loader.php';
			require_once CFL_PLUGIN_PATH . 'widgets/coolform-addons-loader.php';
			CoolForm_Addons_Loader::get_instance();
		}

		if (get_option('cfkef_enable_hello_plus', true)) {
			require_once CFL_PLUGIN_PATH . 'widgets/base-addons-loader.php';
			require_once CFL_PLUGIN_PATH . 'widgets/helloplus-addons-loader.php';
			HelloPlus_Addons_Loader::get_instance();
		}

		if (get_option('cfkef_enable_atomic_form', true)) {	
			if ( is_plugin_active( 'elementor-pro/elementor-pro.php' ) || is_plugin_active( 'pro-elements/pro-elements.php' ) ) {
				// After `elementor/init`, core services (e.g. experiments) are initialized; on `elementor/loaded` they are often still null.
				if ( did_action( 'elementor/init' ) ) {
					$this->load_atomic_form_addon();
				} else {
					add_action( 'elementor/init', array( $this, 'load_atomic_form_addon' ), 20 );
				}
			}
		}

		if (is_admin()) {

			require_once CFL_PLUGIN_PATH . 'admin/review-notice.php';
			new Review_notice();

			require_once CFL_PLUGIN_PATH . 'admin/feedback/admin-feedback-form.php';
			if (!class_exists('CPFM_Feedback_Notice')) {
				require_once CFL_PLUGIN_PATH . 'admin/feedback/cpfm-feedback-notice.php';
			}
			if (!get_option('CFL_initial_save_version')) {
				add_option('CFL_initial_save_version', CFL_VERSION);
			}

			if (!get_option('cfl-install-date')) {
				add_option('cfl-install-date', gmdate('Y-m-d h:i:s'));
			}
		}

		add_filter('plugin_action_links_' . plugin_basename(CFL_PLUGIN_MAIN_FILE), array($this, 'EEF_plugin_dashboard_link'));
		add_filter('plugin_action_links_' . plugin_basename(CFL_PLUGIN_MAIN_FILE), array(
			$this,
			'EEF_get_pro_link'
		));
		add_filter('plugin_row_meta', array($this, 'cfkef_plugin_row_meta'), 10, 2);
	}

	public function load_atomic_form_addon() {
		if ( ! is_plugin_active( 'elementor-pro/elementor-pro.php' ) && ! is_plugin_active( 'pro-elements/pro-elements.php' ) ) {
			return;
		}

		if ( ! did_action( 'elementor/init' ) || ! class_exists( '\Elementor\Plugin' ) ) {
			return;
		}

		if ( ! self::is_elementor_atomic_form_supported() ) {
			if ( is_admin() && get_option( 'cfkef_enable_atomic_form', true ) ) {
				add_action( 'admin_notices', array( $this, 'admin_notice_elementor_atomic_form_version' ) );
			}
			return;
		}

		$elementor = \Elementor\Plugin::$instance;
		if ( ! $elementor ) {
			return;
		}

		$experiments = isset( $elementor->experiments ) ? $elementor->experiments : null;
		if ( ! self::are_elementor_atomic_form_experiments_active( $experiments ) ) {
			return;
		}

		require_once CFL_PLUGIN_PATH . 'widgets/atomic-form-addon-loader.php';
		Atomic_Form_Addon_Loader::get_instance();
	}

	/**
	 * Atomic Form extensions require Elementor 4.0+ (matches Elementor Pro atomic-form module).
	 */
	public static function is_elementor_atomic_form_supported(): bool {
		return defined( 'ELEMENTOR_VERSION' )
			&& version_compare( ELEMENTOR_VERSION, CFL_MIN_ELEMENTOR_ATOMIC_FORM_VERSION, '>=' );
	}

	/**
	 * @param mixed $experiments \Elementor\Core\Experiments\Manager|null.
	 */
	private static function are_elementor_atomic_form_experiments_active( $experiments ): bool {
		if ( ! self::is_elementor_atomic_form_supported() ) {
			return false;
		}

		if ( ! $experiments || ! is_object( $experiments ) || ! method_exists( $experiments, 'is_feature_active' ) ) {
			return false;
		}

		return $experiments->is_feature_active( 'e_atomic_elements' )
			&& $experiments->is_feature_active( 'e_pro_atomic_form' );
	}

	public function admin_notice_elementor_atomic_form_version() {
		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		$file_path = 'elementor/elementor.php';
		$upgrade_link = wp_nonce_url(
			self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $file_path,
			'upgrade-plugin_' . $file_path
		);

		printf(
			'<div class="notice notice-warning is-dismissible"><p><strong>%1$s</strong> %2$s <a href="%3$s">%4$s</a></p></div>',
			esc_html__( 'Cool FormKit:', 'extensions-for-elementor-form' ),
			esc_html__(
				'Atomic Form extensions require Elementor 4.0 or newer. Update Elementor to use this feature, or disable Atomic Form in Cool FormKit settings.',
				'extensions-for-elementor-form'
			),
			esc_url( $upgrade_link ),
			esc_html__( 'Update Elementor', 'extensions-for-elementor-form' )
		);
	}

	public function cfkef_plugin_row_meta($plugin_meta, $plugin_file)
	{
		if (plugin_basename(CFL_PLUGIN_MAIN_FILE) === $plugin_file) {
			$row_meta = array(
				'docs' => '<a href="' . esc_url('https://docs.coolplugins.net/plugin/cool-formkit-for-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=docs&utm_content=plugins_list') . '" aria-label="' . esc_attr(esc_html__('View Cool FormKit Documentation', 'extensions-for-elementor-form')) . '" target="_blank">' . esc_html__('View Documentation', 'extensions-for-elementor-form') . '</a>',
			);

			$plugin_meta = array_merge($plugin_meta, $row_meta);
		}
		return $plugin_meta;
	}

	public function EEF_get_pro_link($links)
	{
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
			wp_safe_redirect( admin_url( 'admin.php?page=cool-formkit' ) );
			exit;
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
			deactivate_plugins(plugin_basename(CFL_PLUGIN_MAIN_FILE));
			return false;
		}

		if (! is_plugin_active('elementor/elementor.php')) {
			add_action('admin_notices', array($this, 'admin_notice_missing_main_plugin'));
			return false;
		}

		$elementor_version = defined('ELEMENTOR_VERSION') ? ELEMENTOR_VERSION : '';
		if ('' === $elementor_version) {
			$elementor_file = WP_PLUGIN_DIR . '/elementor/elementor.php';
			if (file_exists($elementor_file)) {
				$elementor_data    = get_file_data($elementor_file, array('Version' => 'Version'), 'plugin');
				$elementor_version = isset($elementor_data['Version']) ? $elementor_data['Version'] : '';
			}
		}

		if ('' === $elementor_version || ! version_compare($elementor_version, CFL_MIN_ELEMENTOR_VERSION, '>=')) {
			add_action('admin_notices', array($this, 'admin_notice_elementor_version_fail'));
			return false;
		}

		return true;
	}

	public function add_global_editor_js()
	{
		$this->register_shared_editor_scripts();
		cfl_register_review_dismiss_script();
		wp_enqueue_script('cfl-global-editor-script', CFL_PLUGIN_URL . 'assets/addons/js/global.js', array('jquery', 'cfkef-review-dismiss'), CFL_VERSION, true);
	}

	/**
	 * Register shared helpers before feature-specific editor scripts.
	 */
	public function register_shared_editor_scripts() {
		if ( wp_script_is( 'cfkef-shared-content-template-editor', 'registered' ) ) {
			return;
		}

		wp_register_script(
			'cfkef-shared-content-template-editor',
			CFL_PLUGIN_URL . 'assets/js/shared/content-template-editor.js',
			array( 'jquery' ),
			CFL_VERSION,
			true
		);
	}

	/**
	 * Skips Elementor Pro's editor-templates-extended package (crashes editor with nested form controls).
	 *
	 * @param mixed $packages Editor v2 packages.
	 * @return mixed
	 */
	public function filter_elementor_pro_editor_packages( $packages ) {
		if ( ! is_array( $packages ) ) {
			return $packages;
		}

		return array_values( array_diff( $packages, array( 'editor-templates-extended' ) ) );
	}

	/**
	 * Stub the removed package handle for other Pro packages that depend on it.
	 */
	public function enqueue_editor_templates_extended_stub() {
		wp_register_script(
			'elementor-v2-editor-templates-extended',
			CFL_PLUGIN_URL . 'assets/js/cfl-editor-templates-extended-stub.js',
			array(),
			CFL_VERSION,
			true
		);
		wp_enqueue_script( 'elementor-v2-editor-templates-extended' );
	}

	public function EEF_plugin_dashboard_link($links)
	{
		$settings_link = '<a href="' . admin_url('admin.php?page=cool-formkit') . '">Settings</a>';
		array_unshift($links, $settings_link);
		return $links;
	}

	// Define a helper function for plugin deactivation and admin notice
	private function deactivate_plugin_with_notice($plugin_path, $plugin_name)
	{
		if (file_exists(plugin_dir_path(__DIR__) . $plugin_path)) {
			if (is_plugin_active($plugin_path)) {
				deactivate_plugins($plugin_path);
				add_action(
					'admin_notices',
					function () use ($plugin_name) {
						$this->admin_notice_deactivating_conditional_field_plugin($plugin_name);
					}
				);
			}
		}
	}

	// Method to deactivate child plugins
	public function deactivate_child_plugins()
	{
		// Array of plugins to deactivate
		$plugins_to_deactivate = array(
			'conditional-fields-for-elementor-form/class-conditional-fields-for-elementor-form.php' => 'Conditional Fields for Elementor Form',
			'country-code-field-for-elementor-form/country-code-field-for-elementor-form.php' => 'Country Code Field For Elementor Form',
			'country-code-field-for-elementor-form/country-code-field-for-elementor-form-pro.php' => 'Country Code Field For Elementor Form Pro',
			'form-masks-for-elementor/form-masks-for-elementor.php' => 'Form Input Masks for Elementor Form',
			'mask-form-elementor/index.php' => 'Input Mask Elementor Form Fields',
		);

		// Loop through the plugins and deactivate them if necessary
		foreach ($plugins_to_deactivate as $plugin_path => $plugin_name) {
			$this->deactivate_plugin_with_notice($plugin_path, $plugin_name);
		}
	}

	public function admin_notice_deactivating_conditional_field_plugin($plugin_name)
	{
?>
		<div class="notice notice-error">
			<p><?php echo esc_html("{$plugin_name} is deactivated because Cool FormKit is installed and activated."); ?></p>
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
		$message = esc_html__(
			'Cool FormKit Pro is active, so Cool FormKit Lite has been deactivated automatically.',
			'extensions-for-elementor-form'
		);
		printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', esc_html($message));
	}

	/**
	 * Display admin notice for PHP version failure.
	 */
	public function admin_notice_php_version_fail()
	{
		$message = sprintf(
			/* translators: 1: Plugin name, 2: Required PHP version */
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
			/* translators: 1: Plugin name, 2: Required WordPress version */
			esc_html__('%1$s requires WordPress version %2$s or greater.', 'extensions-for-elementor-form'),
			'<strong>Cool Formkit Lite</strong>',
			WP_MINIMUM_VERSION
		);

		echo wp_kses_post(sprintf('<div class="notice notice-error"><p>%1$s</p></div>', $message));
	}

	/**
	 * Display admin notice for Elementor version failure.
	 */
	public function admin_notice_elementor_version_fail()
	{
		$message = sprintf(
			/* translators: 1: Plugin name, 2: Required Elementor version */
			esc_html__('%1$s requires Elementor version %2$s or greater.', 'extensions-for-elementor-form'),
			'<strong>Cool Formkit Lite</strong>',
			CFL_MIN_ELEMENTOR_VERSION
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
			}
		}
	}

	public function autoload($class_name)
	{
		if (0 !== strpos($class_name, __NAMESPACE__)) {
			return;
		}

		if (! class_exists($class_name)) {
			$filename = strtolower(
				preg_replace(
					['/^' . __NAMESPACE__ . '\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/'],
					['', '$1-$2', '-', DIRECTORY_SEPARATOR],
					$class_name
				)
			);

			$filename = trailingslashit(CFL_PLUGIN_PATH) . $filename . '.php';

			if (is_readable($filename)) {
				include $filename;
			}
		}
	}

	public static function eef_activate()
	{
		$legacy_install_date = get_option('eef-installDate');
		if (!get_option('cfl-install-date')) {
			add_option('cfl-install-date', $legacy_install_date ? $legacy_install_date : gmdate('Y-m-d h:i:s'));
		}

		if (!get_option('CFL_initial_save_version')) {
			add_option('CFL_initial_save_version', CFL_VERSION);
		}

		$enabled = get_option( 'cfkef_enabled_elements', array() );
		if ( empty( $enabled ) ) {
			update_option( 'cfkef_enabled_elements', array(
				'conditional_logic',
				'country_code',
				'form_input_mask',
				'whatsapp_redirect',
			) );
		}

		$settings       = get_option('cfef_usage_share_data');

		if (!empty($settings) || $settings === 'on') {

			self::cfl_cron_job_init();
		}
	}

	public static function cfl_cron_job_init()
	{
		if (!wp_next_scheduled('cfl_extra_data_update')) {
			wp_schedule_event(time(), 'every_30_days', 'cfl_extra_data_update');
		}
	}

	public static function eef_deactivate()
	{

		if (wp_next_scheduled('cfl_extra_data_update')) {
			wp_clear_scheduled_hook('cfl_extra_data_update');
		}
	}
}

// Initialize the plugin.
Cool_Formkit_Lite_For_Elementor_Form::instance();

register_activation_hook(CFL_PLUGIN_MAIN_FILE, array('Cool_FormKit\Cool_Formkit_Lite_For_Elementor_Form', 'eef_activate'));
register_deactivation_hook(CFL_PLUGIN_MAIN_FILE, array('Cool_FormKit\Cool_Formkit_Lite_For_Elementor_Form', 'eef_deactivate'));

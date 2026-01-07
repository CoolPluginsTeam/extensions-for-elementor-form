<?php

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;

/**
 * Cool Marketing Controllers
 *
 * Handles marketing notices and AJAX actions for Elementor forms.
 *
 * @package Conditional Fields for Elementor Form
 */

if (! class_exists('CFL_Marketing_Controllers')) {

	class CFL_Marketing_Controllers
	{
		private static $instance = null;
		
		/**
		 * ✅ Singleton instance
		 */
		public static function get_instance()
		{

			if (self::$instance === null) {

				self::$instance = new self();
			}

			return self::$instance;
		}
		
		/**
		 * ✅ Constructor
		 *
		 * Initializes hooks and actions.
		 */
		public function __construct() {

			$active_plugins = get_option( 'active_plugins', [] );

			if(!defined("formdb_marketing_submission")){

				define("formdb_marketing_submission", true);

				if(!in_array( 'sb-elementor-contact-form-db/sb_elementor_contact_form_db.php', $active_plugins ) && !get_option('cfef_formdb_marketing_dismissed', false)) {
	
					add_action('admin_enqueue_scripts', [$this, 'cfl_formdb_marketing_script']);
		
					add_action('in_admin_header', array($this, 'cfl_admin_notice_for_formsdb'));
				}
	
				if(!in_array( 'sb-elementor-contact-form-db/sb_elementor_contact_form_db.php', $active_plugins ) && get_option('cfef_formdb_marketing_dismissed', false)){
	
					add_action('admin_enqueue_scripts', [$this, 'cfl_formdb_marketing_script']);
	
					add_action('admin_enqueue_scripts', array($this, 'formdb_plugin_install_button'));
				}
			}

			add_action('wp_ajax_cfl_install_plugin', [$this, 'cfl_install_plugin']);
          	add_action('wp_ajax_cfl_mkt_dismiss_notice', [$this,'cfl_dismiss_notice_callback']);
		}

		function cfl_dismiss_notice_callback() {

			if ( ! current_user_can( 'manage_options' ) ) {
                 wp_send_json_error([ 'message' => 'Permission denied' ]);
			}

			$type  = sanitize_text_field($_POST['notice_type'] ?? '');
           $nonce = isset($_POST['nonce']) ? $_POST['nonce'] : '';
          
		    if ( empty( $nonce ) || empty( $type ) || ! wp_verify_nonce( $nonce, "cfl_dismiss_nonce_{$type}" ) ) {
            wp_send_json_error([ 'message' => 'Invalid nonce' ]);
         }
			if ($type === 'formdb_notice') {
				update_option('cfef_formdb_marketing_dismissed', true);
				wp_send_json_success();

			}

			wp_send_json_error(['message' => 'Unknown notice type']);
	}

		public function cfl_install_plugin() {


        if (! current_user_can('install_plugins')) {
			$status['errorMessage'] = __('Sorry, you are not allowed to install plugins on this site.');
			wp_send_json_error($status);
		}

		check_ajax_referer('cfl_install_nonce');

		if (empty($_POST['slug'])) {
			wp_send_json_error(array(
				'slug'         => '',
				'errorCode'    => 'no_plugin_specified',
				'errorMessage' => __('No plugin specified.'),
			));
		}

		$plugin_slug = sanitize_key(wp_unslash($_POST['slug']));


		$status = array(
			'install' => 'plugin',
			'slug'    => sanitize_key(wp_unslash($_POST['slug'])),
		);

		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		require_once ABSPATH . 'wp-admin/includes/plugin.php';


		if ($plugin_slug == 'conditional-fields-for-elementor-form-pro') {

			if (! current_user_can('activate_plugin', $plugin_slug)) {
				wp_send_json_error(['message' => 'Permission denied']);
			}

			$conditional_pro_plugin_file = 'conditional-fields-for-elementor-form-pro/class-conditional-fields-for-elementor-form-pro.php';

			$pagenow        = isset($_POST['pagenow']) ? sanitize_key($_POST['pagenow']) : '';
			$network_wide = (is_multisite() && 'import' !== $pagenow);
			$activation_result = activate_plugin($conditional_pro_plugin_file, '', $network_wide);

			if (is_wp_error($activation_result)) {
				wp_send_json_error(['message' => $activation_result->get_error_message()]);
			}

			wp_send_json_success(['message' => 'Plugin activated successfully']);
		} else {

			$api = plugins_api('plugin_information', array(
				'slug'   => $plugin_slug,
				'fields' => array(
					'sections' => false,
				),
			));

			if (is_wp_error($api)) {
				$status['errorMessage'] = $api->get_error_message();
				wp_send_json_error($status);
			}

			$status['pluginName'] = $api->name;

			$skin     = new WP_Ajax_Upgrader_Skin();
			$upgrader = new Plugin_Upgrader($skin);
			$result   = $upgrader->install($api->download_link);

			if (defined('WP_DEBUG') && WP_DEBUG) {
				$status['debug'] = $skin->get_upgrade_messages();
			}

			if (is_wp_error($result)) {

				$status['errorCode']    = $result->get_error_code();
				$status['errorMessage'] = $result->get_error_message();
				wp_send_json_error($status);
			} elseif (is_wp_error($skin->result)) {

				if ($skin->result->get_error_message() === 'Destination folder already exists.') {

					$install_status = install_plugin_install_status($api);
					$pagenow        = isset($_POST['pagenow']) ? sanitize_key($_POST['pagenow']) : '';

					if (current_user_can('activate_plugin', $install_status['file'])) {

						$network_wide = (is_multisite() && 'import' !== $pagenow);
						$activation_result = activate_plugin($install_status['file'], '', $network_wide);
						if (is_wp_error($activation_result)) {

							$status['errorCode']    = $activation_result->get_error_code();
							$status['errorMessage'] = $activation_result->get_error_message();
							wp_send_json_error($status);
						} else {

							$status['activated'] = true;
						}
						wp_send_json_success($status);
					}
				} else {

					$status['errorCode']    = $skin->result->get_error_code();
					$status['errorMessage'] = $skin->result->get_error_message();
					wp_send_json_error($status);
				}
			} elseif ($skin->get_errors()->has_errors()) {

				$status['errorMessage'] = $skin->get_error_messages();
				wp_send_json_error($status);
			} elseif (is_null($result)) {

				global $wp_filesystem;

				$status['errorCode']    = 'unable_to_connect_to_filesystem';
				$status['errorMessage'] = __('Unable to connect to the filesystem. Please confirm your credentials.');

				if ($wp_filesystem instanceof WP_Filesystem_Base && is_wp_error($wp_filesystem->errors) && $wp_filesystem->errors->has_errors()) {
					$status['errorMessage'] = esc_html($wp_filesystem->errors->get_error_message());
				}

				wp_send_json_error($status);
			}

			$install_status = install_plugin_install_status($api);
			$pagenow        = isset($_POST['pagenow']) ? sanitize_key($_POST['pagenow']) : '';

			// 🔄 Auto-activate the plugin right after successful install
			if (current_user_can('activate_plugin', $install_status['file']) && is_plugin_inactive($install_status['file'])) {

				$network_wide = (is_multisite() && 'import' !== $pagenow);
				$activation_result = activate_plugin($install_status['file'], '', $network_wide);

				if (is_wp_error($activation_result)) {
					$status['errorCode']    = $activation_result->get_error_code();
					$status['errorMessage'] = $activation_result->get_error_message();
					wp_send_json_error($status);
				} else {
					$status['activated'] = true;
				}
			}
			wp_send_json_success($status);
		}
	}

		public function formdb_plugin_install_button(){

			$screen = get_current_screen();

            if ( $screen && 'elementor_page_e-form-submissions' === $screen->id ) {

                $button_text = __('Save To Google Sheet - Install Plugin', 'cfl');
				$nonce = wp_create_nonce('cfl_install_nonce');
                
                $custom_js = "
                    jQuery(document).ready(function($) {

                        var button = '<a data-nonce=\"{$nonce}\" data-plugin=\"form-db\" target=\"_blank\" class=\"button button-primary cfl-install-plugin\">{$button_text}</a>';
                        $('#e-form-submissions .e-form-submissions-search').prepend(button);
                    });
                ";
                wp_add_inline_script('jquery-core', $custom_js);
            }
		}

		public function cfl_formdb_marketing_script($page) {


			if ( $page !== 'elementor_page_e-form-submissions' ) {
				return;
			}

			wp_register_script(
				'cfl-formdb-marketing-js',
				CFL_PLUGIN_URL . 'admin/assets/js/cfl-formdb-marketing.js',
				['jquery'],
				CFL_VERSION,
				true
			);

			wp_enqueue_script('cfl-formdb-marketing-js');

			wp_localize_script(
				'cfl-formdb-marketing-js',
				'cflFormDBMarketing',
				[
					'nonce'    => wp_create_nonce('cfl_install_nonce'),
					'plugin'   => 'form-db',
					'ajax_url' => admin_url('admin-ajax.php'),
					'formdb_type' => 'formdb_notice',
					'formdb_dismiss_nonce' => wp_create_nonce('cfl_dismiss_nonce_formdb_notice'),
					'redirect_to_formdb' => true
				]
			);
		}
		

		public function cfl_admin_notice_for_formsdb()
		{

			if ( ! isset($_GET['page']) || $_GET['page'] !== 'e-form-submissions' ) {
				return;
			}	

			$admin_notices = \Elementor\Plugin::$instance->admin->get_component('admin-notices');

			$notice_options = [
				'button_secondary' => [
					'text' => esc_html__('Install Plugin', 'cfl'),
					'classes' => ['cfl-install-plugin'],
					'url' => '',
					'type' => 'cta',
				],
				'description' => '<b>Did you Know?</b> you can also save your form submissions to Google Sheets.',
				'id' => 'formdb-marketing-elementor-form-submissions',
				'type' => 'cta',
				
			];

			$admin_notices->print_admin_notice($notice_options);
		}

		/**
		 * Initialize hooks
		 * Registers the necessary hooks for marketing notices and AJAX actions.
		 */

		public function cfl_init_hooks() {

			add_action('elementor/editor/after_enqueue_scripts', [$this, 'enqueue_editor_scripts']);
		}

		/**
		 * Adds ACF Repeater marketing notice to Loop Grid Query controls
		 * 
		 * @param \Elementor\Widget_Base $element
		 */

		public function cfl_add_acf_repeater_mkt_query_controls($element) {

			$element->add_control(

				'lgefep_mkt_country_notice',
					array(
						'name'            => 'cfl_mkt_country_notice',
						'type'            => \Elementor\Controls_Manager::SWITCHER,
						'label'        => esc_html__('Use ACF Repeater', 'cfl'),
						'type'         => \Elementor\Controls_Manager::SWITCHER,
						'label_on'     => esc_html__('Yes', 'cfl'),
						'label_off'    => esc_html__('No', 'cfl'),

					),
			);

			$element->add_control(

				'lgefep_acf_mkt_repeater_tag',
					[
						'name'      => 'lgefep_acf_mkt_repeater_tag',
						'label'     => '',
						'type'      => \Elementor\Controls_Manager::RAW_HTML,
							'raw'       => '<div class="elementor-control-raw-html cool-form-wrp"><div class="elementor-control-notice elementor-control-notice-type-info">
											<div class="elementor-control-notice-icon"><img class="cfl-highlight-icon" src="'.esc_url( CFL_PLUGIN_URL . 'assets/images/cfl-highlight-icon.svg' ).'" width="250" alt="Highlight Icon" />
											</div>
											<div class="elementor-control-notice-main">
											<div class="elementor-control-notice-main-content">Display ACF Repeater fields in your Elementor loop grid.</div>
											<div class="elementor-control-notice-main-actions">
											<button type="button" class="elementor-button e-btn e-info e-btn-1 cfl-install-plugin"  data-plugin="loop-grid" data-nonce="' . esc_attr(wp_create_nonce('cfl_install_nonce')) . '">Install Loop Grid Extender</button></button>
											</div></div></div></div>',
							'condition'       => array(
								'lgefep_mkt_country_notice' => 'yes'
							),
					]

			);
			
		}

		/**
		 * ✅ Enqueue editor scripts
		 */

		public function enqueue_editor_scripts(){

			wp_enqueue_script(
				'coolplugin-editor-js',
				CFL_PLUGIN_URL . 'admin/assets/js/cfl-formdb-marketing.js',
				['jquery'],
				CFL_VERSION,
				true
			);
		}
	}

	CFL_Marketing_Controllers::get_instance();
}

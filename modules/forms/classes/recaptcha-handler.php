<?php


namespace Cool_FormKit\Modules\Forms\Classes;


use Elementor\Settings;
use Elementor\Widget_Base;
use Cool_FormKit\Includes\Utils;

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * Integration with Google reCAPTCHA
 */
class Recaptcha_Handler
{

	const OPTION_NAME_SITE_KEY = 'elementor_pro_recaptcha_site_key';

	const OPTION_NAME_SECRET_KEY = 'elementor_pro_recaptcha_secret_key';

	const OPTION_NAME_RECAPTCHA_THRESHOLD = 'elementor_pro_recaptcha_threshold';

	const V2_CHECKBOX = 'v2_checkbox';

	protected static function get_recaptcha_name()
	{
		return 'recaptcha';
	}

	public static function get_site_key()
	{
		return get_option(self::OPTION_NAME_SITE_KEY);
	}

	public static function get_secret_key()
	{
		return get_option(self::OPTION_NAME_SECRET_KEY);
	}

	public static function get_recaptcha_type()
	{
		return self::V2_CHECKBOX;
	}

	public static function is_enabled()
	{
		return static::get_site_key() && static::get_secret_key();
	}

	public static function get_setup_message()
	{
		return esc_html__('To use reCAPTCHA, you need to add the API Key and complete the setup process in Dashboard > Elementor > Settings > Integrations > reCAPTCHA.', 'cool-formkit');
	}

	public function register_admin_fields(Settings $settings)
	{
		$settings->add_section(Settings::TAB_INTEGRATIONS, static::get_recaptcha_name(), [
			'label' => esc_html__('reCAPTCHA', 'cool-formkit'),
			'callback' => function () {
				echo sprintf(
					/* translators: 1: Link opening tag, 2: Link closing tag. */
					esc_html__('%1$sreCAPTCHA%2$s is a free service by Google that protects your website from spam and abuse. It does this while letting your valid users pass through with ease.', 'cool-formkit'),
					'<a href="https://www.google.com/recaptcha/" target="_blank">',
					'</a>'
				);
			},
			'fields' => [
				'pro_recaptcha_site_key' => [
					'label' => esc_html__('Site Key', 'cool-formkit'),
					'field_args' => [
						'type' => 'text',
					],
				],
				'pro_recaptcha_secret_key' => [
					'label' => esc_html__('Secret Key', 'cool-formkit'),
					'field_args' => [
						'type' => 'text',
					],
				],
			],
		]);
	}

	protected static function get_script_render_param()
	{
		return 'explicit';
	}

	protected static function get_script_name()
	{
		return 'elementor-' . static::get_recaptcha_name() . '-api';
	}


	public function enqueue_scripts()
	{
		if (utils::elementor()->preview->is_preview_mode()) {
			return;
		}
		$script_name = static::get_script_name();
		wp_enqueue_script($script_name);
	}

	/**
	 * @param Form_Record  $record
	 * @param Ajax_Handler $ajax_handler
	 */
	public function validation($record, $ajax_handler)
	{
		$fields = $record->get_field([
			'type' => static::get_recaptcha_name(),
		]);

		if (empty($fields)) {
			return;
		}

		$field = current($fields);

		// PHPCS - response protected by recaptcha secret
		$recaptcha_response = Utils::_unstable_get_super_global_value($_POST, 'g-recaptcha-response'); // phpcs:ignore WordPress.Security.NonceVerification.Missing


		if (empty($recaptcha_response)) {
			$ajax_handler->add_error($field['id'], esc_html__('The Captcha field cannot be blank.', 'cool-formkit'));

			return;
		}

		$recaptcha_errors = [
			'missing-input-secret' => esc_html__('The secret parameter is missing.', 'cool-formkit'),
			'invalid-input-secret' => esc_html__('The secret parameter is invalid or malformed.', 'cool-formkit'),
			'missing-input-response' => esc_html__('The response parameter is missing.', 'cool-formkit'),
			'invalid-input-response' => esc_html__('The response parameter is invalid or malformed.', 'cool-formkit'),
		];

		$recaptcha_secret = static::get_secret_key();
		$client_ip = Utils::get_client_ip();

		$request = [
			'body' => [
				'secret' => $recaptcha_secret,
				'response' => $recaptcha_response,
				'remoteip' => $client_ip,
			],
		];

		$response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', $request);

		$response_code = wp_remote_retrieve_response_code($response);

		if (200 !== (int) $response_code) {
			/* translators: %d: Response code. */
			$ajax_handler->add_error($field['id'], sprintf(esc_html__('Can not connect to the reCAPTCHA server (%d).', 'cool-formkit'), $response_code));

			return;
		}

		$body = wp_remote_retrieve_body($response);

		$result = json_decode($body, true);

		if (! $this->validate_result($result, $field)) {
			$message = esc_html__('Invalid form, reCAPTCHA validation failed.', 'cool-formkit');

			if (isset($result['error-codes'])) {
				$result_errors = array_flip($result['error-codes']);

				foreach ($recaptcha_errors as $error_key => $error_desc) {
					if (isset($result_errors[$error_key])) {
						$message = $recaptcha_errors[$error_key];
						break;
					}
				}
			}

			$this->add_error($ajax_handler, $field, $message);
		}

		// If success - remove the field form list (don't send it in emails and etc )
		$record->remove_field($field['id']);
	}

	/**
	 * @param Ajax_Handler $ajax_handler
	 * @param $field
	 * @param $message
	 */
	protected function add_error($ajax_handler, $field, $message)
	{
		$ajax_handler->add_error($field['id'], $message);
	}

	protected function validate_result($result, $field)
	{
		if (! $result['success']) {
			return false;
		}

		return true;
	}

	/**
	 * @param $item
	 * @param $item_index
	 * @param $widget Widget_Base
	 */
	public function render_field($item, $item_index, $widget)
	{
		$recaptcha_html = '<div class="cool-form-field" id="form-field-' . $item['custom_id'] . '" >';

		if (static::is_enabled()) {
			$this->enqueue_scripts();

			$recaptcha_name = static::get_recaptcha_name();

			// Get the widget settings for theme & size
			$theme = $item['recaptcha_style'];
			$size = $item['recaptcha_size'];

			// Add attributes dynamically
			$widget->add_render_attribute($recaptcha_name . $item_index, [
				'class' => 'cool-form-recaptcha',
				'data-sitekey' => static::get_site_key(),
				'data-theme' => esc_attr($theme),
				'data-size' => esc_attr($size),
				'data-recaptcha-version' => static::get_recaptcha_type()
			]);

			$recaptcha_html .= '<div ' . $widget->get_render_attribute_string($recaptcha_name . $item_index) . '></div>';
		} else {
			$recaptcha_html .= '<div class="elementor-alert elementor-alert-info">';
			$recaptcha_html .= static::get_setup_message();
			$recaptcha_html .= '</div>';
		}

		$recaptcha_html .= '</div>';


		echo $recaptcha_html;
	}

	/**
	 * @param $item
	 * @param $item_index
	 * @param $widget Widget_Base
	 */
	protected function add_version_specific_render_attributes($item, $item_index, $widget)
	{
		$recaptcha_name = static::get_recaptcha_name();
		$widget->add_render_attribute($recaptcha_name . $item_index, [
			'data-theme' => $item['recaptcha_style'],
			'data-size' => $item['recaptcha_size'],
		]);
	}

	public function add_field_type($field_types)
	{
		$field_types['recaptcha'] = esc_html__('reCAPTCHA', 'cool-formkit');

		return $field_types;
	}

	public function filter_field_item($item)
	{
		if (static::get_recaptcha_name() === $item['field_type']) {
			$item['field_label'] = false;
		}

		return $item;
	}

	public function my_plugin_register_frontend_scripts()
	{

		wp_register_script('cool-formkit-recaptcha-api', 'https://www.google.com/recaptcha/api.js?onload=recaptchaLoaded&render=explicit', [], null, true);

		wp_register_script(
				'cool-formkit-recaptcha-handler',
				CFL_PLUGIN_URL . 'assets/js/recaptcha-handler.min.js',
				['jquery', 'elementor-frontend'],
				CFL_VERSION,
				true
			);
	
		wp_localize_script('cool-formkit-recaptcha-handler', 'coolFormKitRecaptcha', [
				'enabled'   => static::is_enabled(),
				'site_key'  => static::get_site_key(),
				'type'      => static::get_recaptcha_type(),
			]);

	}

	public function my_plugin_enqueue_frontend_scripts(){
		wp_enqueue_script('cool-formkit-recaptcha-api', true);
		wp_enqueue_script('cool-formkit-recaptcha-handler', true);

	}

	public function localize_settings($settings){

		$settings = array_replace_recursive( $settings, [
			'forms' => [
				static::get_recaptcha_name() => [
					'enabled' => static::is_enabled(),
					'type' => static::get_recaptcha_type(),
					'site_key' => static::get_site_key(),
					'setup_message' => static::get_setup_message(),
				],
			],
		] );

		return $settings;
		
	}


	public function __construct()
	{

		add_filter('cool_formkit/forms/field_types', [$this, 'add_field_type']);
		add_action('cool_formkit/forms/render_field/' . static::get_recaptcha_name(), [$this, 'render_field'], 10, 3);
		add_filter('cool_formkit/forms/render/item', [$this, 'filter_field_item']);

		add_filter('elementor/editor/localize_settings', [$this, 'localize_settings'] );


		if (static::is_enabled()) {
			add_action('cool_formkit/forms/validation', [$this, 'validation'], 10, 2);
			add_action('elementor/preview/enqueue_scripts', [$this, 'enqueue_scripts']);
		}

		if (is_admin()) {
			add_action('elementor/admin/after_create_settings/' . Settings::PAGE_ID, [$this, 'register_admin_fields']);
		}


		if (!static::is_enabled()) {
			return; // Do not load scripts if reCAPTCHA is not enabled
		}

		
		if (!is_admin()) {

			add_action('elementor/frontend/after_register_scripts', [$this,'my_plugin_register_frontend_scripts']);
		
			add_action( 'elementor/frontend/after_enqueue_scripts', [$this,'my_plugin_enqueue_frontend_scripts'] );

		}
	}
}

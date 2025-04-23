<?php


namespace Cool_FormKit\Modules\Forms\Classes;


use Cool_FormKit\Includes\Utils;

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * Integration with Google reCAPTCHA
 */
class Cloudflare_recaptcha
{

	const OPTION_NAME_SITE_KEY = 'cfl_cloudflare_site_key';

	const OPTION_NAME_SECRET_KEY = 'cfl_cloudflare_secret_key';

	protected static function get_recaptcha_name()
	{
		return 'cloudflare_recaptcha';
	}

	public static function get_site_key()
	{
		return get_option(self::OPTION_NAME_SITE_KEY);
	}

	public static function get_secret_key()
	{
		return get_option(self::OPTION_NAME_SECRET_KEY);
	}

	public static function is_enabled()
	{
		return static::get_site_key() && static::get_secret_key();
	}

	public static function get_setup_message()
	{
		return esc_html__('To use reCAPTCHA, you need to add the API Key and complete the setup process in Dashboard > Elementor > Cool FormKit Lite > Settings > reCAPTCHA', 'cool-formkit');
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
		'type' => static::get_recaptcha_name(), // This should return something like 'turnstile'
	]);

	if (empty($fields)) {
		return;
	}

	$field = current($fields);

	// Get Turnstile response token
	$turnstile_response = Utils::_unstable_get_super_global_value($_POST, 'cf-turnstile-response'); // Cloudflare returns this in the POST

	if (empty($turnstile_response)) {
		$ajax_handler->add_error($field['id'], esc_html__('The Captcha field cannot be blank.', 'cool-formkit'));
		return;
	}

	$turnstile_errors = [
		'missing-input-secret' => esc_html__('The secret parameter is missing.', 'cool-formkit'),
		'invalid-input-secret' => esc_html__('The secret parameter is invalid or malformed.', 'cool-formkit'),
		'missing-input-response' => esc_html__('The response parameter is missing.', 'cool-formkit'),
		'invalid-input-response' => esc_html__('The response parameter is invalid or malformed.', 'cool-formkit'),
	];

	$secret_key = static::get_secret_key(); // Your Turnstile secret
	$client_ip = Utils::get_client_ip();

	$request = [
		'body' => [
			'secret' => $secret_key,
			'response' => $turnstile_response,
			'remoteip' => $client_ip,
		],
	];

	// Turnstile verification endpoint
	$response = wp_remote_post('https://challenges.cloudflare.com/turnstile/v0/siteverify', $request);

	$response_code = wp_remote_retrieve_response_code($response);

	if (200 !== (int) $response_code) {
		$ajax_handler->add_error($field['id'], sprintf(esc_html__('Can not connect to the Turnstile server (%d).', 'cool-formkit'), $response_code));
		return;
	}

	$body = wp_remote_retrieve_body($response);
	$result = json_decode($body, true);

	if (empty($result['success']) || ! $this->validate_result($result, $field)) {
		$message = esc_html__('Invalid form, Turnstile validation failed.', 'cool-formkit');

		if (!empty($result['error-codes'])) {
			$result_errors = array_flip($result['error-codes']);

			foreach ($turnstile_errors as $error_key => $error_desc) {
				if (isset($result_errors[$error_key])) {
					$message = $turnstile_errors[$error_key];
					break;
				}
			}
		}

		$this->add_error($ajax_handler, $field, $message);
		return;
	}

	// On success, remove the field from further processing
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
			$language = $item['cloudflare_recaptcha_language'];


			// Add attributes dynamically
			$widget->add_render_attribute($recaptcha_name . $item_index, [
				'class' => 'cool-form-cloudflare-recaptcha',
				'data-sitekey' => static::get_site_key(),
				'data-theme' => esc_attr($theme),
				'data-size' => esc_attr($size),
                'data-language' => esc_attr($language),
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
		$field_types['cloudflare_recaptcha'] = esc_html__('cloudflare reCaptcha', 'cool-formkit');

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


		wp_register_script('cool-formkit-cloudflare-recaptcha-api', 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit', [], null, true);   

		wp_register_script(
				'cool-formkit-cloudflare-recaptcha-handler',
				CFL_PLUGIN_URL . 'assets/js/cloudflare-recpatcha-handler.js',
				['jquery', 'elementor-frontend'],
				CFL_VERSION,
				true
			);
	
		wp_localize_script('cool-formkit-cloudflare-recaptcha-handler', 'coolFormKitRecaptcha', [
				'enabled'   => true,
				'site_key'  => static::get_site_key(),
			]);

	}

	public function my_plugin_enqueue_frontend_scripts(){
		wp_enqueue_script('cool-formkit-cloudflare-recaptcha-api', true);
		wp_enqueue_script('cool-formkit-cloudflare-recaptcha-handler', true);

	}

	public function localize_settings($settings){

		$settings = array_replace_recursive( $settings, [
			'forms' => [
				static::get_recaptcha_name() => [
					'enabled' => static::is_enabled(),
					'sitekey' => '0x4AAAAAABRZpG6Iz-Srw8Us',
					'setup_message' => 'ravi',
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

		if (!static::is_enabled()) {
			return; // Do not load scripts if reCAPTCHA is not enabled
		}

		
		if (!is_admin()) {

			add_action('elementor/frontend/after_register_scripts', [$this,'my_plugin_register_frontend_scripts']);
		
			add_action( 'elementor/frontend/after_enqueue_scripts', [$this,'my_plugin_enqueue_frontend_scripts'] );

		}
	}
}

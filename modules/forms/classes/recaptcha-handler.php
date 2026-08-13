<?php


namespace Cool_FormKit\Modules\Forms\Classes;


use Cool_FormKit\Includes\Utils;

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

// phpcs:disable WordPress.WP.EnqueuedResourceParameters.MissingVersion
// phpcs:disable WordPress.WP.EnqueuedResourceParameters.NotInFooter


/**
 * Integration with Google reCAPTCHA
 */
class Recaptcha_Handler
{

	const OPTION_NAME_SITE_KEY = 'cfl_site_key_v2';

	const OPTION_NAME_SECRET_KEY = 'cfl_secret_key_v2';


	const V2_CHECKBOX = 'v2_checkbox';

	/**
	 * Last siteverify error-code map used by resolve_recaptcha_error_message().
	 *
	 * @var array
	 */
	protected $last_recaptcha_error_map = [];

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
		return esc_html__('To use reCAPTCHA, you need to add the API Key and complete the setup process in Dashboard > Elementor > Cool FormKit Lite > Settings > reCAPTCHA', 'extensions-for-elementor-form');
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
			$ajax_handler->add_error($field['id'], $this->get_empty_response_message());
			return;
		}

		$result = $this->verify_recaptcha_response($recaptcha_response, $ajax_handler, $field);
		if (null === $result) {
			return;
		}

		if (!$this->validate_result($result, $field)) {
			$message = $this->resolve_recaptcha_error_message($result);
			$this->add_error($ajax_handler, $field, $message);
		}

		// If success - remove the field form list (don't send it in emails and etc )
		$record->remove_field($field['id']);
	}

	/**
	 * Message when g-recaptcha-response is missing.
	 *
	 * @return string
	 */
	protected function get_empty_response_message()
	{
		return esc_html__('The Captcha field cannot be blank.', 'extensions-for-elementor-form');
	}

	/**
	 * Verify token with Google siteverify API.
	 *
	 * @param string       $recaptcha_response Token.
	 * @param Ajax_Handler $ajax_handler Handler.
	 * @param array        $field Field data.
	 * @return array|null Decoded response or null when an error was already added.
	 */
	protected function verify_recaptcha_response($recaptcha_response, $ajax_handler, $field)
	{
		$recaptcha_errors = [
			'missing-input-secret' => esc_html__('The secret parameter is missing.', 'extensions-for-elementor-form'),
			'invalid-input-secret' => esc_html__('The secret parameter is invalid or malformed.', 'extensions-for-elementor-form'),
			'missing-input-response' => esc_html__('The response parameter is missing.', 'extensions-for-elementor-form'),
			'invalid-input-response' => esc_html__('The response parameter is invalid or malformed.', 'extensions-for-elementor-form'),
		];

		$recaptcha_secret = static::get_secret_key();
		if (empty($recaptcha_secret)) {
			$ajax_handler->add_error($field['id'], esc_html__('Missing reCAPTCHA secret key.', 'extensions-for-elementor-form'));
			return null;
		}

		$response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', [
			'body' => [
				'secret' => $recaptcha_secret,
				'response' => $recaptcha_response,
				'remoteip' => Utils::get_client_ip(),
			],
			'timeout' => 10,
		]);

		if (is_wp_error($response)) {
			$ajax_handler->add_error($field['id'], esc_html__('Error verifying reCAPTCHA. Please try again.', 'extensions-for-elementor-form'));
			return null;
		}

		$response_code = wp_remote_retrieve_response_code($response);
		if (200 !== (int) $response_code) {
			/* translators: %d: Response code. */
			$ajax_handler->add_error($field['id'], sprintf(esc_html__('Can not connect to the reCAPTCHA server (%d).', 'extensions-for-elementor-form'), $response_code));
			return null;
		}

		$result = json_decode(wp_remote_retrieve_body($response), true);
		$this->last_recaptcha_error_map = $recaptcha_errors;

		return is_array($result) ? $result : [];
	}

	/**
	 * Map Google error-codes to a user message.
	 *
	 * @param array $result Siteverify response.
	 * @return string
	 */
	protected function resolve_recaptcha_error_message($result)
	{
		$message = esc_html__('Invalid form, reCAPTCHA validation failed.', 'extensions-for-elementor-form');
		$recaptcha_errors = $this->last_recaptcha_error_map ?? [];

		if (isset($result['error-codes']) && !empty($recaptcha_errors)) {
			$result_errors = array_flip($result['error-codes']);
			foreach ($recaptcha_errors as $error_key => $error_desc) {
				if (isset($result_errors[$error_key])) {
					return $recaptcha_errors[$error_key];
				}
			}
		}

		return $message;
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
		if (empty($result['success'])) {
			return false;
		}

		return true;
	}

	/**
	 * Build render attributes for the captcha widget markup.
	 *
	 * @param array $item Field item.
	 * @return array
	 */
	protected function get_render_attributes($item)
	{
		return [
			'class' => 'cool-form-recaptcha',
			'data-sitekey' => static::get_site_key(),
			'data-theme' => esc_attr($item['recaptcha_style'] ?? 'light'),
			'data-size' => esc_attr($item['recaptcha_size'] ?? 'normal'),
			'data-recaptcha-version' => static::get_recaptcha_type(),
		];
	}

	/**
	 * @param $item
	 * @param $item_index
	 * @param $widget Widget_Base
	 */
	public function render_field($item, $item_index, $widget)
	{
		$custom_id = esc_attr($item['custom_id'] ?? '');
		$recaptcha_html = '<div class="cool-form-field" id="form-field-' . $custom_id . '">';

		if (static::is_enabled()) {
			$this->enqueue_scripts();

			$recaptcha_name = static::get_recaptcha_name();
			$widget->add_render_attribute($recaptcha_name . $item_index, $this->get_render_attributes($item));
			$recaptcha_html .= '<div ' . $widget->get_render_attribute_string($recaptcha_name . $item_index) . '></div>';
		} else {
			$recaptcha_html .= '<div class="elementor-alert elementor-alert-info">';
			$recaptcha_html .= static::get_setup_message();
			$recaptcha_html .= '</div>';
		}

		$recaptcha_html .= '</div>';

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $recaptcha_html;
	}

	public function add_field_type($field_types)
	{
		$field_types['recaptcha'] = esc_html__('reCAPTCHA', 'extensions-for-elementor-form');

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

		wp_register_script('cool-formkit-recaptcha-api', 'https://www.google.com/recaptcha/api.js?onload=recaptchaLoaded&render=explicit', [], CFL_VERSION, true);

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

		if (!static::is_enabled()) {
			return; // Do not load scripts if reCAPTCHA is not enabled
		}

		
		if (!is_admin()) {

			add_action('elementor/frontend/after_register_scripts', [$this,'my_plugin_register_frontend_scripts']);
		
			add_action( 'elementor/frontend/after_enqueue_scripts', [$this,'my_plugin_enqueue_frontend_scripts'] );

		}
	}
}

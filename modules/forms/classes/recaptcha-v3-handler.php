<?php

namespace Cool_FormKit\Modules\Forms\Classes;


use Cool_FormKit\Includes\Utils;


if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly             
}

/**
 * Integration with Google reCAPTCHA
 */
class Recaptcha_V3_Handler extends Recaptcha_Handler
{

    const OPTION_NAME_V3_SITE_KEY = 'cfl_site_key_v3';
    const OPTION_NAME_V3_SECRET_KEY = 'cfl_secret_key_v3';
    const OPTION_NAME_RECAPTCHA_THRESHOLD = 'cfl_threshold_v3';
    const V3 = 'v3';
    const V3_DEFAULT_THRESHOLD = 0.5;
    const V3_DEFAULT_ACTION = 'Form';

    protected static function get_recaptcha_name()
    {
        return 'recaptcha_v3';
    }

    public static function get_site_key()
    {
        return get_option(self::OPTION_NAME_V3_SITE_KEY);
    }

    public static function get_secret_key()
    {
        return get_option(self::OPTION_NAME_V3_SECRET_KEY);
    }

    public static function get_recaptcha_type()
    {
        return self::V3;
    }

    public static function get_threshold(){

        return get_option(self::OPTION_NAME_RECAPTCHA_THRESHOLD);
    }

    public static function is_enabled()
    {
        return static::get_site_key() && static::get_secret_key();
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

        if (!wp_script_is($script_name, 'enqueued')) {
            wp_enqueue_script($script_name);
        }
	}

    public static function get_setup_message()
    {
        return esc_html__('To use reCAPTCHA V3, you need to add the API Key and complete the setup process in Dashboard > Elementor > Cool FormKit Lite > Settings > reCAPTCHA V3.', 'extensions-for-elementor-form');
    }

    public function render_field($item, $item_index, $widget)
    {


        $recaptcha_html = '<div class="cool-form-field" id="form-field-' . esc_attr($item['custom_id']) . '" >';

        if (static::is_enabled()) {
            $this->enqueue_scripts();

            $recaptcha_name = static::get_recaptcha_name();
            $badge = isset($item["recaptcha_badge"]) ? esc_attr($item["recaptcha_badge"]) : 'inline';

            $widget->add_render_attribute($recaptcha_name . $item_index, [
                'class' => 'cool-form-recaptcha',
                'data-sitekey' => static::get_site_key(),
                'data-action' => self::V3_DEFAULT_ACTION,
                'data-badge' => $badge,
                'data-recaptcha-version' => static::get_recaptcha_type(),
                'data-theme' => 'light',
                'data-size' => 'invisible',
            ]);

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
        $field_types['recaptcha_v3'] = esc_html__('reCAPTCHA V3', 'extensions-for-elementor-form');

        return $field_types;
    }

    public function filter_field_item($item)
    {
        if (static::get_recaptcha_name() === $item['field_type']) {
            $item['field_label'] = false;
        }

        return $item;
    }

    public function validation($record, $ajax_handler) {
        // Get reCAPTCHA fields from the form
        $fields = $record->get_field([
            'type' => static::get_recaptcha_name(),
        ]);
    
        if (empty($fields)) {
            return; // No reCAPTCHA field found, exit validation
        }
    
        $field = current($fields);
    
        // Get the reCAPTCHA token from the submitted form data
        // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
        $recaptcha_response = sanitize_text_field(wp_unslash($_POST['g-recaptcha-response'])) ?? '';
    
        if (empty($recaptcha_response)) {
            $ajax_handler->add_error($field['id'], esc_html__('Captcha validation failed. Please try again.', 'extensions-for-elementor-form'));
            return;
        }
    
        // Get reCAPTCHA secret key
        $recaptcha_secret = static::get_secret_key(); 
        if (empty($recaptcha_secret)) {
            $ajax_handler->add_error($field['id'], esc_html__('Missing reCAPTCHA secret key.', 'extensions-for-elementor-form'));
            return;
        }
    
        // Get client IP
        $client_ip = Utils::get_client_ip();
    
        // Prepare API request to verify reCAPTCHA response
        $request_args = [
            'body' => [
                'secret'   => $recaptcha_secret,
                'response' => $recaptcha_response,
                'remoteip' => $client_ip,
            ],
            'timeout' => 10, // Set timeout for the request
        ];
    
        $response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', $request_args);
        
        // Check for HTTP request errors
        if (is_wp_error($response)) {
            $ajax_handler->add_error($field['id'], esc_html__('Error verifying reCAPTCHA. Please try again.', 'extensions-for-elementor-form'));
            return;
        }
    
        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        $response_data = json_decode($response_body, true);
    
        if ($response_code !== 200 || empty($response_data['success'])) {
            $ajax_handler->add_error($field['id'], esc_html__('Captcha verification failed. Please try again.', 'extensions-for-elementor-form'));
            return;
        }
    
        // Check reCAPTCHA score (for v3)

        
        if(!static::get_threshold())
            $thres_hold = '0.5';
        else
            $thres_hold = (float) static::get_threshold();

        
        if (isset($response_data['score']) && $response_data['score'] < $thres_hold) {
            $ajax_handler->add_error($field['id'], esc_html__('Suspicious activity detected. Please try again.', 'extensions-for-elementor-form'));
            return;
        }
    
        // If validation passes, remove the field from processing
        $record->remove_field($field['id']);
    }

}

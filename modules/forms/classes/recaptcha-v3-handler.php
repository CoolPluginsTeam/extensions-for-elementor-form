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
        $threshold = get_option(self::OPTION_NAME_RECAPTCHA_THRESHOLD);
        return ( '' === $threshold || false === $threshold ) ? self::V3_DEFAULT_THRESHOLD : $threshold;
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

    protected function get_empty_response_message()
    {
        return esc_html__('Captcha validation failed. Please try again.', 'extensions-for-elementor-form');
    }

    protected function get_render_attributes($item)
    {
        $badge = isset($item['recaptcha_badge']) ? esc_attr($item['recaptcha_badge']) : 'inline';

        return [
            'class' => 'cool-form-recaptcha',
            'data-sitekey' => static::get_site_key(),
            'data-action' => self::V3_DEFAULT_ACTION,
            'data-badge' => $badge,
            'data-recaptcha-version' => static::get_recaptcha_type(),
            'data-theme' => 'light',
            'data-size' => 'invisible',
        ];
    }

    public function add_field_type($field_types)
    {
        $field_types['recaptcha_v3'] = esc_html__('reCAPTCHA V3', 'extensions-for-elementor-form');

        return $field_types;
    }

    protected function validate_result($result, $field)
    {
        if (!parent::validate_result($result, $field)) {
            return false;
        }

        $threshold = (float) static::get_threshold();
        if (isset($result['score']) && $result['score'] < $threshold) {
            return false;
        }

        return true;
    }

    protected function resolve_recaptcha_error_message($result)
    {
        $threshold = (float) static::get_threshold();
        if (isset($result['success'], $result['score']) && $result['success'] && $result['score'] < $threshold) {
            return esc_html__('Suspicious activity detected. Please try again.', 'extensions-for-elementor-form');
        }

        $message = parent::resolve_recaptcha_error_message($result);
        if (esc_html__('Invalid form, reCAPTCHA validation failed.', 'extensions-for-elementor-form') === $message) {
            return esc_html__('Captcha verification failed. Please try again.', 'extensions-for-elementor-form');
        }

        return $message;
    }
}

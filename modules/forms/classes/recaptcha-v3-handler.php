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
class Recaptcha_V3_Handler extends Recaptcha_Handler
{

    const OPTION_NAME_V3_SITE_KEY = 'elementor_pro_recaptcha_v3_site_key';
    const OPTION_NAME_V3_SECRET_KEY = 'elementor_pro_recaptcha_v3_secret_key';
    const OPTION_NAME_RECAPTCHA_THRESHOLD = 'elementor_pro_recaptcha_v3_threshold';
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

    public static function is_enabled()
    {
        return static::get_site_key() && static::get_secret_key();
    }

    public static function get_setup_message()
    {
        return esc_html__('To use reCAPTCHA V3, you need to add the API Key and complete the setup process in Dashboard > Elementor > Settings > Integrations > reCAPTCHA V3.', 'cool-formkit');
    }


    public function register_admin_fields(Settings $settings)
    {
        $settings->add_section(Settings::TAB_INTEGRATIONS, 'recaptcha_v3', [
            'label' => esc_html__('reCAPTCHA V3', 'cool-formkit'),
            'callback' => function () {
                echo sprintf(
                    /* translators: 1: Link opening tag, 2: Link closing tag. */
                    esc_html__('%1$sreCAPTCHA V3%2$s is a free service by Google that protects your website from spam and abuse. It does this while letting your valid users pass through with ease.', 'cool-formkit'),
                    '<a href="https://www.google.com/recaptcha/intro/v3.html" target="_blank">',
                    '</a>'
                );
            },
            'fields' => [
                'pro_recaptcha_v3_site_key' => [
                    'label' => esc_html__('Site Key', 'cool-formkit'),
                    'field_args' => [
                        'type' => 'text',
                    ],
                ],
                'pro_recaptcha_v3_secret_key' => [
                    'label' => esc_html__('Secret Key', 'cool-formkit'),
                    'field_args' => [
                        'type' => 'text',
                    ],
                ],
                'pro_recaptcha_v3_threshold' => [
                    'label' => esc_html__('Score Threshold', 'cool-formkit'),
                    'field_args' => [
                        'attributes' => [
                            'min' => 0,
                            'max' => 1,
                            'placeholder' => '0.5',
                            'step' => '0.1',
                        ],
                        'std' => 0.5,
                        'type' => 'number',
                        'desc' => esc_html__('Score threshold should be a value between 0 and 1, default: 0.5', 'cool-formkit'),
                    ],
                ],
            ],
        ]);
    }

    public function render_field($item, $item_index, $widget)
    {
        $recaptcha_html = '<div class="elementor-field" id="form-field-' . $item['custom_id'] . '" >';

        if (static::is_enabled()) {
            $this->enqueue_scripts();

            $recaptcha_name = static::get_recaptcha_name();

            // Get the widget settings for theme & size

            $badge = $item["recaptcha_badge"];

            // Add attributes dynamically
            $widget->add_render_attribute($recaptcha_name . $item_index, [
                'class' => 'g-recaptcha',
                'data-sitekey' => static::get_site_key(),
                'data-action' => 'Form',
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


        echo $recaptcha_html;
    }

    public function add_field_type($field_types)
    {
        $field_types['recaptcha_v3'] = esc_html__('reCAPTCHA V3', 'cool-formkit');

        return $field_types;
    }

    public function filter_field_item($item)
    {
        if (static::get_recaptcha_name() === $item['field_type']) {
            $item['field_label'] = false;
        }

        return $item;
    }

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
            $ajax_handler->add_error($field['id'], esc_html__('The Captcha validation failed. Please try again.', 'cool-formkit'));
            return;
        }

        $recaptcha_secret = static::get_secret_key();
        $client_ip = Utils::get_client_ip();

        // API Request to verify response
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
            $ajax_handler->add_error($field['id'], sprintf(
                esc_html__('Cannot connect to the reCAPTCHA server (%d).', 'cool-formkit'),
                $response_code
            ));
            return;
        }
        // If validation passes, remove the field from processing
        $record->remove_field($field['id']);
    }

    public function my_plugin_register_frontend_scripts()
	{

		// Localize script
		wp_localize_script('cool-formkit-recaptcha-handler', 'coolFormKitRecaptcha3', [
			'enabled'   => static::is_enabled(),
			'site_key'  => static::get_site_key(),
			'type'      => static::get_recaptcha_type(),
		]);
	}

    // public function my_plugin_enqueue_frontend_scripts(){
	// 	wp_enqueue_script('cool-formkit-recaptcha3-api', true);
	// 	wp_enqueue_script('cool-formkit-recaptcha3-handler', true);
	// }

}

<?php
namespace Cool_FormKit\Widgets\HelloPlusAddons;

use HelloPlus\Modules\Forms\Classes\Action_Base;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class HelloPlus_Whatsapp_Redirect
 */
class HelloPlus_Whatsapp_Redirect extends Action_Base {
	/**
	 * Get Name
	 *
	 * Return the action name
	 *
	 * @return string
	 */
	public function get_name():string {
		return 'whatsapp';
	}

	/**
	 * Get Label
	 *
	 * Returns the action label
	 *
	 * @return string
	 */
	public function get_label():string {
		return 'WhatsApp';
	}

	private static $registered_actions = [];
	/**
	 * Register Settings Section
	 *
	 * Registers the Action controls
	 *
	 * @param \Elementor\Widget_Base $widget
	 */
	public function register_settings_section( $widget ) {
		$control_id = 'section_whatsapp-redirect';
        
        if ( in_array( $control_id, self::$registered_actions, true ) ) {
            return; // Already registered
        }

        self::$registered_actions[] = $control_id;

		$widget->start_controls_section(
			'section_whatsapp-redirect',
			[
				'label' => \esc_html__( 'WhatsApp Redirect', 'cool-formkit' ),
				'condition' => [
					'cool_formkit_submit_actions' => $this->get_name(),
				],
			]
		);

		$widget->add_control(
			'whatsapp_to',
			[
				'label' => \esc_html__( 'WhatsApp Phone', 'cool-formkit' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => \esc_html__( '13459999999', 'cool-formkit' ),
				'label_block' => true,
				'render_type' => 'none',
				'classes' => 'elementor-control-whats-phone-direction-ltr',
				'description' => \esc_html__( 'Phone with country code, like: 5551999999999', 'cool-formkit' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$widget->add_control(
			'whatsapp_message',
			[
				'label' => \esc_html__( 'WhatsApp Message', 'cool-formkit' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'placeholder' => \esc_html__( 'Write yout text or use fields shortcode', 'cool-formkit' ),
				'label_block' => true,
				'render_type' => 'none',
				'classes' => 'elementor-control-whats-direction-ltr',
				'description' => \esc_html__( 'Use fields shortcodes for send form data or write your custom text.<br>=> To add break line use token: %break%', 'cool-formkit' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$widget->end_controls_section();
	}

	/**
	 * On Export
	 *
	 * Clears form settings on export
	 * @param array $element
	 */
	public function on_export( $element ) {
		unset(
			$element['settings']['whatsapp_to'],
			$element['settings']['whatsapp_message']
		);

		return $element;
	}

	/**
	 * Runs the action after submit
	 *
	 * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
	 * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
	 */
	public function run( $record, $ajax_handler ) {
		$whatsapp_to = $record->get_form_settings( 'whatsapp_to' );

		if(empty($whatsapp_to)){
			return;
		}

		$whatsapp_message = $record->get_form_settings( 'whatsapp_message' );

		$whatsapp_message = str_replace('%break%', '%0D%0A', $whatsapp_message);

		$whatsapp_to = 'https://wa.me/'.$whatsapp_to.'?text='.$whatsapp_message.'';
		$whatsapp_to = $record->replace_setting_shortcodes( $whatsapp_to, true );

		if ( ! empty( $whatsapp_to ) ) {
			$ajax_handler->add_response_data( 'redirect_url', $whatsapp_to );
		}
	}
}

<?php
namespace Cool_FormKit\Widgets\Addons;

use Cool_FormKit\Modules\Forms\Classes\Action_Base;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class CoolForm_Whatsapp_Redirect
 */
class CoolForm_Whatsapp_Redirect extends Action_Base {
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

	/**
	 * Register Settings Section
	 *
	 * Registers the Action controls
	 *
	 * @param \Elementor\Widget_Base $widget
	 */
	public function register_settings_section( $widget ) {
		$widget->start_controls_section(
			'section_whatsapp-redirect',
			[
				'label' => \esc_html__( 'WhatsApp Redirect', 'extensions-for-elementor-form' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);

		$widget->add_control(
			'whatsapp_to',
			[
				'label' => \esc_html__( 'WhatsApp Phone', 'extensions-for-elementor-form' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => \esc_html__( '13459999999', 'extensions-for-elementor-form' ),
				'label_block' => true,
				'render_type' => 'none',
				'classes' => 'elementor-control-whats-phone-direction-ltr',
				'description' => \esc_html__( 'Phone with country code, like: 5551999999999', 'extensions-for-elementor-form' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$widget->add_control(
			'whatsapp_message',
			[
				'label' => \esc_html__( 'WhatsApp Message', 'extensions-for-elementor-form' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'placeholder' => \esc_html__( 'Write yout text or use fields shortcode', 'extensions-for-elementor-form' ),
				'label_block' => true,
				'render_type' => 'none',
				'classes' => 'elementor-control-whats-direction-ltr',
				/* translators: %break% is a literal token used to insert a line break. */
				'description' => \esc_html__( 'Use fields shortcodes for send form data or write your custom text.<br>=> To add break line use token: %break%', 'extensions-for-elementor-form' ),
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

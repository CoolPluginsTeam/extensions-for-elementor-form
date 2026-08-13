<?php
/**
 * Shared WhatsApp Redirect action logic for Elementor Pro, Cool Form, and Hello Plus.
 *
 * Concrete classes must extend their platform Action_Base and use this trait.
 *
 * @package Cool_FormKit
 */

namespace Cool_FormKit\Includes\Actions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait Whatsapp_Redirect_Action_Trait {

	/**
	 * @var array
	 */
	private static $registered_actions = array();

	/**
	 * @var array<string, mixed>
	 */
	private $whatsapp_config = array(
		'submit_actions_key' => 'submit_actions',
		'guard_duplicate'    => false,
		'extra_conditions'   => array(),
		'bail_on_empty'      => true,
	);

	/**
	 * @param array<string, mixed> $config
	 * @return void
	 */
	protected function apply_whatsapp_config( array $config ): void {
		$this->whatsapp_config = array_merge( $this->whatsapp_config, $config );
	}

	/**
	 * Form settings key for submit actions (submit_actions vs cool_formkit_submit_actions).
	 *
	 * @return string
	 */
	protected function get_submit_actions_setting_key(): string {
		return (string) $this->whatsapp_config['submit_actions_key'];
	}

	/**
	 * Whether to skip re-registering the settings section for the same control ID.
	 *
	 * @return bool
	 */
	protected function should_guard_duplicate_section_registration(): bool {
		return (bool) $this->whatsapp_config['guard_duplicate'];
	}

	/**
	 * Whether to bail early when WhatsApp phone setting is empty.
	 *
	 * @return bool
	 */
	protected function should_bail_on_empty_whatsapp_to(): bool {
		return (bool) $this->whatsapp_config['bail_on_empty'];
	}

	/**
	 * Extra conditions applied to individual controls (Hello Plus).
	 *
	 * @return array
	 */
	protected function get_control_extra_conditions(): array {
		return is_array( $this->whatsapp_config['extra_conditions'] ) ? $this->whatsapp_config['extra_conditions'] : array();
	}

	public function get_name(): string {
		return 'whatsapp';
	}

	public function get_label(): string {
		return 'WhatsApp';
	}

	/**
	 * @param \Elementor\Widget_Base $widget
	 * @return void
	 */
	public function register_settings_section( $widget ) {
		$control_id = 'section_whatsapp-redirect';

		if ( $this->should_guard_duplicate_section_registration() ) {
			if ( in_array( $control_id, self::$registered_actions, true ) ) {
				return;
			}
			self::$registered_actions[] = $control_id;
		}

		$submit_key       = $this->get_submit_actions_setting_key();
		$extra_conditions = $this->get_control_extra_conditions();

		$section_args = array(
			'label'     => \esc_html__( 'WhatsApp Redirect', 'extensions-for-elementor-form' ),
			'condition' => array(
				$submit_key => $this->get_name(),
			),
		);

		$widget->start_controls_section( $control_id, $section_args );

		$phone_control = array(
			'label'       => \esc_html__( 'WhatsApp Phone', 'extensions-for-elementor-form' ),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'placeholder' => \esc_html__( '13459999999', 'extensions-for-elementor-form' ),
			'label_block' => true,
			'render_type' => 'none',
			'classes'     => 'elementor-control-whats-phone-direction-ltr',
			'description' => \esc_html__( 'Phone with country code, like: 5551999999999', 'extensions-for-elementor-form' ),
			'dynamic'     => array(
				'active' => true,
			),
		);

		$message_control = array(
			'label'       => \esc_html__( 'WhatsApp Message', 'extensions-for-elementor-form' ),
			'type'        => \Elementor\Controls_Manager::TEXTAREA,
			'placeholder' => \esc_html__( 'Write yout text or use fields shortcode', 'extensions-for-elementor-form' ),
			'label_block' => true,
			'render_type' => 'none',
			'classes'     => 'elementor-control-whats-direction-ltr',
			'description' => \esc_html__( 'Use fields shortcodes for send form data or write your custom text.<br>=> To add break line use token: %break%', 'extensions-for-elementor-form' ),
			'dynamic'     => array(
				'active' => true,
			),
		);

		if ( ! empty( $extra_conditions ) ) {
			$phone_control['condition']   = $extra_conditions;
			$message_control['condition'] = $extra_conditions;
		}

		$widget->add_control( 'whatsapp_to', $phone_control );
		$widget->add_control( 'whatsapp_message', $message_control );

		$widget->end_controls_section();
	}

	/**
	 * @param array $element
	 * @return array
	 */
	public function on_export( $element ) {
		unset(
			$element['settings']['whatsapp_to'],
			$element['settings']['whatsapp_message']
		);

		return $element;
	}

	/**
	 * @param mixed $record
	 * @param mixed $ajax_handler
	 * @return void
	 */
	public function run( $record, $ajax_handler ) {
		$whatsapp_to = $record->get_form_settings( 'whatsapp_to' );

		if ( $this->should_bail_on_empty_whatsapp_to() && empty( $whatsapp_to ) ) {
			return;
		}

		$whatsapp_message = $record->get_form_settings( 'whatsapp_message' );

		$fields = $record->get( 'fields' );
		$form_data = array();
		$field_metadata = array();
		if ( is_array( $fields ) ) {
			foreach ( $fields as $id => $field ) {
				$form_data[ $id ] = $field['value'] ?? '';
				$field_metadata[ $id ] = array(
					'label' => $field['title'] ?? '',
				);
			}
		}

		$whatsapp_to      = cfl_replace_whatsapp_message_shortcodes( (string) $whatsapp_to, $form_data, $field_metadata );
		$whatsapp_message = cfl_replace_whatsapp_message_shortcodes( (string) $whatsapp_message, $form_data, $field_metadata );
		$whatsapp_message = cfl_replace_whatsapp_break_token( $whatsapp_message );

		$whatsapp_to = add_query_arg(
			array( 'text' => $whatsapp_message ),
			'https://wa.me/' . rawurlencode( $whatsapp_to )
		);

		if ( ! empty( $whatsapp_to ) ) {
			$ajax_handler->add_response_data( 'redirect_url', $whatsapp_to );
		}
	}
}

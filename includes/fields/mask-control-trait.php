<?php
/**
 * Shared Mask Control addon logic for Elementor Pro, Cool Form, and Hello Plus.
 *
 * Concrete classes are standalone (do not extend Field_Base).
 * Call init_mask_control() from the constructor.
 *
 * @package Cool_FormKit
 */

namespace Cool_FormKit\Includes\Fields;

use Elementor\Controls_Manager as ElementorControls;
use Elementor\Repeater as ElementorRepeater;
use Cool_FormKit\Includes\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait Mask_Control_Trait {

	/**
	 * @var array
	 */
	public $allowed_fields = array(
		'text',
	);

	/**
	 * Form fields section hook for injecting controls.
	 *
	 * @return string
	 */
	abstract protected function get_form_fields_section_hook(): string;

	/**
	 * Forms render/item filter hook.
	 *
	 * @return string
	 */
	abstract protected function get_render_item_hook(): string;

	/**
	 * Action fired after mask attributes are applied.
	 *
	 * @return string
	 */
	abstract protected function get_after_mask_attribute_action(): string;

	/**
	 * Elementor plugin instance exposing controls_manager.
	 *
	 * @return object
	 */
	abstract protected function get_elementor_plugin();

	/**
	 * Apply platform-specific mask attributes to the field / widget.
	 *
	 * @param array  $field        Field settings.
	 * @param string $field_index  Field index.
	 * @param mixed  $form_widget  Form renderer / form object.
	 * @return array
	 */
	abstract protected function apply_mask_attributes( $field, $field_index, $form_widget );

	/**
	 * Whether in_array for field_type should use strict comparison.
	 *
	 * @return bool
	 */
	protected function use_strict_field_type_check(): bool {
		return false;
	}

	/**
	 * Shared constructor body. Call from the concrete class constructor.
	 */
	protected function init_mask_control(): void {
		add_action( $this->get_form_fields_section_hook(), array( $this, 'add_mask_control' ), 100, 2 );
		add_filter( $this->get_render_item_hook(), array( $this, 'add_mask_attributes' ), 10, 3 );
	}

	/**
	 * Add mask control
	 *
	 * @since 1.0
	 * @param mixed $element
	 * @param mixed $args
	 */
	public function add_mask_control( $element, $args ) {
		$elementor    = $this->get_elementor_plugin();
		$control_data = $elementor->controls_manager->get_control_from_stack( $element->get_name(), 'form_fields' );

		if ( is_wp_error( $control_data ) ) {
			return;
		}

		$controls_to_register = array(
			'fme_mask_control'              => array(
				'label'         => esc_html__( 'Mask Control', 'extensions-for-elementor-form' ),
				'type'          => ElementorControls::SELECT,
				'tab'           => 'content',
				'tabs_wrapper'  => 'form_fields_tabs',
				'inner_tab'     => 'form_fields_advanced_tab',
				'default'       => 'mask',
				'options'       => array(
					'mask'          => esc_html__( 'Select Mask', 'extensions-for-elementor-form' ),
					'ev-phone'      => esc_html__( 'Phone', 'extensions-for-elementor-form' ),
					'ev-time'       => esc_html__( 'Date & Time', 'extensions-for-elementor-form' ),
					'ev-money'      => esc_html__( 'Money', 'extensions-for-elementor-form' ),
					'ev-ccard'      => esc_html__( 'Credit Card', 'extensions-for-elementor-form' ),
					'ev-br_fr'      => esc_html__( 'Brazilian Formats', 'extensions-for-elementor-form' ),
					'ev-ip-address' => esc_html__( 'IP Address', 'extensions-for-elementor-form' ),
				),
				'conditions'    => array(
					'terms' => array(
						array(
							'name'     => 'field_type',
							'operator' => 'in',
							'value'    => $this->allowed_fields,
						),
					),
				),
			),
			'fme_mask_auto_placeholders'    => array(
				'label'         => esc_html__( 'Mask Placeholders', 'extensions-for-elementor-form' ),
				'type'          => ElementorControls::SWITCHER,
				'tab'           => 'content',
				'tabs_wrapper'  => 'form_fields_tabs',
				'inner_tab'     => 'form_fields_advanced_tab',
				'default'       => '',
				'label_on'      => esc_html__( 'On', 'extensions-for-elementor-form' ),
				'label_off'     => esc_html__( 'Off', 'extensions-for-elementor-form' ),
				'conditions'    => array(
					'terms' => array(
						array(
							'name'     => 'fme_mask_control',
							'operator' => 'in',
							'value'    => array( 'ev-phone', 'ev-cpf', 'ev-cnpj', 'ev-money', 'ev-ccard', 'ev-cep', 'ev-time', 'ev-ip-address', 'ev-br_fr' ),
						),
						array(
							'name'     => 'field_type',
							'operator' => 'in',
							'value'    => $this->allowed_fields,
						),
					),
				),
			),
			'fme_money_mask_format'         => array(
				'label'         => esc_html__( 'Thousand separator', 'extensions-for-elementor-form' ),
				'type'          => ElementorControls::SELECT,
				'tab'           => 'content',
				'tabs_wrapper'  => 'form_fields_tabs',
				'inner_tab'     => 'form_fields_advanced_tab',
				'default'       => 'dot',
				'options'       => array(
					'dot'   => esc_html__( 'Dot (.)', 'extensions-for-elementor-form' ),
					'comma' => esc_html__( 'Comma (,)', 'extensions-for-elementor-form' ),
				),
				'conditions'    => array(
					'terms' => array(
						array(
							'name'     => 'fme_mask_control',
							'operator' => 'in',
							'value'    => array( 'ev-money' ),
						),
						array(
							'name'     => 'field_type',
							'operator' => 'in',
							'value'    => $this->allowed_fields,
						),
					),
				),
			),
			'fme_money_mask_prefix'         => array(
				'label'         => esc_html__( 'Mask Prefix', 'extensions-for-elementor-form' ),
				'type'          => ElementorControls::TEXT,
				'tab'           => 'content',
				'tabs_wrapper'  => 'form_fields_tabs',
				'inner_tab'     => 'form_fields_advanced_tab',
				'default'       => '',
				'ai'            => array(
					'active' => false,
				),
				'conditions'    => array(
					'terms' => array(
						array(
							'name'     => 'fme_mask_control',
							'operator' => 'in',
							'value'    => array( 'ev-money' ),
						),
						array(
							'name'     => 'field_type',
							'operator' => 'in',
							'value'    => $this->allowed_fields,
						),
					),
				),
			),
			'fme_money_mask_decimal_places' => array(
				'label'         => esc_html__( 'Mask Decimal Places', 'extensions-for-elementor-form' ),
				'type'          => ElementorControls::TEXT,
				'tab'           => 'content',
				'tabs_wrapper'  => 'form_fields_tabs',
				'inner_tab'     => 'form_fields_advanced_tab',
				'default'       => '2',
				'ai'            => array(
					'active' => false,
				),
				'conditions'    => array(
					'terms' => array(
						array(
							'name'     => 'fme_mask_control',
							'operator' => 'in',
							'value'    => array( 'ev-money' ),
						),
						array(
							'name'     => 'field_type',
							'operator' => 'in',
							'value'    => $this->allowed_fields,
						),
					),
				),
			),
			'fme_time_mask_format'          => array(
				'label'         => esc_html__( 'Date Format', 'extensions-for-elementor-form' ),
				'type'          => ElementorControls::SELECT,
				'tab'           => 'content',
				'tabs_wrapper'  => 'form_fields_tabs',
				'inner_tab'     => 'form_fields_advanced_tab',
				'default'       => 'one',
				'options'       => array(
					'three' => esc_html__( 'Date (dd/mm/yyyy)', 'extensions-for-elementor-form' ),
					'four'  => esc_html__( 'Date (mm/dd/yyyy)', 'extensions-for-elementor-form' ),
					'five'  => esc_html__( 'DateTime (dd/mm/yyyy hh:mm)', 'extensions-for-elementor-form' ),
					'six'   => esc_html__( 'DateTime (mm/dd/yyyy hh:mm)', 'extensions-for-elementor-form' ),
					'one'   => esc_html__( 'Time (hh:mm)', 'extensions-for-elementor-form' ),
					'two'   => esc_html__( 'Time (hh:mm:ss)', 'extensions-for-elementor-form' ),
					'seven' => esc_html__( 'Month/Year (mm/yyyy)', 'extensions-for-elementor-form' ),
				),
				'conditions'    => array(
					'terms' => array(
						array(
							'name'     => 'fme_mask_control',
							'operator' => 'in',
							'value'    => array( 'ev-time' ),
						),
						array(
							'name'     => 'field_type',
							'operator' => 'in',
							'value'    => $this->allowed_fields,
						),
					),
				),
			),
			'fme_brazilian_formats'         => array(
				'label'         => esc_html__( 'Select Format', 'extensions-for-elementor-form' ),
				'type'          => ElementorControls::SELECT,
				'tab'           => 'content',
				'tabs_wrapper'  => 'form_fields_tabs',
				'inner_tab'     => 'form_fields_advanced_tab',
				'default'       => 'fme_cpf',
				'options'       => array(
					'fme_cpf'  => esc_html__( 'CPF', 'extensions-for-elementor-form' ),
					'fme_cnpj' => esc_html__( 'CNPJ', 'extensions-for-elementor-form' ),
					'fme_cep'  => esc_html__( 'CEP', 'extensions-for-elementor-form' ),
				),
				'conditions'    => array(
					'terms' => array(
						array(
							'name'     => 'fme_mask_control',
							'operator' => 'in',
							'value'    => array( 'ev-br_fr' ),
						),
						array(
							'name'     => 'field_type',
							'operator' => 'in',
							'value'    => $this->allowed_fields,
						),
					),
				),
			),
			'fme_credit_card_options'       => array(
				'label'         => esc_html__( 'Credit Card Options', 'extensions-for-elementor-form' ),
				'type'          => ElementorControls::SELECT,
				'tab'           => 'content',
				'tabs_wrapper'  => 'form_fields_tabs',
				'inner_tab'     => 'form_fields_advanced_tab',
				'default'       => 'hyphen',
				'options'       => array(
					'space'                   => esc_html__( 'Credit card with space', 'extensions-for-elementor-form' ),
					'hyphen'                  => esc_html__( 'Credit card with hyphen', 'extensions-for-elementor-form' ),
					'credit_card_date'        => esc_html__( 'Expiry Date (MM/YY)', 'extensions-for-elementor-form' ),
					'credit_card_expiry_date' => esc_html__( 'Expiry Date (MM/YYYY)', 'extensions-for-elementor-form' ),
				),
				'conditions'    => array(
					'terms' => array(
						array(
							'name'     => 'fme_mask_control',
							'operator' => 'in',
							'value'    => array( 'ev-ccard' ),
						),
						array(
							'name'     => 'field_type',
							'operator' => 'in',
							'value'    => $this->allowed_fields,
						),
					),
				),
			),
			'fme_phone_format'              => array(
				'label'         => esc_html__( 'Phone Format', 'extensions-for-elementor-form' ),
				'type'          => ElementorControls::SELECT,
				'tab'           => 'content',
				'tabs_wrapper'  => 'form_fields_tabs',
				'inner_tab'     => 'form_fields_advanced_tab',
				'default'       => 'phone_usa',
				'options'       => array(
					'phone_usa'  => esc_html__( 'Phone (USA)', 'extensions-for-elementor-form' ),
					'phone_d8'   => esc_html__( 'Phone (8-digit)', 'extensions-for-elementor-form' ),
					'phone_ddd8' => esc_html__( 'Phone (DDD + 8-digit)', 'extensions-for-elementor-form' ),
					'phone_ddd9' => esc_html__( 'Phone (DDD + 9-digit)', 'extensions-for-elementor-form' ),
				),
				'conditions'    => array(
					'terms' => array(
						array(
							'name'     => 'fme_mask_control',
							'operator' => 'in',
							'value'    => array( 'ev-phone' ),
						),
						array(
							'name'     => 'field_type',
							'operator' => 'in',
							'value'    => $this->allowed_fields,
						),
					),
				),
			),
		);

		/**
		 * Filter to pro version change control.
		 *
		 * @since 1.5
		 */
		$controls_to_register = apply_filters( 'fme_after_mask_control_created', $controls_to_register );

		$controls_repeater = new ElementorRepeater();
		foreach ( $controls_to_register as $key => $control ) {
			$controls_repeater->add_control( $key, $control );
		}

		$pattern_field = $controls_repeater->get_controls();

		/**
		 * Register control in form advanced tab.
		 *
		 * @since 1.5.2
		 */
		Utils::register_control_in_form_advanced_tab( $element, $control_data, $pattern_field );
	}

	/**
	 * Render/add new mask attributes on input field.
	 *
	 * @since 1.0
	 * @param array  $field
	 * @param string $field_index
	 * @param mixed  $form_widget
	 * @return array
	 */
	public function add_mask_attributes( $field, $field_index, $form_widget ) {
		if (
			! empty( $field['fme_mask_control'] ) &&
			in_array( $field['field_type'], $this->allowed_fields, $this->use_strict_field_type_check() ) &&
			$field['fme_mask_control'] !== 'mask'
		) {
			$field = $this->apply_mask_attributes( $field, $field_index, $form_widget );
		}

		/**
		 * After mask attribute added
		 *
		 * Action fired to allow pro version to add custom attributes.
		 *
		 * @since 1.5.2
		 */
		do_action( $this->get_after_mask_attribute_action(), $field, $field_index, $form_widget );

		return $field;
	}
}

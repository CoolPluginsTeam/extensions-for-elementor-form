<?php
/**
 * Configurable mask-control addon (Cool Form, Hello Plus, Elementor Pro).
 *
 * @package Cool_FormKit
 */

namespace Cool_FormKit\Includes\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/mask-control-trait.php';
require_once __DIR__ . '/configurable-addon-trait.php';

class Mask_Control_Addon {
	use Mask_Control_Trait;
	use Configurable_Addon_Trait;

	/**
	 * @param array<string, mixed> $config
	 */
	public function __construct( array $config ) {
		$this->addon_config = $config;
		$this->init_mask_control();
	}

	/**
	 * @return array<string, mixed>
	 */
	public static function coolform_config(): array {
		return array(
			'elementor'                 => 'elementor',
			'form_fields_section'       => 'elementor/element/cool-form/section_form_fields/before_section_end',
			'render_item_hook'          => 'cool_formkit/forms/render/item',
			'after_mask_attribute'      => 'coolform_fme_after_mask_attribute_added',
			'apply_strategy'            => 'coolform',
			'strict_field_type_check'   => true,
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	public static function helloplus_config(): array {
		return array(
			'elementor'            => 'elementor',
			'form_fields_section'  => 'elementor/element/ehp-form/section_form_fields/before_section_end',
			'render_item_hook'     => 'hello_plus/forms/render/item',
			'after_mask_attribute' => 'helloplus_after_mask_attribute_added',
			'apply_strategy'       => 'helloplus',
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	public static function elementor_config(): array {
		return array(
			'elementor'            => 'elementor',
			'form_fields_section'  => 'elementor/element/form/section_form_fields/before_section_end',
			'render_item_hook'     => 'elementor_pro/forms/render/item',
			'after_mask_attribute' => 'fme_after_mask_attribute_added',
			'apply_strategy'       => 'elementor',
		);
	}

	protected function get_form_fields_section_hook(): string {
		return (string) $this->cfg( 'form_fields_section' );
	}

	protected function get_render_item_hook(): string {
		return (string) $this->cfg( 'render_item_hook' );
	}

	protected function get_after_mask_attribute_action(): string {
		return (string) $this->cfg( 'after_mask_attribute' );
	}

	protected function get_elementor_plugin() {
		return $this->resolve_elementor_plugin();
	}

	protected function use_strict_field_type_check(): bool {
		return (bool) $this->cfg( 'strict_field_type_check', false );
	}

	/**
	 * @param array  $field
	 * @param string $field_index
	 * @param mixed  $form_widget
	 * @return array
	 */
	protected function apply_mask_attributes( $field, $field_index, $form_widget ) {
		$strategy = $this->cfg( 'apply_strategy', 'elementor' );

		if ( 'coolform' === $strategy ) {
			$classes = array_filter(
				array(
					'fme-mask-input',
					'mask_control_@' . $field['fme_mask_control'],
					'money_mask_format_@' . ( $field['fme_money_mask_format'] ?? '' ),
					'mask_prefix_@' . ( $field['fme_money_mask_prefix'] ?? '' ),
					'mask_decimal_places_@' . ( $field['fme_money_mask_decimal_places'] ?? '' ),
					'mask_time_mask_format_@' . ( $field['fme_time_mask_format'] ?? '' ),
					'fme_phone_format_@' . ( $field['fme_phone_format'] ?? '' ),
					'credit_card_options_@' . ( $field['fme_credit_card_options'] ?? '' ),
					'mask_auto_placeholder_@' . ( $field['fme_mask_auto_placeholders'] ?? '' ),
					'fme_brazilian_formats_@' . ( $field['fme_brazilian_formats'] ?? '' ),
				)
			);

			$field['custom_mask_attributes'] = array(
				'data-mask' => $field['fme_mask_control'],
				'class'     => implode( ' ', $classes ),
			);

			return $field;
		}

		$target = $form_widget;
		if ( 'helloplus' === $strategy ) {
			try {
				$reflection = new \ReflectionClass( $form_widget );
				$property   = $reflection->getProperty( 'widget' );
				$property->setAccessible( true );
				$target = $property->getValue( $form_widget );
			} catch ( \ReflectionException $e ) {
				return $field;
			}
		}

		if ( ! is_object( $target ) || ! method_exists( $target, 'add_render_attribute' ) ) {
			return $field;
		}

		$target->add_render_attribute(
			'input' . $field_index,
			'data-mask',
			$field['fme_mask_control']
		);

		$target->add_render_attribute(
			'input' . $field_index,
			'class',
			'fme-mask-input ' .
			'mask_control_@' . $field['fme_mask_control'] . ' ' .
			'money_mask_format_@' . ( $field['fme_money_mask_format'] ?? '' ) . ' ' .
			'mask_prefix_@' . ( $field['fme_money_mask_prefix'] ?? '' ) . ' ' .
			'mask_decimal_places_@' . ( $field['fme_money_mask_decimal_places'] ?? '' ) . ' ' .
			'mask_time_mask_format_@' . ( $field['fme_time_mask_format'] ?? '' ) . ' ' .
			'fme_phone_format_@' . ( $field['fme_phone_format'] ?? '' ) . ' ' .
			'credit_card_options_@' . ( $field['fme_credit_card_options'] ?? '' ) . ' ' .
			'mask_auto_placeholder_@' . ( $field['fme_mask_auto_placeholders'] ?? '' ) . ' ' .
			'fme_brazilian_formats_@' . ( $field['fme_brazilian_formats'] ?? '' )
		);

		return $field;
	}
}

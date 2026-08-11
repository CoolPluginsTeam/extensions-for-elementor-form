<?php

namespace Cool_FormKit\Widgets\HelloPlusAddons;

require_once CFL_PLUGIN_PATH . 'includes/fields/mask-control-trait.php';

use Cool_FormKit\Includes\Fields\Mask_Control_Trait;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Hello Plus Mask Control addon.
 */
class FME_Elementor_Forms_Mask {
	use Mask_Control_Trait;

	public function __construct() {
		$this->init_mask_control();
	}

	protected function get_form_fields_section_hook(): string {
		return 'elementor/element/ehp-form/section_form_fields/before_section_end';
	}

	protected function get_render_item_hook(): string {
		return 'hello_plus/forms/render/item';
	}

	protected function get_after_mask_attribute_action(): string {
		return 'helloplus_after_mask_attribute_added';
	}

	protected function get_elementor_plugin() {
		return \Elementor\Plugin::instance();
	}

	/**
	 * @param array  $field
	 * @param string $field_index
	 * @param mixed  $form_widget
	 * @return array
	 */
	protected function apply_mask_attributes( $field, $field_index, $form_widget ) {
		try {
			$reflection = new \ReflectionClass( $form_widget );
			$property   = $reflection->getProperty( 'widget' );
			$property->setAccessible( true );
			$widget = $property->getValue( $form_widget );
		} catch ( \ReflectionException $e ) {
			return $field;
		}

		$widget->add_render_attribute(
			'input' . $field_index,
			'data-mask',
			$field['fme_mask_control']
		);

		$widget->add_render_attribute(
			'input' . $field_index,
			'class',
			'fme-mask-input ' .
			'mask_control_@' . $field['fme_mask_control'] . ' ' .
			'money_mask_format_@' . $field['fme_money_mask_format'] . ' ' .
			'mask_prefix_@' . $field['fme_money_mask_prefix'] . ' ' .
			'mask_decimal_places_@' . $field['fme_money_mask_decimal_places'] . ' ' .
			'mask_time_mask_format_@' . $field['fme_time_mask_format'] . ' ' .
			'fme_phone_format_@' . $field['fme_phone_format'] . ' ' .
			'credit_card_options_@' . $field['fme_credit_card_options'] . ' ' .
			'mask_auto_placeholder_@' . $field['fme_mask_auto_placeholders'] . ' ' .
			'fme_brazilian_formats_@' . $field['fme_brazilian_formats']
		);

		return $field;
	}
}

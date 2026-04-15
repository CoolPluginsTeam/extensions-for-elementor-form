<?php
namespace Cool_FormKit\Widgets\AtomicForm\Checkbox;

use Cool_FormKit\Widgets\AtomicForm\Input\Conditional_Input_Definition;
use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\Controls\Types\Switch_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Text_Control;
use Elementor\Modules\AtomicWidgets\Elements\Base\Has_Template;
use Elementor\Modules\AtomicWidgets\PropTypes\Attributes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Classes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Boolean_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;
use Elementor\Modules\Components\PropTypes\Overridable_Prop_Type;
use ElementorPro\Modules\AtomicForm\Checkbox\Checkbox as AtomicFormCheckbox;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once CFL_PLUGIN_PATH . 'widgets/atomic-form/field-controls-definition/conditional-input-definition.php';

class Checkbox extends AtomicFormCheckbox {
	use Has_Template;

	public static $widget_description = 'Display a checkbox input with required, readonly, and attributes.';

	public static function get_element_type(): string {
		return 'e-form-checkbox';
	}

	public function get_title(): string {
		return esc_html__( 'Checkbox', 'elementor-pro' );
	}

	public function get_icon(): string {
		return 'eicon-atomic-checkbox';
	}

	public function get_categories(): array {
		return [ 'atomic-form' ];
	}

	public function get_keywords() {
		return [ 'atomic', 'form', 'checkbox' ];
	}

	protected static function define_props_schema(): array {
		return array_merge( [
			'classes' => Classes_Prop_Type::make()
				->default( [] ),
			'name' => String_Prop_Type::make()
				->default( '' ),
			'value' => String_Prop_Type::make()
				->default( '' ),
			'required' => Boolean_Prop_Type::make()
				->default( false ),
			'checked' => Boolean_Prop_Type::make()
				->default( false ),
			'attributes' => Attributes_Prop_Type::make()->meta( Overridable_Prop_Type::ignore() ),
		], Conditional_Input_Definition::props_schema() );
	}

	protected function define_atomic_controls(): array {
		return [
			Section::make()
				->set_label( __( 'Content', 'elementor-pro' ) )
				->set_items( [
					Text_Control::bind_to( 'name' )
						->set_label( __( 'Group name', 'elementor-pro' ) )
						->set_placeholder( __( 'Enter checkbox group name', 'elementor-pro' ) )
						->set_meta( [
							'layout' => 'two-columns',
						] ),
					Text_Control::bind_to( 'value' )
						->set_label( __( 'Choice value', 'elementor-pro' ) )
						->set_placeholder( __( 'Enter choice value', 'elementor-pro' ) )
						->set_meta( [
							'layout' => 'two-columns',
						] ),
					Switch_Control::bind_to( 'required' )
						->set_label( __( 'Required', 'elementor-pro' ) ),
					Switch_Control::bind_to( 'checked' )
						->set_label( __( 'Checked', 'elementor-pro' ) ),
				] ),
			Section::make()
				->set_label( __( 'Settings', 'elementor-pro' ) )
				->set_id( 'settings' )
				->set_items( $this->get_settings_controls() ),
			Conditional_Input_Definition::conditions_section(),
		];
	}

	protected function get_templates(): array {
		return [
			'checkbox' => __DIR__ . '/checkbox.html.twig',
		];
	}

}

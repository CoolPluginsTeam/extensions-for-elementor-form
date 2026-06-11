<?php
namespace Cool_FormKit\Widgets\AtomicForm\Time_Picker;

use Cool_FormKit\Widgets\AtomicForm\Input\Conditional_Input_Definition;
use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\Controls\Types\Switch_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Time_Range_Control;
use Elementor\Modules\AtomicWidgets\Elements\Base\Has_Template;
use Elementor\Modules\AtomicWidgets\PropTypes\Attributes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Classes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Boolean_Prop_Type;
use Elementor\Modules\Components\PropTypes\Overridable_Prop_Type;
use ElementorPro\Modules\AtomicForm\Default_Id_Provider;
use Elementor\Modules\AtomicWidgets\PropTypes\Time_Range_Prop_Type;
use ElementorPro\Modules\AtomicForm\Time_Picker\Time_Picker as AtomicFormTimePicker;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once CFL_PLUGIN_PATH . 'widgets/atomic-form/field-controls-definition/conditional-input-definition.php';

class Time_Picker extends AtomicFormTimePicker {
	use Has_Template;

	public static $widget_description = 'Display a time picker input with required, min, max, and attributes.';

	public static function get_element_type(): string {
		return 'e-form-time-picker';
	}

	public function get_title(): string {
		return esc_html__( 'Time picker', 'extensions-for-elementor-form' );
	}

	public function get_icon(): string {
		return 'eicon-atomic-time-picker';
	}

	public function get_categories(): array {
		return [ 'atomic-form' ];
	}

	public function get_keywords() {
		return [ 'atomic', 'form', 'time', 'picker' ];
	}

	protected static function define_props_schema(): array {
		$schema = [
			'classes' => Classes_Prop_Type::make()
				->default( [] ),
			'min_max' => Time_Range_Prop_Type::make(),
			'required' => Boolean_Prop_Type::make()
				->default( false ),
			'attributes' => Attributes_Prop_Type::make()->meta( Overridable_Prop_Type::ignore() ),
			'_cssid' => Default_Id_Provider::get_default_id_prop( self::get_element_type() ),
		];

		if ( Conditional_Input_Definition::is_conditional_logic_enabled() ) {
			$schema = array_merge( $schema, Conditional_Input_Definition::props_schema() );
		}

		return $schema;
	}

	protected function define_atomic_controls(): array {
		$sections = [
			Section::make()
				->set_label( __( 'Content', 'extensions-for-elementor-form' ) )
				->set_items( [
					Time_Range_Control::bind_to( 'min_max' )
						->set_meta( [
							'layout' => 'custom',
						] ),
					Switch_Control::bind_to( 'required' )
						->set_label( __( 'Required', 'extensions-for-elementor-form' ) ),
				] ),
			Section::make()
				->set_label( __( 'Settings', 'extensions-for-elementor-form' ) )
				->set_id( 'settings' )
				->set_items( $this->get_settings_controls() ),
		];

		if ( Conditional_Input_Definition::is_conditional_logic_enabled() ) {
			$sections[] = Conditional_Input_Definition::conditions_section();
		}

		return $sections;
	}

	protected function get_templates(): array {
		return [
			'time-picker' => __DIR__ . '/time-picker.html.twig',
		];
	}

}

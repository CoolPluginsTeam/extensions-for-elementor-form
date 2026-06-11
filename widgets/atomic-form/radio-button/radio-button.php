<?php
namespace Cool_FormKit\Widgets\AtomicForm\Radio_Button;

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
use ElementorPro\Modules\AtomicForm\Default_Id_Provider;
use ElementorPro\Modules\AtomicForm\Radio_Button\Radio_Button as AtomicFormRadioButton;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once CFL_PLUGIN_PATH . 'widgets/atomic-form/field-controls-definition/conditional-input-definition.php';

class Radio_Button extends AtomicFormRadioButton {
	use Has_Template;

	protected $animation_duration = 200;
	public static $widget_description = 'Display a radio button input with required, and attributes.';

	public static function get_element_type(): string {
		return 'e-form-radio-button';
	}

	public function get_title(): string {
		return esc_html__( 'Radio button', 'extensions-for-elementor-form' );
	}

	public function get_icon(): string {
		return 'eicon-atomic-radiobutton';
	}

	public function get_categories(): array {
		return [ 'atomic-form' ];
	}

	public function get_keywords() {
		return [ 'atomic', 'form', 'radio', 'button' ];
	}

	protected static function define_props_schema(): array {

		$schema = [
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
						Text_Control::bind_to( 'name' )
							->set_label( __( 'Group name', 'extensions-for-elementor-form' ) )
							->set_placeholder( __( 'Enter radio button group name', 'extensions-for-elementor-form' ) )
							->set_meta( [
								'layout' => 'two-columns',
							] ),
						Text_Control::bind_to( 'value' )
							->set_label( __( 'Choice value', 'extensions-for-elementor-form' ) )
							->set_placeholder( __( 'Enter choice value', 'extensions-for-elementor-form' ) )
							->set_meta( [
								'layout' => 'two-columns',
							] ),
						Switch_Control::bind_to( 'required' )
							->set_label( __( 'Required', 'extensions-for-elementor-form' ) ),
						Switch_Control::bind_to( 'checked' )
							->set_label( __( 'Checked', 'extensions-for-elementor-form' ) ),
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
			'radio_button' => __DIR__ . '/radio-button.html.twig',
		];
	}

}

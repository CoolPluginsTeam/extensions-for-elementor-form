<?php
namespace Cool_FormKit\Widgets\AtomicForm\Select;

use Cool_FormKit\Widgets\AtomicForm\Input\Conditional_Input_Definition;
use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\Controls\Types\Switch_Control;
use Elementor\Modules\AtomicWidgets\Elements\Base\Has_Template;
use Elementor\Modules\AtomicWidgets\PropTypes\Attributes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Classes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Boolean_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;
use Elementor\Modules\Components\PropTypes\Overridable_Prop_Type;
use ElementorPro\Modules\Attributes\Controls\Repeatable_Attributes_Control;
use Elementor\Modules\AtomicWidgets\PropTypes\Key_Value_Prop_Type;
use ElementorPro\Modules\AtomicForm\Default_Id_Provider;
use Elementor\Modules\AtomicWidgets\PropTypes\Options_Prop_Type;
use ElementorPro\Modules\AtomicForm\Select\Select as AtomicFormSelect;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly or if Options_Prop_Type is not available
}

require_once CFL_PLUGIN_PATH . 'widgets/atomic-form/field-controls-definition/conditional-input-definition.php';

class Select extends AtomicFormSelect {
	use Has_Template;

	public static $widget_description = 'Display a select with options';

	public static function get_element_type(): string {
		return 'e-form-select';
	}

	public function get_title(): string {
		return esc_html__( 'Select', 'elementor-pro' );
	}

	public function get_icon(): string {
		return 'eicon-atomic-select';
	}

	public function get_categories(): array {
		return [ 'atomic-form' ];
	}

	public function get_keywords() {
		return [ 'atomic', 'form', 'select', 'dropdown' ];
	}

	protected static function define_props_schema(): array {

		$schema = [
			'classes' => Classes_Prop_Type::make()
				->default( [] ),
			'name' => String_Prop_Type::make()
				->default( '' ),
			'options' => Options_Prop_Type::make()
				->default( [
					Key_Value_Prop_Type::generate( [
						'key' => String_Prop_Type::generate( 'Select from the list' ),
						'value' => String_Prop_Type::generate( 'empty' ),
					] ),
				] ),
			'required' => Boolean_Prop_Type::make()
				->default( false ),
			'multiple' => Boolean_Prop_Type::make()
				->default( false ),
			'attributes' => Attributes_Prop_Type::make()->meta( Overridable_Prop_Type::ignore() ),
			'_cssid' => Default_Id_Provider::get_default_id_prop( self::get_element_type() ),
		];

		if ( Conditional_Input_Definition::is_conditional_logic_enabled() ) {
			$schema = array_merge( $schema, Conditional_Input_Definition::props_schema() );
		}

		return $schema;
	}

	private function define_options_control() {
		$control = Repeatable_Attributes_Control::bind_to( 'options' )
			->set_child_control_props( (object) [] )
			->set_repeaterLabel( __( 'Options', 'elementor-pro' ) )
			->set_patternLabel( '${value.key.value} (${value.value.value})' )
			->set_placeholder( 'Empty option' )
			->set_child_control_type( 'options' )
			->hide_toggle()
			->set_prop_key( 'options' )
			->set_is_sortable( true )
			->set_add_item_tooltip_props( [
				'newItemIndex' => null,
			] )
			->set_initialValues( [] );

		return $control;
	}

	protected function define_atomic_controls(): array {
		$sections = [
			Section::make()
				->set_label( __( 'Content', 'elementor-pro' ) )
				->set_items( [
					$this->define_options_control(),
					Switch_Control::bind_to( 'required' )
						->set_label( __( 'Required', 'elementor-pro' ) ),
					Switch_Control::bind_to( 'multiple' )
						->set_label( __( 'Multiple selections', 'elementor-pro' ) ),
				] ),
			Section::make()
				->set_label( __( 'Settings', 'elementor-pro' ) )
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
			'select' => __DIR__ . '/select.html.twig',
		];
	}

}

<?php

namespace Cool_FormKit\Widgets\AtomicForm\Input;

use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\Controls\Types\Select_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Switch_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Text_Control;
use Elementor\Modules\AtomicWidgets\Elements\Base\Has_Template;
use Elementor\Modules\AtomicWidgets\PropTypes\Attributes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Classes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Boolean_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;
use Elementor\Modules\Components\PropTypes\Overridable_Prop_Type;
use ElementorPro\Modules\AtomicForm\Input\Input as AtomicFormInput;

if (! defined('ABSPATH')) exit;

require_once CFL_PLUGIN_PATH . 'widgets/atomic-form/field-controls-definition/country-code-input-definition.php';
require_once CFL_PLUGIN_PATH . 'widgets/atomic-form/field-controls-definition/mask-input-definition.php';
require_once CFL_PLUGIN_PATH . 'widgets/atomic-form/field-controls-definition/conditional-input-definition.php';

class Input extends AtomicFormInput
{
    use Has_Template;

    public static $widget_description = 'Display a text input with customizable type, placeholder, default value, required, readonly, and attributes.';


    public static function get_element_type(): string
    {
        return 'e-form-input';
    }

    public function get_title(): string {
		return esc_html__( 'Input', 'elementor-pro' );
	}

    public function get_icon(): string {
		return 'eicon-atomic-input';
	}

	public function get_categories(): array {
		return [ 'atomic-form' ];
	}

	public function get_keywords() {
		return [ 'atomic', 'form', 'input', 'text', 'email', 'number', 'tel', 'password' ];
	}

    protected static function define_props_schema(): array
    {
	return array_merge( [
		'classes' => Classes_Prop_Type::make()->default( [] ),
		'placeholder' => String_Prop_Type::make()->default( '' ),
		'type' => String_Prop_Type::make()
			->default( 'text' )
			->enum( [ 'text', 'email', 'number', 'tel', 'password' ] ),
		'required' => Boolean_Prop_Type::make()->default( false ),
		'readonly' => Boolean_Prop_Type::make()->default( false ),
		'attributes' => Attributes_Prop_Type::make()->meta( Overridable_Prop_Type::ignore() ),
	], Country_Code_Input_Definition::props_schema(), Mask_Input_Definition::props_schema(), Conditional_Input_Definition::props_schema() );
    }

    protected function define_atomic_controls(): array
    {

        return [
			Section::make()
				->set_label( __( 'Content', 'elementor-pro' ) )
				->set_items( array_merge( [
					Text_Control::bind_to( 'placeholder' )
					  ->set_placeholder( 'Enter placeholder text' )
						->set_label( __( 'Input placeholder', 'elementor-pro' ) ),
					Select_Control::bind_to( 'type' )
						->set_label( __( 'Type', 'elementor-pro' ) )
						->set_options( [
							[
								'label' => __( 'Text', 'elementor-pro' ),
								'value' => 'text',
							],
							[
								'label' => __( 'Email', 'elementor-pro' ),
								'value' => 'email',
							],
							[
								'label' => __( 'Number', 'elementor-pro' ),
								'value' => 'number',
							],
							[
								'label' => __( 'Tel', 'elementor-pro' ),
								'value' => 'tel',
							],
							[
								'label' => __( 'Password', 'elementor-pro' ),
								'value' => 'password',
							],
						] ),
					Switch_Control::bind_to( 'required' )
						->set_label( __( 'Required', 'elementor-pro' ) ),
					Switch_Control::bind_to( 'readonly' )
						->set_label( __( 'Read only', 'elementor-pro' ) ),
				], Country_Code_Input_Definition::content_controls(), Mask_Input_Definition::content_controls() ) ),
			Section::make()
				->set_label( __( 'Settings', 'elementor-pro' ) )
				->set_id( 'settings' )
				->set_items( $this->get_settings_controls() ),
			Conditional_Input_Definition::conditions_section(),
		];
    }

    protected function get_templates(): array
    {

        return [
            'input' => __DIR__ . '/input.html.twig',
        ];
    }

}

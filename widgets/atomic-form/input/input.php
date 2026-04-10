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
use Elementor\Modules\AtomicWidgets\PropDependencies\Manager as Dependency_Manager;

if (! defined('ABSPATH')) exit;

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
        $tel_only_dependencies = Dependency_Manager::make()
		->where( [
			'operator' => 'eq',
			'path' => [ 'type' ],
			'value' => 'tel',
			'effect' => 'hide',
		] )
		->get();
	return [
		'classes' => Classes_Prop_Type::make()->default( [] ),
		'placeholder' => String_Prop_Type::make()->default( '' ),
		'type' => String_Prop_Type::make()
			->default( 'text' )
			->enum( [ 'text', 'email', 'number', 'tel', 'password' ] ),
		'required' => Boolean_Prop_Type::make()->default( false ),
		'readonly' => Boolean_Prop_Type::make()->default( false ),
		'attributes' => Attributes_Prop_Type::make()->meta( Overridable_Prop_Type::ignore() ),
		'country_code' => Boolean_Prop_Type::make()
			->set_dependencies( $tel_only_dependencies )
			->default( false ),
		'default_country' => String_Prop_Type::make()
			->set_dependencies( $tel_only_dependencies )
			->default( 'in' ),
		'include' => String_Prop_Type::make()
			->set_dependencies( $tel_only_dependencies )
			->default( '' ),
		'exclude' => String_Prop_Type::make()
			->set_dependencies( $tel_only_dependencies )
			->default( '' ),
		'dial_code_visibility' => String_Prop_Type::make()
			->set_dependencies( $tel_only_dependencies )
			->default( 'show' ),
		'strict_mode' => Boolean_Prop_Type::make()
			->set_dependencies( $tel_only_dependencies )
			->default( false ),
	];
    }

    protected function define_atomic_controls(): array
    {

        return [
			Section::make()
				->set_label( __( 'Content', 'elementor-pro' ) )
				->set_items( [
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
                    // ✅ MAIN TOGGLE
                    Switch_Control::bind_to('country_code')
                        ->set_label('Enable Country Code'),
                    // ✅ EXTRA SETTINGS
                    Text_Control::bind_to('default_country')
                        ->set_label('Default Country (e.g. in, us)'),

                    Text_Control::bind_to('include')
                        ->set_label('Only Countries (comma separated)'),

                    Text_Control::bind_to('exclude')
                        ->set_label('Exclude Countries'),
					Select_Control::bind_to( 'dial_code_visibility' )
						->set_label( __( 'Dial Code Visibility', 'elementor-pro' ) )
						->set_options( [
							[
								'label' => __( 'Show', 'elementor-pro' ),
								'value' => 'show',
							],
							[
								'label' => __( 'Hide', 'elementor-pro' ),
								'value' => 'hide',
							],
							[
								'label' => __( 'Separate', 'elementor-pro' ),
								'value' => 'separate',
							],
						] ),
					Switch_Control::bind_to('strict_mode')
						->set_label('Strict Mode'),
				] ),
			Section::make()
				->set_label( __( 'Settings', 'elementor-pro' ) )
				->set_id( 'settings' )
				->set_items( $this->get_settings_controls() ),
		];
    }

    protected function get_templates(): array
    {

        return [
            'input' => __DIR__ . '/input.html.twig',
        ];
    }

}

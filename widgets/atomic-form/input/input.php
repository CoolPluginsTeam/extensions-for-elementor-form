<?php

namespace Cool_FormKit\Widgets\AtomicForm\Input;

use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\Controls\Types\Select_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Switch_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Text_Control;
use Elementor\Modules\AtomicWidgets\Elements\Base\Atomic_Widget_Base;
use Elementor\Modules\AtomicWidgets\Elements\Base\Has_Template;
use Elementor\Modules\AtomicWidgets\PropTypes\Attributes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Classes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Boolean_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;
use Elementor\Modules\AtomicWidgets\Styles\Style_Definition;
use Elementor\Modules\AtomicWidgets\Styles\Style_Variant;
use Elementor\Modules\AtomicWidgets\PropTypes\Size_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Color_Prop_Type;
use Elementor\Modules\AtomicWidgets\Styles\Style_States;
use Elementor\Modules\Components\PropTypes\Overridable_Prop_Type;
use ElementorPro\Modules\AtomicForm\Input\Input as AtomicFormInput;

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
        return [
            'classes' => Classes_Prop_Type::make()
				->default( [] ),
			'placeholder' => String_Prop_Type::make()
				->default( '' ),
			'type' => String_Prop_Type::make()
				->default( 'text' )
				->enum( [ 'text', 'email', 'number', 'tel', 'password' ] ),
			'required' => Boolean_Prop_Type::make()
				->default( false ),
			'readonly' => Boolean_Prop_Type::make()
				->default( false ),
			'attributes' => Attributes_Prop_Type::make()->meta( Overridable_Prop_Type::ignore() ),
            // ✅ COUNTRY CODE FEATURE
            'country_code' => Boolean_Prop_Type::make()->default(false),
            'default_country' => String_Prop_Type::make()->default('in'),
            'include' => String_Prop_Type::make()->default(''),
            'exclude' => String_Prop_Type::make()->default(''),

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
				] ),
			Section::make()
				->set_label( __( 'Settings', 'elementor-pro' ) )
				->set_id( 'settings' )
				->set_items( $this->get_settings_controls() ),
		];
    }

    protected function get_templates(): array
    {

        $this->load_assets();

        return [
            'input' => __DIR__ . '/input.html.twig',
        ];
    }

    protected function define_base_styles(): array {
		$border_radius_value = Size_Prop_Type::generate( [
			'size' => 0,
			'unit' => 'px',
		] );

		$height_value = Size_Prop_Type::generate( [
			'size' => 36,
			'unit' => 'px',
		] );

		$border_color_value = Color_Prop_Type::generate( '#D6D5D5' );

		return [
			'base' => Style_Definition::make()
				->add_variant(
					Style_Variant::make()
							->add_props( [
								'border-radius' => $border_radius_value,
								'height' => $height_value,
								'border-color' => $border_color_value,
								'font-family' => String_Prop_Type::generate( 'Poppins' ),
								'font-size' => Size_Prop_Type::generate( [
									'size' => 12,
									'unit' => 'px',
								] ),
							] ),
				)
				->add_variant(
					Style_Variant::make()
						->set_state( Style_States::FOCUS )
						->add_props( [
							'border-color' => Color_Prop_Type::generate( '#706F6F' ),
							'outline-style' => String_Prop_Type::generate( 'none' ),
						] ),
				),
			'base::placeholder' => Style_Definition::make() // this should be changed once we support placeholder/pseudo-elements styles in the styles system.
				->add_variant(
					Style_Variant::make()
						->add_props( [
							'color' => Color_Prop_Type::generate( '#9DA5AE' ),
						] ),
				),
		];
	}

    protected function get_css_id_control_meta(): array {
		return [
			'layout' => 'two-columns',
			'topDivider' => false,
		];
	}

    public function load_assets()
    {

        wp_register_script('cfl-country-code-library-script', CFL_PLUGIN_URL . 'assets/addons/intl-tel-input/js/intlTelInput.js', array(), CFL_VERSION, true);
        wp_register_style('cfl-country-code-library-style', CFL_PLUGIN_URL . 'assets/addons/intl-tel-input/css/intlTelInput.min.css', array(), CFL_VERSION, 'all');
        wp_register_style('cfl-country-code-style', CFL_PLUGIN_URL . 'assets/addons/css/country-code-style.min.css', array(), CFL_VERSION, 'all');


        if (! wp_script_is('cfl-country-code-library-script', 'enqueued') && ! wp_script_is('cfl-country-code-library-script', 'done')) {
            wp_enqueue_script('cfl-country-code-library-script');
        }

        

        if (! wp_style_is('cfl-country-code-library-style', 'enqueued') && ! wp_style_is('cfl-country-code-library-style', 'done')) {
            wp_enqueue_style('cfl-country-code-library-style');
        }

        if (! wp_style_is('cfl-country-code-style', 'enqueued') && ! wp_style_is('cfl-country-code-style', 'done')) {
            wp_enqueue_style('cfl-country-code-style');
        }
    }
}

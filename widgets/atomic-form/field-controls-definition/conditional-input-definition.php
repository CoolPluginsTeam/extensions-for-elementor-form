<?php

namespace Cool_FormKit\Widgets\AtomicForm\Input;

use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\Controls\Types\Select_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Switch_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Text_Control;
use Elementor\Modules\AtomicWidgets\PropDependencies\Manager as Dependency_Manager;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Boolean_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;
use Elementor\Modules\AtomicWidgets\Controls\Types\Textarea_Control;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Conditional logic props and controls for the atomic Input widget.
 */
final class Conditional_Input_Definition {

	/**
	 * @return array<string, mixed>|null
	 */
	private static function conditions_enabled_dependencies(): ?array {
		return Dependency_Manager::make()
			->where(
				[
					'operator' => 'eq',
					'path' => [ 'cfef_logic' ],
					'value' => true,
					'effect' => 'hide',
				]
			)
			->get();
	}

	/**
	 * @return array<string, mixed>
	 */
	public static function props_schema(): array {
		return [
			'cfef_logic' => Boolean_Prop_Type::make()->default( false ),
			'cfef_logic_mode' => String_Prop_Type::make()
				->set_dependencies( self::conditions_enabled_dependencies() )
				->default( 'show' )
				->enum( [ 'show', 'hide' ] ),
			'cfef_logic_meet' => String_Prop_Type::make()
				->set_dependencies( self::conditions_enabled_dependencies() )
				->default( 'All' )
				->enum( [ 'All', 'Any' ] ),
			'cfef_logic_field_id' => String_Prop_Type::make()
				->set_dependencies( self::conditions_enabled_dependencies() )
				->default( '' ),
			'cfef_logic_field_is' => String_Prop_Type::make()
				->set_dependencies( self::conditions_enabled_dependencies() )
				->default( '==' )
				->enum( [ '==', '!=', '>', '<', '>=', '<=', 'e', '!e', 'c', '!c', '^', '~' ] ),
			'cfef_logic_compare_value' => String_Prop_Type::make()
				->set_dependencies( self::conditions_enabled_dependencies() )
				->default( '' ),
			'cfef_logic_repeater' => String_Prop_Type::make()
				->set_dependencies( self::conditions_enabled_dependencies() )
				->default( '' ),
		];
	}

	public static function conditions_section(): Section {
		return Section::make()
			->set_id( 'conditions' )
			->set_label( __( 'Conditions', 'extensions-for-elementor-form' ) )
			->set_items(
				[
					Switch_Control::bind_to( 'cfef_logic' )
						->set_label( esc_html__( 'Enable Conditions', 'extensions-for-elementor-form' ) ),
					Select_Control::bind_to( 'cfef_logic_mode' )
						->set_label( esc_html__( 'Show / Hide Field', 'extensions-for-elementor-form' ) )
						->set_options(
							[
								[
									'label' => esc_html__( 'Show', 'extensions-for-elementor-form' ),
									'value' => 'show',
								],
								[
									'label' => esc_html__( 'Hide', 'extensions-for-elementor-form' ),
									'value' => 'hide',
								],
							]
						),
					Select_Control::bind_to( 'cfef_logic_meet' )
						->set_label( esc_html__( 'Conditions Trigger', 'extensions-for-elementor-form' ) )
						->set_options(
							[
								[
									'label' => esc_html__( 'All - AND Conditions', 'extensions-for-elementor-form' ),
									'value' => 'All',
								],
								[
									'label' => esc_html__( 'Any - OR Conditions', 'extensions-for-elementor-form' ),
									'value' => 'Any',
								],
							]
						),
					Text_Control::bind_to( 'cfef_logic_field_id' )
						->set_label( esc_html__( 'Field ID', 'extensions-for-elementor-form' ) ),
					Select_Control::bind_to( 'cfef_logic_field_is' )
						->set_label( esc_html__( 'Operator', 'extensions-for-elementor-form' ) )
						->set_options(
							[
								[
									'label' => esc_html__( 'is equal ( == )', 'extensions-for-elementor-form' ),
									'value' => '==',
								],
								[
									'label' => esc_html__( 'is not equal (!=)', 'extensions-for-elementor-form' ),
									'value' => '!=',
								],
								[
									'label' => __( 'greater than (>)', 'extensions-for-elementor-form' ),
									'value' => '>',
								],
								[
									'label' => __( 'less than (<)', 'extensions-for-elementor-form' ),
									'value' => '<',
								],
							]
						),
					Text_Control::bind_to( 'cfef_logic_compare_value' )
						->set_label( esc_html__( 'Value to compare', 'extensions-for-elementor-form' ) ),
					Textarea_Control::bind_to( 'cfef_logic_repeater' )
						->set_label( esc_html__( 'Repeater Data', 'extensions-for-elementor-form' ) ),
				]
			);
	}
}

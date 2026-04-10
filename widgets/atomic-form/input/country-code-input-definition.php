<?php

namespace Cool_FormKit\Widgets\AtomicForm\Input;

use Elementor\Modules\AtomicWidgets\Controls\Types\Select_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Switch_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Text_Control;
use Elementor\Modules\AtomicWidgets\PropDependencies\Manager as Dependency_Manager;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Boolean_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Prop schema and editor controls for tel country code (intl-tel-input) on the atomic Input widget.
 */
final class Country_Code_Input_Definition {

	public static function tel_only_dependencies(): array {
		return Dependency_Manager::make()
			->where( [
				'operator' => 'eq',
				'path' => [ 'type' ],
				'value' => 'tel',
				'effect' => 'hide',
			] )
			->get();
	}

	/**
	 * @return array<string, mixed>
	 */
	public static function props_schema(): array {
		$tel_only_dependencies = self::tel_only_dependencies();

		return [
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

	/**
	 * Control items for the Content section (append after type/required/readonly).
	 *
	 * @return array<int, mixed>
	 */
	public static function content_controls(): array {
		return [
			Switch_Control::bind_to( 'country_code' )
				->set_label( 'Enable Country Code' ),
			Text_Control::bind_to( 'default_country' )
				->set_label( 'Default Country (e.g. in, us)' ),
			Text_Control::bind_to( 'include' )
				->set_label( 'Only Countries (comma separated)' ),
			Text_Control::bind_to( 'exclude' )
				->set_label( 'Exclude Countries' ),
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
			Switch_Control::bind_to( 'strict_mode' )
				->set_label( 'Strict Mode' ),
		];
	}
}

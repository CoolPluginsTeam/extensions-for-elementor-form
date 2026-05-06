<?php

namespace Cool_FormKit\Widgets\AtomicForm\Input;

use Elementor\Modules\AtomicWidgets\Controls\Types\Switch_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Toggle_Control;
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

	private static function tel_and_tel_field_on_dependencies(): ?array {
		return Dependency_Manager::make( Dependency_Manager::RELATION_AND )
			->where(
				[
					'operator' => 'eq',
					'path' => [ 'type' ],
					'value' => 'tel',
					'effect' => 'hide',
				]
			)
			->where(
				[
					'operator' => 'eq',	
					'path' => [ 'country_code' ],
					'value' => true,
					'effect' => 'hide',
				]
			)
			->get() ?? [];
	}

	/**
	 * @return array<string, mixed>
	 */
	public static function props_schema(): array {
		$tel_only_dependencies = self::tel_only_dependencies();
		$tel_and_tel_field_on_dependencies = self::tel_and_tel_field_on_dependencies();

		return [
			'country_code' => Boolean_Prop_Type::make()
				->set_dependencies( $tel_only_dependencies )
				->default( false ),
			'default_country' => String_Prop_Type::make()
				->set_dependencies( $tel_and_tel_field_on_dependencies )
				->default( 'in' ),
			'include' => String_Prop_Type::make()
				->set_dependencies( $tel_and_tel_field_on_dependencies )
				->default( '' ),
			// phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
			'exclude' => String_Prop_Type::make()
				->set_dependencies( $tel_and_tel_field_on_dependencies )
				->default( '' ),
			'dial_code_visibility' => String_Prop_Type::make()
				->set_dependencies( $tel_and_tel_field_on_dependencies )
				->enum( [ 'show', 'hide', 'separate' ] )
				->default( 'show' ),
			'strict_mode' => Boolean_Prop_Type::make()
				->set_dependencies( $tel_and_tel_field_on_dependencies )
				->default( false ),
		];
	}

	/**
	 * Control items for the Content section (append after type/required/readonly).
	 *
	 * @return array<int, mixed>
	 */
	public static function content_controls(): array {
		$dial_code_visibility_control = Toggle_Control::bind_to( 'dial_code_visibility' )
			->set_label( __( 'Dial Code Visibility', 'extensions-for-elementor-form' ) )
			->set_meta( [ 'layout' => 'two-columns' ] );

		if ( $dial_code_visibility_control instanceof Toggle_Control ) {
			$dial_code_visibility_control
				->add_options( [
					'show' => [
						'title' => __( 'Show', 'extensions-for-elementor-form' ),
						'atomic-icon' => 'EyeIcon',
					],
					'hide' => [
						'title' => __( 'Hide', 'extensions-for-elementor-form' ),
						'atomic-icon' => 'EyeOffIcon',
					],
					'separate' => [
						'title' => __( 'Separate', 'extensions-for-elementor-form' ),
						'atomic-icon' => 'ArrowBarBothIcon',
					],
				] )
				->set_exclusive( true )
				->set_convert_options( true )
				->set_size( 'tiny' )
				->set_full_width( true );
		}

		return [
			Switch_Control::bind_to( 'country_code' )
				->set_label( 'Country Code' ),
			Text_Control::bind_to( 'default_country' )
				->set_label( 'Default Country (e.g. in, us)' ),
			Text_Control::bind_to( 'include' )
				->set_label( 'Only Countries (comma separated)' ),
			Text_Control::bind_to( 'exclude' )
				->set_label( 'Exclude Countries' ),
			$dial_code_visibility_control,
			Switch_Control::bind_to( 'strict_mode' )
				->set_label( 'Strict Mode' ),
		];
	}
}

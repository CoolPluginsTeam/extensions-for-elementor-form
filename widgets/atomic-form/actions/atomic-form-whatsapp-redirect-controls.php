<?php

namespace Cool_FormKit\Widgets\AtomicForm\Actions;

use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\Controls\Types\Chips_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Text_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Textarea_Control;
use Elementor\Modules\AtomicWidgets\PropDependencies\Manager as Dependency_Manager;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Editor props and controls for the WhatsApp redirect action on the extended Atomic Form.
 */
class Atomic_Form_Whatsapp_Redirect_Controls {

	public const ACTION_TYPE = 'whatsapp_redirect';

	/**
	 * @param array<string, mixed> $schema Parent props schema.
	 * @return array<string, mixed>
	 */
	public static function extend_props_schema( array $schema ): array {
		$deps = self::whatsapp_prop_dependencies();
		$deps_rev = self::whatsapp_prop_dependencies_rev();

		$extended = [];
		foreach ( $schema as $key => $definition ) {
			$extended[ $key ] = $definition;
			if ( 'email' === $key ) {
				$extended['cfl-whatsapp-warning'] = String_Prop_Type::make()
					->default( \__( 'Enable WhatsApp under “Actions after submit” to configure these fields.', 'extensions-for-elementor-form' ) )
					->set_dependencies( $deps_rev );
				$extended['cfl-whatsapp-to'] = String_Prop_Type::make()
					->default( '' )
					->set_dependencies( $deps );
				$extended['cfl-whatsapp-message'] = String_Prop_Type::make()
					->default( '' )
					->set_dependencies( $deps );
			}
		}

		return $extended;
	}

	public static function build_actions_after_submit_chips( string $collect_submissions_action_value ): Chips_Control {
		return Chips_Control::bind_to( 'actions-after-submit' )
			->set_label( \__( 'Actions after submit', 'elementor' ) )
			->set_meta( [ 'topDivider' => true ] )
			->set_options(
				[
					[
						'label' => \__( 'Collect submissions', 'elementor' ),
						'value' => $collect_submissions_action_value,
					],
					[
						'label' => \__( 'Email', 'elementor' ),
						'value' => 'email',
					],
					[
						'label' => \__( 'WhatsApp', 'extensions-for-elementor-form' ),
						'value' => self::ACTION_TYPE,
					],
				]
			);
	}

	public static function define_whatsapp_section(): Section {
		return Section::make()
			->set_id( 'cfl-whatsapp' )
			->set_label( \__( 'WhatsApp Redirect', 'extensions-for-elementor-form' ) )
			->set_description(
				\__( 'Shown when WhatsApp is enabled under Actions after submit.', 'extensions-for-elementor-form' )
			)
			->set_items(
				[
					Text_Control::bind_to( 'cfl-whatsapp-warning' )
						->set_label( \__( 'Add WhatsApp redirect action to use this action.', 'extensions-for-elementor-form' ) ),
					Text_Control::bind_to( 'cfl-whatsapp-to' )
						->set_label( \__( 'WhatsApp phone', 'extensions-for-elementor-form' ) )
						->set_description( \__( 'Phone with country code, e.g. 5551999999999', 'extensions-for-elementor-form' ) )
						->set_placeholder( \__( '13459999999', 'extensions-for-elementor-form' ) )
						->set_meta( [ 'classes' => 'elementor-control-whats-phone-direction-ltr' ] ),
					Textarea_Control::bind_to( 'cfl-whatsapp-message' )
						->set_label( \__( 'WhatsApp message', 'extensions-for-elementor-form' ) )
						->set_description( \__( 'Use field shortcodes or custom text. To add a line break, use the token: %break%', 'extensions-for-elementor-form' ) )
						->set_placeholder( \__( 'Write your text or use field shortcodes', 'extensions-for-elementor-form' ) )
						->set_meta( [ 'classes' => 'elementor-control-whats-direction-ltr' ] ),
				]
			);
	}

	/**
	 * @return mixed Dependencies config for WhatsApp props (hide when action not selected).
	 */
	private static function whatsapp_prop_dependencies() {
		return Dependency_Manager::make()
			->where(
				[
					'operator' => 'contains',
					'path' => [ 'actions-after-submit' ],
					'value' => self::ACTION_TYPE,
					'effect' => 'hide',
				]
			)
			->get();
	}

	/**
	 * Reverse dependency used for the warning: show it when WhatsApp action is NOT selected.
	 *
	 * @return mixed
	 */
	private static function whatsapp_prop_dependencies_rev() {
		return Dependency_Manager::make()
			->where(
				[
					'operator' => 'ncontains',
					'path' => [ 'actions-after-submit' ],
					'value' => self::ACTION_TYPE,
					'effect' => 'hide',
				]
			)
			->get();
	}
}

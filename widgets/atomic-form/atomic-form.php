<?php

namespace Cool_FormKit\Widgets\AtomicForm;

use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\Controls\Types\Chips_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Text_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Textarea_Control;
use Elementor\Modules\AtomicWidgets\Elements\Atomic_Form\Atomic_Form as Core_Atomic_Form;
use Elementor\Modules\AtomicWidgets\PropDependencies\Manager as Dependency_Manager;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Extends core Atomic Form: WhatsApp submit action and a dedicated panel section for its fields.
 */
class Atomic_Form extends Core_Atomic_Form {

	protected static function define_props_schema(): array {
		$schema = parent::define_props_schema();

		$whatsapp_dependencies = Dependency_Manager::make()
			->where(
				[
					'operator' => 'contains',
					'path' => [ 'actions-after-submit' ],
					'value' => 'whatsapp_redirect',
					'effect' => 'hide',
				]
			)
			->get();

		$with_whatsapp = [];
		foreach ( $schema as $key => $definition ) {
			$with_whatsapp[ $key ] = $definition;
			if ( 'email' === $key ) {
				$with_whatsapp['cfl-whatsapp-to'] = String_Prop_Type::make()
					->default( '' )
					->set_dependencies( $whatsapp_dependencies );
				$with_whatsapp['cfl-whatsapp-message'] = String_Prop_Type::make()
					->default( '' )
					->set_dependencies( $whatsapp_dependencies );
			}
		}

		return $with_whatsapp;
	}

	protected function define_atomic_controls(): array {
		$sections  = parent::define_atomic_controls();
		$result                    = [];
		$whatsapp_section_inserted = false;

		foreach ( $sections as $section ) {
			if ( ! ( $section instanceof Section ) ) {
				$result[] = $section;
				continue;
			}

			if ( 'settings' === $section->get_id() ) {
				$result[] = $section;
				continue;
			}

			// Content section (core leaves id empty).
			$items     = $section->get_items();
			$new_items = [];

			foreach ( $items as $item ) {
				if ( $item instanceof Chips_Control && 'actions-after-submit' === $item->get_bind() ) {
					$new_items[] = Chips_Control::bind_to( 'actions-after-submit' )
						->set_label( \__( 'Actions after submit', 'elementor' ) )
						->set_meta( [ 'topDivider' => true ] )
						->set_options(
							[
								[
									'label' => \__( 'Collect submissions', 'elementor' ),
									'value' => self::ACTION_COLLECT_SUBMISSIONS,
								],
								[
									'label' => \__( 'Email', 'elementor' ),
									'value' => 'email',
								],
								[
									'label' => \__( 'WhatsApp', 'extensions-for-elementor-form' ),
									'value' => 'whatsapp_redirect',
								],
							]
						);
					continue;
				}

				$new_items[] = $item;
			}

			$section->set_items( $new_items );
			$result[] = $section;

			if ( ! $whatsapp_section_inserted ) {
				$result[] = $this->define_whatsapp_section();
				$whatsapp_section_inserted = true;
			}
		}

		return $result;
	}

	private function define_whatsapp_section(): Section {
		return Section::make()
			->set_id( 'cfl-whatsapp-redirect' )
			->set_label( \__( 'WhatsApp Redirect', 'extensions-for-elementor-form' ) )
			->set_description(
				\__( 'Shown when WhatsApp is enabled under Actions after submit.', 'extensions-for-elementor-form' )
			)
			->set_items(
				[
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
}

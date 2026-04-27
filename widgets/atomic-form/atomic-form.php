<?php

namespace Cool_FormKit\Widgets\AtomicForm;

use Cool_FormKit\Widgets\AtomicForm\Actions\Atomic_Form_Whatsapp_Redirect_Controls;
use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\Controls\Types\Chips_Control;
use Elementor\Modules\AtomicWidgets\Elements\Atomic_Form\Atomic_Form as Core_Atomic_Form;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once CFL_PLUGIN_PATH . 'widgets/atomic-form/actions/atomic-form-whatsapp-redirect-controls.php';

/**
 * Extends core Atomic Form: WhatsApp submit action and a dedicated panel section for its fields.
 */
class Atomic_Form extends Core_Atomic_Form {

	private function is_field_enabled( $field_key ) {
		return self::is_cfkef_element_enabled( $field_key );
	}

	protected static function define_props_schema(): array {
		$schema = parent::define_props_schema();
		if ( ! self::is_cfkef_element_enabled( 'whatsapp_redirect' ) ) {
			return $schema;
		}
		return Atomic_Form_Whatsapp_Redirect_Controls::extend_props_schema( $schema );
	}

	protected function define_atomic_controls(): array {
		$sections                  = parent::define_atomic_controls();
		$result                    = [];
		$whatsapp_section_inserted = false;
		$whatsapp_enabled          = $this->is_field_enabled( 'whatsapp_redirect' );

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
				if (
					$whatsapp_enabled
					&& $item instanceof Chips_Control
					&& 'actions-after-submit' === $item->get_bind()
				) {
					$new_items[] = Atomic_Form_Whatsapp_Redirect_Controls::build_actions_after_submit_chips(
						self::ACTION_COLLECT_SUBMISSIONS
					);
					continue;
				}

				$new_items[] = $item;
			}

			$section->set_items( $new_items );
			$result[] = $section;

			if ( $whatsapp_enabled && ! $whatsapp_section_inserted ) {
				$result[]                  = Atomic_Form_Whatsapp_Redirect_Controls::define_whatsapp_section();
				$whatsapp_section_inserted = true;
			}
		}

		return $result;
	}

	/**
	 * @param string $field_key Option list entry from cfkef_enabled_elements.
	 */
	private static function is_cfkef_element_enabled( $field_key ): bool {
		$enabled_elements = get_option( 'cfkef_enabled_elements', array() );
		return in_array( sanitize_key( $field_key ), array_map( 'sanitize_key', (array) $enabled_elements ), true );
	}
}

<?php

namespace Cool_FormKit\Widgets\AtomicForm\Actions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Reads WhatsApp action settings from atomic form widget settings (flat string props).
 */
class Atomic_Form_Whatsapp_Redirect_Settings {

	private array $settings;

	public function __construct( array $widget_settings ) {
		$this->settings = $widget_settings;
	}

	public function to(): string {
		return $this->extract_string( $this->settings['cfl-whatsapp-to'] ?? '' );
	}

	public function message(): string {
		return $this->extract_string( $this->settings['cfl-whatsapp-message'] ?? '' );
	}

	/**
	 * @param mixed $raw
	 */
	private function extract_string( $raw ): string {
		if ( is_array( $raw ) && array_key_exists( 'value', $raw ) ) {
			return (string) $raw['value'];
		}

		return is_string( $raw ) ? $raw : '';
	}
}

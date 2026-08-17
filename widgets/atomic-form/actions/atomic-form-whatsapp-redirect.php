<?php
namespace Cool_FormKit\Widgets\AtomicForm\Actions;

use ElementorPro\Modules\AtomicForm\Actions\Action_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/atomic-form-whatsapp-redirect-settings.php';

/**
 * Class CoolForm_Whatsapp_Redirect
 */
class AtomicForm_Whatsapp_Redirect extends Action_Base {

	public function get_type(): string {
		return 'whatsapp_redirect';
	}

	public function execute( array $form_data, array $widget_settings, array $context ): array {

		$whatsapp_settings = new Atomic_Form_Whatsapp_Redirect_Settings( $widget_settings );
		$whatsapp_to = $whatsapp_settings->to();
		$whatsapp_message = $whatsapp_settings->message();

		$whatsapp_to = preg_replace( '/\D+/', '', (string) $whatsapp_to );
		if ( empty( $whatsapp_to ) ) {
			return $this->failure( \__( 'WhatsApp phone number is required', 'extensions-for-elementor-form' ) );
		}

		$field_metadata = $context['field_metadata'] ?? [];
		$whatsapp_message = cfl_replace_whatsapp_message_shortcodes( (string) $whatsapp_message, $form_data, $field_metadata );
		$whatsapp_message = cfl_replace_whatsapp_break_token( $whatsapp_message );

		$redirect_url = sprintf(
			'https://wa.me/%1$s?text=%2$s',
			$whatsapp_to,
			rawurlencode( $whatsapp_message )
		);

		return $this->success(
			\__( 'WhatsApp redirect generated successfully', 'extensions-for-elementor-form' ),
			[
				'redirect_url' => $redirect_url,
			]
		);
	}
}

<?php

namespace Cool_FormKit\Widgets\AtomicForm;

use ElementorPro\Plugin;
use ElementorPro\Modules\AtomicWidgets\Settings_Resolver;
use Elementor\Utils as Elementor_Utils;
use ElementorPro\Modules\AtomicForm\Actions\Action_Runner;
use ElementorPro\Modules\Forms\Classes\Ajax_Handler;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Handle_Atomic_Form_Submission {

    const NONCE_ACTION = 'elementor_pro_atomic_forms_send_form';

    private $validate_form = false;

	public function __construct() {
		add_action( 'wp_ajax_elementor_pro_atomic_forms_send_form', [ $this, 'ajax_send_form' ]);
		add_action( 'wp_ajax_nopriv_elementor_pro_atomic_forms_send_form', [ $this, 'ajax_send_form' ]);
	}

    private function resolve_form_name( string $posted_form_name, string $form_id ): string {
		return ! empty( $posted_form_name ) ? $posted_form_name : $form_id;
	}

    private function get_widget_settings( int $post_id, string $form_id ) {
		$document = Plugin::elementor()->documents->get( $post_id );

		if ( ! $document ) {
			return new \WP_Error(
				'document_not_found',
				__( 'Document not found', 'elementor-pro' )
			);
		}

		$element_data = $document->get_elements_data();
		$form_element = Elementor_Utils::find_element_recursive( $element_data, $form_id );

		if ( empty( $form_element ) ) {
			return new \WP_Error(
				'form_not_found',
				__( 'Form element not found', 'elementor-pro' )
			);
		}

		$settings = $form_element['settings'] ?? [];

		$resolved = Settings_Resolver::resolve( $settings );

		if ( ! isset( $resolved['actions-after-submit'] ) && isset( $resolved['email'] ) ) {
			$resolved['actions-after-submit'] = [ 'email' ];
		}

		return $resolved;
	}

	private function send_invalid_form_response(): void {

		wp_send_json_error( [
			'message' => Ajax_Handler::get_default_message( Ajax_Handler::INVALID_FORM, [] ),
		] );
	}

	private function send_error_response( string $message = '' ): void {
		wp_send_json_error( [
			'message' => $message ?? Ajax_Handler::get_default_message( Ajax_Handler::ERROR, [] ),
		] );
	}

	private function send_response( array $action_results, bool $all_actions_succeeded, array $failed_actions ): void {
		$response_data = [
			'actionResults' => $action_results,
			'allActionsSucceeded' => $all_actions_succeeded,
			'failedActions' => $failed_actions,
		];

		if ( $all_actions_succeeded ) {
			wp_send_json_success( [
				'message' => Ajax_Handler::get_default_message( Ajax_Handler::SUCCESS, [] ),
				'data' => $response_data,
			] );
		} else {
			$has_success = ! empty( $action_results ) && count( $failed_actions ) < count( $action_results );

			if ( $has_success ) {
				wp_send_json_success( [
					'message' => Ajax_Handler::get_default_message( Ajax_Handler::SUCCESS, [] ),
					'data' => $response_data,
				] );
			} else {
				wp_send_json_error( [
					'message' => Ajax_Handler::get_default_message( Ajax_Handler::ERROR, [] ),
					'data' => $response_data,
				] );
			}
		}
	}

    private function extract_field_metadata( array $form_fields ): array {
		$metadata = [];

		foreach ( $form_fields as $field ) {
			if ( ! is_array( $field ) ) {
				continue;
			}

			$id = sanitize_text_field( $field['id'] ?? '' );

			if ( ! $id ) {
				continue;
			}

			$metadata[ $id ] = [
				'label' => sanitize_text_field( $field['label'] ?? '' ),
				'type' => sanitize_text_field( $field['type'] ?? '' ),
			];
		}

		return $metadata;
	}

    private function convert_form_fields_to_data( array $form_fields ): array {
		$form_data = [];

		foreach ( $form_fields as $field ) {
			if ( ! is_array( $field ) ) {
				continue;
			}

			$id = sanitize_text_field( $field['id'] ?? '' );
			$value = $field['value'] ?? '';

			if ( ! $id ) {
				continue;
			}

			if ( is_array( $value ) ) {
				$form_data[ $id ] = array_map( 'sanitize_text_field', $value );
			} else {
				$type = sanitize_text_field( $field['type'] ?? 'text' );

				if ( 'textarea' === $type ) {
					$form_data[ $id ] = sanitize_textarea_field( $value );
				} else {
					$form_data[ $id ] = sanitize_text_field( $value );
				}
			}
		}

		return $form_data;
	}

    private function is_nonce_valid( array $post_data ): bool {
		$nonce = $post_data['_nonce'] ?? '';

		if ( ! $nonce ) {
			return false;
		}

		return wp_verify_nonce( $nonce, self::NONCE_ACTION );
	}

    public function ajax_send_form(): void {

		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce is validated below.
		$post_data = [
			'_nonce' => Elementor_Utils::get_super_global_value( $_POST, '_nonce' ),
			'post_id' => Elementor_Utils::get_super_global_value( $_POST, 'post_id' ),
			'form_id' => Elementor_Utils::get_super_global_value( $_POST, 'form_id' ),
			'form_name' => Elementor_Utils::get_super_global_value( $_POST, 'form_name' ),
			'form_fields' => Elementor_Utils::get_super_global_value( $_POST, 'form_fields' ) ?? [],
		];
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		if ( ! $this->is_nonce_valid( $post_data ) ) {
			$this->send_invalid_form_response();
		}

		$post_id = absint( $post_data['post_id'] ?? 0 );
		$form_id = sanitize_text_field( $post_data['form_id'] ?? '' );
		$form_fields = $post_data['form_fields'] ?? [];

		if ( ! $post_id || ! $form_id || empty( $form_fields ) ) {
			$this->send_invalid_form_response();
		}

        $form_fields = $this->validate_form_fields( $form_fields );

		$form_data = $this->convert_form_fields_to_data( $form_fields );

		if ( empty( $form_data ) ) {
			$this->send_invalid_form_response();
		}

		$field_metadata = $this->extract_field_metadata( $form_fields );

		$widget_settings = $this->get_widget_settings( $post_id, $form_id );

		if ( is_wp_error( $widget_settings ) ) {
			$this->send_error_response( $widget_settings->get_error_message() );
		}

		$posted_form_name = sanitize_text_field( $post_data['form_name'] ?? '' );
		$form_name = $this->resolve_form_name( $posted_form_name, $form_id );

		$spam_check = apply_filters(
			'elementor_pro/atomic_forms/spam_check',
			false,
			$form_fields,
			$widget_settings,
			$post_id
		);
		
		if ( $spam_check ) {
			$this->send_error_response(
				__( 'Your submission was flagged as spam. Please try again or contact the site administrator.', 'elementor-pro' )
			);
		}

		$actions = $widget_settings['actions-after-submit'] ?? [];

		if ( empty( $actions ) ) {
			$this->send_error_response( __( 'No actions configured for this form', 'elementor-pro' ) );
		}

		$results = Action_Runner::execute_actions(
			$actions,
			$form_data,
			$widget_settings,
			[
				'post_id' => $post_id,
				'form_id' => $form_id,
				'form_name' => $form_name,
				'field_metadata' => $field_metadata,
			]
		);

		$this->send_response(
			$results['actionResults'],
			$results['allActionsSucceeded'],
			$results['failedActions']
		);
	}

    public function validate_form_fields( array $form_fields ) {
        $disallowed_values = array(
			'^newOptionTest',
			'newchkTest',
			'1003-01-01',
			'11:59',
			'+1234567890',
			'https://testing.com',
			'cool_plugins@abc.com',
			'cool_plugins',
			'000',
			'premium1@',
			'cool23plugins',
		);

        if ( $this->validate_form ) {
            return $form_fields;
        }

        $this->validate_form = true;

        foreach ( $form_fields as $index => $field ) {
            if ( ! is_array( $field ) ) {
                continue;
            }

            $value = $field['value'] ?? '';

            if ( is_array( $value ) ) {
                $sanitized_values = array_map( 'sanitize_text_field', $value );

                foreach ( $sanitized_values as $single_value ) {
                    if ( in_array( $single_value, $disallowed_values, true ) ) {
                        unset( $form_fields[ $index ] );
                        break;
                    }
                }

                continue;
            }

            $sanitized_value = sanitize_text_field( (string) $value );
            if ( in_array( $sanitized_value, $disallowed_values, true ) ) {
                unset( $form_fields[ $index ] );
            }
        }

        return $form_fields;
    }
}

new Handle_Atomic_Form_Submission();
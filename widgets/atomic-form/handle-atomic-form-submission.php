<?php

namespace Cool_FormKit\Widgets\AtomicForm;

use ElementorPro\Plugin;
use ElementorPro\Modules\AtomicWidgets\Settings_Resolver;
use Elementor\Utils as Elementor_Utils;
use ElementorPro\Modules\AtomicForm\Actions\Action_Runner;
use ElementorPro\Modules\AtomicForm\Actions\Action_Type;
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

    private function check_condition_rule( $field_value, string $operator, string $compare_value ): bool {
		$value_a = trim( html_entity_decode( $this->normalize_field_value_for_condition_check( $field_value ), ENT_QUOTES, 'UTF-8' ) );
		$value_b = trim( html_entity_decode( $compare_value, ENT_QUOTES, 'UTF-8' ) );
		$values = array_map( 'trim', explode( ',', $value_a ) );
		$match_found = in_array( $value_b, $values, true );

		switch ( $operator ) {
			case '==':
				return $match_found && '' !== $value_a;
			case '!=':
				return ! $match_found && '' !== $value_a;
			case 'e':
				return '' === $value_a;
			case '!e':
				return '' !== $value_a;
			case 'c':
				return false !== strpos( $value_a, $value_b );
			case '!c':
				return '' !== $value_a && false === strpos( $value_a, $value_b );
			case '^':
				return '' !== $value_b && 0 === strpos( $value_a, $value_b );
			case '~':
				return '' !== $value_b && str_ends_with( $value_a, $value_b );
			case '>':
				return intval( $value_a ) > intval( $value_b );
			case '<':
				return intval( $value_a ) < intval( $value_b );
			case '>=':
				return intval( $value_a ) >= intval( $value_b );
			case '<=':
				return intval( $value_a ) <= intval( $value_b );
			default:
				return false;
		}
	}

    private function normalize_field_value_for_condition_check( $field_value ): string {
		if ( is_array( $field_value ) ) {
			$field_value = implode(
				',',
				array_map(
					static function ( $value ): string {
						return is_scalar( $value ) ? (string) $value : '';
					},
					$field_value
				)
			);
		}

		if ( ! is_scalar( $field_value ) ) {
			return '';
		}

		return (string) $field_value;
	}

    private function resolve_atomic_setting_value( array $settings, string $key, $default = '' ) {
		if ( ! array_key_exists( $key, $settings ) ) {
			return $default;
		}

		$value = $settings[ $key ];


		if ( is_array( $value ) && array_key_exists( 'value', $value ) ) {
			return $value['value'];
		}


		return $value;
	}

	private function resolve_atomic_bool_setting_value( array $settings, string $key, bool $default = false ): bool {
		$value = $this->resolve_atomic_setting_value( $settings, $key, $default );

		if ( is_bool( $value ) ) {
			return $value;
		}

		$parsed = filter_var( $value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );
		return null === $parsed ? (bool) $value : $parsed;
	}

    private function collect_atomic_logic_rules( array $settings ): array {
		$raw_rules = $this->resolve_atomic_setting_value( $settings, 'cfef_repeater_data', [] );

		if ( empty( $raw_rules ) ) {
			$raw_rules = $this->resolve_atomic_setting_value( $settings, 'cfef_logic_repeater', [] );
		}

		if ( is_string( $raw_rules ) ) {
			$decoded_rules = json_decode( $raw_rules, true );
			$raw_rules = is_array( $decoded_rules ) ? $decoded_rules : [];
		}

		$rules = [];

		if ( is_array( $raw_rules ) && ! empty( $raw_rules ) ) {
			foreach ( $raw_rules as $rule ) {
				if ( ! is_array( $rule ) ) {
					continue;
				}


				$field_id = (string) $this->resolve_atomic_setting_value( $rule, 'cfef_logic_field_id', '' );
				$operator = (string) $this->resolve_atomic_setting_value( $rule, 'cfef_logic_field_is', '==' );
				$compare_value = (string) $this->resolve_atomic_setting_value( $rule, 'cfef_logic_compare_value', '' );

				if ( '' === $field_id ) {
					continue;
				}

				$rules[] = [
					'cfef_logic_field_id' => $field_id,
					'cfef_logic_field_is' => $operator,
					'cfef_logic_compare_value' => $compare_value,
				];
			}
		}

		if ( empty( $rules ) ) {
			$field_id = (string) $this->resolve_atomic_setting_value( $settings, 'cfef_logic_field_id', '' );
			if ( '' !== $field_id ) {
				$rules[] = [
					'cfef_logic_field_id' => $field_id,
					'cfef_logic_field_is' => (string) $this->resolve_atomic_setting_value( $settings, 'cfef_logic_field_is', '==' ),
					'cfef_logic_compare_value' => (string) $this->resolve_atomic_setting_value( $settings, 'cfef_logic_compare_value', '' ),
				];
			}
		}

		return $rules;
	}

	private function flatten_form_elements( array $elements ): array {
		$flattened = [];
		$stack = $elements;

		while ( ! empty( $stack ) ) {
			$element = array_shift( $stack );
			if ( ! is_array( $element ) ) {
				continue;
			}

			$flattened[] = $element;
			$children = $element['elements'] ?? [];
			if ( is_array( $children ) && ! empty( $children ) ) {
				$stack = array_merge( $children, $stack );
			}
		}

		return $flattened;
	}

    private function map_submitted_values_to_atomic_ids( array $form_fields, array $form_elements ): array {
		$submitted_by_widget_id = [];
		$mapped_values = [];

		foreach ( $form_fields as $field ) {
			if ( ! is_array( $field ) ) {
				continue;
			}

			$id = sanitize_text_field( $field['id'] ?? '' );
			if ( '' === $id ) {
				continue;
			}

			$submitted_by_widget_id[ $id ] = $field['value'] ?? '';
			$mapped_values[ $id ] = $field['value'] ?? '';
		}

		foreach ( $form_elements as $element ) {
			if ( ! is_array( $element ) ) {
				continue;
			}

			$element_id = sanitize_text_field( $element['id'] ?? '' );
			if ( '' === $element_id || ! array_key_exists( $element_id, $submitted_by_widget_id ) ) {
				continue;
			}

			$value = $submitted_by_widget_id[ $element_id ];
			$settings = $element['settings'] ?? [];
			$css_id = sanitize_text_field( (string) $this->resolve_atomic_setting_value( $settings, '_cssid', '' ) );

			if ( '' !== $css_id ) {
				$mapped_values[ $css_id ] = $value;
			}
		}

		return $mapped_values;
	}

    private function get_condition_rules( int $post_id, string $form_id, array $form_fields ) {
		$document = Plugin::elementor()->documents->get( $post_id );
        if ( ! $document ) {
			return new \WP_Error(
				'document_not_found',
				__( 'Document not found', 'elementor-pro' )
			);
		}
		$element_data = $document->get_elements_data();
        
        $form_element = Elementor_Utils::find_element_recursive( $element_data, $form_id );

		if ( empty( $form_element ) || empty( $form_element['elements'] ) || ! is_array( $form_element['elements'] ) ) {
			return [];
		}

		$elements = $this->flatten_form_elements( $form_element['elements'] );
		$field_values = $this->map_submitted_values_to_atomic_ids( $form_fields, $elements );
		$hidden_fields = [];

		foreach ( $elements as $element ) {
			if ( ! is_array( $element ) ) {
				continue;
			}
			

			$settings = $element['settings'] ?? [];
			$logic_enabled = $this->resolve_atomic_bool_setting_value( $settings, 'cfef_logic', false );

			if ( ! $logic_enabled ) {
				continue;
			}

			$rules = $this->collect_atomic_logic_rules( $settings );
			if ( empty( $rules ) ) {
				continue;
			}

			$checks = [];
			foreach ( $rules as $rule ) {
				$source_field_id = sanitize_text_field( $rule['cfef_logic_field_id'] ?? '' );
				if ( '' === $source_field_id ) {
					continue;
				}

				$checks[] = $this->check_condition_rule(
					$field_values[ $source_field_id ] ?? '',
					(string) ( $rule['cfef_logic_field_is'] ?? '==' ),
					(string) ( $rule['cfef_logic_compare_value'] ?? '' )
				);
			}

			if ( empty( $checks ) ) {
				continue;
			}

			$fire_action = (string) $this->resolve_atomic_setting_value( $settings, 'cfef_logic_meet', 'All' );
			$display_mode = (string) $this->resolve_atomic_setting_value( $settings, 'cfef_logic_mode', 'show' );

			$logic_result = ( 'All' === $fire_action )
				? ! in_array( false, $checks, true )
				: in_array( true, $checks, true );

			$should_show = ( 'show' === $display_mode ) ? $logic_result : ! $logic_result;

			if ( ! $should_show ) {
				$hidden_fields[] = [
					'id' => sanitize_text_field( $element['id'] ?? '' ),
					'cssid' => sanitize_text_field( (string) $this->resolve_atomic_setting_value( $settings, '_cssid', '' ) ),
				];
			}
		}

        return $hidden_fields;

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

	/**
	 * Execute built-in and custom registered actions.
	 *
	 * Core Action_Runner::execute_actions() validates only built-in Action_Type values.
	 * This wrapper allows custom action types when they were registered successfully.
	 *
	 * @param string[] $actions Action type strings.
	 * @param array    $form_data Sanitized form data.
	 * @param array    $widget_settings Full widget settings.
	 * @param array    $context Form context.
	 * @return array
	 */
	private function execute_registered_actions( array $actions, array $form_data, array $widget_settings, array $context ): array {
		$action_results = [];
		$failed_actions = [];

		foreach ( $actions as $action_type ) {
			$is_builtin_type = Action_Type::is_valid( $action_type );
			$action = Action_Runner::create_action( $action_type );

			// Accept either a core/built-in type or a custom type that is registered.
			if ( ! $is_builtin_type && ! $action ) {
				$action_results[] = [
					'type' => $action_type,
					'status' => 'failed',
					'error' => sprintf( __( 'Invalid action type: %s', 'elementor-pro' ), $action_type ),
				];
				$failed_actions[] = $action_type;
				continue;
			}

			try {
				if ( ! $action ) {
					throw new \Exception( sprintf( __( 'Could not create action: %s', 'elementor-pro' ), $action_type ) );
				}

				$result = $action->execute( $form_data, $widget_settings, $context );
				$action_results[] = array_merge(
					[ 'type' => $action_type ],
					$result
				);
			} catch ( \Exception $e ) {
				$action_results[] = [
					'type' => $action_type,
					'status' => 'failed',
					'error' => $e->getMessage(),
				];
				$failed_actions[] = $action_type;
			}
		}

		return [
			'actionResults' => $action_results,
			'allActionsSucceeded' => empty( $failed_actions ),
			'failedActions' => $failed_actions,
		];
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

        $widget_settings = $this->get_widget_settings( $post_id, $form_id );

        
        $hidden_fields = $this->get_condition_rules( $post_id, $form_id, $form_fields );
        $form_fields = $this->validate_form_fields( $form_fields, $hidden_fields );

		$form_data = $this->convert_form_fields_to_data( $form_fields );

		if ( empty( $form_data ) ) {
			$this->send_invalid_form_response();
		}

		$field_metadata = $this->extract_field_metadata( $form_fields );


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

		$results = $this->execute_registered_actions(
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

    public function validate_form_fields( array $form_fields, array $hidden_fields ) {

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
		$hidden_ids = [];

		foreach ( $hidden_fields as $hidden_field ) {
			if ( ! is_array( $hidden_field ) ) {
				continue;
			}

			$hidden_id = sanitize_text_field( $hidden_field['id'] ?? '' );
			if ( '' !== $hidden_id ) {
				$hidden_ids[] = $hidden_id;
			}
		}

		$hidden_ids = array_unique( $hidden_ids );

        foreach ( $form_fields as $index => $field ) {
            if ( ! is_array( $field ) ) {
                continue;
            }

			$field_id = sanitize_text_field( $field['id'] ?? '' );
			if ( '' !== $field_id && in_array( $field_id, $hidden_ids, true ) ) {
				unset( $form_fields[ $index ] );
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
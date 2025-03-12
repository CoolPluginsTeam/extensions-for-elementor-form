<?php
namespace Cool_FormKit\Modules\Forms\Components;

use Cool_FormKit\Includes\Utils;
use Cool_FormKit\Modules\Forms\Classes\Form_Record;
use Cool_FormKit\Modules\Forms\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Ajax_Handler {

	public $is_success = true;
	public $messages = [
		'success' => [],
		'error' => [],
		'admin_error' => [],
	];
	public $data = [];
	public $errors = [];

	private $current_form;

	const SUCCESS = 'success';
	const ERROR = 'error';
	const FIELD_REQUIRED = 'required_field';
	const INVALID_FORM = 'invalid_form';
	const SERVER_ERROR = 'server_error';
	const SUBSCRIBER_ALREADY_EXISTS = 'subscriber_already_exists';
	const NONCE_ACTION = 'cool-form-submission';

	public static function get_default_messages(): array {
		return [
			self::SUCCESS => esc_html__( 'Your submission was successful.', 'cool-formkit' ),
			self::ERROR => esc_html__( 'Your submission failed because of an error.', 'cool-formkit' ),
			self::FIELD_REQUIRED => esc_html__( 'This field is required.', 'cool-formkit' ),
			self::INVALID_FORM => esc_html__( 'Your submission failed because the form is invalid.', 'cool-formkit' ),
			self::SERVER_ERROR => esc_html__( 'Your submission failed because of a server error.', 'cool-formkit' ),
			self::SUBSCRIBER_ALREADY_EXISTS => esc_html__( 'Subscriber already exists.', 'cool-formkit' ),
		];
	}

	public static function get_default_message( $id, $settings ): string {
		if ( ! empty( $settings['custom_messages'] ) ) {
			$field_id = $id . '_message';
			if ( isset( $settings[ $field_id ] ) ) {
				return $settings[ $field_id ];
			}
		}

		$default_messages = self::get_default_messages();

		return $default_messages[ $id ] ?? esc_html__( 'Unknown error.', 'cool-formkit' );
	}

	public function ajax_send_form() {
		check_ajax_referer( self::NONCE_ACTION, 'nonce' );

		// $post_id that holds the form settings.
		$post_id = filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT );
		$queried_id = filter_input( INPUT_POST, 'queried_id', FILTER_SANITIZE_NUMBER_INT );

		// $queried_id the post for dynamic values data.
		if ( ! $queried_id ) {
			$queried_id = $post_id;
		}

		// Make the post as global post for dynamic values.
		Utils::elementor()->db->switch_to_post( $queried_id );

		$form_id = filter_input( INPUT_POST, 'form_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		$elementor = Utils::elementor();
		$document = $elementor->documents->get( $post_id );
		$form = null;
		$template_id = null;

		if ( $document ) {
			$form = Module::find_element_recursive( $document->get_elements_data(), (string) $form_id );
		}

		if ( empty( $form ) ) {
			$this
				->add_error_message( self::get_default_message( self::INVALID_FORM, [] ) )
				->send();
		}

		// restore default values
		$widget = $elementor->elements_manager->create_element_instance( $form );
		$form['settings'] = $widget->get_settings_for_display();
		$form['settings']['id'] = $form_id;
		$form['settings']['form_post_id'] = $template_id ? $template_id : $post_id;

		// TODO: Should be removed if there is an ability to edit "global widgets"
		$form['settings']['edit_post_id'] = $post_id;

		$this->current_form = $form;

		if ( empty( $form['settings']['form_fields'] ) ) {
			$this
				->add_error_message( self::get_default_message( self::INVALID_FORM, $form['settings'] ) )
				->send();
		}

		// the fields are not fixed so they will be validated afterwards
		$form_fields = filter_input(
			INPUT_POST,
			'form_fields',
			FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			FILTER_REQUIRE_ARRAY
		);

		$record = new Form_Record( $form_fields, $form );

		if ( ! $record->validate( $this ) ) {
			$this
				->add_error( $record->get( 'errors' ) )
				->add_error_message( self::get_default_message( self::ERROR, $form['settings'] ) )
				->send();
		}

		$record->process_fields( $this );
		//check for process errors
		if ( ! empty( $this->errors ) ) {
			$this->send();
		}

		$module = Module::instance();

		$actions = $module->actions_registrar->get();
		$errors = array_merge( $this->messages['error'], $this->messages['admin_error'] );

		foreach ( $actions as $action ) {

			$exception = null;

			try {
				$action->run( $record, $this );

				$this->handle_bc_errors( $errors );
			} catch ( \Exception $e ) {
				$exception = $e;

				// Add an admin error.
				if ( ! in_array( $exception->getMessage(), $this->messages['admin_error'], true ) ) {
					$this->add_admin_error_message( "{$action->get_label()} {$exception->getMessage()}" );
				}

				// Add a user error.
				$this->add_error_message( $this->get_default_message( self::ERROR, $this->current_form['settings'] ) );
			}

			$errors = array_merge( $this->messages['error'], $this->messages['admin_error'] );
		}

		$this->send();
	}

	public function add_success_message( $message ) {
		$this->messages['success'][] = $message;

		return $this;
	}

	public function add_response_data( $key, $data ) {
		$this->data[ $key ] = $data;

		return $this;
	}

	public function add_error_message( $message ) {
		$this->messages['error'][] = $message;
		$this->set_success( false );

		return $this;
	}

	public function add_error( $field, $message = '' ) {
		if ( is_array( $field ) ) {
			$this->errors += $field;
		} else {
			$this->errors[ $field ] = $message;
		}

		$this->set_success( false );

		return $this;
	}

	public function add_admin_error_message( $message ) {
		$this->messages['admin_error'][] = $message;
		$this->set_success( false );

		return $this;
	}

	public function set_success( $is_success ) {
		$this->is_success = $is_success;

		return $this;
	}

	public function send() {
		if ( $this->is_success ) {
			wp_send_json_success( [
				'message' => $this->get_default_message( self::SUCCESS, $this->current_form['settings'] ),
				'data' => $this->data,
			] );
		}

		if ( empty( $this->messages['error'] ) && ! empty( $this->errors ) ) {
			$this->add_error_message( $this->get_default_message( self::INVALID_FORM, $this->current_form['settings'] ) );
		}

		$post_id = filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT );

		$error_msg = implode( '<br>', $this->messages['error'] );

		if ( current_user_can( 'edit_post', $post_id ) && ! empty( $this->messages['admin_error'] ) ) {
			$this->add_admin_error_message( esc_html__( 'This message is not visible to site visitors.', 'cool-formkit' ) );
			$error_msg .= '<div class="elementor-forms-admin-errors">' . implode( '<br>', $this->messages['admin_error'] ) . '</div>';
		}

		wp_send_json_error( [
			'message' => $error_msg,
			'errors' => $this->errors,
			'data' => $this->data,
		] );
	}

	public function get_current_form() {
		return $this->current_form;
	}

	/**
	 * BC: checks if the current action add some errors to the errors array
	 * if it adds an error the "run" method treat it as a failed action.
	 *
	 * @param $errors
	 *
	 * @throws \Exception
	 */
	private function handle_bc_errors( $errors ) {
		$current_errors = array_merge( $this->messages['error'], $this->messages['admin_error'] );
		$errors_diff = array_diff( $current_errors, $errors );

		if ( count( $errors_diff ) > 0 ) {
			throw new \Exception( esc_html( implode( ', ', $errors_diff ) ) );
		}
	}

	public function __construct() {
		add_action( 'wp_ajax_coolformkit_forms_send_form', [ $this, 'ajax_send_form' ] );
		add_action( 'wp_ajax_nopriv_coolformkit_forms_send_form', [ $this, 'ajax_send_form' ] );
	}
}

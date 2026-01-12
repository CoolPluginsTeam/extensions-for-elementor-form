<?php
namespace Cool_FormKit\Modules\Forms\Classes;

use Cool_FormKit\Includes\Utils;
use Cool_FormKit\Modules\Forms\Components\Ajax_Handler;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Form_Record {
	protected $sent_data;
	protected $fields;
	protected $form_type;
	public $form_settings;
	protected $files = [];
	protected $meta = [];

	public function get_formatted_data( $with_meta = false ): array {
		$formatted = [];
		$no_label = esc_html__( 'No Label', 'extensions-for-elementor-form' );
		$fields = $this->fields;

		if ( $with_meta ) {
			$fields = array_merge( $fields, $this->meta );
		}

		foreach ( $fields as $key => $field ) {
			if ( empty( $field['title'] ) ) {
				$formatted[ $no_label . ' ' . $key ] = $field['value'];
			} else {
				$formatted[ $field['title'] ] = $field['value'];
			}
		}

		return $formatted;
	}

	/**
	 * @param Ajax_Handler $ajax_handler An instance of the ajax handler.
	 *
	 * @return bool
	 */
	public function validate( Ajax_Handler $ajax_handler ): bool {
		foreach ( $this->fields as $id => $field ) {
			$field_type = $field['type'];
			if ( ! empty( $field['required'] ) && '' === $field['value'] && 'upload' !== $field_type ) {
				$ajax_handler->add_error( $id, Ajax_Handler::get_default_message( Ajax_Handler::FIELD_REQUIRED, $this->form_settings ) );
			}

			/**
			 * Cool form field validation.
			 *
			 * Fires when a single form field is being validated. This hook allows developers
			 * to validate individual field types.
			 *
			 * The dynamic portion of the hook name, `$field_type`, refers to the field type.
			 *
			 * @since 1.0.0
			 *
			 * @param array        $field        Form field.
			 * @param Form_Record  $this         An instance of the form record.
			 * @param Ajax_Handler $ajax_handler An instance of the ajax handler.
			 */
			do_action( "cool_formkit/forms/validation/{$field_type}", $field, $this, $ajax_handler );
		}

		/**
		 * Cool form validation.
		 *
		 * Fires when form fields are being validated. This hook allows developers
		 * to validate all form fields.
		 *
		 * @since 1.0.0
		 *
		 * @param Form_Record  $this         An instance of the form record.
		 * @param Ajax_Handler $ajax_handler An instance of the ajax handler.
		 */
		do_action( 'cool_formkit/forms/validation', $this, $ajax_handler );

		return empty( $ajax_handler->errors );
	}

	/**
	 * @param Ajax_Handler $ajax_handler An instance of the ajax handler.
	 *
	 */
	public function process_fields( Ajax_Handler $ajax_handler ) {
		foreach ( $this->fields as $id => $field ) {
			$field_type = $field['type'];

			/**
			 * Cool form field process.
			 *
			 * Fires when a single form field is being processed. This hook allows developers
			 * to process individual field types.
			 *
			 * The dynamic portion of the hook name, `$field_type`, refers to the field type.
			 *
			 * @since 1.0.0
			 *
			 * @param array        $field        Form field.
			 * @param Form_Record  $this         An instance of the form record.
			 * @param Ajax_Handler $ajax_handler An instance of the ajax handler.
			 */
			do_action( "cool_formkit/forms/process/{$field_type}", $field, $this, $ajax_handler );
		}

		/**
		 * Cool form process.
		 *
		 * Fires when form fields are being processed. This hook allows developers
		 * to process all form fields.
		 *
		 * @since 1.0.0
		 *
		 * @param Form_Record  $this         An instance of the form record.
		 * @param Ajax_Handler $ajax_handler An instance of the ajax handler.
		 */
		do_action( 'cool_formkit/forms/process', $this, $ajax_handler );
	}

	public function get( $property ) {
		if ( isset( $this->{$property} ) ) {
			return $this->{$property};
		}

		return null;
	}

	public function set( $property, $value ) {
		$this->{$property} = $value;
	}

	public function get_form_settings( $setting ) {
		if ( isset( $this->form_settings[ $setting ] ) ) {
			return $this->form_settings[ $setting ];
		}

		return null;
	}

	public function get_field( $args ) {
		return wp_list_filter( $this->fields, $args );
	}

	public function remove_field( $id ) {
		unset( $this->fields[ $id ] );
	}

	public function update_field( $field_id, $property, $value ) {
		if ( ! isset( $this->fields[ $field_id ] ) || ! isset( $this->fields[ $field_id ][ $property ] ) ) {
			return;
		}
		$this->fields[ $field_id ][ $property ] = $value;
	}

	public function get_form_meta( $meta_keys = [] ) {

		$result = [];

		foreach ( $meta_keys as $metadata_type ) {
			switch ( $metadata_type ) {
				case 'date':
					$result['date'] = [
						'title' => esc_html__( 'Date', 'extensions-for-elementor-form' ),
						'value' => date_i18n( get_option( 'date_format' ) ),
					];
					break;

				case 'time':
					$result['time'] = [
						'title' => esc_html__( 'Time', 'extensions-for-elementor-form' ),
						'value' => date_i18n( get_option( 'time_format' ) ),
					];
					break;

				case 'page_url':
					$referrer = filter_input( INPUT_POST, 'referrer', FILTER_SANITIZE_URL );
					$result['page_url'] = [
						'title' => esc_html__( 'Page URL', 'extensions-for-elementor-form' ),
						'value' => $referrer ? esc_url_raw( wp_unslash( $referrer ) ) : '',
					];
					break;

				case 'page_title':
					$referrer_title = filter_input( INPUT_POST, 'referer_title', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
					$result['page_title'] = [
						'title' => esc_html__( 'Page Title', 'extensions-for-elementor-form' ),
						'value' => $referrer_title ? sanitize_text_field( wp_unslash( $referrer_title ) ) : '',
					];
					break;

				case 'user_agent':
					$user_agent = filter_input( INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
					$result['user_agent'] = [
						'title' => esc_html__( 'User Agent', 'extensions-for-elementor-form' ),
						'value' => $user_agent ? sanitize_textarea_field( wp_unslash( $user_agent ) ) : '',
					];
					break;

				case 'remote_ip':
					$result['remote_ip'] = [
						'title' => esc_html__( 'Remote IP', 'extensions-for-elementor-form' ),
						'value' => Utils::get_client_ip(),
					];
					break;
				case 'credit':
					$result['credit'] = [
						'title' => esc_html__( 'Powered by', 'extensions-for-elementor-form' ),
						'value' => esc_html__( 'Elementor', 'extensions-for-elementor-form' ),
					];
					break;
			}
		}

		return $result;
	}

	private function set_meta() {
		$form_metadata = $this->form_settings['form_metadata'];

		if ( empty( $form_metadata ) ) {
			return;
		}

		$this->meta = $this->get_form_meta( $form_metadata );
	}

	private function set_fields() {
		foreach ( $this->form_settings['form_fields'] as $form_field ) {
			$field = [
				'id' => $form_field['custom_id'],
				'type' => $form_field['field_type'],
				'title' => $form_field['field_label'],
				'value' => '',
				'raw_value' => '',
				'required' => ! empty( $form_field['required'] ),
			];

			if ( 'upload' === $field['type'] ) {
				$field['file_sizes'] = $form_field['file_sizes'] ?? '';
				$field['file_types'] = $form_field['file_types'] ?? '';
				$field['max_files'] = $form_field['max_files'] ?? '';
				$field['attachment_type'] = $form_field['attachment_type'] ?? '';
			}

			if ( isset( $this->sent_data[ $form_field['custom_id'] ] ) ) {
				$field['raw_value'] = $this->sent_data[ $form_field['custom_id'] ];

				$value = $field['raw_value'];

				if ( is_array( $value ) ) {
					$value = implode( ', ', $value );
				}

				$field['value'] = $this->sanitize_field( $field, $value );
			}
			$this->fields[ $form_field['custom_id'] ] = $field;
		}
	}

	private function sanitize_field( $field, $value ) {
		$field_type = $field['type'];
		switch ( $field_type ) {
			case 'text':
			case 'password':
			case 'hidden':
			case 'search':
			case 'checkbox':
			case 'radio':
			case 'select':
				$value = sanitize_text_field( $value );
				break;
			case 'url':
				$value = esc_url_raw( $value );
				break;
			case 'textarea':
				$value = sanitize_textarea_field( $value );
				break;
			case 'email':
				$value = sanitize_email( $value );
				break;
			default:
				/**
				 * Sanitize field value.
				 *
				 * Filters the value of the form field for sanitization purpose. This hook allows
				 * developers to add custom sanitization for field values.
				 *
				 * The dynamic portion of the hook name, `$field_type`, refers to the field type.
				 *
				 * @since 1.0.0
				 *
				 * @param string $value The field value.
				 * @param array  $field The field array.
				 */
				$value = apply_filters( "cool_formkit/forms/sanitize/{$field_type}", $value, $field );
		}

		return $value;
	}

	public function replace_setting_shortcodes( $setting, $urlencode = false ) {
		// Shortcode can be `[field id="fds21fd"]` or `[field title="Email" id="fds21fd"]`, multiple shortcodes are allowed

		if ( is_null( $setting ) ) {
			return '';  
		}

		return preg_replace_callback( '/(\[field[^]]*id="(\w+)"[^]]*\])/', function ( $matches ) use ( $urlencode ) {
			$value = '';

			if ( isset( $this->fields[ $matches[2] ] ) ) {
				$value = $this->fields[ $matches[2] ]['value'];
			}

			if ( $urlencode ) {
				$value = rawurlencode( $value );
			}
			return $value;
		}, $setting );
	}


	public function has_field_type( $type ) {
		foreach ( $this->fields as $id => $field ) {
			if ( $type === $field['field_type'] ) {
				return true;
			}
		}

		return false;
	}

	public function __construct( $sent_data, $form ) {
		$this->form_type = $form['widgetType'];
		$this->form_settings = $form['settings'];
		$this->sent_data = stripslashes_deep( $sent_data );

		$this->set_fields();
		$this->set_meta();
	}
}

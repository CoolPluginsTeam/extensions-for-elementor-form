<?php

namespace Cool_FormKit\Includes\Actions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use \Elementor\Plugin as ElementorPlugin;
use \Elementor\Controls_Manager as ElementorControls;
use \Elementor\Repeater as ElementorRepeater;

/**
 * Register post after form submit.
 */
class Register_Post extends \ElementorPro\Modules\Forms\Classes\Action_Base {
	public function __construct() {
		\add_action( 'elementor/element/form/section_form_fields/before_section_end', array( $this, 'add_control_fields' ), 100, 2 );
	}
	/**
	 * Get Name
	 *
	 * Return the action name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'eef-register-post';
	}

	/**
	 * Get Label
	 *
	 * Returns the action label
	 *
	 * @return string
	 */
	public function get_label() {
		return 'Register Post/Custom Post';
	}

	/**
	 * Get public post types
	 */
	private function get_registered_post_types() {
		$registered_post_types = get_post_types( array( 'public' => true ), 'objects' );
		$post_type_options =  array();
		foreach ( $registered_post_types as $post_type ) {
			$post_type_options[ $post_type->name ] = $post_type->label;
		}

		return $post_type_options;
	}

	/**
	 * Register Settings Section
	 *
	 * Registers the Action controls
	 *
	 * @param \Elementor\Widget_Base $widget
	 */
	public function register_settings_section( $widget ) {
		$widget->start_controls_section(
			'eef-register-post-section',
			[
				'label' => \esc_html__( 'Register Post/Custom Post', 'extensions-for-elementor-form' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);

		/**
		 * TODO:
		 * 1. Add or edit existent post.
		 * 2. Post terms (taxonomy).
		 * 3. Redirect to post after submit/created?.
		 * 4. Se post status === private add campo de senha.
		 */
		$widget->add_control(
			'eef-register-post-post-type',
			[
				'label' => \esc_html__( 'Post Type', 'extensions-for-elementor-form' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'post',
				'options' => $this->get_registered_post_types(),
			]
		);

		$widget->add_control(
			'eef-register-post-post-status',
			[
				'label' => \esc_html__( 'Post Status', 'extensions-for-elementor-form' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'draft',
				'options' => [
					'draft'  => \esc_html__( 'Draft', 'extensions-for-elementor-form' ),
					'publish' => \esc_html__( 'Publish', 'extensions-for-elementor-form' ),
					'pending' => \esc_html__( 'Pending', 'extensions-for-elementor-form' ),
				],
			]
		);

		$widget->add_control(
			'eef-register-post-user-permission',
			[
				'label' => \esc_html__( 'Run only to logged in users?', 'extensions-for-elementor-form' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => \esc_html__( 'Yes', 'extensions-for-elementor-form' ),
				'label_off' => \esc_html__( 'No', 'extensions-for-elementor-form' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'description' => \esc_html__( 'Warning: Save data from not logged in users can be a security risk', 'extensions-for-elementor-form' ),
			]
		);

		$widget->end_controls_section();
	}

	/**
	 * On Export
	 *
	 * Clears form settings on export
	 * @param array $element
	 */
	public function on_export( $element ) {
		unset(
			$element['settings']['eef-register-post-post-type'],
			$element['settings']['eef-register-post-post-status'],
			$element['settings']['eef-register-post-user-permission'],
		);

		return $element;
	}

	/**
	 * Runs the action after submit
	 *
	 * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
	 * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
	 */
	public function run( $record, $ajax_handler ) {
		$new_post_data = array(
			'post_type' => $record->get_form_settings( 'eef-register-post-post-type' ),
			'post_status' => $record->get_form_settings( 'eef-register-post-post-status' ),
			'post_title' => $record->get_form_settings( 'form_name' ),
			'post_content' => $record->get_form_settings( 'form_name' ),
		);

		$form_fields = $record->get( 'fields' );
		$fields_settings = $record->get_form_settings( 'form_fields' );
		$formated_fields = array();
		foreach ( $fields_settings as $key => $field ) {
			$formated_fields[ $key ] = $form_fields[ $field['custom_id'] ];
			$formated_fields[ $key ]['field-to-register'] = $field['eef-register-post-field'];
			$formated_fields[ $key ]['custom-field-to-register'] = $field['eef-register-post-custom-field'];
		}

		$custom_fields_to_register = array();
		foreach ( $formated_fields as $field ) {
			if ( $field['field-to-register'] !== 'custom_field' ) {
				$new_post_data[ $field['field-to-register'] ] = $field['value'];
			} else {
				$custom_fields_to_register[ $field['custom-field-to-register'] ] = $field['value'];
			}
		}

		$is_restrict_to_loggedin_users = $record->get_form_settings( 'eef-register-post-user-permission' );
		if ( $is_restrict_to_loggedin_users !== 'yes' ) {
			$post_id = wp_insert_post( $new_post_data, true );
			if ( ! is_wp_error( $post_id ) ) {
				foreach ( $custom_fields_to_register as $meta_key => $meta_value ) {
					add_post_meta( $post_id, $meta_key, $meta_value );
				}
			}
			return;
		}

		if ( is_user_logged_in() ) {
			$post_id = wp_insert_post( $new_post_data, true );
			if ( ! is_wp_error( $post_id ) ) {
				foreach ( $custom_fields_to_register as $meta_key => $meta_value ) {
					add_post_meta( $post_id, $meta_key, $meta_value );
				}
			}
		}
	}

	/**
	 * Add create post fields
	 *
	 * @param $element
	 * @param $args
	 */
	public function add_control_fields( $element, $args ) {
		$elementor = ElementorPlugin::instance();
		$control_data = $elementor->controls_manager->get_control_from_stack( $element->get_name(), 'form_fields' );

		if ( is_wp_error( $control_data ) ) {
			return;
		}

		$new_control = [
			'label' => \esc_html__( 'Field to Register', 'extensions-for-elementor-form' ),
			'type' => ElementorControls::SELECT,
			'tab' => 'content',
			'tabs_wrapper' => 'form_fields_tabs',
			'inner_tab' => 'form_fields_advanced_tab',
			'classes' => 'elementor-hidden-control',
			'description' => \esc_html__( 'Use this input to define what post field will receive this data when post is registered', 'extensions-for-elementor-form' ),
			'default' => 'select',
			'options' => [
				'select' => \esc_html__( 'Select', 'extensions-for-elementor-form' ),
				'post_title' => \esc_html__( 'Post Title', 'extensions-for-elementor-form' ),
				'post_content' => \esc_html__( 'Post Content', 'extensions-for-elementor-form' ),
				'post_excerpt' => \esc_html__( 'Post Excerpt', 'extensions-for-elementor-form' ),
				'post_author' => \esc_html__( 'Post Author', 'extensions-for-elementor-form' ),
				'custom_field' => \esc_html__( 'Custom Field', 'extensions-for-elementor-form' ),
			],
		];

		$new_control_2 = [
			'label' => \esc_html__( 'Custom Field Name', 'extensions-for-elementor-form' ),
			'type' => ElementorControls::TEXT,
			'placeholder' => \esc_html__( 'custom_field_name', 'extensions-for-elementor-form' ),
			'tab' => 'content',
			'tabs_wrapper' => 'form_fields_tabs',
			'inner_tab' => 'form_fields_advanced_tab',
			'description' => \esc_html__( 'Add the Custom Field name here. You can use default fields or custom created with ACF or similars', 'extensions-for-elementor-form' ),
			'condition' => [
					'eef-register-post-field' => 'custom_field',
			],
		];

		$mask_control = new ElementorRepeater();
		$mask_control->add_control( 'eef-register-post-field', $new_control );
		$mask_control->add_control( 'eef-register-post-custom-field', $new_control_2 );
		$pattern_field = $mask_control->get_controls();

		/**
		 * Register control in form advanced tab.
		 */
		$this->register_control_in_form_advanced_tab( $element, $control_data, $pattern_field );
	}

	/**
	 * Register control in form advanced tab
	 *
	 * @param object $element
	 * @param array $control_data
	 * @param array $pattern_field
	 */
	public function register_control_in_form_advanced_tab( $element, $control_data, $pattern_field ) {
		foreach( $pattern_field as $key => $control ) {
			if( $key !== '_id' ) {
				$new_order = [];
				foreach ( $control_data['fields'] as $field_key => $field ) {
					if ( 'field_value' === $field['name'] ) {
						$new_order[$key] = $control;
					}
					$new_order[ $field_key ] = $field;
				}

				$control_data['fields'] = $new_order;
			}
		}

		return $element->update_control( 'form_fields', $control_data );
	}
}

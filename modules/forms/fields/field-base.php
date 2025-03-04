<?php
namespace Cool_FormKit\Modules\Forms\Fields;

use Cool_FormKit\Modules\Forms\Classes;
use Cool_FormKit\Modules\Forms\Components\Ajax_Handler;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

abstract class Field_Base {
	public $depended_scripts = [];
	public $depended_styles = [];

	abstract public function get_type();

	abstract public function get_name();

	/**
	 * Get the field ID.
	 *
	 * TODO: Make it an abstract function that will replace `get_type()`.
	 *
	 * @since 3.5.0
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->get_type();
	}

	abstract public function render( $item, $item_index, $form );

	public function validation( $field, Classes\Form_Record $record, Ajax_Handler $ajax_handler ) {}

	public function process_field( $field, Classes\Form_Record $record, Ajax_Handler $ajax_handler ) {}

	public function add_assets_depends() {
		foreach ( $this->depended_scripts as $script ) {
			wp_enqueue_script( $script );
		}

		foreach ( $this->depended_styles as $style ) {
			wp_enqueue_style( $style );
		}
	}

	public function add_preview_depends() {
		foreach ( $this->depended_scripts as $script ) {
			wp_enqueue_script( $script );
		}

		foreach ( $this->depended_styles as $style ) {
			wp_enqueue_style( $style );
		}
	}

	public function add_field_type( $field_types ) {
		if ( ! in_array( $this->get_type(), $field_types, true ) ) {
			$field_types[ $this->get_type() ] = $this->get_name();
		}

		return $field_types;
	}

	public function field_render( $item, $item_index, $form ) {
		$this->add_assets_depends( $form );
		$this->render( $item, $item_index, $form );
	}

	public function sanitize_field( $value, $field ) {
		return sanitize_text_field( $value );
	}

    public static function elementor() {
		return \Elementor\Plugin::$instance;
	}

	public function inject_field_controls( $data, $controls_to_inject ) {
		$keys = array_keys( $data );
		$key_index = array_search( 'required', $keys, true ) + 1;

		return array_merge( array_slice( $data, 0, $key_index, true ),
			$controls_to_inject,
			array_slice( $data, $key_index, null, true )
		);
	}

	public function __construct() {
		$field_type = $this->get_type();
		add_action( "cool_formkit/forms/render_field/{$field_type}", [ $this, 'field_render' ], 10, 3 );
		add_action( "cool_formkit/forms/validation/{$field_type}", [ $this, 'validation' ], 10, 3 );
		add_action( "cool_formkit/forms/process/{$field_type}", [ $this, 'process_field' ], 10, 3 );
		add_filter( 'cool_formkit/forms/field_types', [ $this, 'add_field_type' ] );
		add_filter( "cool_formkit/forms/sanitize/{$field_type}", [ $this, 'sanitize_field' ], 10, 2 );
		add_action( 'elementor/preview/enqueue_scripts', [ $this, 'add_preview_depends' ] );
		if ( method_exists( $this, 'update_controls' ) ) {
			add_action( 'elementor/element/cool-form/section_form_fields/before_section_end', [ $this, 'update_controls' ] );
		}
	}
}

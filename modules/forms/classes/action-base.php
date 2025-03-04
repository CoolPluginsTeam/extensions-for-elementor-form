<?php
namespace Cool_FormKit\Modules\Forms\Classes;

use Cool_FormKit\Modules\Forms\Components\Ajax_Handler;
use Cool_FormKit\Modules\Forms\Widgets\Cool_Form;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class Action_Base {

	abstract public function get_name(): string;

	abstract public function get_label(): string;

	public function get_id() {
		return $this->get_name();
	}

	/**
	 * @param Form_Record  $record
	 * @param Ajax_Handler $ajax_handler
	 */
	abstract public function run( Form_Record $record, Ajax_Handler $ajax_handler );

	/**
	 * @param Cool_Form $form
	 */
	abstract public function register_settings_section( $form );

	/**
	 * @param array $element
	 */
	abstract public function on_export( $element );
}

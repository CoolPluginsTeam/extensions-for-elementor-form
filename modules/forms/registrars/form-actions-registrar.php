<?php

namespace Cool_FormKit\Modules\Forms\Registrars;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Basic form actions registration manager.
 */
class Form_Actions_Registrar extends Registrar {

	const FEATURE_NAME_CLASS_NAME_MAP = [
		'email' => 'Email',
		'redirect' => 'Redirect',
	];

	/**
	 * Form_Actions_Registrar constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();

		$this->init();
	}

	/**
	 * Initialize the default fields.
	 *
	 * @return void
	 */
	public function init() {
		foreach ( static::FEATURE_NAME_CLASS_NAME_MAP as $action ) {
			$class_name = 'Cool_FormKit\Modules\Forms\Actions\\' . $action;
			$this->register( new $class_name() );
		}
	}
}

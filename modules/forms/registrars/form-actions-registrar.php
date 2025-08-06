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
		'collect-entries' => 'Collect_Entries',
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
		add_action( 'cool_form/forms/actions/register', function ( Form_Actions_Registrar $actions_registrar ) {

			$form_actions = static::FEATURE_NAME_CLASS_NAME_MAP;
			foreach ( $form_actions as $action ) {
				$class_name = 'Cool_FormKit\Modules\Forms\Actions\\' . $action;
				$actions_registrar->register( new $class_name() );
			}
		} );

		/**
		 * Cool Form actions registration.
		 *
		 * Fires when a new form action is registered. This hook allows developers to
		 * register new form actions.
		 *
		 * @since 3.5.0
		 *
		 * @param Form_Actions_Registrar $this An instance of form actions registration
		 *                                     manager.
		 */

		add_action('init', function () {
        	do_action('cool_form/forms/actions/register', $this);
    	});

	}
}

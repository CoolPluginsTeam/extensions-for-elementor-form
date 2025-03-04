<?php

namespace Cool_FormKit\Includes\Actions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Register_Actions {
	/**
	 * Actions
	 */
	private array $actions;

	public function __construct( $actions ) {
		$this->actions = $actions;
	}

	public function set_hooks() : void {
		\add_action( 'elementor_pro/forms/actions/register', array( $this, 'register' ), -10 );
	}

	/**
	 * Register form acitons to be used after subumitting the form.
	 *
	 * @since 2.0
	 * @param ElementorPro\Modules\Forms\Registrars\Form_Actions_Registrar $form_actions_registrar
	 */
	public function register( $form_actions_registrar ) : void {
		if ( empty( $this->actions ) ) {
			return;
		}

		foreach ( $this->actions as $action ) {
			include_once CFL_PLUGIN_PATH . $action['relative_path'] ?? '';

			$class_name = 'Cool_FormKit\Includes\Actions\\' . $action['class_name'];
			if ( class_exists( $class_name ) ) {
				$form_actions_registrar->register( new $class_name() );
			}
		}
	}
}

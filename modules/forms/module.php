<?php

namespace Cool_FormKit\Modules\Forms;

use Elementor\Controls_Manager;
use Cool_FormKit\Includes\Module_Base;
use Cool_FormKit\Modules\Forms\components\Ajax_Handler;
use Cool_FormKit\Modules\Forms\Controls\Fields_Map;
use Cool_FormKit\Modules\Forms\Controls\Fields_Repeater;
use Cool_FormKit\Modules\Forms\Registrars\Form_Actions_Registrar;
use Cool_FormKit\Modules\Forms\Registrars\Form_Fields_Registrar;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module extends Module_Base {
	/**
	 * @var Form_Actions_Registrar
	 */
	public $actions_registrar;

	/**
	 * @var Form_Fields_Registrar
	 */
	public $fields_registrar;


	public static function get_name(): string {
		return 'cool-forms';
	}

	protected function get_widget_ids(): array {
		return [
			'Cool_Form',
		];
	}

	/**
	 * Get the base URL for assets.
	 *
	 * @return string
	 */
	public function get_assets_base_url(): string {
		return CFL_PLUGIN_URL;
	}

	/**
	 * Register styles.
	 *
	 * At build time, Elementor compiles `/modules/forms/assets/scss/frontend.scss`
	 * to `/assets/css/widget-forms.min.css`.
	 *
	 * @return void
	 */
	public function register_styles() {
		wp_register_style(
			'Cool_FormKit-forms',
			CFL_STYLE_URL . 'Cool_FormKit-forms.css',
			[ 'elementor-frontend' ],
			CFL_VERSION
		);

		wp_register_style(
			'cool-form-material-css',
			CFL_PLUGIN_URL . 'assets/css/Material-css/material.css',
			[ 'elementor-frontend' ],
			CFL_VERSION
		);
	}

	public static function find_element_recursive( $elements, $form_id ) {
		foreach ( $elements as $element ) {
			if ( $form_id === $element['id'] ) {
				return $element;
			}

			if ( ! empty( $element['elements'] ) ) {
				$element = self::find_element_recursive( $element['elements'], $form_id );

				if ( $element ) {
					return $element;
				}
			}
		}

		return false;
	}

	public function register_controls( Controls_Manager $controls_manager ) {
		$controls_manager->register( new Fields_Repeater() );
		$controls_manager->register( new Fields_Map() );
	}

	public function enqueue_editor_styles(){
		wp_enqueue_style(
			'Cool_FormKit-forms-editor',
			CFL_STYLE_URL . 'Cool_FormKit-editor.css',
			[],
			CFL_VERSION,
			'all'
		);
	}
	public function enqueue_editor_scripts() {
		wp_enqueue_script(
			'Cool_FormKit-forms-editor',
			CFL_SCRIPTS_URL . 'Cool_FormKit-forms-editor.js',
			[ 'elementor-editor', 'wp-i18n' ],
			CFL_VERSION,
			true
		);
	}

	public function register_scripts() {
		wp_register_script(
			'Cool_FormKit-forms-fe',
			CFL_SCRIPTS_URL . 'Cool_FormKit-forms-fe.js',
			// [ 'elementor-common', 'elementor-frontend-modules', 'elementor-frontend' ],
			[ 'elementor-frontend' ],
			CFL_VERSION,
			true
		);

		wp_register_script(
			'cool-form-material-js',
			CFL_PLUGIN_URL . 'assets/js/Material-js/material.js',
			// [ 'elementor-common', 'elementor-frontend-modules', 'elementor-frontend' ],
			[ 'elementor-frontend' ],
			CFL_VERSION,
			true
		);

		wp_register_script(
			'cool-form-material-handle-js',
			CFL_PLUGIN_URL . 'assets/js/Material-js/material-field-handle.js',
			// [ 'elementor-common', 'elementor-frontend-modules', 'elementor-frontend' ],
			[ 'elementor-frontend' ],
			CFL_VERSION,
			true
		);

		wp_localize_script(
			'Cool_FormKit-forms-fe',
			'coolFormsData',
			[
				'nonce' => wp_create_nonce( Ajax_Handler::NONCE_ACTION ),
			]
		);
	}

	protected function get_component_ids(): array {
		return [ 'Ajax_Handler' ];
	}

	public static function get_site_domain() {
		return str_ireplace( 'www.', '', wp_parse_url( home_url(), PHP_URL_HOST ) );
	}

	protected function register_hooks(): void {
		parent::register_hooks();

		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'register_scripts' ] );
		add_action( 'elementor/frontend/after_register_styles', [ $this, 'register_styles' ] );
		add_action( 'elementor/controls/register', [ $this, 'register_controls' ] );
		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'enqueue_editor_scripts' ] );
		add_action( 'elementor/editor/after_enqueue_styles', [$this,'enqueue_editor_styles'],999);
	}

	/**
	 * Module constructor.
	 */
	public function __construct() {
		parent::__construct();

		// Initialize registrars.
		$this->actions_registrar = new Form_Actions_Registrar();
		$this->fields_registrar = new Form_Fields_Registrar();
		 new Ajax_Handler();
	}
}

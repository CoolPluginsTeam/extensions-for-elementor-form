<?php

namespace Cool_FormKit\Modules\Forms;

use Elementor\Controls_Manager;
use Cool_FormKit\Includes\Module_Base;
use Cool_FormKit\Modules\Forms\components\Ajax_Handler;
use Cool_FormKit\Modules\Forms\Controls\Fields_Map;
use Cool_FormKit\Modules\Forms\Controls\Fields_Repeater;
use Cool_FormKit\Modules\Forms\Registrars\Form_Actions_Registrar;
use Cool_FormKit\Modules\Forms\Registrars\Form_Fields_Registrar;
use Cool_FormKit\Modules\Forms\Classes\Recaptcha_Handler;
use Cool_FormKit\Modules\Forms\Classes\Recaptcha_V3_Handler;
use Cool_FormKit\Widgets\CREATE_COUNTRY_FIELD;


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


	const OPTION_NAME_SITE_KEY = 'elementor_pro_recaptcha_site_key';

	const OPTION_NAME_SECRET_KEY = 'elementor_pro_recaptcha_secret_key';

	const OPTION_NAME_RECAPTCHA_THRESHOLD = 'elementor_pro_recaptcha_threshold';

	const  OPTION_NAME_V3_SITE_KEY = "elementor_pro_recaptcha_v3_site_key";

    const OPTION_NAME_V3_SECRET_KEY = 'elementor_pro_recaptcha_v3_secret_key';


	const V2_CHECKBOX = 'v2_checkbox';

    const V3 = 'v3';


	protected static function get_recaptcha_name()
	{
		return 'recaptcha';
	}

	public static function get_site_key()
	{
		return get_option(self::OPTION_NAME_SITE_KEY);
	}

	public static function get_site_key_v3()
	{
		return get_option(self::OPTION_NAME_V3_SITE_KEY);
	}

	public static function get_secret_key()
	{
		return get_option(self::OPTION_NAME_SECRET_KEY);
	}

	public static function get_recaptcha_type()
	{
		return self::V2_CHECKBOX;
	}

	public static function get_recaptcha_type_v3()
    {
        return self::V3;
    }

	public static function get_site_key3()
    {
        return get_option(self::OPTION_NAME_V3_SITE_KEY);
    }

    public static function get_secret_key3()
    {
        return get_option(self::OPTION_NAME_V3_SECRET_KEY);
    }


	public static function is_enabled()
	{
		return static::get_site_key() && static::get_secret_key();
	}

	public static function is_enabled3()
	{
		return static::get_site_key3() && static::get_secret_key3();
	}


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

		wp_register_style(
			'cool-form-material-helper-css',
			CFL_PLUGIN_URL . 'assets/css/Material-css/material-helper.css',
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
		wp_register_script(
			'Cool_FormKit-forms-editor',
			CFL_SCRIPTS_URL . 'Cool_FormKit-forms-editor.min.js',
			[ 'elementor-editor', 'wp-i18n' ],
			CFL_VERSION,
			true
		);

		wp_localize_script('Cool_FormKit-forms-editor', 'coolFormKitRecaptcha', [
			'enabled'   => static::is_enabled(),
			'enabled3' => static::is_enabled3(),
			'site_key_v2'  => static::get_site_key(),
			'site_key_v3'  => static::get_site_key_v3(),
			'type_v2'      => static::get_recaptcha_type(),
			'type_v3'      => static::get_recaptcha_type_v3(),

		]);

		wp_enqueue_script('Cool_FormKit-forms-editor', true);
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

		add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'register_scripts' ] );
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

		if (class_exists(Recaptcha_Handler::class)) {

			$this->add_component( 'recaptcha', instance: new Classes\Recaptcha_Handler() );

        }

		if (class_exists(Recaptcha_V3_Handler::class)) {

			$this->add_component( 'recaptcha_v3', instance: new Classes\Recaptcha_V3_Handler() );

        }

		
		// Initialize registrars.
		$this->actions_registrar = new Form_Actions_Registrar();
		$this->fields_registrar = new Form_Fields_Registrar();
		 new Ajax_Handler();
		 new CREATE_COUNTRY_FIELD();
	}
}

<?php
/**
 * Shared Country Code addon logic for Elementor Pro, Cool Form, and Hello Plus.
 *
 * Concrete classes are standalone singletons (do not extend Field_Base).
 * Call init_country_code_addon() from the constructor — do not use parent:: for trait methods.
 *
 * @package Cool_FormKit
 */

namespace Cool_FormKit\Includes\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait Country_Code_Addon_Trait {

	/**
	 * @var bool
	 */
	private $validate_form = false;

	/**
	 * Forms validation hook (e.g. elementor_pro/forms/validation).
	 *
	 * @return string
	 */
	abstract protected function get_validation_hook(): string;

	/**
	 * Render field hook including tel type (e.g. elementor_pro/forms/render_field/tel).
	 *
	 * @return string
	 */
	abstract protected function get_render_field_hook(): string;

	/**
	 * Form fields section hook for injecting controls.
	 *
	 * @return string
	 */
	abstract protected function get_form_fields_section_hook(): string;

	/**
	 * Tel field type string (`tel` or `ehp-tel`).
	 *
	 * @return string
	 */
	abstract protected function get_tel_field_type(): string;

	/**
	 * @return string
	 */
	abstract protected function get_library_style_handle(): string;

	/**
	 * @return string
	 */
	abstract protected function get_style_handle(): string;

	/**
	 * @return string Absolute URL to the country-code stylesheet.
	 */
	abstract protected function get_style_src(): string;

	/**
	 * @return string
	 */
	abstract protected function get_library_script_handle(): string;

	/**
	 * @return string
	 */
	abstract protected function get_main_script_handle(): string;

	/**
	 * @return string Absolute URL to the platform country-code script.
	 */
	abstract protected function get_main_script_src(): string;

	/**
	 * @return string
	 */
	abstract protected function get_editor_script_handle(): string;

	/**
	 * @return string Absolute URL to the editor script.
	 */
	abstract protected function get_editor_script_src(): string;

	/**
	 * Elementor plugin instance exposing controls_manager.
	 *
	 * @return object
	 */
	abstract protected function get_elementor_plugin();

	/**
	 * Whether to inject the Elementor review notice control.
	 *
	 * @return bool
	 */
	protected function should_include_review_notice(): bool {
		return false;
	}

	/**
	 * Version for the main platform script.
	 *
	 * @return string
	 */
	protected function get_main_script_version(): string {
		return $this->version_from_src( $this->get_main_script_src() );
	}

	/**
	 * Version for the shared country-code script.
	 *
	 * @return string
	 */
	protected function get_shared_script_version(): string {
		return function_exists( 'cfl_asset_version' )
			? cfl_asset_version( 'assets/js/shared/country-code-script.js' )
			: CFL_VERSION;
	}

	/**
	 * Shared constructor body. Call from the concrete class constructor.
	 */
	protected function init_country_code_addon(): void {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );

		add_action( $this->get_render_field_hook(), array( $this, 'elementor_form_tel_field_rendering' ), 9, 3 );
		add_action( $this->get_form_fields_section_hook(), array( $this, 'update_controls' ), 100, 2 );
		add_action( 'elementor/preview/init', array( $this, 'editor_inline_JS' ) );
		add_action( $this->get_validation_hook(), array( $this, 'submit_validation' ), 9, 3 );

		if ( $this->should_include_review_notice() ) {
			add_action( 'elementor/editor/before_enqueue_styles', array( $this, 'editor_assets' ) );
			add_action( 'wp_ajax_ccfef_elementor_review_notice', array( $this, 'ccfef_elementor_review_notice' ) );
		}
	}

	public function register_styles() {
		$library_ver = function_exists( 'cfl_asset_version' )
			? cfl_asset_version( 'assets/css/intlTelInput.min.css' )
			: CFL_VERSION;
		$style_src   = $this->get_style_src();
		$style_ver   = CFL_VERSION;
		if ( function_exists( 'cfl_asset_version' ) && defined( 'CFL_PLUGIN_URL' ) && 0 === strpos( $style_src, CFL_PLUGIN_URL ) ) {
			$style_ver = cfl_asset_version( substr( $style_src, strlen( CFL_PLUGIN_URL ) ) );
		}

		wp_register_style(
			$this->get_library_style_handle(),
			CFL_PLUGIN_URL . 'assets/css/intlTelInput.min.css',
			array(),
			$library_ver,
			'all'
		);
		wp_register_style(
			$this->get_style_handle(),
			$style_src,
			array(),
			$style_ver,
			'all'
		);
	}

	public function register_scripts() {
		$library_handle = $this->get_library_script_handle();
		$main_handle    = $this->get_main_script_handle();
		$dependency_array    = array( 'elementor-frontend', 'jquery', $library_handle );

		cfl_register_intl_tel_input_script();
		cfl_register_shared_country_code_script( $this->get_shared_script_version() );

		wp_register_script(
			$main_handle,
			$this->get_main_script_src(),
			array_merge( $dependency_array, array( 'cfkef-shared-country-code-script' ) ),
			$this->get_main_script_version(),
			true
		);

		wp_localize_script(
			$main_handle,
			'CCFEFCustomData',
			array(
				'pluginDir' => CFL_PLUGIN_URL,
				'errorMap'  => cfl_get_country_code_error_map(),
			)
		);
	}

	/**
	 * @param mixed $record
	 * @param mixed $ajax_handler
	 * @return void
	 */
	public function submit_validation( $record, $ajax_handler ) {
		if ( false === $this->validate_form ) {
			$raw_fields    = $record->get( 'fields' );
			$form_settings = $record->get( 'form_settings' );
			$form_fields   = $form_settings['form_fields'];
			$tel_type      = $this->get_tel_field_type();

			foreach ( $form_fields as $fields ) {
				if ( $fields['field_type'] == $tel_type && $fields['ccfef-country-code-field'] == 'yes' ) {
					$field_id = $fields['custom_id'];

					foreach ( $raw_fields as $fields ) {
						if ( $fields['id'] == $field_id ) {
							if ( ! empty( $fields['value'] ) ) {
								if ( ! preg_match( '/^\+/', $fields['value'] ) ) {
									$ajax_handler->add_error(
										$field_id,
										esc_html__( 'country code missing!', 'extensions-for-elementor-form' )
									);
								} else {
									if ( preg_match_all( '/\+/', $fields['value'], $matches ) > 1 ) {
										$ajax_handler->add_error(
											$field_id,
											esc_html__( 'Invalid Number!', 'extensions-for-elementor-form' )
										);
									}
								}
							}
						}
					}
				}
			}
		}

		$this->validate_form = true;
	}

	/**
	 * @param mixed $item
	 * @param mixed $item_index
	 * @param mixed $form
	 * @return void
	 */
	public function elementor_form_tel_field_rendering( $item, $item_index, $form ) {
		if ( $this->get_tel_field_type() === $item['field_type'] && 'yes' === $item['ccfef-country-code-field'] ) {
			$default_country = $item['ccfef-country-code-default'];
			if ( preg_match( '/[^a-zA-Z]/', $default_country ) ) {
				$default_country = 'NAN';
			}

			$include_countries    = $item['ccfef-country-code-include'];
			$excluded_countries   = $item['ccfef-country-code-exclude'];
			$dial_code_visibility = $item['ccfef-dial-code-visibility'];
			$strict_mode          = $item['ccfef-strict-mode'];

			if ( is_string( $include_countries ) ) {
				$include_countries = array_map( 'trim', explode( ',', $include_countries ) );
			}
			if ( is_string( $excluded_countries ) ) {
				$excluded_countries = array_map( 'trim', explode( ',', $excluded_countries ) );
			}

			$include_countries_orig  = $include_countries;
			$excluded_countries_orig = $excluded_countries;
			sort( $include_countries_orig );
			sort( $excluded_countries_orig );
			$common_attr = ( $include_countries_orig === $excluded_countries_orig ) ? 'same' : '';

			$include_countries_str = implode( ',', $include_countries );

			echo '<span class="ccfef-editor-intl-input" data-id="form-field-' . esc_attr( $item['custom_id'] ) . '" data-field-id="' . esc_attr( $item['_id'] ) . '" data-default-country="' . esc_attr( $default_country ) . '" data-include-countries="' . esc_attr( $include_countries_str ) . '" data-exclude-countries="' . esc_attr( implode( ',', $excluded_countries ) ) . '" data-common-countries="' . esc_attr( $common_attr ) . '" data-strict-mode="' . esc_attr( $strict_mode ) . '" data-dial-code-visibility="' . esc_attr( $dial_code_visibility ) . '" style="display: none;"></span>';

			$this->register_common_assets();
		}
	}

	public function editor_inline_JS() {
		if ( ! wp_script_is( 'cfkef-shared-content-template-editor', 'registered' ) ) {
			wp_register_script(
				'cfkef-shared-content-template-editor',
				CFL_PLUGIN_URL . 'assets/js/shared/content-template-editor.js',
				array( 'jquery' ),
				CFL_VERSION,
				true
			);
		}

		wp_enqueue_script( 'cfkef-shared-content-template-editor' );

		$editor_src = $this->get_editor_script_src();
		$editor_ver = CFL_VERSION;
		if ( function_exists( 'cfl_asset_version' ) && defined( 'CFL_PLUGIN_URL' ) && 0 === strpos( $editor_src, CFL_PLUGIN_URL ) ) {
			$editor_ver = cfl_asset_version( substr( $editor_src, strlen( CFL_PLUGIN_URL ) ) );
		}

		wp_enqueue_script(
			$this->get_editor_script_handle(),
			$editor_src,
			array( 'jquery', 'cfkef-shared-content-template-editor' ),
			$editor_ver,
			true
		);
		$this->register_common_assets();
	}

	/**
	 * Editor panel assets (review notice dismiss + styles).
	 */
	public function editor_assets() {
		wp_enqueue_style(
			'cfl-country-code-editor-style',
			CFL_PLUGIN_URL . 'assets/addons/css/ccfef_editor.min.css',
			array(),
			function_exists( 'cfl_asset_version' )
				? cfl_asset_version( 'assets/addons/css/ccfef_editor.min.css' )
				: CFL_VERSION,
			'all'
		);
		if ( function_exists( 'cfl_register_review_dismiss_script' ) ) {
			cfl_register_review_dismiss_script();
		}
		wp_enqueue_script(
			'cfl-country-code-editor-panel-script',
			CFL_PLUGIN_URL . 'assets/addons/js/ccfef-review-dismiss.min.js',
			array( 'jquery', 'cfkef-review-dismiss' ),
			function_exists( 'cfl_asset_version' )
				? cfl_asset_version( 'assets/addons/js/ccfef-review-dismiss.min.js' )
				: CFL_VERSION,
			true
		);
	}

	/**
	 * Register common assets for the plugin.
	 */
	public function register_common_assets() {
		$library_handle = $this->get_library_script_handle();
		$library_style  = $this->get_library_style_handle();

		if ( ! wp_script_is( $library_handle, 'enqueued' ) ) {
			wp_enqueue_script( $library_handle );
		}
		wp_enqueue_script( $this->get_main_script_handle() );
		wp_enqueue_style( $library_style );

		if ( get_option( 'cfefp_cdn_image' ) ) {
			$inline_css = '
			.cfefp-intl-container .iti__country-container .iti__flag:not(.iti__globe)  {
				background-image: url("' . esc_url( 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/23.0.7/img/flags@2x.png' ) . '");
			}';
			wp_add_inline_style( $library_style, $inline_css );
		}

		wp_enqueue_style( $this->get_style_handle() );
	}

	/**
	 * @param \Elementor\Widget_Base $widget The form widget instance.
	 * @return void
	 */
	public function update_controls( $widget ) {
		$elementor    = $this->get_elementor_plugin();
		$control_data = $elementor->controls_manager->get_control_from_stack( $widget->get_unique_name(), 'form_fields' );
		if ( is_wp_error( $control_data ) ) {
			return;
		}

		$tel_type       = $this->get_tel_field_type();
		$field_controls = array();

		$this->add_geo_controls( $field_controls, $tel_type );
		$this->add_flag_controls( $field_controls, $tel_type );
		$this->add_behavior_controls( $field_controls, $tel_type );
		$this->add_review_notice_control( $field_controls, $tel_type );

		$control_data['fields'] = \array_merge( $control_data['fields'], $field_controls );
		$widget->update_control( 'form_fields', $control_data );
	}

	/**
	 * Shared tab/condition keys for country-code field controls.
	 *
	 * @param string $tel_type
	 * @param bool   $require_enabled
	 * @return array<string, mixed>
	 */
	private function get_country_code_control_meta( string $tel_type, bool $require_enabled = true ): array {
		$condition = array(
			'field_type' => $tel_type,
		);

		if ( $require_enabled ) {
			$condition['ccfef-country-code-field'] = 'yes';
		}

		return array(
			'condition'    => $condition,
			'tab'          => 'content',
			'inner_tab'    => 'form_fields_content_tab',
			'tabs_wrapper' => 'form_fields_tabs',
		);
	}

	/**
	 * @param array  $field_controls
	 * @param string $tel_type
	 * @return void
	 */
	private function add_geo_controls( array &$field_controls, string $tel_type ): void {
		$enabled_meta = $this->get_country_code_control_meta( $tel_type );

		$field_controls['ccfef-country-code-field'] = array_merge(
			array(
				'name'         => 'ccfef-country-code-field',
				'label'        => esc_html__( 'Country Code', 'extensions-for-elementor-form' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'extensions-for-elementor-form' ),
				'label_off'    => esc_html__( 'Hide', 'extensions-for-elementor-form' ),
				'return_value' => 'yes',
				'default'      => 'no',
			),
			$this->get_country_code_control_meta( $tel_type, false )
		);

		$field_controls['ccfef-country-code-default'] = array_merge(
			array(
				'name'        => 'ccfef-country-code-default',
				'label'       => esc_html__( 'Default Country', 'extensions-for-elementor-form' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => sprintf(
					"%s <b>'%s'</b> %s.",
					esc_html__( 'Set default country code in tel field, like', 'extensions-for-elementor-form' ),
					esc_html__( 'in', 'extensions-for-elementor-form' ),
					esc_html__( 'for India', 'extensions-for-elementor-form' )
				),
				'default'     => 'in',
				'ai'          => array( 'active' => false ),
			),
			$enabled_meta
		);

		$field_controls['ccfef-country-code-include'] = array_merge(
			array(
				'name'        => 'ccfef-country-code-include',
				'label'       => esc_html__( 'Only country', 'extensions-for-elementor-form' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => sprintf(
					'%s - <b>%s</b>,<b>%s</b>,<b>%s</b>,<b>%s</b>',
					esc_html__( 'Display only these countries, add comma separated', 'extensions-for-elementor-form' ),
					esc_html__( 'ca', 'extensions-for-elementor-form' ),
					esc_html__( 'in', 'extensions-for-elementor-form' ),
					esc_html__( 'us', 'extensions-for-elementor-form' ),
					esc_html__( 'gb', 'extensions-for-elementor-form' )
				),
				'ai'          => array( 'active' => false ),
			),
			$enabled_meta
		);

		$field_controls['ccfef-country-code-exclude'] = array_merge(
			array(
				'name'        => 'ccfef-country-code-exclude',
				'label'       => esc_html__( 'Exclude Countries', 'extensions-for-elementor-form' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => sprintf(
					'%s - <b>%s</b>,<b>%s</b><br><br>%s - <a target="__blank" href="' . esc_url( 'https://www.iban.com/country-codes' ) . '">https://www.iban.com/country-codes</a>',
					esc_html__( 'Exclude some countries, add comma separated', 'extensions-for-elementor-form' ),
					esc_html__( 'af', 'extensions-for-elementor-form' ),
					esc_html__( 'pk', 'extensions-for-elementor-form' ),
					esc_html__( 'Check country codes alpha-2 list here', 'extensions-for-elementor-form' )
				),
				'ai'          => array( 'active' => false ),
			),
			$enabled_meta
		);

		$field_controls['ccfef-country-code-auto-detect'] = array_merge(
			array(
				'name'         => 'ccfef-country-code-auto-detect',
				'label'        => esc_html__( 'Auto Detect Country', 'extensions-for-elementor-form' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'extensions-for-elementor-form' ),
				'label_off'    => esc_html__( 'No', 'extensions-for-elementor-form' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'description'  => sprintf(
					'%s <br> To use - <a target="__blank" href="https://coolplugins.net/cool-formkit-for-elementor-forms/?utm_source=ccfef_plugin&utm_medium=inside&utm_campaign=get-pro&utm_content=editor-panel">(UPGRADE TO PRO)</a>',
					esc_html__( 'Auto select user country using ipapi.co', 'extensions-for-elementor-form' )
				),
				'ai'           => array( 'active' => false ),
				'disabled'     => true,
			),
			$enabled_meta
		);

		$field_controls['ccfef-country-code-prefer'] = array_merge(
			array(
				'name'        => 'ccfef-country-code-prefer',
				'label'       => esc_html__( 'Preferred Countries', 'extensions-for-elementor-form' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => sprintf(
					'%s To use - <a target="__blank" href="https://coolplugins.net/cool-formkit-for-elementor-forms/?utm_source=ccfef_plugin&utm_medium=inside&utm_campaign=get-pro&utm_content=editor-panel">(UPGRADE TO PRO)</a>',
					esc_html__( 'The Specified countries will appear at the top of the list.', 'extensions-for-elementor-form' )
				),
				'ai'          => array( 'active' => false ),
				'disabled'    => true,
			),
			$enabled_meta
		);
	}

	/**
	 * @param array  $field_controls
	 * @param string $tel_type
	 * @return void
	 */
	private function add_flag_controls( array &$field_controls, string $tel_type ): void {
		$field_controls['ccfef-dial-code-visibility'] = array_merge(
			array(
				'name'    => 'ccfef-dial-code-visibility',
				'label'   => esc_html__( 'Dial Code Visibility', 'extensions-for-elementor-form' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'options' => array(
					'show'     => array(
						'title' => esc_html__( 'Show', 'extensions-for-elementor-form' ),
						'icon'  => 'far fa-eye',
					),
					'hide'     => array(
						'title' => esc_html__( 'Hide', 'extensions-for-elementor-form' ),
						'icon'  => 'far fa-eye-slash',
					),
					'separate' => array(
						'title' => esc_html__( 'Separate', 'extensions-for-elementor-form' ),
						'icon'  => 'fas fa-arrows-alt-h',
					),
				),
				'default' => 'show',
				'ai'      => array( 'active' => false ),
			),
			$this->get_country_code_control_meta( $tel_type )
		);
	}

	/**
	 * @param array  $field_controls
	 * @param string $tel_type
	 * @return void
	 */
	private function add_behavior_controls( array &$field_controls, string $tel_type ): void {
		$field_controls['ccfef-strict-mode'] = array_merge(
			array(
				'name'         => 'ccfef-strict-mode',
				'label'        => esc_html__( 'Strict Mode', 'extensions-for-elementor-form' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'extensions-for-elementor-form' ),
				'label_off'    => esc_html__( 'No', 'extensions-for-elementor-form' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'description'  => esc_html__( 'As the user types in the input, ignore any irrelevant characters. Basically, the user can only enter numeric characters, and an optional plus at the beginning. Cap the length at the maximum valid number length.', 'extensions-for-elementor-form' ),
				'ai'           => array( 'active' => false ),
			),
			$this->get_country_code_control_meta( $tel_type )
		);
	}

	/**
	 * @param array  $field_controls
	 * @param string $tel_type
	 * @return void
	 */
	private function add_review_notice_control( array &$field_controls, string $tel_type ): void {
		if ( ! $this->should_include_review_notice() || get_option( 'ccfef_review_notice_dismiss' ) ) {
			return;
		}

		$review_nonce = wp_create_nonce( 'ccfef_elementor_review' );
		$url          = admin_url( 'admin-ajax.php' );
		$html         = '<div class="ccfef_elementor_review_wrapper ccfef_custom_html">';
		$html        .= '<div id="ccfef_elementor_review_dismiss" data-url="' . esc_url( $url ) . '" data-nonce="' . esc_attr( $review_nonce ) . '">Close Notice X</div>
						<div class="ccfef_elementor_review_msg">Hope this addon solved your problem! <br><a href="https://wordpress.org/support/plugin/country-code-field-for-elementor-form/reviews/#new-post" target="_blank" rel="noopener noreferrer">Share the love with a ⭐⭐⭐⭐⭐ rating.</a><br><br></div>
						<div class="ccfef_elementor_demo_btn"><a href="https://wordpress.org/support/plugin/country-code-field-for-elementor-form/reviews/#new-post" target="_blank" rel="noopener noreferrer">Submit Review</a></div>
						</div>';

		$field_controls['ccfef_review_notice'] = array_merge(
			array(
				'name'            => 'ccfef_review_notice',
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => $html,
				'content_classes' => 'ccfef_elementor_review_notice',
			),
			$this->get_country_code_control_meta( $tel_type )
		);
	}

	/**
	 * Dismiss country-code Elementor review notice.
	 *
	 * @return void
	 */
	public function ccfef_elementor_review_notice() {
		if ( ! check_ajax_referer( 'ccfef_elementor_review', 'nonce', false ) ) {
			wp_send_json_error( __( 'Invalid security token sent.', 'extensions-for-elementor-form' ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error();
		}

		if ( isset( $_POST['ccfef_notice_dismiss'] ) && 'true' === sanitize_text_field( wp_unslash( $_POST['ccfef_notice_dismiss'] ) ) ) {
			update_option( 'ccfef_review_notice_dismiss', 'yes' );
		}
		exit;
	}
}

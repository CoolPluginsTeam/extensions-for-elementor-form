<?php

namespace Cool_FormKit\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class CREATE_COUNTRY_FIELD {

    public function __construct() {
        $this->register_common_assets();
        add_action( 'cool_formkit/forms/render_field/tel', array( $this, 'elementor_form_tel_field_rendering' ), 9, 3 );
        add_action( 'elementor/preview/init', array( $this, 'editor_inline_JS' ) );
        add_action('elementor/element/cool-form/section_form_fields/before_section_end', array($this, 'update_controls'), 100, 2);
        add_action( 'elementor/frontend/after_enqueue_scripts', array( $this, 'frontend_assets' ) );
    }
    public function elementor_form_tel_field_rendering( $item, $item_index, $form ) {

		if ( 'tel' === $item['field_type'] && 'yes' === $item['ccfef-country-code-field'] ) {
			$default_country     = esc_attr($item['ccfef-country-code-default']);
			$include_countries   = esc_attr($item['ccfef-country-code-include']);
			$excluded_countries  = '' === $item['ccfef-country-code-include'] ? esc_attr($item['ccfef-country-code-exclude']) : '';
			$preferred_countries = esc_attr($item['ccfef-country-code-prefer']);
			$auto_detect_country = esc_attr($item['ccfef-country-code-auto-detect']);

			echo '<span class="ccfef-editor-intl-input" data-id="form-field-' . esc_attr( $item['custom_id'] ) . '" data-field-id="' . esc_attr( $item['_id'] ) . '" data-include-countries="' . esc_attr( $include_countries ) . '" data-exclude-countries="' . esc_attr( $excluded_countries ) . '" data-default-country="' . esc_attr( $default_country ) . '" data-preferred-countries="' . esc_attr( $preferred_countries ) . '" data-auto-detect="' . esc_attr( $auto_detect_country ) . '" style="display: none;"></span>';
		}
	}
    

    public function editor_inline_JS() {
		wp_enqueue_script( 'ccfef-country-code-editor-script', CFL_PLUGIN_URL . 'assets/js/countryCode/ccfef-editor.js', array(), CFL_VERSION, true ); // for AOS animation
	}

    public function register_common_assets() {
		$error_map = [
			__("The phone number you entered is not valid. Please check the format and try again.", "cool-formkit"),
			__("The country code you entered is not recognized. Please ensure it is correct and try again.", "cool-formkit"),
			__("The phone number you entered is too short. Please enter a complete phone number, including the country code.", "cool-formkit"),
			__("The phone number you entered is too long. Please ensure it is in the correct format and try again.", "cool-formkit"),
			__("The phone number you entered is not valid. Please check the format and try again.", "cool-formkit")
		];
		
		if ( ! wp_script_is( 'ccfef-country-code-library-script', 'registered' ) ) {
			wp_register_script( 'ccfef-country-code-library-script', CFL_PLUGIN_URL . 'assets/js/countryCode/intlTelInput.min.js', array(), CFL_VERSION, true );
		}
		wp_register_script( 'ccfef-country-code-script', CFL_PLUGIN_URL . 'assets/js/countryCode/country-code-script.js', array( 'elementor-frontend', 'jquery', 'ccfef-country-code-library-script' ), CFL_VERSION, true );
		wp_register_style( 'ccfef-country-code-library-style', CFL_PLUGIN_URL . 'assets/css/countryCode/intlTelInput.min.css', array(), CFL_VERSION, 'all' );
		wp_register_style( 'ccfef-country-code-style', CFL_PLUGIN_URL . 'assets/css/countryCode/country-code-style.css', array(), CFL_VERSION, 'all' );
		// wp_register_style( 'ccfef-country-code-label-style', CFL_PLUGIN_URL . 'assets/css/countryCode/label_style.css', array(), CFL_VERSION, 'all' );

		wp_localize_script(
			'ccfef-country-code-script',
			'CCFEFCustomData',
			array(
				'pluginDir'      => CFL_PLUGIN_URL,
				'geo_lookup_key' => get_option('cfkef_country_code_api_key',''),
				'errorMap'  => $error_map, 
			)
		);
	}

    /**
	 * Enqueue frontend assets for the plugin.
	 */
	public function frontend_assets() {

		
		if ( ! wp_script_is( 'ccfef-country-code-library-script', 'enqueued' ) ) {
			wp_enqueue_script( 'ccfef-country-code-library-script' );
		}
		wp_enqueue_script( 'ccfef-country-code-script');
		wp_enqueue_style( 'ccfef-country-code-library-style' );
		if ( get_option( 'cfefp_cdn_image' ) ) {
			$inline_css = '
			.cfefp-intl-container .iti__country-container .iti__flag:not(.iti__globe)  {
				background-image: url("'.esc_url("https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/23.8.1/img/flags@2x.png").'");
			}';
		wp_add_inline_style( 'ccfef-country-code-library-style', $inline_css );
		}
		wp_enqueue_style( 'ccfef-country-code-style' );
		// wp_enqueue_style( 'ccfef-country-code-label-style' );
	}

    public function update_controls( $widget ) {
		$elementor    = \Elementor\Plugin::instance();
		$control_data = $elementor->controls_manager->get_control_from_stack( $widget->get_unique_name(), 'form_fields' );
		if ( is_wp_error( $control_data ) ) {
				return;
		}

		$ccfef_auto_detect_desc = sprintf(
			'%s - <a href="admin.php?page=cool-formkit&tab=settings" target="__blank">%s</a> %s.',
			esc_html__( 'Auto select user country using ipapi.co', 'cool-formkit' ),
			esc_html__( 'Add API key', 'cool-formkit' ),
			esc_html__( 'to use it', 'cool-formkit' ),
		);

		$ccfef_include_desc = sprintf(
			'%s - <b>%s</b>,<b>%s</b>,<b>%s</b>,<b>%s</b>',
			esc_html__( 'Display only these countries, add comma separated', 'cool-formkit' ),
			esc_html__( 'ca', 'cool-formkit' ),
			esc_html__( 'in', 'cool-formkit' ),
			esc_html__( 'us', 'cool-formkit' ),
			esc_html__( 'gb', 'cool-formkit' ),
		);

		$ccfef_exclude_desc = sprintf(
			'%s - <b>%s</b>,<b>%s</b>',
			esc_html__( 'Exclude some countries, add comma separated', 'cool-formkit' ),
			esc_html__( 'af', 'cool-formkit' ),
			esc_html__( 'pk', 'cool-formkit' ),
		);

		$ccfef_prefer_desc = sprintf(
			'%s - <b>%s</b>,<b>%s</b><br><br>%s - <a target="__blank" href="' . esc_url( 'https://www.iban.com/country-codes' ) . '">https://www.iban.com/country-codes</a>',
			esc_html__( 'These countries will appear at the top of the list', 'cool-formkit' ),
			esc_html__( 'in', 'cool-formkit' ),
			esc_html__( 'us', 'cool-formkit' ),
			esc_html__( 'Check country codes alpha-2 list here', 'cool-formkit' ),
		);

		$field_controls = array(
			'ccfef-country-code-field'       => array(
				'name'         => 'ccfef-country-code-field',
				'label'        => esc_html__( 'Country Code', 'cool-formkit' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'cool-formkit' ),
				'label_off'    => esc_html__( 'Hide', 'cool-formkit' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'condition'    => array(
					'field_type' => 'tel',
				),
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			),

			'ccfef-country-code-default'     => array(
				'name'         => 'ccfef-country-code-default',
				'label'        => esc_html__( 'Default Country', 'cool-formkit' ),
				'type'         => \Elementor\Controls_Manager::TEXT,
				'condition'    => array(
					'field_type'               => 'tel',
					'ccfef-country-code-field' => 'yes',
				),
				'default'      => 'us',
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
				'ai'           => array(
					'active' => false,
				),
			),

			'ccfef-country-code-auto-detect' => array(
				'name'         => 'ccfef-country-code-auto-detect',
				'label'        => esc_html__( 'Auto Detect Country', 'cool-formkit' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'cool-formkit' ),
				'label_off'    => esc_html__( 'No', 'cool-formkit' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'description'  => $ccfef_auto_detect_desc,
				'condition'    => array(
					'field_type'               => 'tel',
					'ccfef-country-code-field' => 'yes',
				),
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
				'ai'           => array(
					'active' => false,
				),
			),

			'ccfef-country-code-include'     => array(
				'name'         => 'ccfef-country-code-include',
				'label'        => esc_html__( 'Only Countries', 'cool-formkit' ),
				'type'         => \Elementor\Controls_Manager::TEXT,
				// 'placeholder'  => esc_html__( 'Only countries, separated by commas', 'cool-formkit' ),
				'description'  => $ccfef_include_desc,
				'condition'    => array(
					'field_type'               => 'tel',
					'ccfef-country-code-field' => 'yes',
				),
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
				'ai'           => array(
					'active' => false,
				),
			),
			'ccfef-country-code-exclude'     => array(
				'name'         => 'ccfef-country-code-exclude',
				'label'        => esc_html__( 'Exclude Countries', 'cool-formkit' ),
				'type'         => \Elementor\Controls_Manager::TEXT,
				'description'  => $ccfef_exclude_desc,
				'condition'    => array(
					'field_type'               => 'tel',
					'ccfef-country-code-field' => 'yes',
				),
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
				'ai'           => array(
					'active' => false,
				),
			),

			'ccfef-country-code-prefer'      => array(
				'name'         => 'ccfef-country-code-prefer',
				'label'        => esc_html__( 'Preferred Countries', 'cool-formkit' ),
				'type'         => \Elementor\Controls_Manager::TEXT,
				// 'placeholder'  => esc_html__( 'Preferred countries, separated by commas', 'cool-formkit' ),
				'description'  => $ccfef_prefer_desc,
				'condition'    => array(
					'field_type'               => 'tel',
					'ccfef-country-code-field' => 'yes',
				),
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
				'ai'           => array(
					'active' => false,
				),
			),
		);

		if ( ! get_option( 'cfkef_elementor_notice_dismiss' ) ) {
			$review_nonce = wp_create_nonce( 'cfef_elementor_review' );
			$url          = admin_url( 'admin-ajax.php' );
			$html         = '<div class="cfef_elementor_review_wrapper cfef_custom_html">';
			$html        .=	'<div id="cfef_elementor_review_dismiss" data-url="' . esc_url( $url ) . '" data-nonce="' . esc_attr( $review_nonce ) . '">Close Notice X</div>
							<div class="cfef_elementor_review_msg">Hope this addon solved your problem! <br><a href="https://wordpress.org/support/plugin/country-code-field-for-elementor-form/reviews/#new-post" target="_blank"">Share the love with a ⭐⭐⭐⭐⭐ rating.</a><br><br></div>
							<div class="cfef_elementor_demo_btn"><a href="https://wordpress.org/support/plugin/country-code-field-for-elementor-form/reviews/#new-post" target="_blank">Submit Review</a></div>
							</div>';

			$field_controls['cfkef_countary_box'] = array(
				'name'            => 'cfkef_countary_box',
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => $html,
				'content_classes' => 'cfef_elementor_review_notice',
				'tab'             => 'content',
				'condition'       => array(
					'field_type' => 'tel'
				),
				'inner_tab'       => 'form_fields_content_tab',
				'tabs_wrapper'    => 'form_fields_tabs',
			);
		}

		$control_data['fields'] = \array_merge( $control_data['fields'], $field_controls );
		$widget->update_control( 'form_fields', $control_data );
	}
}


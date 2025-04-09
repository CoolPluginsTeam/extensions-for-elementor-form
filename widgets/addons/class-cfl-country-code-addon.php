<?php
namespace Cool_FormKit\Widgets\Addons;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class for managing the country code field in Elementor forms.
 *
 * This class adds functionality to include country codes in the telephone input fields of Elementor forms.
 *
 * @package ccfef
 *
 * @version 1.0.0
 */
class CFL_COUNTRY_CODE_FIELD {

	/**
	 * Plugin instance.
	 *
	 * @var CFL_COUNTRY_CODE_FIELD
	 *
	 * @access private
	 * @var null
	 */
	private static $instance = null;

	/**
	 * Get the plugin instance.
	 *
	 * @return CFL_COUNTRY_CODE_FIELD
	 * @static
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor method to initialize actions and assets.
	 */
	public function __construct() {
		// $this->register_common_assets();
		add_action('wp_enqueue_scripts',array($this,'register_common_assets'));
		add_action( 'cool_formkit/forms/render_field/tel', array( $this, 'elementor_form_tel_field_rendering' ), 9, 3 );
		add_action( 'elementor/element/cool-form/section_form_fields/before_section_end', array( $this, 'update_controls' ), 100, 2);
		add_action( 'elementor/preview/init', array( $this, 'editor_inline_JS' ) );
		add_action( 'elementor/frontend/after_enqueue_scripts', array( $this, 'frontend_assets' ) );
		add_action( 'elementor/editor/before_enqueue_styles', array( $this, 'editor_assets') );
		add_action( 'wp_ajax_ccfef_elementor_review_notice', array( $this, 'ccfef_elementor_review_notice' ) );
	}

	public function elementor_form_tel_field_rendering( $item, $item_index, $form ) {
		if ( ( 'ehp-tel' === $item['field_type'] || 'tel' === $item['field_type'] ) && 'yes' === $item['ccfef-country-code-field'] ) {
			// Get and sanitize the default country.
			$default_country = $item['ccfef-country-code-default'];
			if ( preg_match( '/[^a-zA-Z]/', $default_country ) ) {
				$default_country = 'NAN';
			}
			
			$include_countries  = $item['ccfef-country-code-include'];
			$excluded_countries = $item['ccfef-country-code-exclude'];
		
			// Convert comma-separated strings to arrays if needed.
			if ( is_string( $include_countries ) ) {
				$include_countries = array_map( 'trim', explode( ',', $include_countries ) );
			}
			if ( is_string( $excluded_countries ) ) {
				$excluded_countries = array_map( 'trim', explode( ',', $excluded_countries ) );
			}
			
			// --- Added code to set data-common-countries ---
			$include_countries_orig  = $include_countries;
			$excluded_countries_orig = $excluded_countries;
			sort( $include_countries_orig );
			sort( $excluded_countries_orig );
			$commonAttr = ( $include_countries_orig === $excluded_countries_orig ) ? 'same' : '';
			// --- End of added code ---
		
			// Convert the include countries array back to a comma-separated string for the data attribute.
			$include_countries_str = implode( ',', $include_countries );
		
			echo '<span class="ccfef-editor-intl-input" data-id="form-field-' . esc_attr( $item['custom_id'] ) . '" data-field-id="' . esc_attr( $item['_id'] ) . '" data-default-country="' . esc_attr( $default_country ) . '" data-include-countries="' . esc_attr( $include_countries_str ) . '" data-exclude-countries="' . esc_attr( implode( ',', $excluded_countries ) ) . '" data-common-countries="' . esc_attr( $commonAttr ) . '" style="display: none;"></span>';
		}
		
	}
	
	
	

	public function editor_inline_JS() {
		// wp_enqueue_script( 'ccfef-country-code-editor-script', CFL_PLUGIN_URL . 'assets/addons/js/ccfef-content-template.min.js', array(), CFL_VERSION, true ); 
		wp_enqueue_script( 'ccfef-country-code-editor-script', CFL_PLUGIN_URL . 'assets/addons/js/ccfef-content-template.js', array(), CFL_VERSION, true ); 
	}

	/**
	 * Register common assets for the plugin.
	 */
	public function register_common_assets() {
		// Define the errorMap constant at the top of your file
		$error_map = [
			__("The phone number you entered is not valid. Please check the format and try again.", "country-code-for-elementor-form-telephone-field"),
			__("The country code you entered is not recognized. Please ensure it is correct and try again.", "country-code-for-elementor-form-telephone-field"),
			__("The phone number you entered is too short. Please enter a complete phone number, including the country code.", "country-code-for-elementor-form-telephone-field"),
			__("The phone number you entered is too long. Please ensure it is in the correct format and try again.", "country-code-for-elementor-form-telephone-field"),
			__("The phone number you entered is not valid. Please check the format and try again.", "country-code-for-elementor-form-telephone-field")
		];

		wp_register_script( 'ccfef-country-code-library-script', CFL_PLUGIN_URL . 'assets/addons/intl-tel-input/js/intlTelInput.js', array(), CFL_VERSION, true );
		// wp_register_script( 'ccfef-country-code-script', CFL_PLUGIN_URL . 'assets/addons/js/country-code-script.min.js', array( 'elementor-frontend', 'jquery', 'ccfef-country-code-library-script' ), CFL_VERSION, true );
		wp_register_script( 'ccfef-country-code-script', CFL_PLUGIN_URL . 'assets/addons/js/country-code-script.js', array( 'elementor-frontend', 'jquery', 'ccfef-country-code-library-script' ), CFL_VERSION, true );
		wp_register_script( 'ccfef-country-code-script-hello', CFL_PLUGIN_URL . 'assets/addons/js/country-code-script-hello.min.js', array( 'elementor-frontend', 'jquery', 'ccfef-country-code-library-script' ), CFL_VERSION, true );
		wp_register_style( 'ccfef-country-code-library-style', CFL_PLUGIN_URL . 'assets/addons/intl-tel-input/css/intlTelInput.min.css', array(), CFL_VERSION, 'all' );
		wp_register_style( 'ccfef-country-code-style', CFL_PLUGIN_URL . 'assets/addons/css/country-code-style.min.css', array(), CFL_VERSION, 'all' );

		wp_localize_script(
			'ccfef-country-code-script',
			'CCFEFCustomData',
			array(
				'pluginDir' => CFL_PLUGIN_URL,
				'errorMap'  => $error_map, 
			)	
		);
		wp_localize_script(
			'ccfef-country-code-script-hello',
			'CCFEFCustomData',
			array(
				'pluginDir' => CFL_PLUGIN_URL,
				'errorMap'  => $error_map, 
			)	
		);
	}

	/**
	 * Enqueue frontend assets for the plugin.
	 */
	public function editor_assets() {
		wp_enqueue_style( 'ccfef-editor-style', CFL_PLUGIN_URL . 'assets/addons/css/ccfef_editor.min.css', array(), CFL_VERSION, 'all' );
		wp_enqueue_script( 'ccfef-editor-script', CFL_PLUGIN_URL . 'assets/addons/js/ccfef-editor.min.js', array( 'jquery' ), CFL_VERSION, true );
	}

	/**
	 * Enqueue frontend assets for the plugin.
	 */
	public function frontend_assets() {
		wp_enqueue_script( 'ccfef-country-code-library-script');
		wp_enqueue_script( 'ccfef-country-code-script');
		wp_enqueue_script( 'ccfef-country-code-script-hello');
		wp_enqueue_style( 'ccfef-country-code-library-style');
		wp_enqueue_style( 'ccfef-country-code-style');	    
	}

	/**
	 * Update form widget controls to include the country code control in tel field.
	 *
	 * Adds country code control to allow users to customize the country code field.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param \Elementor\Widget_Base $widget The form widget instance.
	 * @return void
	 */
	public function update_controls( $widget ) {
		$elementor = \Elementor\Plugin::instance();
		$control_data = $elementor->controls_manager->get_control_from_stack( $widget->get_unique_name(), 'form_fields' );
		if ( is_wp_error( $control_data ) ) {
				return;
		}

		$ccfef_default_desc = sprintf(
			"%s <b>'%s'</b> %s.",
			esc_html__( 'Set default country code in tel field, like', 'country-code-for-elementor-form-telephone-field' ),
			esc_html__( 'in', 'country-code-for-elementor-form-telephone-field' ),
			esc_html__( 'for India', 'country-code-for-elementor-form-telephone-field' ),
		);

		$ccfef_auto_detect_desc = sprintf(
			'%s <br> To use - <a target="__blank" href="https://coolplugins.net/cool-formkit-for-elementor-forms/?utm_source=ccfef_plugin&utm_medium=inside&utm_campaign=get-pro&utm_content=editor-panel">(UPGRADE TO PRO)</a>',
			esc_html__( 'Auto select user country using ipapi.co', 'country-code-for-elementor-form-telephone-field' )			
		);


		$ccfef_include_desc = sprintf(
			'%s - <b>%s</b>,<b>%s</b>,<b>%s</b>,<b>%s</b>',
			esc_html__( 'Display only these countries, add comma separated', 'country-code-for-elementor-form-telephone-field' ),
			esc_html__( 'ca', 'country-code-for-elementor-form-telephone-field' ),
			esc_html__( 'in', 'country-code-for-elementor-form-telephone-field' ),
			esc_html__( 'us', 'country-code-for-elementor-form-telephone-field' ), 
			esc_html__( 'gb', 'country-code-for-elementor-form-telephone-field' ),
		);

		$ccfef_prefer_desc = sprintf(
			'%s To use - <a target="__blank" href="https://coolplugins.net/cool-formkit-for-elementor-forms/?utm_source=ccfef_plugin&utm_medium=inside&utm_campaign=get-pro&utm_content=editor-panel">(UPGRADE TO PRO)</a>',
			esc_html__( 'The Specified countries will appear at the top of the list.', 'country-code-for-elementor-form-telephone-field' ),			
		);

		$ccfef_exclude_desc = sprintf(
			'%s - <b>%s</b>,<b>%s</b><br><br>%s - <a target="__blank" href="' . esc_url( 'https://www.iban.com/country-codes' ) . '">https://www.iban.com/country-codes</a>',
			esc_html__( 'Exclude some countries, add comma separated', 'country-code-for-elementor-form-telephone-field' ),
			esc_html__( 'af', 'country-code-for-elementor-form-telephone-field' ),
			esc_html__( 'pk', 'country-code-for-elementor-form-telephone-field' ),
			esc_html__( 'Check country codes alpha-2 list here', 'country-code-for-elementor-form-telephone-field' ),
		);

		$field_controls = array(
			'ccfef-country-code-field'   => array(
				'name'         => 'ccfef-country-code-field',
				'label'        => esc_html__( 'Country Code', 'country-code-for-elementor-form-telephone-field' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'country-code-for-elementor-form-telephone-field' ),
				'label_off'    => esc_html__( 'Hide', 'country-code-for-elementor-form-telephone-field' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'condition'    => array(
					'field_type' => array('tel', 'ehp-tel'),
				),
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			),

			'ccfef-country-code-default' => array(
				'name'         => 'ccfef-country-code-default',
				'label'        => esc_html__( 'Default Country', 'country-code-for-elementor-form-telephone-field' ),
				'type'         => \Elementor\Controls_Manager::TEXT,
				'condition'    => array(
					'field_type'               => array('tel', 'ehp-tel'),
					'ccfef-country-code-field' => 'yes',
				),
				'description'  => $ccfef_default_desc,
				'default'      => 'in',
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
				'ai'           => array(
					'active' => false,
				),
			),

			'ccfef-country-code-include' => array(
				'name'         => 'ccfef-country-code-include',
				'label'        => esc_html__( 'Only country', 'country-code-for-elementor-form-telephone-field' ),
				'type'         => \Elementor\Controls_Manager::TEXT,
				'description'  => $ccfef_include_desc,
				'condition'    => array(
					'field_type'               => array('tel', 'ehp-tel'),
					'ccfef-country-code-field' => 'yes',
				),
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
				'ai'           => array(
					'active' => false,
				),
			),
		
			'ccfef-country-code-exclude' => array(
				'name'         => 'ccfef-country-code-exclude',
				'label'        => esc_html__( 'Exclude Countries', 'country-code-for-elementor-form-telephone-field' ),
				'type'         => \Elementor\Controls_Manager::TEXT,
				'description'  => $ccfef_exclude_desc,
				'condition'    => array(
					'field_type'               => array('tel', 'ehp-tel'),
					'ccfef-country-code-field' => 'yes',
				),
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
				'ai'           => array(
					'active' => false,
				),
			),

			'ccfef-country-code-auto-detect' => array(
		'name'         => 'ccfef-country-code-auto-detect',
		'label'        => esc_html__( 'Auto Detect Country', 'country-code-for-elementor-form-telephone-field' ),
		'type'         => \Elementor\Controls_Manager::SWITCHER,
		'label_on'     => esc_html__( 'Yes', 'country-code-for-elementor-form-telephone-field' ),
		'label_off'    => esc_html__( 'No', 'country-code-for-elementor-form-telephone-field' ),
		'return_value' => 'yes',
		'default'      => 'no',
		'description'  => $ccfef_auto_detect_desc,
		'condition'    => array(
			'field_type'               => array('tel', 'ehp-tel'),
			'ccfef-country-code-field' => 'yes',
		),
		'tab'          => 'content',
		'inner_tab'    => 'form_fields_content_tab',
		'tabs_wrapper' => 'form_fields_tabs',
		'ai'           => array(
			'active' => false,
		),
		'disabled'     => true, // This ensures the control is always disabled.
),


			'ccfef-country-code-prefer'      => array(
				'name'         => 'ccfef-country-code-prefer',
				'label'        => esc_html__( 'Preferred Countries', 'country-code-for-elementor-form-telephone-field' ),
				'type'         => \Elementor\Controls_Manager::TEXT,
				'description'  => $ccfef_prefer_desc,
				'condition'    => array(
					'field_type'               => array('tel', 'ehp-tel'),
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
		
		if( !is_plugin_active('conditional-fields-for-elementor-form/class-conditional-fields-for-elementor-form.php') && !is_plugin_active('conditional-fields-for-elementor-form-pro/class-conditional-fields-for-elementor-form-pro.php')){
			$condition_field_controls = array(
			'ccfef-country-code-conditions' => array(
				'name'         => 'ccfef-country-code-conditions',
				'label'        => esc_html__( 'Enable Conditions', 'country-code-for-elementor-form-telephone-field' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'country-code-for-elementor-form-telephone-field' ),
				'label_off'    => esc_html__( 'No', 'country-code-for-elementor-form-telephone-field' ),
				'condition'    => array(
					'field_type' => array( 'text', 'email', 'textarea', 'number', 'select', 'radio', 'checkbox', 'tel'),				
				),
				'tab'          => 'content',
				'default'      => 'no',
				'inner_tab'    => 'form_fields_advanced_tab',
				'tabs_wrapper' => 'form_fields_tabs',
				'ai'           => array(
					'active' => false,
				),
			),
		
			'ccfef_condition_notice' => array(
				'name'            => 'ccfef_condition_notice',
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => 'To Apply conditional logic to Elementor form fields - <a href="plugin-install.php?s=Conditional%2520Fields%2520for%2520Elementor%2520Form%2520by%2520coolplugins&tab=search&type=term" target="_blank">Activate Plugin</a>',
				'content_classes' => 'ccfef_elementor_review_notice',
				'tab'             => 'content',
				'condition'       => array(
					'field_type' => array( 'text', 'email', 'textarea', 'number', 'select', 'radio', 'checkbox', 'tel' ),	
					'ccfef-country-code-conditions' => 'yes'	
				),
				'inner_tab'       => 'form_fields_advanced_tab',
				'tabs_wrapper'    => 'form_fields_tabs',
			)
			);
			$field_controls = array_merge( $field_controls, $condition_field_controls );
		}

		

		if ( ! get_option( 'ccfef_review_notice_dismiss' ) ) {
			$review_nonce = wp_create_nonce( 'ccfef_elementor_review' );
			$url          = admin_url( 'admin-ajax.php' );
			$html         = '<div class="ccfef_elementor_review_wrapper ccfef_custom_html">';
			$html        .= '<div id="ccfef_elementor_review_dismiss" data-url="' . esc_url( $url ) . '" data-nonce="' . esc_attr( $review_nonce ) . '">Close Notice X</div>
<div class="ccfef_elementor_review_msg">Hope this addon solved your problem! <br><a href="https://wordpress.org/support/plugin/country-code-field-for-elementor-form/reviews/#new-post" target="_blank"">Share the love with a ⭐⭐⭐⭐⭐ rating.</a><br><br></div>
<div class="ccfef_elementor_demo_btn"><a href="https://wordpress.org/support/plugin/country-code-field-for-elementor-form/reviews/#new-post" target="_blank">Submit Review</a></div>
</div>';

			$field_controls['ccfef_review_notice'] = array(
				'name'            => 'ccfef_review_notice',
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => $html,
				'content_classes' => 'ccfef_elementor_review_notice',
				'tab'             => 'content',
				'condition'       => array(
					'field_type'               => 'tel',
					'ccfef-country-code-field' => 'yes',
				),
				'inner_tab'       => 'form_fields_content_tab',
				'tabs_wrapper'    => 'form_fields_tabs',
			);
		}

		$control_data['fields'] = \array_merge( $control_data['fields'], $field_controls );
		$widget->update_control( 'form_fields', $control_data );
	}

	// Elementor Review notice ajax request function
	public function ccfef_elementor_review_notice() {
		if ( ! check_ajax_referer( 'ccfef_elementor_review', 'nonce', false ) ) {
			wp_send_json_error( __( 'Invalid security token sent.', 'country-code-for-elementor-form-telephone-field' ) );
			wp_die( '0', 400 );
		}

		if ( isset( $_POST['ccfef_notice_dismiss'] ) && 'true' === $_POST['ccfef_notice_dismiss'] ) {
			update_option( 'ccfef_review_notice_dismiss', 'yes' );
		}
		exit;
	}

}

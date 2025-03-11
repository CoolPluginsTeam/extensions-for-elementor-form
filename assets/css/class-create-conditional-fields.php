<?php
/**
 * Main file for adding conditional fields to Elementor Pro forms in WordPress.
 *
 * @package cfef
 *
 * @version 1.0.0
 */

use Elementor\Widget_Base;
use ElementorPro\Modules\Forms;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use ElementorPro\Plugin;

	/**
	 * Class for creating conditional fields and varify logic comparision before send
	 */
class Create_Conditional_Fields {

	/**
	 * Validate checker varibale.
	 *
	 * @var validate_form
	 */
	private $validate_form = false;

	/**
	 * Constructor
	 *
	 * @access public
	 */
	public function __construct() {
		add_action( 'elementor-pro/forms/pre_render', array( $this, 'all_field_conditions' ), 10, 3 );
		add_action( 'elementor/element/form/section_form_fields/before_section_end', array( $this, 'append_conditional_fields_controler' ), 10, 2 );
		add_action( 'wp_enqueue_scripts', array( $this, 'add_assets_files' ) );
		add_action( 'elementor/controls/register', array( $this, 'register_fields_repeater_controler' ) );
		add_action( 'elementor_pro/forms/validation', array( $this, 'check_validation' ), 9, 3 );
		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'add_editor_js' ) );
		add_action( 'wp_ajax_cfef_elementor_review_notice', array( $this, 'cfef_elementor_review_notice' ) );
	}

	/**
	 * Js and css files loaded for frontend form validation check
	 */
	public function add_assets_files() {
		// wp_register_script( 'cfef_logic', CFEF_PLUGIN_URL . 'assets/js/cfef_logic_frontend.min.js', array( 'jquery' ), CFEF_VERSION, true );
		wp_register_script( 'cfef_logic', CFEF_PLUGIN_URL . 'assets/js/cfef_logic_frontend.js', array( 'jquery' ), CFEF_VERSION, true );
		wp_localize_script(
			'cfef_logic',
			'my_script_vars',
			array(
				'pluginConstant' => CFEF_PLUGIN_DIR,
			)
		);
		wp_enqueue_script( 'cfef_logic' );
		 // Add hidden class CSS
		wp_register_style( 'hide_field_class_style', false );
		wp_enqueue_style( 'hide_field_class_style' );
		wp_add_inline_style(
			'hide_field_class_style',
			'.cfef-hidden {
			display: none !important;
	}'
		);
	}

	/**
	 *
	 * Js and css files loaded for elementor editor mode for add dynamic tags
	 */
	public function add_editor_js() {
		wp_register_script( 'cfef_logic_editor', CFEF_PLUGIN_URL . 'assets/js/cfef_editor.min.js', array( 'jquery' ), CFEF_VERSION, true );
		wp_enqueue_style( 'cfef_logic_editor', CFEF_PLUGIN_URL . 'assets/css/cfef_editor.css', null, CFEF_VERSION );
		wp_enqueue_script( 'cfef_logic_editor' );
	}

	/**
	 * Function for create conditional fields and add fields repeater.
	 *
	 * @param object $widget use for add new fields to form.
	 */
	public function append_conditional_fields_controler( $widget ) {

		$elementor    = \Elementor\Plugin::instance();
		$control_data = $elementor->controls_manager->get_control_from_stack( $widget->get_unique_name(), 'form_fields' );
		if ( is_wp_error( $control_data ) ) {
				return;
		}
			$field_controls = array(
				'form_fields_conditions_tab' =>
					array(
						'type'         => 'tab',
						'tab'          => 'content',
						'label'        => esc_html__( 'Conditions', 'cfef' ),
						'tabs_wrapper' => 'form_fields_tabs',
						'name'         => 'form_fields_conditions_tab',
						'condition'    => array(
							'field_type' => array( 'text', 'email', 'textarea', 'number', 'select', 'radio', 'checkbox', 'tel', 'url', 'date', 'time', 'html', 'upload', 'recaptcha', 'recaptcha_v3', 'password', 'acceptance', 'step' ),
						),
					),
				'cfef_logic'                 => array(
					'name'         => 'cfef_logic',
					'label'        => esc_html__( 'Enable Conditions', 'cfef' ),
					'type'         => Controls_Manager::SWITCHER,
					'tab'          => 'content',
					'inner_tab'    => 'form_fields_conditions_tab',
					'tabs_wrapper' => 'form_fields_tabs',

				),
				'cfef_logic_mode'            => array(
					'name'         => 'cfef_logic_mode',
					'label'        => esc_html__( 'Show / Hide Field', 'cfef' ),
					'type'         => Controls_Manager::CHOOSE,
					'tab'          => 'content',
					'options'      => array(
						'show' => array(
							'title' => esc_html__( 'Show', 'cfef' ),
							'icon'  => 'fa fa-eye',
						),
						'hide' => array(
							'title' => esc_html__( 'Hide', 'cfef' ),
							'icon'  => 'fa fa-eye-slash',
						),
					),
					'condition'    => array(
						'cfef_logic' => 'yes',
					),
					'default'      => 'show',
					'inner_tab'    => 'form_fields_conditions_tab',
					'tabs_wrapper' => 'form_fields_tabs',
				),
				'cfef_logic_meet'            => array(
					'name'         => 'cfef_logic_meet',
					'label'        => esc_html__( 'Conditions Trigger', 'cfef' ),
					'type'         => Controls_Manager::SELECT,
					'tab'          => 'content',
					'condition'    => array(
						'cfef_logic' => 'yes',
					),
					'options'      => array(
						'All' => esc_html__( 'All - AND Conditions', 'cfef' ),
						'Any' => esc_html__( 'Any - OR Conditions', 'cfef' ),
					),
					'default'      => 'All',
					'inner_tab'    => 'form_fields_conditions_tab',
					'tabs_wrapper' => 'form_fields_tabs',
				),

				'cfef_repeater_data'         => array(
					'name'           => 'cfef_repeater_data',
					'label'          => esc_html__( 'Show / Hide Fields If', 'cfef' ),
					'type'           => 'field_condition_repeater',
					'tab'            => 'content',
					'inner_tab'      => 'form_fields_conditions_tab',
					'tabs_wrapper'   => 'form_fields_tabs',
					'fields'         => array(
						array(
							'name'        => 'cfef_logic_field_id',
							'label'       => esc_html__( 'Field ID', 'cfef' ),
							'type'        => Controls_Manager::TEXT,
							'label_block' => true,
							'default'     => '',
							'ai'          => array(
								'active' => false,
							),
						),
						array(
							'name'        => 'cfef_logic_field_is',
							'label'       => esc_html__( 'Operator', 'cfef' ),
							'type'        => Controls_Manager::SELECT,
							'label_block' => true,
							'options'     => array(
								'==' => esc_html__( 'is equal ( == )', 'cfef' ),
								'!=' => esc_html__( 'is not equal (!=)', 'cfef' ),
								'>'  => esc_html__( 'greater than (>)', 'cfef' ),
								'<'  => esc_html__( 'less than (<)', 'cfef' ),
								'>=' => esc_html__( 'greater than equal (>=)', 'cfef' ),
								'<=' => esc_html__( 'less than equal (<=)', 'cfef' ),
								'e'  => esc_html__( "empty ('')", 'cfef' ),
								'!e' => esc_html__( 'not empty', 'cfef' ),
								'c'  => esc_html__( 'contains', 'cfef' ),
								'!c' => esc_html__( 'does not contain', 'cfef' ),
								'^'  => esc_html__( 'starts with', 'cfef' ),
								'~'  => esc_html__( 'ends with', 'cfef' ),
							),
							'default'     => '==',
						),
						array(
							'name'        => 'cfef_logic_compare_value',
							'label'       => esc_html__( 'Value', 'cfef' ),
							'type'        => Controls_Manager::TEXT,
							'label_block' => true,
							'default'     => '',
							'ai'          => array(
								'active' => false,
							),
						),
					),
					'condition'      => array(
						'cfef_logic' => 'yes',
					),
					'style_transfer' => false,
					'title_field'    => '{{{ cfef_logic_field_id  }}} {{{ cfef_logic_field_is  }}} {{{ cfef_logic_compare_value  }}}',
					'default'        => array(
						array(
							'cfef_logic_field_id'      => '',
							'cfef_logic_field_is'      => '==',
							'cfef_logic_compare_value' => '',
						),
					),
				),
			);

			if ( !is_plugin_active( 'country-code-field-for-elementor-form/country-code-field-for-elementor-form.php' ) ) {
				$field_controls['cfef_country_code_toggle'] = array(
					'name'            => 'cfef_country_code_toggle',
					'label'        => esc_html__( 'Enable Country Code', 'cfef' ),
					'type'            => Controls_Manager::SWITCHER,
					'tab'             => 'content',
					'condition'       => array(
						'field_type' => 'tel',
					),
					'inner_tab'       => 'form_fields_content_tab',
					'tabs_wrapper'    => 'form_fields_tabs',
				);

				$field_controls['cfef_country_code_link_button'] = array(
					'name'            => 'cfef_country_code_link_button',
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => "<p>To Add country code to Elementor form fields <a href='plugin-install.php?s=Country%2520Code%2520For%2520Elementor%2520Form%2520Telephone%2520Field%2520by%2520coolplugins&tab=search&type=term' target='_blank' >Activate Plugin</a></p>",
					'content_classes' => 'get_ccfef_link',
					'condition'       => array(
						'cfef_country_code_toggle' => 'yes',
						'field_type' => 'tel'
					),
					'inner_tab'       => 'form_fields_content_tab',
					'tabs_wrapper'    => 'form_fields_tabs',
				);
			}


			$field_controls['cfef_pro_link_buttons'] = array(
				'name'            => 'cfef_pro_link_buttons',
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => "<a href='https://coolplugins.net/product/conditional-fields-for-elementor-form/?utm_source=cfef_plugin&utm_medium=inside&utm_campaign=get-pro&utm_content=above-notice' target='_blank' >Get Conditional Fields Pro</a>",
				'content_classes' => 'cfef_pro_link_button',
				'tab'             => 'content',
				'condition'       => array(
					'cfef_logic' => 'yes',
				),
				'inner_tab'       => 'form_fields_conditions_tab',
				'tabs_wrapper'    => 'form_fields_tabs',
			);
			if ( ! get_option( 'cfef_elementor_notice_dismiss' ) ) {
				$review_nonce = wp_create_nonce( 'cfef_elementor_review' );
				$url          = admin_url( 'admin-ajax.php' );
				$html         = '<div class="cfef_elementor_review_wrapper">';
				$html        .= '<div id="cfef_elementor_review_dismiss" data-url="' . esc_url( $url ) . '" data-nonce="' . esc_attr( $review_nonce ) . '">Close Notice X</div>
								<div class="cfef_elementor_review_msg">' . __( 'Hope this addon solved your problem!', 'cfef' ) . '<br><a href="https://wordpress.org/support/plugin/conditional-fields-for-elementor-form/reviews/#new-post" target="_blank"">Share the love with a ⭐⭐⭐⭐⭐ rating.</a><br><br></div>
								<div class="cfef_elementor_demo_btn"><a href="https://wordpress.org/support/plugin/conditional-fields-for-elementor-form/reviews/#new-post" target="_blank">Submit Review</a></div>
								</div>';

				$field_controls['cfef_pro_image'] = array(
					'name'            => 'cfef_pro_image',
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => $html,
					'content_classes' => 'cfef_elementor_review_notice',
					'tab'             => 'content',
					'condition'       => array(
						'cfef_logic' => 'yes',
					),
					'inner_tab'       => 'form_fields_conditions_tab',
					'tabs_wrapper'    => 'form_fields_tabs',
				);
			}

			$control_data['fields'] = \array_merge( $control_data['fields'], $field_controls );
			$widget->update_control( 'form_fields', $control_data );
	}
	/**
	 * Function for call repeater call to add field repeater functionality
	 *
	 * @param object $controls_manager use for register repeater.
	 */
	public function register_fields_repeater_controler( $controls_manager ) {
		include CFEF_PLUGIN_DIR . 'includes/class-control-repeater-field.php';
		$controls_manager->register( new \Control_Repeater_Field() );
	}
	/**
	 * Function for check all the values added in conditional  fields
	 *
	 * @param string $compare_field_value having field value that use for compare.
	 * @param string $condition_operation which type of comparision apply.
	 * @param string $compare_value use for comparison.
	 */
	public function cfef_check_field_logic( $compare_field_value, $condition_operation, $compare_value ) {
		$disallowed_values = array(
			'^newOptionTest',
			'newchkTest',
			'1003-01-01',
			'11:59',
			'+1234567890',
			'https://testing.com',
			'cool_plugins@abc.com',
			'cool_plugins',
			'000',
			'premium1@',
			'cool23plugins',
		);

		// Check for disallowed values when display is 'show'.
		if ( in_array( $compare_field_value, $disallowed_values, true ) ) {
			return false;
		}

		// Sanitize and escape dynamic values.
		$compare_field_value = esc_html( $compare_field_value );
		$compare_value       = trim( $compare_value );
		$compare_value       = esc_html( $compare_value );

		$values = array_map('trim', explode(',', $compare_field_value));
		// Check if any value matches the compare value
		$match_found = in_array($compare_value, $values);

		switch ( $condition_operation ) {
			case '==':
				return $match_found && '' !== $compare_field_value;
			case '!=':
				return !$match_found && '' !== $compare_field_value;
			case '>':
				return (int) $compare_field_value > (int) $compare_value && '' !== $compare_field_value;
			case '<':
				return (int) $compare_field_value < (int) $compare_value && '' !== $compare_field_value;
			default:
				return false;
		}
	}
	/**
	 * Check all the conditional fields and create array of that validation checks of all fields and add that json object to hidden textarea that is used by js file for check validation on frontend
	 *
	 * @param  array $instance_var get form all fields.
	 */
	public function all_field_conditions( $instance ) {
		$logic_object = array();
		
		foreach ( $instance['form_fields'] as $item_index => $field ) :
			if ( ! empty( $field['cfef_logic'] ) && 'yes' === $field['cfef_logic'] ) {
				$repeater_data = $field['cfef_repeater_data'];
				$logic_object[ $field['custom_id'] ] = array(
					'display_mode' => esc_html( $field['cfef_logic_mode'] ),
					'fire_action'  => esc_html( $field['cfef_logic_meet'] ),
					'file_types' => ! empty( $field['file_types'] ) ? esc_html( $field['file_types'] ) : 'png',
				);
				foreach ( $repeater_data as $key => $data ) {
					if ( is_array( $data ) ) {
						foreach ( $data as $keys => $value ) {
							if ( is_array( $value ) ) {
								foreach ( $value as $nested_key => $nested_value ) {
									$logic_object[ $field['custom_id'] ]['logic_data'][ $key ][ $keys ][ $nested_key ] = esc_html( $nested_value );
								}
							} else {
								$logic_object[ $field['custom_id'] ]['logic_data'][ $key ][ $keys ] = esc_html( $value );
							}
						}
					} else {
						$logic_object[ $field['custom_id'] ]['logic_data'][ $key ] = is_array( $data ) ? array_map( 'esc_html', $data ) : esc_html( $data );
					}
				}
			}
		endforeach;
	
		$condition = count( $logic_object ) > 0 ? wp_json_encode( $logic_object ) : '';
		echo '<textarea class="cfef_logic_data_js cfef-hidden">' . esc_textarea( $condition ) . '</textarea>';
	}
	/**
	 * Function to validate form before submit and remove hidden fields
	 *
	 * @param  object $form_record get submitted form all fields.
	 * @param  object $ajax_handler get form all fields.
	 */
	public function check_validation( $form_record, $ajax_handler ) {
		if ( false === $this->validate_form ) {
			$submitted_form_settings = $form_record->get( 'form_settings' );
			$form_fields             = $form_record->get( 'fields' );
			foreach ( $submitted_form_settings['form_fields'] as $id => $field ) {
				if ( 'yes' === $field['cfef_logic'] ) {
					$display_mode        = $field['cfef_logic_mode'];
					$fire_action         = $field['cfef_logic_meet'];
					$condition_pass_fail = array();
					foreach ( $field['cfef_repeater_data'] as $logic_key => $logic_values ) {
						$value_id = isset( $form_fields[ $logic_values['cfef_logic_field_id'] ] )
						? $form_fields[ $logic_values['cfef_logic_field_id'] ]['value']
						: $logic_values['cfef_logic_field_id'];
						if ( is_array( $value_id ) ) {
							$value_id = implode( ', ', $value_id );
						}
						$operator              = $logic_values['cfef_logic_field_is'];
						$value                 = $logic_values['cfef_logic_compare_value'];
						$condition_pass_fail[] = $this->cfef_check_field_logic( $value_id, $operator, $value, $display_mode );
					}
					$action_type = ( 'All' === $fire_action ) ? array_reduce(
						$condition_pass_fail,
						function ( $carry, $item ) {
							return $carry && $item;
						},
						true
					) : array_reduce(
						$condition_pass_fail,
						function ( $carry, $item ) {
							return $carry || $item;
						},
						false
					);
					if ( 'show' === $display_mode && ! $action_type ) {
						$form_record->remove_field( $field['custom_id'] );
					} elseif ( 'show' !== $display_mode && $action_type ) {
						$form_record->remove_field( $field['custom_id'] );
					}
				}
			}
		}

		$this->validate_form = true;
	}

	// Elementor Review notice ajax request function
	public function cfef_elementor_review_notice() {
		if ( ! check_ajax_referer( 'cfef_elementor_review', 'nonce', false ) ) {
			wp_send_json_error( __( 'Invalid security token sent.', 'cfef' ) );
			wp_die( '0', 400 );
		}

		if ( isset( $_POST['cfef_notice_dismiss'] ) && 'true' === sanitize_text_field($_POST['cfef_notice_dismiss']) ) {
			update_option( 'cfef_elementor_notice_dismiss', 'yes' );
		}
		exit;
	}
}
new Create_Conditional_Fields();


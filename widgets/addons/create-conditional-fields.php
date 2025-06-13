<?php
namespace Cool_FormKit\Widgets\Addons;
use Cool_FormKit\Widgets\Addons\Control_Repeater_Field;
/**
 * Main file for adding conditional fields to Elementor Pro forms in WordPress.
 *
 * @package Cool_FormKit
 *
 * @version 1.1.7
 */

use Elementor\Widget_Base;
use ElementorPro\Modules\Forms;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use ElementorPro\Plugin;

	/**
	 * Class for creating conditional fields and varify logic comparision before send
	 */
if(!class_exists('Cfl_Create_Conditional_Fields')) {	 
class Cfl_Create_Conditional_Fields {

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
		add_action( 'elementor/frontend/widget/before_render', array( $this, 'all_field_conditions' ), 10, 3 );
		add_action( 'elementor/element/cool-form/section_form_fields/before_section_end', array( $this, 'append_conditional_fields_controler' ), 10, 2 );
		add_action( 'wp_enqueue_scripts', array( $this, 'add_assets_files' ) );
		add_action( 'elementor/controls/register', array( $this, 'register_fields_repeater_controler' ) );
		add_action( 'elementor/frontend/after_enqueue_scripts', array( $this, 'frontend_assets' ) );
		add_action( 'cool_formkit/forms/validation', array( $this, 'check_validation' ), 9, 3 );
		add_action( 'elementor/editor/before_enqueue_styles', array( $this, 'editor_assets' ) );
		add_action( 'wp_ajax_cfef_elementor_review_notice', array( $this, 'cfef_elementor_review_notice' ) );
		add_action( 'elementor/editor/before_enqueue_scripts', function() {
			if ( defined( 'ELEMENTOR_PLUGIN_BASE' ) ) {
				wp_enqueue_style(
					'elementor-fontawesome',
					plugin_dir_url( ELEMENTOR_PLUGIN_BASE ) . 'assets/lib/font-awesome/css/fontawesome.min.css',
					array(),
					ELEMENTOR_VERSION
				);
				wp_enqueue_style(
					'elementor-fontawesome-regular',
					plugin_dir_url( ELEMENTOR_PLUGIN_BASE ) . 'assets/lib/font-awesome/css/regular.min.css',
					array(),
					ELEMENTOR_VERSION
				);
			}
		}, 20 );
		
		
			
	}

	/**
	 * Js and css files loaded for frontend form validation check
	 */

	public function add_assets_files() {
		wp_register_script( 'cfefp_logic_cfl', CFL_PLUGIN_URL . 'assets/addons/js/logic_frontend.js', array( 'jquery' ), CFL_VERSION, true );

		// $post = \Elementor\Plugin::instance()->documents->get( get_the_ID() )->get_elements_data();
		// if($this->cfefp_check_elementor_content_for_widget($post,'form')){
			wp_enqueue_script( 'cfefp_logic_cfl' );
			wp_localize_script('cfefp_logic_cfl', 'my_script_vars', array(
				'pluginConstant' => CFL_VERSION
			));
		// }
	
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

	// Recursive function to check for a specific widget in Elementor content
	function cfefp_check_elementor_content_for_widget($elements, $widget_name) {
		foreach ($elements as $element) {
			if (isset($element['widgetType']) && $element['widgetType'] === $widget_name) {
				return true;
			}
			if (isset($element['elements']) && is_array($element['elements'])) {
				if ($this->cfefp_check_elementor_content_for_widget($element['elements'], $widget_name)) {
					return true;
				}
			}
		}
		return false;
	}
	/**
	 *
	 * Js and css files loaded for elementor editor mode for add dynamic tags
	 */
	public function editor_assets() {
		wp_register_script( 'cfl_logic_editor', CFL_PLUGIN_URL . 'assets/addons/js/editor.min.js', array( 'jquery' ), CFL_VERSION, true );
		wp_enqueue_style( 'cfl_logic_editor_css', CFL_PLUGIN_URL . 'assets/addons/css/editor.min.css', array(), CFL_VERSION );
	}


	public function frontend_assets() {
		wp_enqueue_script( 'cfl_logic_editor');
		wp_enqueue_style( 'cfl_logic_editor_css');
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
			'form_fields_conditions_tab' => array(
				'type'         => 'tab',
				'tab'          => 'content', 
				'label'        => esc_html__( 'Conditions', 'cool-formkit' ),
				'tabs_wrapper' => 'form_fields_tabs',
				'name'         => 'form_fields_conditions_tab',
				'condition'    => array(
					'field_type' => array( 'text', 'email', 'textarea', 'number', 'select', 'radio', 'checkbox', 'tel', 'url', 'date', 'time', 'html','upload', 'recaptcha' , 'recaptcha_v3' , 'password' , 'acceptance' , 'country' , 'rating' , 'slider', 'calculator', 'signature', 'step','image_radio','state','WYSIWYG','currency','monthWeek'),
				),
			),
			'cfef_logic' => array(
				'name'         => 'cfef_logic',
				'label'        => esc_html__( 'Enable Conditions', 'cool-formkit' ),
				'type'         => Controls_Manager::SWITCHER,
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_conditions_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			),
				'cfef_logic_mode' => array(
				'name'    => 'cfef_logic_mode',
				'label'   => esc_html__( 'Show / Hide Field', 'cool-formkit' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'show' => array(
						'title' => esc_html__( 'Show', 'cool-formkit' ),
						'icon'  => 'far fa-eye',
					),
					'hide' => array(
						'title' => esc_html__( 'Hide', 'cool-formkit' ),
						'icon'  => 'far fa-eye-slash',
					),
				),
				'default'      => 'show',
				'tab'          => 'content',
				'condition'    => array(
					'cfef_logic' => 'yes',
				),
				'inner_tab'    => 'form_fields_conditions_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			),
             'cfef_logic_meet' => array(
				'name'         => 'cfef_logic_meet',
				'label'        => esc_html__( 'Conditions Trigger', 'cool-formkit' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'All' => esc_html__('All - AND Conditions','cool-formkit'),
					'Any' => esc_html__('Any - OR Conditions','cool-formkit'),
				),
				'default'      => 'All',
				'tab'          => 'content',
				'condition'    => array(
					'cfef_logic' => 'yes',
				),
				'inner_tab'    => 'form_fields_conditions_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			),
			'cfef_repeater_data' => array(
				'name'           => 'cfef_repeater_data',
				'label'          => esc_html__( 'Show / Hide Fields If', 'cool-formkit' ),
				'type'           => Controls_Manager::REPEATER,
				'tab'            => 'content',
				'inner_tab'      => 'form_fields_conditions_tab',
				'tabs_wrapper'   => 'form_fields_tabs',
				'fields'         => array(
					array(
						'name'        => 'cfef_logic_field_id',
						'label'       => esc_html__( 'Field ID', 'cool-formkit' ),
						'type'        => Controls_Manager::TEXT,
						'label_block' => true,
						'default'     => '',
						'ai'          => array(
							'active' => false,
						),
					),
					array(
						'name'        => 'cfef_logic_field_is',
						'label'       => esc_html__( 'Operator', 'cool-formkit' ),
						'type'        => Controls_Manager::SELECT,
						'label_block' => true,
						'options'     => array(
							'==' => esc_html__( 'is equal ( == )', 'cool-formkit' ),
							'!=' => esc_html__( 'is not equal (!=)', 'cool-formkit' ),
							'>'  => esc_html__( 'greater than (>)', 'cool-formkit' ),
							'<'  => esc_html__( 'less than (<)', 'cool-formkit' ),
							'>='  => esc_html__( 'greater than equal (>=)', 'cool-formkit' ),
							'<='  => esc_html__( 'less than equal (<=)', 'cool-formkit' ),
							'e'  => esc_html__( "empty ('')", 'cool-formkit' ),
							'!e' => esc_html__( 'not empty', 'cool-formkit' ),
							'c'  => esc_html__( 'contains', 'cool-formkit' ),
							'!c' => esc_html__( 'does not contain', 'cool-formkit' ),
							'^'  => esc_html__( 'starts with', 'cool-formkit' ),
							'~'  => esc_html__( 'ends with', 'cool-formkit' ),
						),
						'default'     => '==',
					),
					array(
						'name'        => 'cfef_logic_compare_value',
						'label'       => esc_html__( 'Value to compare', 'cool-formkit' ),
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

		if ( ! get_option( 'cfkef_elementor_notice_dismiss' ) ) {
			$review_nonce = wp_create_nonce( 'cfef_elementor_review' );
			$url          = admin_url( 'admin-ajax.php' );
			$html         = '<div class="cfef_elementor_review_wrapper cfef_custom_html">';
			$html        .=	'<div id="cfef_elementor_review_dismiss" data-url="' . esc_url( $url ) . '" data-nonce="' . esc_attr( $review_nonce ) . '">Close Notice X</div>
							<div class="cfef_elementor_review_msg">Hope this addon solved your problem! <br><a href="https://wordpress.org/support/plugin/conditional-fields-for-elementor-form/reviews/#new-post/" target="_blank"">Share the love with a ⭐⭐⭐⭐⭐ rating.</a><br><br></div>
							<div class="cfef_elementor_demo_btn"><a href="https://wordpress.org/support/plugin/conditional-fields-for-elementor-form/reviews/#new-post" target="_blank">Submit Review</a></div>
							</div>';

			$field_controls['cfkef_conditional_field_box'] = array(
				'name'            => 'cfkef_conditional_field_box',
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
		include CFL_PLUGIN_PATH . 'widgets/addons/class-control-repeater-field.php';
		$controls_manager->register( new Control_Repeater_Field() );
	}

	/**
	 * Function for check all the values added in conditional  fields
	 *
	 * @param string $value_id having field value that use for compare.
	 * @param string $operator which type of comparision apply.
	 * @param string $value use for comparison.
	 * @param string $display_mode having value to either show or hide condition.
	 */
	public function cfefp_check_field_logic( $value_id, $operator, $value, $display_mode ) {

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

		// Check for disallowed values when display mode is 'show'.
		if ( 'show' === $display_mode && in_array( $value_id, $disallowed_values, true ) ) {
			return false;
		}

		// Sanitize and escape dynamic values.
		$value_id = esc_html( $value_id );
		$value    = trim( $value );
		$value    = esc_html( $value );
		
		$values = array_map('trim', explode(',', $value_id));
		// Check if any value matches the compare value
		$match_found = in_array($value, $values);

		switch ( $operator ) {
			case '==':
				return $match_found && '' !== $value_id;
			case '!=':
				return !$match_found && '' !== $value_id;
			case 'e':
				return empty( $value_id );
			case '!e':
				return ! empty( $value_id );
			case 'c':
				return strpos( $value_id, $value ) !== false;
			case '!c':
				return ! empty( $value_id ) && strpos( $value_id, $value ) === false;
			case '^':
				return strpos( $value_id, $value ) === 0;
			case '~':
				$position = strrpos( $value_id, $value );
				return false !== $position && strlen( $value_id ) - strlen( $value ) === $position;
			case '>':
				return (int) $value_id > (int) $value;
			case '<':
				return (int) $value_id < (int) $value;
			case '>=':
				return (int) $value_id >= (int) $value;
			case '<=':
				return (int) $value_id <= (int) $value;
			default:
				return false;
		}
	}
	/**
	 * Check all the conditional fields and create array of that validation checks of all fields and add that json object to hidden textarea that is used by js file for check validation on frontend
	 *
	 * @param  array $instance get form all fields.
	 */
	public function all_field_conditions( $instance ) {
		// Check if $instance is an object and has a get_settings() method.
		if ( is_object( $instance ) && method_exists( $instance, 'get_name' ) ) {
			$widget_name = $instance->get_name();
			if($widget_name !== 'cool-form'){
				return;
			}
		}

		if ( is_object( $instance ) && method_exists( $instance, 'get_settings' ) ) {
			$settings = $instance->get_settings();
		} else {
			$settings = $instance;
		}
	
		// Ensure we have form fields data.
		if ( empty( $settings['form_fields'] ) || ! is_array( $settings['form_fields'] ) ) {
			return;
		}
	
		$logic_object = array();
	
		foreach ( $settings['form_fields'] as $item_index => $field ) {
			if ( ! empty( $field['cfef_logic'] ) && 'yes' === $field['cfef_logic'] ) {
				// Skip if both mode and meet are not set.
				if ( ! isset( $field['cfef_logic_mode'] ) && ! isset( $field['cfef_logic_meet'] ) ) {
					continue;
				}
				$repeater_data = $field['cfef_repeater_data'];
				$logic_object[ $field['custom_id'] ] = array(
					'display_mode' => isset($field['cfef_logic_mode']) ? esc_html( $field['cfef_logic_mode'] ) : 'show',
					'fire_action'  => isset($field['cfef_logic_meet']) ? esc_html( $field['cfef_logic_meet'] ) : 'All',
					'file_types'   => ! empty( $field['file_types'] ) ? esc_html( $field['file_types'] ) : 'png',
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
		}
		$condition = count( $logic_object ) > 0 ? wp_json_encode( $logic_object ) : '';
		if ( ! empty( $condition ) ) {
			if ( is_object( $instance ) && method_exists( $instance, 'get_id' ) ) {
				$form_id = $instance->get_id();
			} elseif ( isset( $settings['id'] ) ) {
				$form_id = $settings['id'];
			} else {
				$form_id = uniqid();
			}
			$textarea_id = 'cfef_logic_data_' . $form_id;
			echo '<textarea id="' . esc_attr( $textarea_id ) . '" class="cfef_logic_data_js cfef-hidden" data-form-id="' . esc_attr( $form_id ) . '">' . esc_textarea( $condition ) . '</textarea>';
		}
	}
	/**
	 * Function to validate form before submit and remove hidden fields
	 *s
	 * @param  object $form_record get form all fields.
	 * @param  object $ajax_handler get form all fields.
	 */
	public function check_validation( $form_record, $ajax_handler ) {
		if ( false === $this->validate_form ) {
			$submitted_form_settings = $form_record->get( 'form_settings' );
			$form_fields   = $form_record->get( 'fields' );
			foreach ( $submitted_form_settings['form_fields'] as $id => $field ) {
				if ( 'yes' === $field['cfef_logic'] ) {
					$display_mode = $field['cfef_logic_mode'];
					$fire_action = $field['cfef_logic_meet'];
					$condition_pass_fail      = array();
					foreach ( $field['cfef_repeater_data'] as $field_key => $field_values ) {
						$value_id = isset( $form_fields[ $field_values['cfef_logic_field_id'] ] )
						? $form_fields[ $field_values['cfef_logic_field_id'] ]['value']
						: $field_values['cfef_logic_field_id'];
						if ( is_array( $value_id ) ) {
							$value_id = implode( ', ', $value_id );
						}
						$operator = $field_values['cfef_logic_field_is'];
						$value    = $field_values['cfef_logic_compare_value'];
						$condition_pass_fail[] = $this->cfefp_check_field_logic( $value_id, $operator, $value, $display_mode );								
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

					if(('disable' === $display_mode && $action_type) || ('enable' === $display_mode && !$action_type)){
               
						if(isset($ajax_handler->errors[$field['custom_id']])){
							unset($ajax_handler->errors[$field['custom_id']]);

							if(count($ajax_handler->errors) === 0){
								$ajax_handler->set_success(true);
							}
						}

						$form_record->remove_field( $field['custom_id'] );
					}

					if ( 'show' === $display_mode && ! $action_type ) {
						$form_record->remove_field( $field['custom_id'] );
					} elseif ( 'hide' == $display_mode && $action_type ) {
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
			wp_send_json_error( __( 'Invalid security token sent.', 'cool-formkit' ) );
			wp_die( '0', 400 );
		}

		if ( isset( $_POST['cfef_notice_dismiss'] ) && 'true' === $_POST['cfef_notice_dismiss'] ) {
			update_option( 'cfkef_elementor_notice_dismiss', 'yes' );
		}
		exit;
	}

}
}

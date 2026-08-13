<?php
/**
 * Shared Conditional Fields (logic) addon for Elementor Pro, Cool Form, and Hello Plus.
 *
 * Concrete classes are standalone (do not extend Field_Base).
 * Call init_conditional_fields_logic() from the constructor — do not use parent:: for trait methods.
 *
 * Trait name avoids colliding with action Conditional_*_Trait classes.
 *
 * @package Cool_FormKit
 */

namespace Cool_FormKit\Includes\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait Conditional_Fields_Logic_Trait {

	/**
	 * Validate checker variable.
	 *
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
	 * Pre-render / before_render hook that outputs logic data markup.
	 *
	 * @return string
	 */
	abstract protected function get_pre_render_hook(): string;

	/**
	 * Form widget name (form / cool-form / ehp-form).
	 *
	 * @return string
	 */
	abstract protected function get_form_widget_name(): string;

	/**
	 * Tel field type string (`tel` or `ehp-tel`) for conditionable field types.
	 *
	 * @return string
	 */
	abstract protected function get_tel_field_type(): string;

	/**
	 * Frontend logic script handle.
	 *
	 * @return string
	 */
	abstract protected function get_frontend_script_handle(): string;

	/**
	 * Absolute URL to the platform frontend logic script.
	 *
	 * @return string
	 */
	abstract protected function get_frontend_script_src(): string;

	/**
	 * Editor script handle.
	 *
	 * @return string
	 */
	abstract protected function get_editor_script_handle(): string;

	/**
	 * Absolute URL to the editor script.
	 *
	 * @return string
	 */
	abstract protected function get_editor_script_src(): string;

	/**
	 * Editor style handle.
	 *
	 * @return string
	 */
	abstract protected function get_editor_style_handle(): string;

	/**
	 * Absolute URL to the editor stylesheet.
	 *
	 * @return string
	 */
	abstract protected function get_editor_style_src(): string;

	/**
	 * Elementor plugin instance exposing controls_manager.
	 *
	 * @return object
	 */
	abstract protected function get_elementor_plugin();

	/**
	 * Optional widget name guard when pre_render receives a widget (Hello Plus: ehp-form).
	 *
	 * @return string Empty to skip guard.
	 */
	protected function get_pre_render_widget_name_guard(): string {
		return '';
	}

	/**
	 * Whether to inject the Elementor review notice control and AJAX dismiss handler.
	 *
	 * @return bool
	 */
	protected function should_include_review_notice(): bool {
		return false;
	}

	/**
	 * Localize `my_script_vars` on the frontend script.
	 *
	 * @return bool
	 */
	protected function should_localize_my_script_vars(): bool {
		return true;
	}

	/**
	 * Localize `my_script_vars_elementor` (includes step copy / plugin URL).
	 *
	 * @return bool
	 */
	protected function should_localize_my_script_vars_elementor(): bool {
		return false;
	}

	/**
	 * Enqueue Elementor Font Awesome in the editor (Cool Form).
	 *
	 * @return bool
	 */
	protected function should_enqueue_editor_fontawesome(): bool {
		return false;
	}

	/**
	 * Early-enqueue frontend logic on Twenty Twenty themes (Elementor Pro).
	 *
	 * @return bool
	 */
	protected function should_early_enqueue_twenty_theme(): bool {
		return false;
	}

	/**
	 * Inline CSS for hidden conditional fields.
	 *
	 * @return string
	 */
	protected function get_hidden_field_inline_css(): string {
		return '.cfef-hidden {
				display: none !important;
			 }';
	}

	/**
	 * Whether the logic data `<template>` includes class `cfef-hidden`.
	 *
	 * @return bool
	 */
	protected function logic_template_has_hidden_class(): bool {
		return false;
	}

	/**
	 * Whether the logic data `<template>` includes `data-form-id`.
	 *
	 * @return bool
	 */
	protected function logic_template_has_data_form_id(): bool {
		return true;
	}

	/**
	 * When true, form ID is taken from the second pre_render argument (`$widget`).
	 * Elementor Pro pre_render passes settings + widget.
	 *
	 * @return bool
	 */
	protected function pre_render_form_id_from_widget_arg(): bool {
		return false;
	}

	/**
	 * Elementor Pro: also remove fields belonging to a hidden step.
	 *
	 * @return bool
	 */
	protected function should_prune_hidden_step_fields(): bool {
		return false;
	}

	/**
	 * Shared constructor body. Call from the concrete class constructor.
	 */
	protected function init_conditional_fields_logic(): void {
		add_action( $this->get_pre_render_hook(), array( $this, 'all_field_conditions' ), 10, 3 );
		add_action(
			'elementor/element/' . $this->get_form_widget_name() . '/section_form_fields/before_section_end',
			array( $this, 'append_conditional_fields_controller' ),
			100,
			2
		);
		add_action( 'wp_enqueue_scripts', array( $this, 'add_assets_files' ) );
		add_action( $this->get_validation_hook(), array( $this, 'check_validation' ), 9, 3 );
		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'add_editor_js' ) );

		if ( $this->should_include_review_notice() ) {
			add_action( 'wp_ajax_cfef_elementor_review_notice', array( $this, 'cfef_elementor_review_notice' ) );
		}
	}

	/**
	 * Js and css files loaded for frontend form validation check.
	 */
	public function add_assets_files() {
		$handle = $this->get_frontend_script_handle();

		wp_register_script(
			'cfkef-shared-utils',
			CFL_PLUGIN_URL . 'assets/js/shared/cfkef-utils.js',
			array(),
			CFL_VERSION,
			true
		);
		wp_register_script(
			'cfkef-shared-field-logic',
			CFL_PLUGIN_URL . 'assets/js/shared/field-logic.js',
			array( 'cfkef-shared-utils' ),
			CFL_VERSION,
			true
		);
		wp_register_script(
			'cfkef-shared-logic-frontend',
			CFL_PLUGIN_URL . 'assets/js/shared/logic-frontend.js',
			array( 'jquery', 'cfkef-shared-utils', 'cfkef-shared-field-logic' ),
			CFL_VERSION,
			true
		);
		wp_register_script(
			$handle,
			$this->get_frontend_script_src(),
			array( 'jquery', 'cfkef-shared-logic-frontend' ),
			CFL_VERSION,
			true
		);

		if ( $this->should_early_enqueue_twenty_theme() ) {
			$theme      = wp_get_theme();
			$stylesheet = strtolower( $theme->get_stylesheet() );
			$theme_name = strtolower( $theme->get( 'Name' ) );

			if ( strpos( $stylesheet, 'twentytwenty' ) !== false || strpos( $theme_name, 'twenty twenty' ) !== false ) {
				wp_enqueue_script( $handle );
			}
		}

		if ( $this->should_localize_my_script_vars_elementor() ) {
			wp_localize_script(
				$handle,
				'my_script_vars_elementor',
				array(
					'pluginConstant' => CFL_PLUGIN_URL,
					'no_input_step'  => __( 'No input is required on this step. Just click "%s" to proceed.', 'extensions-for-elementor-form' ),
				)
			);
		}

		if ( $this->should_localize_my_script_vars() ) {
			wp_localize_script(
				$handle,
				'my_script_vars',
				array(
					'pluginConstant' => CFL_PLUGIN_URL,
				)
			);
		}

		wp_register_style( 'hide_field_class_style', false );
		wp_enqueue_style( 'hide_field_class_style' );
		wp_add_inline_style( 'hide_field_class_style', $this->get_hidden_field_inline_css() );
	}

	/**
	 * Js and css files loaded for elementor editor mode for add dynamic tags.
	 */
	public function add_editor_js() {
		$deps = array( 'jquery', 'cfkef-shared-conditional-editor' );
		if ( $this->should_include_review_notice() ) {
			if ( function_exists( 'cfl_register_review_dismiss_script' ) ) {
				cfl_register_review_dismiss_script();
			}
			$deps[] = 'cfkef-review-dismiss';
		}
		wp_register_script(
			'cfkef-shared-conditional-editor',
			CFL_PLUGIN_URL . 'assets/js/shared/conditional-editor.js',
			array( 'jquery' ),
			CFL_VERSION,
			true
		);
		wp_register_script(
			$this->get_editor_script_handle(),
			$this->get_editor_script_src(),
			$deps,
			CFL_VERSION,
			true
		);
		wp_register_style(
			$this->get_editor_style_handle(),
			$this->get_editor_style_src(),
			null,
			CFL_VERSION
		);
		wp_enqueue_style( $this->get_editor_style_handle() );
		wp_enqueue_script( $this->get_editor_script_handle() );

		if ( $this->should_enqueue_editor_fontawesome() && defined( 'ELEMENTOR_PLUGIN_BASE' ) ) {
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
	}

	/**
	 * Field types that support the Conditions tab.
	 *
	 * @return array
	 */
	protected function get_conditionable_field_types(): array {
		return array(
			'text',
			'email',
			'textarea',
			'number',
			'select',
			'radio',
			'checkbox',
			$this->get_tel_field_type(),
			'url',
			'date',
			'time',
			'html',
			'upload',
			'recaptcha',
			'recaptcha_v3',
			'password',
			'acceptance',
			'country',
			'rating',
			'slider',
			'step',
		);
	}

	/**
	 * Function for create conditional fields and add fields repeater.
	 *
	 * @param object $widget use for add new fields to form.
	 */
	public function append_conditional_fields_controller( $widget ) {
		$elementor    = $this->get_elementor_plugin();
		$control_data = $elementor->controls_manager->get_control_from_stack( $widget->get_unique_name(), 'form_fields' );
		if ( is_wp_error( $control_data ) ) {
			return;
		}

		$field_controls = array_merge(
			$this->get_conditional_fields_tab_controls(),
			$this->get_conditional_fields_enable_controls(),
			$this->get_conditional_fields_visibility_controls(),
			$this->get_conditional_fields_condition_controls()
		);

		$review_notice_control = $this->get_conditional_fields_review_notice_control();
		if ( null !== $review_notice_control ) {
			$field_controls['cfkef_conditional_field_box'] = $review_notice_control;
		}

		$control_data['fields'] = \array_merge( $control_data['fields'], $field_controls );
		$widget->update_control( 'form_fields', $control_data );
	}

	/**
	 * Shared placement for controls on the form field Conditions tab.
	 *
	 * @return array<string, string>
	 */
	private function get_conditional_fields_tab_placement(): array {
		return array(
			'tab'          => 'content',
			'inner_tab'    => 'form_fields_conditions_tab',
			'tabs_wrapper' => 'form_fields_tabs',
		);
	}

	/**
	 * @return array<string, array<string, mixed>>
	 */
	private function get_conditional_fields_tab_controls(): array {
		return array(
			'form_fields_conditions_tab' => array(
				'type'         => 'tab',
				'tab'          => 'content',
				'label'        => esc_html__( 'Conditions', 'extensions-for-elementor-form' ),
				'tabs_wrapper' => 'form_fields_tabs',
				'name'         => 'form_fields_conditions_tab',
				'condition'    => array(
					'field_type' => $this->get_conditionable_field_types(),
				),
			),
		);
	}

	/**
	 * @return array<string, array<string, mixed>>
	 */
	private function get_conditional_fields_enable_controls(): array {
		return array(
			'cfef_logic' => array_merge(
				array(
					'name'  => 'cfef_logic',
					'label' => esc_html__( 'Enable Conditions', 'extensions-for-elementor-form' ),
					'type'  => \Elementor\Controls_Manager::SWITCHER,
				),
				$this->get_conditional_fields_tab_placement()
			),
		);
	}

	/**
	 * Show / hide / enable / disable mode when conditions apply.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	private function get_conditional_fields_visibility_controls(): array {
		return array(
			'cfef_logic_mode' => array_merge(
				array(
					'name'      => 'cfef_logic_mode',
					'label'     => esc_html__( 'Show / Hide Field', 'extensions-for-elementor-form' ),
					'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => array(
					'show'    => array(
						'title' => esc_html__( 'Show', 'extensions-for-elementor-form' ),
						'icon'  => 'fa fa-eye',
					),
					'hide'    => array(
						'title' => esc_html__( 'Hide', 'extensions-for-elementor-form' ),
						'icon'  => 'fa fa-eye-slash',
					),
				),
					'default'   => 'show',
					'condition' => array(
						'cfef_logic' => 'yes',
					),
				),
				$this->get_conditional_fields_tab_placement()
			),
		);
	}

	/**
	 * AND/OR trigger and repeater rules.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	private function get_conditional_fields_condition_controls(): array {
		$enabled_condition = array(
			'cfef_logic' => 'yes',
		);

		return array(
			'cfef_logic_meet' => array_merge(
				array(
					'name'      => 'cfef_logic_meet',
					'label'     => esc_html__( 'Conditions Trigger', 'extensions-for-elementor-form' ),
					'type'      => \Elementor\Controls_Manager::SELECT,
					'options'   => array(
						'All' => esc_html__( 'All - AND Conditions', 'extensions-for-elementor-form' ),
						'Any' => esc_html__( 'Any - OR Conditions', 'extensions-for-elementor-form' ),
					),
					'default'   => 'All',
					'condition' => $enabled_condition,
				),
				$this->get_conditional_fields_tab_placement()
			),
			'cfef_repeater_data' => array_merge(
				array(
					'name'           => 'cfef_repeater_data',
					'label'          => esc_html__( 'Show / Hide Fields If', 'extensions-for-elementor-form' ),
					'type'           => \Elementor\Controls_Manager::REPEATER,
					'fields'         => $this->get_conditional_fields_repeater_fields(),
					'condition'      => $enabled_condition,
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
				$this->get_conditional_fields_tab_placement()
			),
		);
	}

	/**
	 * Repeater row controls for a single condition rule.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	private function get_conditional_fields_repeater_fields(): array {
		return array(
			array(
				'name'        => 'cfef_logic_field_id',
				'label'       => esc_html__( 'Field ID', 'extensions-for-elementor-form' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => '',
				'ai'          => array(
					'active' => false,
				),
			),
			array(
				'name'        => 'cfef_logic_field_is',
				'label'       => esc_html__( 'Operator', 'extensions-for-elementor-form' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'options'     => array(
					'==' => esc_html__( 'is equal ( == )', 'extensions-for-elementor-form' ),
					'!=' => esc_html__( 'is not equal (!=)', 'extensions-for-elementor-form' ),
					'>'  => esc_html__( 'greater than (>)', 'extensions-for-elementor-form' ),
					'<'  => esc_html__( 'less than (<)', 'extensions-for-elementor-form' ),
					'>=' => esc_html__( 'greater than equal (>=)', 'extensions-for-elementor-form' ),
					'<=' => esc_html__( 'less than equal (<=)', 'extensions-for-elementor-form' ),
					'e'  => esc_html__( "empty ('')", 'extensions-for-elementor-form' ),
					'!e' => esc_html__( 'not empty', 'extensions-for-elementor-form' ),
					'c'  => esc_html__( 'contains', 'extensions-for-elementor-form' ),
					'!c' => esc_html__( 'does not contain', 'extensions-for-elementor-form' ),
					'^'  => esc_html__( 'starts with', 'extensions-for-elementor-form' ),
					'~'  => esc_html__( 'ends with', 'extensions-for-elementor-form' ),
				),
				'default'     => '==',
			),
			array(
				'name'        => 'cfef_logic_compare_value',
				'label'       => esc_html__( 'Value to compare', 'extensions-for-elementor-form' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => '',
				'ai'          => array(
					'active' => false,
				),
			),
		);
	}

	/**
	 * Optional Elementor.org review notice on the Conditions tab.
	 *
	 * @return array<string, mixed>|null
	 */
	private function get_conditional_fields_review_notice_control(): ?array {
		if ( ! $this->should_include_review_notice() || get_option( 'cfkef_elementor_notice_dismiss' ) ) {
			return null;
		}

		$review_nonce = wp_create_nonce( 'cfef_elementor_review' );
		$url          = admin_url( 'admin-ajax.php' );
		$html         = '<div class="cfef_elementor_review_wrapper cfef_custom_html">';
		$html        .= '<div id="cfef_elementor_review_dismiss" data-url="' . esc_url( $url ) . '" data-nonce="' . esc_attr( $review_nonce ) . '">Close Notice X</div>
								<div class="cfef_elementor_review_msg">Hope this addon solved your problem! <br><a href="https://wordpress.org/support/plugin/conditional-fields-for-elementor-form/reviews/#new-post/" target="_blank" rel="noopener noreferrer"">Share the love with a ⭐⭐⭐⭐⭐ rating.</a><br><br></div>
								<div class="cfef_elementor_demo_btn"><a href="https://wordpress.org/support/plugin/conditional-fields-for-elementor-form/reviews/#new-post" target="_blank" rel="noopener noreferrer">Submit Review</a></div>
								</div>';

		return array_merge(
			array(
				'name'            => 'cfkef_conditional_field_box',
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => $html,
				'content_classes' => 'cfef_elementor_review_notice',
				'condition'       => array(
					'cfef_logic' => 'yes',
				),
			),
			$this->get_conditional_fields_tab_placement()
		);
	}

	/**
	 * Function for check all the values added in conditional fields.
	 *
	 * @param string $value_id Field value that use for compare.
	 * @param string $operator Which type of comparision apply.
	 * @param string $value    Use for comparison.
	 * @return bool
	 */
	public function cfefp_check_field_logic( $value_id, $operator, $value ) {
		return cfl_check_field_logic( $value_id, $operator, $value );
	}

	/**
	 * Resolve form settings and optional form ID sources from pre_render args.
	 *
	 * @param mixed $instance First pre_render argument.
	 * @param mixed $widget   Second pre_render argument (Elementor Pro).
	 * @return array{settings: array|mixed, form_id: string|null}
	 */
	protected function resolve_pre_render_context( $instance, $widget = null ): array {
		$guard = $this->get_pre_render_widget_name_guard();
		if ( '' !== $guard ) {
			if ( ! is_object( $instance ) || ! method_exists( $instance, 'get_name' ) || $instance->get_name() !== $guard ) {
				return array(
					'settings' => null,
					'form_id'  => null,
				);
			}
		}

		if ( is_object( $instance ) && method_exists( $instance, 'get_settings' ) ) {
			$settings = $instance->get_settings();
		} else {
			$settings = $instance;
		}

		$form_id = null;
		if ( $this->pre_render_form_id_from_widget_arg() ) {
			if ( is_object( $widget ) && method_exists( $widget, 'get_id' ) ) {
				$form_id = $widget->get_id();
			}
		} elseif ( is_object( $instance ) && method_exists( $instance, 'get_id' ) ) {
			$form_id = $instance->get_id();
		} elseif ( is_array( $settings ) && isset( $settings['id'] ) ) {
			$form_id = $settings['id'];
		}

		return array(
			'settings' => $settings,
			'form_id'  => $form_id,
		);
	}

	/**
	 * Check all the conditional fields and emit logic JSON for frontend JS.
	 *
	 * @param mixed $instance get form all fields / widget.
	 * @param mixed $widget   Optional widget (Elementor Pro pre_render).
	 */
	public function all_field_conditions( $instance, $widget = null ) {
		$context = $this->resolve_pre_render_context( $instance, $widget );
		if ( null === $context['settings'] ) {
			return;
		}

		$settings = $context['settings'];

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

				// Some form registrars initialize after wp_enqueue_scripts. Ensure the
				// wrapper and its shared dependency exist before enqueueing the handle.
				if ( ! wp_script_is( $this->get_frontend_script_handle(), 'registered' ) ) {
					$this->add_assets_files();
				}
				wp_enqueue_script( $this->get_frontend_script_handle() );
				$repeater_data                       = $field['cfef_repeater_data'];
				$logic_object[ $field['custom_id'] ] = array(
					'display_mode' => esc_html( $field['cfef_logic_mode'] ),
					'fire_action'  => esc_html( $field['cfef_logic_meet'] ),
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
		if ( empty( $condition ) ) {
			return;
		}

		$form_id = $context['form_id'];
		if ( null === $form_id || '' === $form_id ) {
			if ( $this->pre_render_form_id_from_widget_arg() ) {
				// Preserve Pro behavior when widget id is unavailable (undefined $form_id historically).
				$form_id = '';
			} else {
				$form_id = uniqid();
			}
		}

		$template_id = 'cfef_logic_data_' . $form_id;
		$classes     = 'cfef_logic_data_js';
		if ( $this->logic_template_has_hidden_class() ) {
			$classes .= ' cfef-hidden';
		}

		$attrs = 'id="' . esc_attr( $template_id ) . '" class="' . esc_attr( $classes ) . '"';
		if ( $this->logic_template_has_data_form_id() ) {
			$attrs .= ' data-form-id="' . esc_attr( $form_id ) . '"';
		}

		echo '<template ' . $attrs . '>' . esc_html( $condition ) . '</template>';
	}

	/**
	 * Resolve a logic repeater row to a comparable field value.
	 *
	 * @param array $form_fields  Submitted fields keyed by custom id.
	 * @param array $field_values Repeater row.
	 * @return array{status: string, value?: mixed} status is ok|skip|fail.
	 */
	protected function resolve_logic_field_value( $form_fields, $field_values ): array {
		$logic_field_id = isset( $field_values['cfef_logic_field_id'] ) ? $field_values['cfef_logic_field_id'] : '';
		$value_id       = isset( $form_fields[ $logic_field_id ] )
			? $form_fields[ $logic_field_id ]['value']
			: $logic_field_id;

		return array(
			'status' => 'ok',
			'value'  => $value_id,
		);
	}

	/**
	 * Delete fields of a hidden step field.
	 *
	 * @param array  $form_fields       Submitted fields.
	 * @param string $hidden_step       Hidden step custom id.
	 * @param array  $disallowed_values Values that trigger removal.
	 * @param object $form_record       Form record.
	 * @return void
	 */
	public function delete_fields_of_hidden_step( $form_fields, $hidden_step, $disallowed_values, $form_record ) {
		// Make sure inputs are usable.
		if ( ! is_array( $form_fields ) || empty( $form_fields ) ) {
			return;
		}
		if ( ! is_string( $hidden_step ) || $hidden_step === '' ) {
			return;
		}
		if ( ! is_array( $disallowed_values ) ) {
			$disallowed_values = array();
		}
		if ( ! is_object( $form_record ) || ! method_exists( $form_record, 'remove_field' ) ) {
			return;
		}

		// Get all keys of the original array.
		$keys = array_keys( $form_fields );

		// Check if hidden step exists.
		if ( ! in_array( $hidden_step, $keys, true ) ) {
			return;
		}

		$index = array_search( $hidden_step, $keys, true );

		// Slice array after the hidden step.
		$sliced_array = array_slice( $form_fields, $index + 1, null, true );

		foreach ( $sliced_array as $key => $value ) {
			// Skip invalid field data.
			if ( ! is_array( $value ) || ! isset( $value['type'] ) ) {
				continue;
			}

			if ( $value['type'] !== 'step' ) {
				// Only check if 'value' exists.
				if ( isset( $value['value'] ) && in_array( $value['value'], $disallowed_values, true ) ) {
					$form_record->remove_field( $key );
				}
			} else {
				// Stop at the next step.
				break;
			}
		}
	}

	/**
	 * Remove a conditionally hidden field and clear its validation errors.
	 *
	 * @param object $form_record  Form record.
	 * @param object $ajax_handler Ajax handler.
	 * @param string $field_id     Field custom ID.
	 * @return void
	 */
	private function remove_hidden_conditional_field( $form_record, $ajax_handler, $field_id ) {
		if ( isset( $ajax_handler->errors[ $field_id ] ) ) {
			unset( $ajax_handler->errors[ $field_id ] );

			if ( method_exists( $ajax_handler, 'set_success' ) && count( $ajax_handler->errors ) === 0 ) {
				$ajax_handler->set_success( true );
			}
		}

		$form_record->remove_field( $field_id );
	}

	/**
	 * Function to validate form before submit and remove hidden fields.
	 *
	 * @param object $form_record  get form all fields.
	 * @param object $ajax_handler get form all fields.
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
					foreach ( $field['cfef_repeater_data'] as $field_key => $field_values ) {
						$resolved = $this->resolve_logic_field_value( $form_fields, $field_values );
						if ( 'skip' === $resolved['status'] ) {
							continue;
						}
						if ( 'fail' === $resolved['status'] ) {
							$condition_pass_fail[] = false;
							continue;
						}

						$value_id = $resolved['value'];
						if ( is_array( $value_id ) ) {
							$value_id = implode( ', ', $value_id );
						}
						$operator              = $field_values['cfef_logic_field_is'];
						$value                 = $field_values['cfef_logic_compare_value'];
						$condition_pass_fail[] = $this->cfefp_check_field_logic( $value_id, $operator, $value );
					}
					$should_keep = cfl_evaluate_conditional_display(
						$condition_pass_fail,
						$fire_action,
						$display_mode
					);

					if ( ! $should_keep ) {
						if ( $this->should_prune_hidden_step_fields() && ( 'show' === $display_mode || 'hide' === $display_mode ) ) {
							$this->delete_fields_of_hidden_step( $form_fields, $field['custom_id'], array(), $form_record );
						}
						$this->remove_hidden_conditional_field( $form_record, $ajax_handler, $field['custom_id'] );
					}
				}
			}
		}
		$this->validate_form = true;
	}

	/**
	 * Elementor Review notice ajax request function.
	 */
	public function cfef_elementor_review_notice() {
		if ( ! check_ajax_referer( 'cfef_elementor_review', 'nonce', false ) ) {
			wp_send_json_error( __( 'Invalid security token sent.', 'extensions-for-elementor-form' ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Permission denied.', 'extensions-for-elementor-form' ) );
		}

		if ( isset( $_POST['cfef_notice_dismiss'] ) && 'true' === sanitize_text_field( wp_unslash( $_POST['cfef_notice_dismiss'] ) ) ) {
			update_option( 'cfkef_elementor_notice_dismiss', 'yes', false );
		}
		exit;
	}
}

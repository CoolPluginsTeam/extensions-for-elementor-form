<?php

namespace Cool_FormKit\Includes\Frontend\Widget;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Custom message on success class.
 */
class Custom_Success_Message {
	/**
	 * Set required hooks
	 */
	public function set_hooks() : void {
		add_action( 'elementor/widget/before_render_content', array( $this, 'add_message_class' ) );
		add_action( 'elementor/element/form/section_integration/after_section_end', array( $this, 'add_message_control' ), 100, 2 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frondend_scripts' ) );
	}

	public function enqueue_frondend_scripts(){
		wp_register_style( 'eef-frontend-style',  CFL_PLUGIN_URL . 'assets/css/style.min.css', array(), CFL_VERSION );
		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter
		wp_register_script( 'eef-frontend-script', CFL_PLUGIN_URL . 'assets/js/frontend-scripts.min.js', array( 'jquery' ), CFL_VERSION );
	}
	/**
	 * add_css_class_field_control
	 * @param $element
	 * @param $args
	 */
	public function add_message_control ( $element, $args ) {
		$element->start_controls_section(
			'evcode_message_template',
			[
				'label' => \esc_html__( 'Custom Success Message', 'extensions-for-elementor-form' ),
			]
		);

		$element->add_control(
			'hide_form_after_submit',
			[
				'label' => \esc_html__( 'Hide form after submit?', 'extensions-for-elementor-form' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => \esc_html__( 'Hide', 'extensions-for-elementor-form' ),
				'label_off' => \esc_html__( 'Show', 'extensions-for-elementor-form' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'description' => \esc_html__( 'This option hide the form after sucess submit.', 'extensions-for-elementor-form' ),
			]
		);

		$element->add_control(
			'template-custom-sucess-message',
			[
				'label' => \esc_html__( 'Message Template', 'extensions-for-elementor-form' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => \esc_html__( '[your-shortcode-here]', 'extensions-for-elementor-form' ),
				'label_block' => true,
				'render_type' => 'none',
				'classes' => 'elementor_control_message_control-ltr',
				'description' => \esc_html__( 'Paste shortcode for your sucess message template.', 'extensions-for-elementor-form' ),
				'condition' => array(
					'hide_form_after_submit' => 'yes'
				)
			]
		);

		$element->end_controls_section();
	}

	/**
	 * Add custom class to message.
	 *
	 * @param [type] $form
	 */
	public function add_message_class ( $form ) {
		if( 'form' === $form->get_name() ) {
			$settings = $form->get_settings();

			add_action( 'elementor-pro/forms/pre_render', [ $this, 'template_message' ] );

			if(isset($settings['hide_form_after_submit'])  &&'yes' == $settings['hide_form_after_submit'] ) {
				wp_enqueue_style( 'eef-frontend-style' );
				wp_enqueue_script( 'eef-frontend-script' );
				$form->add_render_attribute( 'wrapper', 'class', 'ele-extensions-hide-form', true );
			}
		}
	}

	/**
	 * Custom temlate message.
	 *
	 * @param [type] $instance
	 */
	public function template_message ( $instance ) {
		if ( ! $instance['template-custom-sucess-message'] == '' ) {
			echo '<div class="extensions-for-elementor-form custom-sucess-message">' . do_shortcode( $instance['template-custom-sucess-message'] ) . '</div>';
		}
	}
}

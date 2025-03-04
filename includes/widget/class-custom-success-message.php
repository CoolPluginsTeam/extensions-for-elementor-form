<?php

namespace Cool_FormKit\Includes;

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
				'label' => \esc_html__( 'Custom Success Message', 'extensions_elementor_form' ),
			]
		);

		$element->add_control(
			'hide_form_after_submit',
			[
				'label' => \esc_html__( 'Hide form after submit?', 'extensions_elementor_form' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => \esc_html__( 'Hide', 'extensions_elementor_form' ),
				'label_off' => \esc_html__( 'Show', 'extensions_elementor_form' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'description' => \esc_html__( 'This option hide the form after sucess submit.', 'extensions_elementor_form' ),
			]
		);

		$element->add_control(
			'template-custom-sucess-message',
			[
				'label' => \esc_html__( 'Message Template', 'extensions_elementor_form' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => \esc_html__( '[your-shortcode-here]', 'extensions_elementor_form' ),
				'label_block' => true,
				'render_type' => 'none',
				'classes' => 'elementor_control_message_control-ltr',
				'description' => \esc_html__( 'Paste shortcode for your sucess message template.', 'extensions_elementor_form' ),
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

			if( 'yes' == $settings['hide_form_after_submit'] ) {
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

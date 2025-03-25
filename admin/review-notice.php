<?php

namespace Cool_FormKit;

if (! defined('ABSPATH')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}


class Review_notice
{
    public function __construct()
    {

        add_action('elementor/element/cool-form/section_form_fields/before_section_end', array($this, 'update_controls'), 10, 2);

        add_action('elementor/editor/before_enqueue_styles', array($this, 'editor_assets'));

        add_action('wp_ajax_cfl_elementor_review_notice', array($this, 'cfl_elementor_review_notice'));
    }


    public function editor_assets()
	{
		wp_register_script('cfl_logic_editor', CFL_PLUGIN_URL . 'assets/js/cfl_editor.min.js', array('jquery'), CFL_VERSION, true);
		wp_enqueue_style('cfl_logic_editor', CFL_PLUGIN_URL . 'assets/css/cfl_editor.min.css', null, CFL_VERSION);
		wp_enqueue_script('cfl_logic_editor');
	}


    public function update_controls($widget)
	{

		$elementor = \Elementor\Plugin::instance();
		$control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');

		// Check if the review notice option has been dismissed
		if (! get_option('cfl_review_notice_dismiss')) {
			// Create nonce for security
			$review_nonce = wp_create_nonce('cfl_elementor_review');
			$url          = admin_url('admin-ajax.php');

			// HTML for the review notice

			$html         = '<div class="cfl_elementor_review_wrapper">';
				$html        .= '<div id="cfl_elementor_review_dismiss" data-url="' . esc_url( $url ) . '" data-nonce="' . esc_attr( $review_nonce ) . '">Close Notice X</div>
								<div class="cfl_elementor_review_msg">' . __( 'Hope this addon solved your problem!', 'cfl' ) . '<br><a href="https://wordpress.org/support/plugin/extensions-for-elementor-form/reviews/#new-post" target="_blank"">Share the love with a ⭐⭐⭐⭐⭐ rating.</a><br><br></div>
								<div class="cfl_elementor_demo_btn"><a href="https://wordpress.org/support/plugin/extensions-for-elementor-form/reviews/#new-post" target="_blank">Submit Review</a></div>
								</div>'; // Close main wrapper 

			// Add review notice field control
			$field_controls['cfl_review_notice'] = array(
				'name'            => 'cfl_review_notice',
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => $html,
				'content_classes' => 'cfl_elementor_review_notice',
				'tab'             => 'content',
				'condition'       => array(
					// 'field_type'               => 'tel',
					// 'cfl-country-code-field' => 'yes',
				),
				'inner_tab'       => 'form_fields_content_tab',
				'tabs_wrapper'    => 'form_fields_tabs',
			);

			// Merge new field controls with existing ones
			if (isset($control_data['fields'])) {
				$control_data['fields'] = array_merge($control_data['fields'], $field_controls);
			} else {
				$control_data['fields'] = $field_controls; // Initialize if not set
			}
			// Update widget controls
			$widget->update_control('form_fields', $control_data);
		}


	}


    public function cfl_elementor_review_notice() {

		if ( ! check_ajax_referer( 'cfl_elementor_review', 'nonce', false ) ) {
			wp_send_json_error( __( 'Invalid security token sent.', 'cfl' ) );
			wp_die( '0', 400 );
		}

		if ( isset( $_POST['cfl_notice_dismiss'] ) && 'true' === sanitize_text_field($_POST['cfl_notice_dismiss']) ) {
			update_option( 'cfl_review_notice_dismiss', 'yes' );
		}
		exit;
	}
}

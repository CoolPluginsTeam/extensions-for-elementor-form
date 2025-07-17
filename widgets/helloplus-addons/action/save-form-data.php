<?php
namespace Cool_FormKit\Widgets\HelloPlusAddons\Action;

use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Save_Form_Data {
    public function __construct() {
        add_action('elementor/element/ehp-form/section_integration/after_section_start', [$this, 'add_controls'], 10, 2);
        
    }

    public function add_controls($element, $args) {
        $element->add_control(
            'save_form_data',
            [
                'label' => esc_html__('Save Form Data', 'cool-formkit'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'cool-formkit'),
                'label_off' => esc_html__('No', 'cool-formkit'),
                'default' => 'yes',
                'description' => esc_html__('Choose whether to save the form data or not', 'cool-formkit'),
            ]
        );
        $element->add_control(
            'helloplus_collect_entries_field_message',
            [
                'type' => \Elementor\Controls_Manager::ALERT,
                'alert_type' => 'info',
                'content' => sprintf(
                    esc_html__('This action will collect the entries and store it in a variable. You can use this variable in the next action or in the same form.', 'cool-formkit'),
                    sprintf('<a href="%s" target="_blank">%s</a>', get_admin_url() . 'admin.php?page=cool-formkit-for-elementor-forms', esc_html__('Learn More', 'cool-formkit')),
                ),
                'condition' => array(
                    'save_form_data' => 'yes'
                ),
            ]
        );

        $element->add_control(
            'helloplus_collect_entries_field',
            [
                'label' => esc_html__('Collect Entries Field', 'cool-formkit'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'all' => esc_html__('All', 'cool-formkit'),
                    'selected' => esc_html__('Selected', 'cool-formkit'),
                ],
                'condition' => array(
                    'save_form_data' => 'yes'
                ),
            ]
        );

        $element->add_control(
            'helloplus_collect_entries_meta_data',
            [
                'label' => esc_html__('Collect Entries Meta Data', 'cool-formkit'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => [
                    'remote_ip' => esc_html__('User IP', 'cool-formkit'),
                    'user_agent' => esc_html__('User Agent', 'cool-formkit')
                ],
                'render_type' => 'none',
                'multiple' => true,
                'label_block' => true,
                'condition' => array(
                    'save_form_data' => 'yes'
                ),
                'default' => [
                    'remote_ip',
                    'user_agent',
                ],
            ]
        );

        $element->add_control(
			'helloplus_submission_divider',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

    }
}
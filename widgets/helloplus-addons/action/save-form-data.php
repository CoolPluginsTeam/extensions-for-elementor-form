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
                'label' => esc_html__('Save Form Data', 'extensions-for-elementor-form'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'extensions-for-elementor-form'),
                'label_off' => esc_html__('No', 'extensions-for-elementor-form'),
                'default' => 'yes',
                'description' => esc_html__('Choose whether to save the form data or not', 'extensions-for-elementor-form'),
            ]
        );
        $element->add_control(
            'helloplus_collect_entries_field_message',
            [
                'type' => \Elementor\Controls_Manager::ALERT,
                'alert_type' => 'info',
                'content' => sprintf(
                    esc_html__('This action will collect the entries and store it in a variable. You can use this variable in the next action or in the same form.', 'extensions-for-elementor-form'),
                    sprintf('<a href="%s" target="_blank">%s</a>', get_admin_url() . 'admin.php?page=cool-formkit-for-elementor-forms', esc_html__('Learn More', 'extensions-for-elementor-form')),
                ),
                'condition' => array(
                    'save_form_data' => 'yes'
                ),
            ]
        );

        $element->add_control(
            'helloplus_collect_entries_field',
            [
                'label' => esc_html__('Collect Entries Field', 'extensions-for-elementor-form'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'all' => esc_html__('All', 'extensions-for-elementor-form'),
                    'selected' => esc_html__('Selected', 'extensions-for-elementor-form'),
                ],
                'condition' => array(
                    'save_form_data' => 'yes'
                ),
            ]
        );

        $element->add_control(
            'helloplus_collect_entries_meta_data',
            [
                'label' => esc_html__('Collect Entries Meta Data', 'extensions-for-elementor-form'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => [
                    'remote_ip' => esc_html__('User IP', 'extensions-for-elementor-form'),
                    'user_agent' => esc_html__('User Agent', 'extensions-for-elementor-form')
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
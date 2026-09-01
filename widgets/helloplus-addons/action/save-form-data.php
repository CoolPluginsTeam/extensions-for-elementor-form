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
			'helloplus_submission_divider',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

    }
}

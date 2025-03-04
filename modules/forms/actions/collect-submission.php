<?php

namespace Cool_FormKit\Modules\Forms\Actions;

use Elementor\Controls_Manager;
use Cool_FormKit\Modules\Forms\Classes\Action_Base;
use Cool_FormKit\Modules\Forms\Classes\Form_Record;
use Cool_FormKit\Modules\Forms\Components\Ajax_Handler;
use Cool_FormKit\Modules\Forms\Module;
use Cool_FormKit\Collect_Submission\CFKEF_Save_Submission;

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Collect Submission Action
 * 
 * This action collects the submission and stores it in a variable.
 * You can use this variable in the next action or in the same form.
 */
class Collect_Submission extends Action_Base
{

    /**
     * Get the name of the action.
     * 
     * @return string
     */
    public function get_name(): string
    {
        return 'cool_collect_submission';
    }

    /**
     * Get the label of the action.
     * 
     * @return string
     */
    public function get_label(): string
    {
        return esc_html__('Collect Submission', 'cool-formkit');
    }

    /**
     * Register the settings section.
     * 
     * @param \Elementor\Widget_Base $widget
     */
    public function register_settings_section($widget)
    {
        $widget->start_controls_section(
            'section_collect_submission',
            [
                'label' => $this->get_label(),
                'tab' => Controls_Manager::TAB_CONTENT,
                'condition' => [
                    'submit_actions' => $this->get_name(),
                ],
            ]
        );

        $widget->add_control(
            'collect_submission_field_message',
            [
                'type' => Controls_Manager::ALERT,
                'alert_type' => 'info',
                'content' => sprintf(
                    esc_html__('This action will collect the submission and store it in a variable. You can use this variable in the next action or in the same form.', 'cool-formkit'),
                    sprintf('<a href="%s" target="_blank">%s</a>', get_admin_url() . 'admin.php?page=cool-formkit-settings', esc_html__('Learn More', 'cool-formkit')),
                ),
            ]
        );

        $widget->add_control(
            'collect_submission_field',
            [
                'label' => esc_html__('Collect Submission Field', 'cool-formkit'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'all' => esc_html__('All', 'cool-formkit'),
                    'selected' => esc_html__('Selected', 'cool-formkit'),
                ],
            ]
        );

        $widget->add_control(
            'collect_submission_meta_data',
            [
                'label' => esc_html__('Collect Submission Meta Data', 'cool-formkit'),
                'type' => Controls_Manager::SELECT2,
                'options' => [
                    'remote_ip' => esc_html__('User IP', 'cool-formkit'),
                    'user_agent' => esc_html__('User Agent', 'cool-formkit')
                ],
                'render_type' => 'none',
                'multiple' => true,
                'label_block' => true,
                'default' => [
                    'remote_ip',
                    'user_agent',
                ],
            ]
        );
        $widget->end_controls_section();
    }

    /**
     * Export the action.
     * 
     * @param \Elementor\Element_Base $element
     */
    public function on_export($element) {}

    /**
     * Run the action.
     * 
     * @param \Cool_FormKit\Modules\Forms\Classes\Form_Record $record
     * @param \Cool_FormKit\Modules\Forms\Components\Ajax_Handler $ajax_handler
     */
    public function run($record, $ajax_handler)
    {
        require_once CFL_PLUGIN_PATH . 'includes/collect-submission/class-cfkef-save-submission.php';
        $save_submission = new CFKEF_Save_Submission();

        do_action('cfkef/form/submission', $record, $ajax_handler, $this);
    }
}

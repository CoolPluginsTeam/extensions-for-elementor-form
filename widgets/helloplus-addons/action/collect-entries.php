<?php
namespace Cool_FormKit\Widgets\HelloPlusAddons\Action;
use HelloPlus\Modules\Forms\Classes\Action_Base;
use Cool_FormKit\Collect_Entries\CFKEF_Save_Entries;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class HelloPlus_Collect_Entries extends Action_Base {
    public function get_name(): string {
        return 'cool_formkit_collect_entries';
    }

    public function get_label(): string {
        return esc_html__('Collect Entries','extensions-for-elementor-form');
    }

    public function register_settings_section($widget): void {
        $widget->start_controls_section(
            'section_collect_entries',
            [
                'label' => $this->get_label(),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                'condition' => [
                    'submit_actions' => $this->get_name(),
                ],
            ]
        );

        $widget->add_control(
            'collect_entries_field_message',
            [
                'type' => \Elementor\Controls_Manager::ALERT,
                'alert_type' => 'info',
                'content' => sprintf(
                    esc_html__('This action will collect the entries and store it in a variable. You can use this variable in the next action or in the same form.','extensions-for-elementor-form'),
                    sprintf('<a href="%s" target="_blank">%s</a>', get_admin_url() . 'admin.php?page=cool-formkit-for-elementor-forms', esc_html__('Learn More','extensions-for-elementor-form')),
                ),
            ]
        );

        $widget->add_control(
            'collect_entries_field',
            [
                'label' => esc_html__('Collect Entries Field','extensions-for-elementor-form'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'all' => esc_html__('All','extensions-for-elementor-form'),
                    'selected' => esc_html__('Selected','extensions-for-elementor-form'),
                ],
            ]
        );

        $widget->add_control(
            'collect_entries_meta_data',
            [
                'label' => esc_html__('Collect Entries Meta Data','extensions-for-elementor-form'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => [
                    'remote_ip' => esc_html__('User IP','extensions-for-elementor-form'),
                    'user_agent' => esc_html__('User Agent','extensions-for-elementor-form')
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

    public function on_export($element): array {
        unset(
            $element['settings']['collect_entries_field'],
            $element['settings']['collect_entries_meta_data']
        );
        return $element;
    }

    public function run($record, $ajax_handler): void {
       
        require_once CFL_PLUGIN_PATH . 'includes/collect-entries/class-cfkef-save-entries.php';
        $settings = $record->get_form_settings('save_form_data');
        if($settings == 'yes'){
            $save_entries = new CFKEF_Save_Entries();
            do_action('cfkef/form/entries', $record, $ajax_handler, $this,'ehp-form');
        }
    }
} 
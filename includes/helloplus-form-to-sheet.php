<?php

namespace Cool_FormKit\Widgets\HelloPlusAddons;
/**
 * Form to Google Sheet Action
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once CFL_PLUGIN_PATH . 'includes/formsdb-marketing-notice.php';

use HelloPlus\Modules\Forms\Classes\Action_Base;

class Sheet_HelloPlus_Action extends Action_Base
{
    /**
     * Unique action name (slug!)
     */
    public function get_name() : string {
        return 'Save Submissions in Google Sheet';
    }

    /**
     * Label shown in UI
     */
    public function get_label(): string {
        return esc_html__('Save Submissions in Google Sheet', 'extensions-for-elementor-form');
    }

    public function add_prefix( $id ) {
        return 'fdbgp_' . $id;
    }

    /**
     * Settings panel
     */
    public function register_settings_section($widget) {
        $widget->start_controls_section(
             $this->add_prefix('section_google_sheets'),
            [
                'label'     => esc_html__('Save Submissions in Google Sheet', 'extensions-for-elementor-form'),
                'condition' => [
                    'cool_formkit_submit_actions' => $this->get_name(),
                ],
            ]
        );

        $widget->add_control(
            $this->add_prefix('fdbgp_plugin_marketing'),
            [
                'name'      => 'fdbgp_plugin_marketing',
                'label'     => '',
                'type'      => \Elementor\Controls_Manager::RAW_HTML,
                'raw'       => cfl_formsdb_marketing_raw_html(),

            ]
        );

        $widget->end_controls_section();
    }

    /**
     * Export handler
     */
    public function on_export($element) {
    }

    /**
     * Run on submit
     */
    public function run($record, $ajax_handler) {
        
    }

    
}
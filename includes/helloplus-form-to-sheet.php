<?php

namespace Cool_FormKit\Widgets\HelloPlusAddons;
/**
 * Form to Google Sheet Action
 */

if (!defined('ABSPATH')) {
    exit;
}


use HelloPlus\Modules\Forms\Classes\Action_Base;

class Sheet_HelloPlus_Action extends Action_Base
{
    public function __construct() {
        
        // Only enqueue in Elementor editor
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            add_action('elementor/editor/after_enqueue_scripts', [$this, 'enqueue_editor_scripts']);
        }
    }

    public function enqueue_editor_scripts() {

        wp_register_script(
				'cfl-formdb-marketing-script',
				CFL_PLUGIN_URL . 'admin/assets/js/cfl-formdb-marketing.js',
				['jquery'],
				CFL_VERSION,
				true
			);

			wp_enqueue_script('cfl-formdb-marketing-script');

			wp_localize_script(
				'cfl-formdb-marketing-script',
				'cflFormDBMarketing',
				[
					'nonce'    => wp_create_nonce('cfl_install_nonce'),
					'plugin'   => 'form-db',
					'ajax_url' => admin_url('admin-ajax.php'),
					'formdb_type' => 'formdb_notice',
					'formdb_dismiss_nonce' => wp_create_nonce('cfl_dismiss_nonce_formdb_notice'),
				]
			);
    }

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
                'raw'       => '<div class="elementor-control-raw-html cool-form-wrp"><div class="elementor-control-notice elementor-control-notice-type-info">
											<div class="elementor-control-notice-icon"><img class="cfl-highlight-icon" src="' . esc_url(CFL_PLUGIN_URL . 'assets/images/cfl-highlight-icon.svg') . '" width="250" alt="Highlight Icon" /></div>
											<div class="elementor-control-notice-main">
												
												<div class="elementor-control-notice-main-content">Save Form Submissions to Google Sheets.</div>
												<div class="elementor-control-notice-main-actions">
												<button type="button" class="elementor-button e-btn e-info e-btn-1 cfl-install-plugin">Install FormsDB</button>
											</div></div>
											</div></div>',

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
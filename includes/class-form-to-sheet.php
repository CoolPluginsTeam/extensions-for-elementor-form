<?php

/**
 * Form to Google Sheet Action
 */

if (! defined('ABSPATH')) {
    exit;
}

// --------------------------------------------------
// Detect platform base class
// --------------------------------------------------

if (
    class_exists('ElementorPro\Modules\Forms\Classes\Action_Base') &&
    class_exists('HelloPlus\Modules\Forms\Classes\Action_Base')
) {

    // Both active → extend Elementor Pro (compatible)
    abstract class Form_to_Sheet_Action extends \ElementorPro\Modules\Forms\Classes\Action_Base
    {
        protected $platform = 'both';
        protected $hello_plus_active = true;
    }
} elseif (class_exists('ElementorPro\Modules\Forms\Classes\Action_Base')) {

    // Elementor Pro only
    abstract class Form_to_Sheet_Action extends \ElementorPro\Modules\Forms\Classes\Action_Base
    {
        protected $platform = 'elementor';
        protected $hello_plus_active = false;
    }
} elseif (class_exists('HelloPlus\Modules\Forms\Classes\Action_Base')) {

    // Hello Plus only
    abstract class Form_to_Sheet_Action extends \HelloPlus\Modules\Forms\Classes\Action_Base
    {
        protected $platform = 'hello_plus';
        protected $hello_plus_active = true;
    }
} else {
    // Neither plugin active
    return;
}

// --------------------------------------------------
// Shared helpers
// --------------------------------------------------

abstract class Form_To_Sheet_Helper extends Form_to_Sheet_Action
{

    protected function add_prefix($id)
    {
        return 'fdbgp_' . $id;
    }
}

// --------------------------------------------------
// Concrete Action Class (REAL ACTION)
// --------------------------------------------------

class Sheet_Action extends Form_To_Sheet_Helper
{
    /**
     * Unique action name (slug!)
     */
    public function get_name()
    {
        return 'Save Submissions in Google Sheet';
    }

    /**
     * Label shown in UI
     */
    public function get_label()
    {
        return esc_html__('Save Submissions in Google Sheet', 'extensions-for-elementor-form');
    }

    /**
     * Settings panel
     */
    public function register_settings_section($widget)
    {

        $widget->start_controls_section(
            $this->add_prefix('section_google_sheets'),
            [
                'label' => esc_html__('Save Submissions in Google Sheet', 'extensions-for-elementor-form'),
                'tab'   => 'connect_google_sheets_tab',
                'condition' => [
                    'submit_actions' => $this->get_name(),
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
												<button type="button" class="elementor-button e-btn e-info e-btn-1 cfl-install-plugin" data-plugin="form-db" data-nonce="' . esc_attr(wp_create_nonce('cfl_install_nonce')) . '">Install FormsDB</button>
											</div></div>
											</div></div>',

            ]
        );

        $widget->end_controls_section();
    }

    /**
     * Export handler
     */
    public function on_export($element)
    {
        return $element;
    }

    /**
     * Run on submit
     */
    public function run($record, $ajax_handler)
    {
    }
}

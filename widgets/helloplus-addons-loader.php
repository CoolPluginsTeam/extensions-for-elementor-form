<?php

namespace Cool_FormKit\Widgets;

use Cool_FormKit\Widgets\HelloPlusAddons\Custom_Ehp_Form;
use Cool_FormKit\Widgets\HelloPlusAddons\Action\HelloPlus_Collect_Entries;
use Cool_FormKit\Widgets\HelloPlusAddons\HelloPlus_Whatsapp_Redirect;

use Cool_FormKit\Widgets\HelloPlusAddons\Action\Save_Form_Data;

use Cool_FormKit\Widgets\HelloPlusAddons\HelloPlus_Create_Conditional_Fields;
use Cool_FormKit\Widgets\HelloPlusAddons\HelloPlus_COUNTRY_CODE_FIELD;
use Cool_FormKit\Widgets\HelloPlusAddons\HelloPlus_FME_Plugin;


if (!defined('ABSPATH')) {
    die;
}

if(!class_exists('HelloPlus_Addons_Loader')) { 
class HelloPlus_Addons_Loader {

    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * The loader instance.
     *
     * @since    1.0.0
     * @access   private
     * @var      HelloPlus_Addons_Loader    $instance    The loader instance.
     */
    private static $instance = null;

    private function __construct() {
        $this->version = CFL_VERSION;

        if(is_plugin_active( 'hello-plus/hello-plus.php' )){
            add_action('plugins_loaded',function(){
                $this->load_addons();
    
                $this->load_entries();

                $this->register_action();
            });
            add_action('elementor/element/ehp-form/section_integration/after_section_end',array($this,'show_actions_on_editor_side') , 10, 2 );
        }
    }

    /**
     * Check if a field is enabled in the settings.
     *
     * @param string $field_key The field key.
     * @return bool True if the field is enabled, false otherwise.
     */
    private function is_field_enabled($field_key) {
        $enabled_elements = get_option('cfkef_enabled_elements', array());
        return in_array(sanitize_key($field_key), array_map('sanitize_key', $enabled_elements));
    }
    
    
    public function load_addons(){
        if($this->is_field_enabled('conditional_logic')){
            require_once CFL_PLUGIN_PATH . 'widgets/helloplus-addons/helloplus-conditional-fields.php';
            new HelloPlus_Create_Conditional_Fields();
        }

        if($this->is_field_enabled('country_code')){
            require_once CFL_PLUGIN_PATH . 'widgets/helloplus-addons/helloplus-country-code-addon.php';
            new HelloPlus_COUNTRY_CODE_FIELD();
        }
        if($this->is_field_enabled('form_input_mask')){
            require_once CFL_PLUGIN_PATH . 'widgets/helloplus-addons/helloplus-fme-plugin.php';
            HelloPlus_FME_Plugin::instance();
        }
    }   

    public function load_entries(){
        require_once CFL_PLUGIN_PATH . 'widgets/helloplus-addons/action/collect-entries.php';
        require_once CFL_PLUGIN_PATH . 'widgets/helloplus-addons/action/save-form-data.php';

        new Save_Form_Data();
        
        if (class_exists('HelloPlus\Modules\Forms\Module')) {
            $forms_module = \HelloPlus\Modules\Forms\Module::instance();

            if ($forms_module && isset($forms_module->actions_registrar)) {
                $forms_module->actions_registrar->register(new HelloPlus_Collect_Entries());
            }
        }
    }   

    public function register_action(){
        if($this->is_field_enabled('whatsapp_redirect')){
            if (class_exists('HelloPlus\Modules\Forms\Module')){
                $forms_module = \HelloPlus\Modules\Forms\Module::instance();
                if ($forms_module && isset($forms_module->actions_registrar)){

                    require_once CFL_PLUGIN_PATH . 'widgets/helloplus-addons/helloplus-whatsapp-redirect.php';
                    $forms_module->actions_registrar->register(new HelloPlus_Whatsapp_Redirect);
                }
            }
        }
    }

    public function show_actions_on_editor_side( $element, $args ) {
        require_once CFL_PLUGIN_PATH . 'widgets/helloplus-addons/helloplus-whatsapp-redirect.php';
        
        $custom_actions   = [];
        $action_instances = [];

        $instance = new HelloPlus_Whatsapp_Redirect();
        $custom_actions[ $instance->get_name() ] = $instance->get_label();
        $action_instances[] = $instance;

        // === 3. Add Dropdown in Editor
        $element->start_controls_section(
            'cool_formkit_conditional_actions_section',
            [
                'label' => esc_html__( 'Cool Actions After Submit', 'cool-formkit' ),
            ]
        );

        $element->add_control( 'cool_formkit_submit_actions', [
            'label'       => __( 'Actions After Submit', 'cool-formkit' ),
            'type'        => \Elementor\Controls_Manager::SELECT2,
            'multiple'    => true,
            'label_block' => true,
            'options'     => $custom_actions,
            'default'     => [ ],
            'render_type' => 'template',
        ] );

        $element->end_controls_section();

        // === 4. Register All Controls with Condition
        foreach ( $action_instances as $instance ) {
            if ( method_exists( $instance, 'register_settings_section' ) ) {
                // Inside each register_settings_section(), use:
                // 'condition' => [ 'cool_formkit_submit_actions' => $this->get_name() ]
                $instance->register_settings_section( $element );
            }
        }
    }
    /**
     * Get the instance of this class.
     *
     * @since    1.0.0
     * @return   CFKEF_Loader    The instance of this class.
     */
    public static function get_instance() {
        if (null == self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since    1.0.0
     * @return   string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}
}
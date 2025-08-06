<?php

namespace Cool_FormKit\Widgets;
use Cool_FormKit\Widgets\Addons\CoolForm_COUNTRY_CODE_FIELD;
use Cool_FormKit\Widgets\Addons\CoolForm_Create_Conditional_Fields;
use Cool_FormKit\Widgets\Addons\CoolForm_FME_Plugin;

use Cool_FormKit\Widgets\Addons\CoolForm_Whatsapp_Redirect;

if (!defined('ABSPATH')) {
    die;
}

if(!class_exists('CoolForm_Addons_Loader')) { 
class CoolForm_Addons_Loader {

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
     * @var      CFL_Addons_Loader    $instance    The loader instance.
     */
    private static $instance = null;

    private function __construct() {
        $this->version = CFL_VERSION;

        $this->load_addons();

        add_action( 'cool_form/forms/actions/register', array($this,'register_new_form_actions') );
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

    public function register_new_form_actions($actions_registrar){
        if($this->is_field_enabled('whatsapp_redirect')){
            require_once CFL_PLUGIN_PATH . 'widgets/addons/coolform-whatsapp-redirect.php';
            $actions_registrar->register(new CoolForm_Whatsapp_Redirect);
        }
    }

    public function load_addons(){
        if($this->is_field_enabled('country_code')){
            require_once CFL_PLUGIN_PATH .'widgets/addons/coolform-country-code-addon.php';
            CoolForm_COUNTRY_CODE_FIELD::get_instance();
        }
        if($this->is_field_enabled('conditional_logic')){
            require_once CFL_PLUGIN_PATH . 'widgets/addons/coolform-create-conditional-fields.php';
            new CoolForm_Create_Conditional_Fields();
        }
        if($this->is_field_enabled('form_input_mask')){
            require_once CFL_PLUGIN_PATH . 'widgets/addons/coolform-fme-plugin.php';
            CoolForm_FME_Plugin::instance();
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
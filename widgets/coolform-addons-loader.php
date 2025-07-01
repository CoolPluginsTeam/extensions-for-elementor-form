<?php

namespace Cool_FormKit\Widgets;
use Cool_FormKit\Widgets\Addons\CoolForm_COUNTRY_CODE_FIELD;
use Cool_FormKit\Widgets\Addons\CoolForm_Create_Conditional_Fields;

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
    }

    public function load_addons(){
        require_once CFL_PLUGIN_PATH .'widgets/addons/coolform-country-code-addon.php';
        CoolForm_COUNTRY_CODE_FIELD::get_instance();
        require_once CFL_PLUGIN_PATH . 'widgets/addons/coolform-create-conditional-fields.php';
        new CoolForm_Create_Conditional_Fields();
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
<?php

namespace Cool_FormKit;
if(!defined('ABSPATH')) {
    die;
}

if(!class_exists('Cfl_Addon_Loader')) {
    class Cfl_Addon_Loader {
       
        protected $plugin_name;
        protected $version;
        private static $instance = null;



        public function __construct() {
            $this->plugin_name = 'cool-formkit-lite';
            $this->version = '1.0.0';

            $this->load_dependencies();
        }


        public static function get_instance() {
            if (null == self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }
    
        /**
         * Load the required dependencies for this plugin.
         *
         * Include the following files that make up the plugin:
         *
         * - CFKEF_i18n. Defines internationalization functionality.
         * - CFKEF_Admin. Defines all hooks for the admin area.
         * - CFKEF_Public. Defines all hooks for the public side of the site.
         *
         * @since    1.0.0
         * @access   private
         */
        private function load_dependencies() {
            require_once CFL_PLUGIN_PATH . 'widgets/create-conditional-fields.php';
            new Cfl_Create_Conditional_Fields();
          
           
        }

          /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since    1.0.0
     * @return   string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
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


<?php
namespace Cool_FormKit\Includes\Frontend;

use Cool_FormKit\Includes\Actions\Whatsapp_Redirect;
use Cool_FormKit\Includes\Actions\Register_Post;

use Cool_FormKit\Includes\Frontend\Widget\Custom_Success_Message;
use Cool_FormKit\Includes\Frontend\Widget\CFL_Create_Conditional_Fields;
use Cool_FormKit\Includes\Frontend\Widget\CFL_COUNTRY_CODE_FIELD;
use Cool_FormKit\Includes\Frontend\Widget\FME_Plugin;

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://coolplugins.net/cool-formkit-for-elementor-forms/
 * @since      1.0.0
 *
 * @package    Cool_FormKit
 * @subpackage Cool_FormKit/frontend
 */

if (!defined('ABSPATH')) {
    die;
}

/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 * @package    Cool_FormKit
 * @subpackage Cool_FormKit/frontend
 */
if(!class_exists('CFKEF_Frontend')) { 
class CFKEF_Frontend {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    string    $plugin_name       The name of this plugin.
     * @param    string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

         // Register custom form fields
        add_action('elementor_pro/forms/fields/register', array($this, 'register_form_fields'));

        add_action( 'elementor_pro/forms/actions/register', array($this,'cfef_register_new_form_actions') );
        $this->include_addons();

    }

    public function include_addons(){
        include_once CFL_PLUGIN_PATH . 'includes/frontend/widget/class-custom-success-message.php';
        $custom_success_message = new Custom_Success_Message();
        $custom_success_message->set_hooks();

        if($this->is_field_enabled('conditional_logic')){
            require_once CFL_PLUGIN_PATH . 'includes/frontend/widget/create-conditional-fields.php';
            new CFL_Create_Conditional_Fields();
        }
        if($this->is_field_enabled('country_code')){
            require_once CFL_PLUGIN_PATH . 'includes/frontend/widget/country-code-addon.php';
            new CFL_COUNTRY_CODE_FIELD();
        }
        if($this->is_field_enabled('form_input_mask')){
            require_once CFL_PLUGIN_PATH . 'includes/frontend/widget/class-fme-plugin.php';
            FME_Plugin::instance();   
        }
    }
     /**
     * Check if a field is enabled in the settings.
     *
     * @since 1.0.0
     * @param string $field_key The field key.
     * @return bool True if the field is enabled, false otherwise.
     */
    private function is_field_enabled($field_key) {
        $enabled_elements = get_option('cfkef_enabled_elements', array());
        return in_array(sanitize_key($field_key), array_map('sanitize_key', $enabled_elements));

    }


    public function cfef_register_new_form_actions($form_actions_registrar){
        if($this->is_field_enabled('whatsapp_redirect')){
            require_once CFL_PLUGIN_PATH . 'includes/actions/class-whatsapp-redirect.php';
            $form_actions_registrar->register(new Whatsapp_Redirect);
        }
        require_once CFL_PLUGIN_PATH . 'includes/actions/class-register-post.php';
        $form_actions_registrar->register(new Register_Post);
    }


    /**
     * Register custom form fields.
     *
     * @since 1.0.0
     * @param \ElementorPro\Modules\Forms\Registrars\Form_Fields_Registrar $form_fields_registrar The form fields registrar.
     * @return void
     */
    public function register_form_fields($form_fields_registrar) {
    }
}
}

<?php

namespace Cool_FormKit\Admin\Recaptcha;

use Cool_FormKit\Admin\Register_Menu_Dashboard\CFKEF_Dashboard;



class Recaptcha_settings{

    private static $instance = null;

    public static $post_type = 'recaptcha-settings';

    /**
     * Get instance
     * 
     * @return CFKEF_Entries_Posts
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }       

    /**
     * Constructor
     */

     public function output_entries_list(CFKEF_Dashboard $dashboard) {


        if($dashboard->current_screen('cool-formkit')){

            ?>

                <div class="cfkef-settings-box">
                    <h3><?php esc_html_e('Recaptcha v2', 'cool-formkit'); ?></h3>

                    <table class="form-table cool-formkit-table">
                            
                            <tr>
                                <th scope="row" class="cool-formkit-table-th">
                                    <label for="site_key_v2" class="cool-formkit-label"><?php esc_html_e('Site Key', 'cool-formkit'); ?></label>
                                </th>
                                <td class="cool-formkit-table-td">
                                    <input type="text" id="site_key_v2" class="regular-text cool-formkit-input" />
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="cool-formkit-table-th">
                                    <label for="secret_key_v2" class="cool-formkit-label"><?php esc_html_e('Secret Key', 'cool-formkit'); ?></label>
                                </th>
                                <td class="cool-formkit-table-td">
                                    <input type="text" id="secret_key_v2" class="regular-text cool-formkit-input" />
                                </td>
                            </tr>
                            
                    </table>

                    <h3 class="cool-formkit-description"><?php esc_html_e('Recaptcha v3', 'cool-formkit'); ?></h3>

                    <table class="form-table cool-formkit-table">
                            
                    <tr>
                                <th scope="row" class="cool-formkit-table-th">
                                    <label for="site_key_v3" class="cool-formkit-label"><?php esc_html_e('Site Key', 'cool-formkit'); ?></label>
                                </th>
                                <td class="cool-formkit-table-td">
                                    <input type="text" id="site_key_v3" class="regular-text cool-formkit-input" />
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="cool-formkit-table-th">
                                    <label for="secret_key_v3" class="cool-formkit-label"><?php esc_html_e('Secret Key', 'cool-formkit'); ?></label>
                                </th>
                                <td class="cool-formkit-table-td">
                                    <input type="text" id="secret_key_v3" class="regular-text cool-formkit-input" />
                                </td>
                            </tr>
                            
                    </table>

                    <div>
                            <?php submit_button(); ?>
                    </div>
                </div>


            <?php
        }
    }

    public function enqueue_admin_scripts(){
        wp_enqueue_style('cfkef-recaptcha-setting', CFL_PLUGIN_URL . 'admin/assets/css/cfkef-recaptcha-settings.css', [], CFL_VERSION);

        wp_register_script('cfkef-admin-script', CFL_PLUGIN_URL . 'assets/js/admin-script.js', array('jquery'), $this->version, true);

        wp_localize_script('cfkef-admin-script', 'ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('my_ajax_nonce')
        ));

        wp_enqueue_script('cfkef-admin-script', true);
    }


    public function add_dashboard_tab($tabs) {
        $tabs[] = array(
            'title' => 'Recaptcha_settings',
            'position' => 2,
            'slug' => 'cool-formkit&tab=recaptcha-settings',
        );

        return $tabs;
    }
    
    public function my_ajax_function() {

        // Security check
        check_ajax_referer('my_ajax_nonce', 'nonce');

        $site_key_v2  = isset($_POST['site_key_v2']) ? sanitize_text_field($_POST['site_key_v2']) : '';
        $secret_key_v2 = isset($_POST['secret_key_v2']) ? sanitize_text_field($_POST['secret_key_v2']) : '';

        $site_key_v3  = isset($_POST['site_key_v3']) ? sanitize_text_field($_POST['site_key_v3']) : '';
        $secret_key_v3 = isset($_POST['secret_key_v3']) ? sanitize_text_field($_POST['secret_key_v3']) : '';

        update_option( "site_key_v2",  $site_key_v2);

        update_option( "secret_key_v2",  $secret_key_v2);


        update_option( "site_key_v3",  $site_key_v3);

        update_option( "secret_key_v3",  $secret_key_v3);
        // // Process request
        $response = array('message' => 'Data Updated!!!');
    
        // Send response
        wp_send_json_success($response);
    }


    public function __construct() {


        add_action('admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ]);
        add_action('cfkef_render_menu_pages', [ $this, 'output_entries_list' ]);
        add_action('wp_ajax_my_ajax_action', [ $this, 'my_ajax_function' ]);
        // add_filter('cfkef_dashboard_tabs', [ $this, 'add_dashboard_tab' ]);
       
    }

}
?>
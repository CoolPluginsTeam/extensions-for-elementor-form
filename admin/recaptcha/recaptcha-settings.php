<?php

namespace Cool_FormKit\Admin\Recaptcha;

use Cool_FormKit\Admin\Register_Menu_Dashboard\CFKEF_Dashboard;



class Recaptcha_settings{

    private static $instance = null;


    /**
     * Get instance
     * 
     * @return Recaptcha_settings
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

     public static function replace_with_star($text){
            $first_three = substr($text, 0, 3); // Get first 3 characters
            $last_three = substr($text, -3);   // Get last 3 characters
            $masked_length = strlen($text) - 6; // Length of the masked portion
            
            if ($masked_length > 0) {
                $masked_part = str_repeat('*', $masked_length); // Replace with '*'
                return $first_three . $masked_part . $last_three;
            }
            
            return $text; // Return as is if it's too short
     }

     public function output_entries_list(CFKEF_Dashboard $dashboard) {


        if($dashboard->current_screen('cool-formkit', 'recaptcha-settings')){
            ?>

                <div class="cfkef-settings-box">
                    <h3><?php esc_html_e('Recaptcha v2', 'cool-formkit'); ?></h3>

                    <table class="form-table cool-formkit-table">
                            
                            <tr>
                                <th scope="row" class="cool-formkit-table-th">
                                    <label for="site_key_v2" class="cool-formkit-label"><?php esc_html_e('Site Key', 'cool-formkit'); ?></label>
                                </th>
                                <td class="cool-formkit-table-td">
                                    <input type="text" id="site_key_v2" class="regular-text cool-formkit-input" value="<?php echo static::replace_with_star( get_option('cfl_site_key_v2') ); ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="cool-formkit-table-th">
                                    <label for="secret_key_v2" class="cool-formkit-label"><?php esc_html_e('Secret Key', 'cool-formkit'); ?></label>
                                </th>
                                <td class="cool-formkit-table-td">
                                    <input type="text" id="secret_key_v2" class="regular-text cool-formkit-input" value="<?php echo static::replace_with_star( get_option('cfl_secret_key_v2') ); ?>"/>
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
                                    <input type="text" id="site_key_v3" class="regular-text cool-formkit-input" value="<?php echo static::replace_with_star( get_option('cfl_site_key_v3') ); ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="cool-formkit-table-th">
                                    <label for="secret_key_v3" class="cool-formkit-label"><?php esc_html_e('Secret Key', 'cool-formkit'); ?></label>
                                </th>
                                <td class="cool-formkit-table-td">
                                    <input type="text" id="secret_key_v3" class="regular-text cool-formkit-input" value="<?php echo static::replace_with_star( get_option('cfl_secret_key_v3') ); ?>"/>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row" class="cool-formkit-table-th">
                                    <label for="thresshold_v3" class="cool-formkit-label"><?php esc_html_e('Thresshold', 'cool-formkit'); ?></label>
                                </th>
                                <td class="cool-formkit-table-td">
                                    <input type="number" id="thresshold_v3" class="regular-text cool-formkit-input" value="<?php echo get_option('cfl_thresshold_v3')?>" step="0.1"/>
                                    <p class="description cool-formkit-description"><?php esc_html_e('Score threshold should be a value between 0 and 1, default: 0.5', 'cool-formkit'); ?></p>
                                </td>
                            </tr>
                            
                    </table>

                    <div>
                        <button id="recaptcha-submit" type="button">Save Changes</button>
                    </div>
                </div>


            <?php
        }
    }

    public function enqueue_admin_scripts(){


        if (isset($_GET['page']) && $_GET['page'] === 'cool-formkit') {

    
            wp_register_script('cfkef-admin-script', CFL_PLUGIN_URL . 'assets/js/admin-script.js', array('jquery'), $this->version, true);
    
            wp_localize_script('cfkef-admin-script', 'ajax_object', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('cfl_recaptcha_ajax_nonce')
            ));
    
            wp_enqueue_script('cfkef-admin-script', true);
        }

    }


    public function add_dashboard_tab($tabs) {
        $tabs[] = array(
            'title' => 'Settings',
            'position' => 1,
            'slug' => 'cool-formkit&tab=recaptcha-settings',
        );

        return $tabs;
    }
    
    public function recaptcha_ajax_function() {

        // Security check
        check_ajax_referer('cfl_recaptcha_ajax_nonce', 'nonce');

        $site_key_v2  = isset($_POST['site_key_v2']) ? sanitize_text_field($_POST['site_key_v2']) : '';
        $secret_key_v2 = isset($_POST['secret_key_v2']) ? sanitize_text_field($_POST['secret_key_v2']) : '';

        $site_key_v3  = isset($_POST['site_key_v3']) ? sanitize_text_field($_POST['site_key_v3']) : '';
        $secret_key_v3 = isset($_POST['secret_key_v3']) ? sanitize_text_field($_POST['secret_key_v3']) : '';

        $thresshold_v3 = isset($_POST['thresshold_v3']) ? $_POST['thresshold_v3'] : '';

        if($thresshold_v3 > 1){
            $thresshold_v3 = 1;
        }else if($thresshold_v3 < 0){
            $thresshold_v3 = 0;
        }


        update_option( "cfl_site_key_v2",  $site_key_v2);

        update_option( "cfl_secret_key_v2",  $secret_key_v2);


        update_option( "cfl_site_key_v3",  $site_key_v3);

        update_option( "cfl_secret_key_v3",  $secret_key_v3);

        update_option( "cfl_thresshold_v3",  $thresshold_v3);

        // Process request
        $response = array('message' => 'Data Updated!!!');
    
        // Send response
        wp_send_json_success($response);
    }


    public function __construct() {

        add_action('admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ]);
        add_action('cfkef_render_menu_pages', [ $this, 'output_entries_list' ]);
        add_action('wp_ajax_cfl_recaptcha_ajax_action', [ $this, 'recaptcha_ajax_function' ]);
        add_filter('cfkef_dashboard_tabs', [ $this, 'add_dashboard_tab' ]);
       
    }

}
?>
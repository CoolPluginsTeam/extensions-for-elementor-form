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

     public function recaptcha_setting_html_output(CFKEF_Dashboard $dashboard) {


        if($dashboard->current_screen('cool-formkit', 'recaptcha-settings')){


            $this->handle_form_submit();

            ?>

                <div class="cfkef-settings-box">
                    <h3><?php esc_html_e('reCAPTCHA', 'cool-formkit'); ?></h3>


    <form method="post" action="" class="cool-formkit-form">


                    <table class="form-table cool-formkit-table">
                            
                            <tr>
                                <th scope="row" class="cool-formkit-table-th">
                                    <label for="site_key_v2" class="cool-formkit-label"><?php esc_html_e('Site Key', 'cool-formkit'); ?></label>
                                </th>
                                <td class="cool-formkit-table-td">
                                    <input type="text" id="site_key_v2" name="site_key_v2" class="regular-text cool-formkit-input" value="<?php echo get_option('cfl_site_key_v2'); ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="cool-formkit-table-th">
                                    <label for="secret_key_v2" class="cool-formkit-label"><?php esc_html_e('Secret Key', 'cool-formkit'); ?></label>
                                </th>
                                <td class="cool-formkit-table-td">
                                    <input type="text" id="secret_key_v2" name="secret_key_v2" class="regular-text cool-formkit-input" value="<?php echo get_option('cfl_secret_key_v2'); ?>"/>
                                </td>
                            </tr>
                            
                    </table>

                    <h3 class="cool-formkit-description"><?php esc_html_e('reCAPTCHA V3', 'cool-formkit'); ?></h3>

                    <table class="form-table cool-formkit-table">
                            
                    <tr>
                                <th scope="row" class="cool-formkit-table-th">
                                    <label for="site_key_v3" class="cool-formkit-label"><?php esc_html_e('Site Key', 'cool-formkit'); ?></label>
                                </th>
                                <td class="cool-formkit-table-td">
                                    <input type="text" id="site_key_v3" name="site_key_v3" class="regular-text cool-formkit-input" value="<?php echo get_option('cfl_site_key_v3'); ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="cool-formkit-table-th">
                                    <label for="secret_key_v3" class="cool-formkit-label"><?php esc_html_e('Secret Key', 'cool-formkit'); ?></label>
                                </th>
                                <td class="cool-formkit-table-td">
                                    <input type="text" id="secret_key_v3" name="secret_key_v3" class="regular-text cool-formkit-input" value="<?php echo get_option('cfl_secret_key_v3'); ?>"/>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row" class="cool-formkit-table-th">
                                    <label for="threshold_v3" class="cool-formkit-label"><?php esc_html_e('Score Threshold', 'cool-formkit'); ?></label>
                                </th>
                                <td class="cool-formkit-table-td">
                                    <input type="number" id="threshold_v3" name="threshold_v3" class="regular-text cool-formkit-input" value="<?php echo get_option('cfl_threshold_v3')?>" min="0" max="1"  step="0.1"/>
                                    <p class="description cool-formkit-description"><?php esc_html_e('Score threshold should be a value between 0 and 1, default: 0.5', 'cool-formkit'); ?></p>
                                </td>
                            </tr>
                            
                    </table>

                    <div>
                        <button id="recaptcha-submit" type="submit" name="save">Save Changes</button>
                    </div>

                    </form>

                </div>


            <?php
        }
    }


    public function add_dashboard_tab($tabs) {
        $tabs[] = array(
            'title' => 'Settings',
            'position' => 2,
            'slug' => 'cool-formkit&tab=recaptcha-settings',
        );

        return $tabs;
    }


    public function handle_form_submit() {

        // Security check

        if(isset($_POST['save'])){


        $pattern = "/(<script|<\/script>|onerror=|onload=|eval\(|javascript:|SELECT |INSERT |DELETE |DROP |UPDATE |UNION )/i";

        if(isset($_POST['site_key_v2'])){

            if (preg_match($pattern, $_POST['site_key_v2'])) {
    
                echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('Invalid Input.', 'cool-formkit') . '</p></div>';
                return;
            }
        }

        if(isset($_POST['secret_key_v2'])){

            if (preg_match($pattern, $_POST['secret_key_v2'])) {
    
                echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('Invalid Input.', 'cool-formkit') . '</p></div>';


                return;
            }
        }

        if(isset($_POST['site_key_v3'])){

            if (preg_match($pattern, $_POST['site_key_v3'])) {
    
                echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('Invalid Input.', 'cool-formkit') . '</p></div>';

                return;
            }
        }

        if(isset($_POST['secret_key_v3'])){

            if (preg_match($pattern, $_POST['secret_key_v3'])) {
    
                echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('Invalid Input.', 'cool-formkit') . '</p></div>';

                return;
            }
        }

        if(isset($_POST['threshold_v3'])){

            if($_POST['threshold_v3'] == ""){
                $_POST['threshold_v3'] = 0.5;
            }

            if (preg_match($pattern, $_POST['threshold_v3'])) {
                echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('Invalid Input.', 'cool-formkit') . '</p></div>';

                return;
            }


            if(!preg_match('/^\d*\.?\d+$/', $_POST['threshold_v3'])){
                echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('Invalid Input.', 'cool-formkit') . '</p></div>';

                return;
            }
        }




        $site_key_v2  = isset($_POST['site_key_v2']) ? sanitize_text_field($_POST['site_key_v2']) : '';
        $secret_key_v2 = isset($_POST['secret_key_v2']) ? sanitize_text_field($_POST['secret_key_v2']) : '';

        $site_key_v3  = isset($_POST['site_key_v3']) ? sanitize_text_field($_POST['site_key_v3']) : '';
        $secret_key_v3 = isset($_POST['secret_key_v3']) ? sanitize_text_field($_POST['secret_key_v3']) : '';

        $threshold_v3 = isset($_POST['threshold_v3']) ?  sanitize_text_field($_POST['threshold_v3']) : '';


        if($threshold_v3 > 1){
            $threshold_v3 = 1;
        }else if($threshold_v3 < 0){
            $threshold_v3 = 0;
        }
        


        update_option( "cfl_site_key_v2",  $site_key_v2);

        update_option( "cfl_secret_key_v2",  $secret_key_v2);


        update_option( "cfl_site_key_v3",  $site_key_v3);

        update_option( "cfl_secret_key_v3",  $secret_key_v3);

        update_option( "cfl_threshold_v3",  $threshold_v3);

    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Settings saved.', 'cool-formkit') . '</p></div>';

    }

    }


    public function __construct() {

        add_action('cfkef_render_menu_pages', [ $this, 'recaptcha_setting_html_output' ]);
        add_filter('cfkef_dashboard_tabs', [ $this, 'add_dashboard_tab' ]);
       
    }

}
?>
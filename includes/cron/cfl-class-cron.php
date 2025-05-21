<?php

namespace Cool_FormKit\Includes\Cron;
use Cool_FormKit\feedback\cfl_feedback;

if (!defined('ABSPATH')) {
    exit();
}
if (!class_exists('CFL_cronjob')) {
    class CFL_cronjob
    {
      

        public function __construct()
        {
            
            add_filter('cron_schedules', array($this, 'cfl_cron_schedules'));
            add_action('cfl_extra_data_update', array($this, 'cfl_cron_extra_data_autoupdater'));

        }

        public function cfl_cron_schedules($schedules)
        {
           
            if (!isset($schedules['every_30_days'])) {

                $schedules['every_30_days'] = array(
                    'interval' => 30 * 24 * 60 * 60, // 2,592,000 seconds
                    'display'  => __('Once every 30 days'),
                );
            }
            return $schedules;
        }

        
        function cfl_cron_extra_data_autoupdater(){
            
            $settings       = get_option('cfl_usage_share_data');
           
            if (!empty($settings) || $settings === 'on'){

                CFL_cronjob::cfl_send_data();
            }

        }

        static public function cfl_send_data() {
 
                 $feedback_url = 'http://feedback.coolplugins.net/wp-json/coolplugins-feedback/v1/site';
                 require_once CFL_PLUGIN_PATH . 'admin/feedback/admin-feedback-form.php';

                 if (!defined('CFL_PLUGIN_PATH') || !class_exists('Cool_FormKit\\feedback\\cfl_feedback')) {
                        return;
                 }
                    
                 $extra_data = new \Cool_FormKit\feedback\cfl_feedback();                 
                 $extra_data_details = $extra_data->cpfm_get_user_info();

                  $server_info        = $extra_data_details['server_info'];
                  $extra_details      = $extra_data_details['extra_details'];
                  $site_url           = get_site_url();
                  $install_date       = get_option('cfl-install-date');
                  $uni_id      		  = '4';
			      $site_id            = $site_url . '-' . $install_date . '-' .$uni_id;
                 
                  $initial_version = get_option('CFL_initial_save_version');
                  $initial_version = is_string($initial_version) ? sanitize_text_field($initial_version) : 'N/A';
                  $plugin_version = defined('CFL_VERSION') ? CFL_VERSION : 'N/A';
                  $admin_email = sanitize_email(get_option('admin_email') ?: 'N/A');
              
                  $post_data = array(
                      'site_id'           => md5($site_id),
                      'plugin_version'    => $plugin_version,
                      'plugin_name'       => "Cool FormKit Lite - Elementor Form Builder",
                      'plugin_initial'    => $initial_version,
                      'email'             => $admin_email,
                      'site_url'          => esc_url_raw($site_url),
                      'server_info'       => $server_info,
                      'extra_details'     => $extra_details,
                  );
              
                  $response = wp_remote_post($feedback_url, array(
                      'method'    => 'POST',
                      'timeout'   => 30,
                      'headers'   => array(
                          'Content-Type' => 'application/json',
                      ),
                      'body'      => wp_json_encode($post_data),
                  ));
              
                  if (is_wp_error($response)) {
                      error_log('CFL Feedback Send Failed: ' . $response->get_error_message());
                      return;
                  }
              
                  $response_body = wp_remote_retrieve_body($response);
                  $decoded = json_decode($response_body, true);
                
                  if (!wp_next_scheduled('cfl_extra_data_update')) {
                    wp_schedule_event(time(), 'every_30_days', 'cfl_extra_data_update');
                }
             
        }
          

    }

    $cron_init = new CFL_cronjob();
}
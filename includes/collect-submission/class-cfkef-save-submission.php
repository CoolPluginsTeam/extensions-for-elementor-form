<?php

namespace Cool_FormKit\Collect_Submission;

use Cool_FormKit\Includes\Utils;
use Elementor\Core\Utils\Collection;
use Cool_FormKit\Modules\Forms\Classes\Form_Record;
use Cool_FormKit\Modules\Forms\Components\Ajax_Handler;

class CFKEF_Save_Submission {

    private $last_entry_key = 'cfkef_last_entry_serial_no';
    private $entry_key = 'cfkef_entry_serial_no';
    private $id = 0;

    public function __construct() {
        add_action('cfkef/form/submission', [ $this, 'save_submission' ], 10, 3);
    }

    public function save_submission($record, $ajax_handler, $collect_submission) {
        $meta_keys = array_merge(['page_url', 'page_title'], $record->get_form_settings('collect_submission_meta_data'));
        $meta = $record->get_form_meta($meta_keys);
        $form_fields = $record->get_form_settings( 'form_fields' );;
        
        $actions_count = (new Collection($record->get_form_settings('submit_actions')))
        ->filter(function ($value) use ($collect_submission) {
            return $value !== $collect_submission->get_name();
        })
        ->count();
        
        $post_id = $record->get_form_settings('form_post_id');
        $element_id = $ajax_handler->get_current_form()['id'];
        $form_name = $record->get_form_settings('form_name');
        $form_fields = $record->get_field( null );

        $submission_number = $this->auto_increment_submission_number();
        
        $post_data = [
            'post_type' => 'cfkef-submission',
            'post_title' => esc_html($form_name) . ' #' . absint($submission_number),
            'post_status' => 'publish'
        ];

        $post_id = wp_insert_post($post_data);

        // Update the form entries in post meta
        update_post_meta($post_id, '_cfkef_form_entries', $actions_count);

        // Update the form post id in post meta
        update_post_meta($post_id, '_cfkef_form_post_id', $post_id);

        // Update the element id in post meta
        update_post_meta($post_id, '_cfkef_element_id', $element_id);

        // Update the form meta in post meta
        update_post_meta($post_id, '_cfkef_form_meta', $meta);

        // Update the form data in post meta
        update_post_meta($post_id, '_cfkef_form_data', $form_data);

        $form_data = [];
        foreach($form_fields as $key => $field) {
            if(!empty($field['value'])) {
                $form_data[$key] = ['value' => $field['value'], 'type' => $field['type']];
            }
        }

        var_dump($form_data);

        update_post_meta($post_id, '_cfkef_form_data', $form_data);
    }

    /**
     * Auto increment submission number
     * 
     * @return int
     */
    private function auto_increment_submission_number() {

        // If the last entry key is empty, then get all the post ids
        if (empty(get_option($this->last_entry_key))) {

            // Get all the post ids
            $all_post_ids = get_posts(array(
                'fields'          => 'ids',
                'posts_per_page'  => -1,
                'orderby' => 'ID',
                'order' => 'ASC',
                'post_type' => 'cfkef-submission'
            ));
        
            // If there are any post ids, then get the last post id and last entry serial no
            if(count($all_post_ids) > 0) {

                $last_post_id = $all_post_ids[count($all_post_ids) - 1];
                $last_entry_serial_no = get_post_meta($last_post_id, $this->entry_key, true);

                // If there is a last entry serial no, then update the last entry key with the last entry serial no
                if($last_entry_serial_no) {
                    update_option($this->last_entry_key, $last_entry_serial_no);
                }else{
                    // If there is no last entry serial no, then increment the last entry key with the last entry serial no
                    foreach ($all_post_ids as $key => $id) {

                        // Update the entry key with the entry serial no
                        update_post_meta($id, $this->entry_key, ++$key);
                        $this->id = $key;
                    }

                    // Update the last entry key with the last entry serial no
                    update_option($this->last_entry_key, $this->id);
                }

            }
        }

        // Get the last entry key
        $id=get_option($this->last_entry_key, 0);

        // Increment the last entry key
        return ++$id;
    }
}           
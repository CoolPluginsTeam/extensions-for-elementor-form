<?php

namespace Cool_FormKit\Collect_Entries;

use Cool_FormKit\Includes\Utils;
use Elementor\Core\Utils\Collection;
use Cool_FormKit\Modules\Forms\Classes\Form_Record;
use Cool_FormKit\Modules\Forms\Components\Ajax_Handler;

class CFKEF_Save_Entries {

    private $last_entry_key = 'cfkef_last_entry_serial_no';
    private $entry_key = 'cfkef_entry_serial_no';
    private $id = 0;

    public function __construct() {
        add_action('cfkef/form/entries', [ $this, 'save_entries' ], 10, 3);
    }

    public function save_entries($record, $ajax_handler, $collect_entries) {
        $meta_keys = array_merge(['page_url', 'page_title'], $record->get_form_settings('collect_entries_meta_data'));
        $meta = $record->get_form_meta($meta_keys);
        $form_fields = $record->get_form_settings( 'form_fields' );;
        
        $actions_count = (new Collection($record->get_form_settings('submit_actions')))
        ->filter(function ($value) use ($collect_entries) {
            return $value !== $collect_entries->get_name();
        })
        ->count();
        
        $post_id = $record->get_form_settings('form_post_id');
        $element_id = $ajax_handler->get_current_form()['id'];
        $form_name = $record->get_form_settings('form_name');
        $form_fields = $record->get_field( null );

        // echo '<pre>';
        // var_dump($form_fields);
        // echo '</pre>';
        // die();
        $meta['form_name'] = $form_name;

        $entries_number = $this->auto_increment_entries_number();
        
        $post_data = [
            'post_type' => 'cfkef-entries',
            'post_title' => esc_html($form_name) . ' #' . absint($entries_number),
            'post_status' => 'publish'
        ];

        $post_id = wp_insert_post($post_data);

        // Update the form action count in post meta
        update_post_meta($post_id, '_cfkef_form_action_count', $actions_count);

        // Update the form entry id in post meta
        update_post_meta($post_id, '_cfkef_form_entry_id', $entries_number);

        // Update the form name in post meta
        update_post_meta($post_id, '_cfkef_form_name', $form_name);

        // Update the element id in post meta
        update_post_meta($post_id, '_cfkef_element_id', $element_id);

        // Update the form meta in post meta
        update_post_meta($post_id, '_cfkef_form_meta', $meta);

        // Update last entry key option
        update_option($this->last_entry_key, $entries_number);
        
        // Update the entry view status in post meta
        update_post_meta($post_id, '_cfkef_entry_view_status', 'false');

        $form_data = [];

        foreach($form_fields as $key => $field) {
            if(!empty($field['value'])) {
                $form_data[$key] = ['value' => $field['value'], 'type' => $field['type'], 'title' => $field['title']];
            }
        }

        update_post_meta($post_id, '_cfkef_form_data', $form_data);
    }

    /**
     * Auto increment entries number
     * 
     * @return int
     */
    private function auto_increment_entries_number() {

        // If the last entry key is empty, then get all the post ids
        if (empty(get_option($this->last_entry_key))) {

            // Get all the post ids
            $all_post_ids = get_posts(array(
                'fields'          => 'ids',
                'posts_per_page'  => -1,
                'orderby' => 'ID',
                'order' => 'ASC',
                'post_type' => 'cfkef-entries'
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
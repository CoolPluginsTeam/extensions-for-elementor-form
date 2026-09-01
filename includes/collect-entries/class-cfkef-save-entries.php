<?php

namespace Cool_FormKit\Collect_Entries;

use Elementor\Core\Utils\Collection;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class CFKEF_Save_Entries {

    private $last_entry_key = 'cfkef_last_entry_serial_no';
    private $entry_key = 'cfkef_entry_serial_no';

    public function __construct() {
        add_action('cfkef/form/entries', [ $this, 'save_entries' ], 10, 3);
    }

    public function save_entries($record, $ajax_handler, $collect_entries) {
        $meta_keys = array_merge( array( 'page_url', 'page_title' ), (array) $record->get_form_settings( 'collect_entries_meta_data' ) );
        $meta = $record->get_form_meta($meta_keys);

        $actions_count = (new Collection($record->get_form_settings('submit_actions')))
        ->filter(function ($value) use ($collect_entries) {
            return $value !== $collect_entries->get_name();
        })
        ->count();
        
        $form_post_id = $record->get_form_settings('form_post_id');
        $element_id = $ajax_handler->get_current_form()['id'];
        $form_name = $record->get_form_settings('form_name');
        $form_fields = $record->get_field( null );

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

        // Update form post id in post meta
        update_post_meta($post_id, '_cfkef_form_post_id', $form_post_id);

        // Update last entry key option
        update_option($this->last_entry_key, $entries_number);
        
        // Update the entry view status in post meta
        update_post_meta($post_id, '_cfkef_entry_view_status', 'false');

        $form_data = [];
        $user_email='';

        foreach($form_fields as $key => $field) {
            if(!empty($field['value']) || !empty($field['raw_value'])) {

                if(empty($user_email) && $field['type'] == 'email') {
                    $user_email = $field['value'];
                }

                $title=empty($field['title']) ? $key : $field['title'];

                $form_data[$key] = ['value' => $field['value'], 'type' => $field['type'], 'title' => $title];
            }
        }

        update_post_meta($post_id, '_cfkef_form_data', $form_data);
        update_post_meta($post_id, '_cfkef_user_email', $user_email);
    }

    /**
     * Auto increment entries number
     * 
     * @return int
     */
    private function auto_increment_entries_number() {

        if ( '' === (string) get_option( $this->last_entry_key, '' ) ) {
            global $wpdb;

            // Prefer the highest existing serial meta without loading every post.
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            $max_serial = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT MAX(CAST(pm.meta_value AS UNSIGNED))
					FROM {$wpdb->postmeta} pm
					INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
					WHERE p.post_type = %s AND pm.meta_key = %s",
                    'cfkef-entries',
                    $this->entry_key
                )
            );

            if ( $max_serial ) {
                update_option( $this->last_entry_key, (int) $max_serial );
            } else {
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
                $count = (int) $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT COUNT(ID) FROM {$wpdb->posts} WHERE post_type = %s AND post_status != 'auto-draft'",
                        'cfkef-entries'
                    )
                );
                update_option( $this->last_entry_key, $count );
            }
        }

        $id = (int) get_option( $this->last_entry_key, 0 );

        return ++$id;
    }
}           
<?php

namespace Cool_FormKit\Admin\Entries;

use WP_List_Table;
use Cool_FormKit\Admin\Entries\CFKEF_Entries_Posts;
use Cool_FormKit\Admin\Entries\CFKEF_Post_Bulk_Actions;

// phpcs:disable PluginCheck.Security.DirectDB.UnescapedDBParameter	

if(!class_exists('CFKEF_List_Table')) { 
class CFKEF_List_Table extends WP_List_Table {

    private static $instance = null;

    private $post_type = 'cfkef-entries';

    private $cfkef_bulk_actions;

    private $bulk_actions;

    public static function get_instance($post_type) {
        if (null === self::$instance) {
            self::$instance = new self($post_type);
        }
        return self::$instance;
    }
    
    public function __construct($post_type) {
        $this->post_type = esc_html($post_type);

        parent::__construct([
            'singular' => 'entry',
            'plural' => 'entries',
            'ajax' => false
        ]);

        $this->cfkef_bulk_actions = new CFKEF_Post_Bulk_Actions();
        $this->cfkef_bulk_actions->init();
    }

    public function get_views() {
        $views = [
            'all' => 'All',
            'trash' => 'Trash',
        ];
        
        return $views;
    }

    public function views() {
        $views = $this->get_views();

        if ( empty( $views ) ) {
            return;
        }
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $current_view = isset($_GET['view']) ? sanitize_key($_GET['view']) : 'all';

        // Get counts for all and trash
        $post_counts = wp_count_posts($this->post_type);
        $all_count = $post_counts->publish + $post_counts->draft + $post_counts->pending;
        $trash_count = $post_counts->trash;
        $index = 0;

        echo "<ul class='subsubsub'>\n";    
        foreach ($views as $view => $label) {
            $class = ($view === $current_view) ? 'current' : '';
            $count = ($view === 'all') ? $all_count : ($view === 'trash' ? $trash_count : 0);
            
            if((($index < (count($views))) && $index !== 0) && count($views) > 0 && $count > 0) {
                echo " | ";
            }

            if ( $count > 0 || $view === 'all') {
                printf(
                    '<li class="%s"><a href="%s">%s</a></li>',
                    esc_attr( $class ),
                    esc_url( add_query_arg(
                        array(
                            'page' => 'cfkef-entries',
                            'view' => $view,
                        )
                    ) ),
                    esc_html( $label )
                );

                printf(
                    '<span class="count">(%s)</span>',
                    esc_html( $count )
                );

            }
            

            if($count > 0 || $view === 'all'){
                ++$index;
            }
        }
        echo "</ul>";
    }

    public function get_bulk_actions() {
        if($this->cfkef_bulk_actions){
            return $this->cfkef_bulk_actions->get_dropdown_items();
        }
    }

    public function get_columns() {
        return [
            'cb' => '<input type="checkbox" />',
            'user_email' => 'Email',
            'form_url' => 'Form',
            'page_title' => 'Page Title',
            'id' => 'ID',
            'submission_date' => 'Submission Date',
        ];
    }

    public function column_cb($item) {
        return sprintf('<input type="checkbox" name="entry_id[]" value="%s" />', esc_attr($item->ID));
    }

    public function column_user_email($item) {
        $email = get_post_meta($item->ID, '_cfkef_user_email', true);
        $edit_url = admin_url('post.php?post='.intval($item->ID).'&action=edit');

        if(!isset($email) || !$email || empty($email)){
            $email = 'N/A';
        }

        return sprintf('<a href="%s">%s</a>', esc_url($edit_url), esc_html($email));
    }

    public function column_form_url($item) {
        $meta_details = get_post_meta($item->ID, '_cfkef_form_meta', true);
        $form_name = isset($meta_details['form_name']) ? $meta_details['form_name'] : '';
        $form_post_id = get_post_meta($item->ID, '_cfkef_form_post_id', true);
        $page_editor_url = admin_url('post.php?post='.intval($form_post_id).'&action=elementor');

        $form_name = empty($form_name) ? 'N/A' : $form_name;

        if($form_post_id && !empty($form_post_id)){
            return sprintf('<a href="%s" target="_blank">%s</a>', esc_url($page_editor_url), esc_html($form_name));
        }

        return esc_html($form_name);
    }

    public function column_id($item) {
        $entry_id = get_post_meta($item->ID, '_cfkef_form_entry_id', true);
        return $entry_id;
    }

    public function column_submission_date($item) {
        return $item->post_date;
    }

    public function column_page_title($item) {
        $meta_details = get_post_meta($item->ID, '_cfkef_form_meta', true);
        $value= isset($meta_details['page_title']['value']) ? $meta_details['page_title']['value'] : '';
        $page_url= isset($meta_details['page_url']['value']) ? $meta_details['page_url']['value'] : '';

        if(!empty($value)){
            return sprintf('<a href="%s" target="_blank">%s</a>', esc_url($page_url), esc_html($value));
        }

        return esc_html($value);
    }
    
    public function column_actions($item) {
        return sprintf('<a href="%s" class="button button-primary">View</a>', esc_url($item->ID));
    }

    protected function handle_row_actions( $item, $column_name, $primary ) {
        if($column_name !== $primary) {
            return '';
        }

        $view = CFKEF_Entries_Posts::get_view();

        $actions           = array();
        if($view === 'all'){
            $edit_url = admin_url('post.php?post='.intval($item->ID).'&action=edit');
            $actions['View']   = sprintf('<a href="%s" class="row-title">View</a>', $edit_url);
            $actions['Trash'] = sprintf('<a href="?page=cfkef-entries&action=trash&entry_id=%s&_wpnonce=%s" class="row-title submitdelete">Trash</a>', $item->ID, wp_create_nonce('bulk-entries'));
        }
        if($view === 'trash'){
            $actions['Restore'] = sprintf('<a href="?page=cfkef-entries&action=restore&entry_id=%s&_wpnonce=%s" class="row-title">Restore</a>', $item->ID, wp_create_nonce('bulk-entries'));
            $actions['Delete'] = sprintf('<a href="?page=cfkef-entries&action=delete&entry_id=%s&_wpnonce=%s" class="row-title submitdelete">Delete</a>', $item->ID, wp_create_nonce('bulk-entries'));
        }

        return $this->row_actions($actions);
    }
    
    protected function row_actions( $actions, $always_visible = false ) {
       
        $action_count = count( $actions );
    
        if ( ! $action_count ) {
            return '';
        }
    
        $mode = get_user_setting( 'posts_list_mode', 'list' );
    
        if ( 'excerpt' === $mode ) {
            $always_visible = true;
        }
    
        $output = '<div class="' . ( $always_visible ? 'row-actions visible' : 'row-actions' ) . '">';
    
        $i = 0;
    
        foreach ( $actions as $action => $link ) {
            ++$i;
    
            $separator = ( $i < $action_count ) ? ' | ' : '';
    
            $output .= "<span class='".esc_attr(lcfirst($action))."'>{$link}{$separator}</span>";
        }
    
        $output .= '</div>';
    
        $output .= '<button type="button" class="toggle-row"><span class="screen-reader-text">' .
            /* translators: Hidden accessibility text. */
            __( 'Show more details', 'extensions-for-elementor-form' ) .
        '</span></button>';
    
        return $output;
    }

    private function get_request_search_query(){
		return filter_input( INPUT_GET, 'cfkef-entries-search', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
    }

    public function search_box($text, $input_id) {
        echo '<p class="search-box">';
        echo '<label class="screen-reader-text" for="' . esc_attr($input_id) . '">' . esc_html($text) . ':</label>';
        echo '<input type="search" id="' . esc_attr($input_id) . '" name="' . esc_attr($input_id) . '" value="' . esc_attr($this->get_request_search_query()) . '" />';
        echo '<input type="submit" class="button" value="' . esc_attr($text) . '" />';
        echo '</p>'; // Added closing tag for search box
    }

    public function prepare_items() {
        
		// Set up the columns.
		$columns = $this->get_columns();

        // Set column headers.
		$this->_column_headers = [ $columns ];

        // phpcs:disable WordPress.Security.NonceVerification.Recommended
        $page     = $this->get_pagenum();
		$order    = isset( $_GET['order'] ) && sanitize_text_field(wp_unslash($_GET['order'])) === 'asc' ? 'ASC' : 'DESC';
        $search= isset($_GET['cfkef-entries-search']) ? sanitize_text_field(wp_unslash($_GET['cfkef-entries-search'])) : '';
		$allowed_orderby = ['ID','post_title','post_date','post_modified','post_status'];
        $orderby = isset($_GET['orderby']) ? sanitize_key($_GET['orderby']) : 'ID';
        $orderby = in_array($orderby, $allowed_orderby, true) ? $orderby : 'ID';
        $per_page = $this->get_items_per_page( $this->get_per_page_option_name() , 20 );
        $date_filter= isset($_GET['date_filter']) && isset($_GET['m']) && !empty($_GET['m']) ? sanitize_text_field(wp_unslash($_GET['m'])) : '';
        $view = CFKEF_Entries_Posts::get_view();
        // phpcs:enable WordPress.Security.NonceVerification.Recommended

        if ( $orderby === 'date' ) {
			$orderby = [
				'modified' => $order,
				'date'     => $order,
			];
		};

        $orderby = esc_sql($orderby);
        $order = esc_sql($order);
        $page = esc_sql($page);
        $view = esc_sql($view);
        $search = esc_sql($search);

        $args = [
            'post_type'      => $this->post_type,
            'orderby'        => $orderby,
            'order'          => $order,
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'no_found_rows'  => false,
            'post_status'    => $view === 'trash' ? array('trash') : array('publish', 'draft', 'pending'),
            's'              => $search,
        ];

        global $wpdb;
            
        $post_placeholders=implode(',', array_fill(0, count($args['post_status']), "%s"));

        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare 
        $post_status_query = $wpdb->prepare("post_status IN ($post_placeholders)", array_map('esc_sql', $args['post_status']));

        $query = $wpdb->prepare(
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            "SELECT * FROM $wpdb->posts WHERE post_type = %s AND $post_status_query",
            $this->post_type,
        );

        if(!empty($search)){
            $query .= $wpdb->prepare(
                " AND post_title LIKE %s",
                '%' . $wpdb->esc_like( $search ) . '%'
            );

        }

        
                // echo "<pre>";
                // var_dump($_GET);
                // echo "</pre>";
                // $date_object = DateTime::createFromFormat('Y-m-d H:i:s', $date_filter);
                // if ($date_object) {
                //     var_dump($date_object->format('m'));
                //     var_dump($date_object->format('Y'));
                // } else {
                //     var_dump('Invalid date format');
                // }
        
        if(!empty($date_filter)){
           if (!empty($date_filter) && preg_match('/^(\d{4})(\d{2})$/', $date_filter, $matches)) {
               $year = $matches[1];
               $month = $matches[2];
               
               $query .= $wpdb->prepare(" AND MONTH(post_date) = %d AND YEAR(post_date) = %d", $month, $year);
           }

        }
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $query .= $wpdb->prepare(" ORDER BY {$args['orderby']} {$args['order']} LIMIT %d OFFSET %d", $args['posts_per_page'], ($args['paged'] - 1) * $args['posts_per_page']);

        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $this->items = $wpdb->get_results($query);

        $total_posts=wp_count_posts($this->post_type);
        $post_count=0;

        foreach($args['post_status'] as $status){
            $post_count+=$total_posts->$status;
        }

        $this->set_pagination_args([
            'total_items' => $post_count,
            'per_page'    => $this->get_items_per_page( $this->get_per_page_option_name() , 20 ),
            'total_pages' => (int) ceil( $post_count / $this->get_items_per_page( $this->get_per_page_option_name() , 20 ) ),
        ]);
    }

    protected function extra_tablenav($which) {
        $view = CFKEF_Entries_Posts::get_view();
        if($which === 'top'){
            $this->months_dropdown( CFKEF_Entries_Posts::$post_type );
            echo "<input type='submit' name='date_filter' id='".esc_attr(CFKEF_Entries_Posts::$post_type)."-date-filter' class='button' value='Filter'>";

            if($view === 'trash'){
                echo '<div class="alignleft actions bulkactions">
                </div>';
            }
            
            if($view === 'trash'){
                echo '<input type="submit" class="button button-secondary" name="action" value="Empty Trash">';
            }
        }
    }

    public function display_tablenav($which) {
        // If there are some forms just call the parent method.
        if ( $this->has_items() ) {
            parent::display_tablenav( $which );
            
            return;
        }
    
        // Otherwise, display bulk actions menu and date filter options.
        ?>
        <div class="tablenav <?php echo esc_attr( $which ); ?>">
			<div class="alignleft actions bulkactions">
				<?php $this->bulk_actions( $which ); ?>
			</div>
			<?php
			$this->extra_tablenav( $which );

			if ( $which === 'top' ) {
				$this->pagination( $which );
			}
			?>
			<br class="clear" />
		</div>
    <?php
    }

    /**
	 * Display the pagination.
	 *
	 * @since 1.8.6
	 *
	 * @param string $which The location of the table pagination: 'top' or 'bottom'.
	 */
	protected function pagination( $which ) {

		if ( $this->has_items() ) {
			parent::pagination( $which );

			return;
		}

		printf(
			'<div class="tablenav-pages one-page">
				<span class="displaying-num">%s</span>
			</div>',
			esc_html__( '0 items', 'extensions-for-elementor-form' )
		);
	}

    	/**
	 * Gets the screen per_page option name.
	 *
	 * @since 1.7.5
	 *
	 * @return string
	 */
	private function get_per_page_option_name() {
		return 'edit_'.CFKEF_Entries_Posts::$post_type. '_per_page';
	}
}   
}   
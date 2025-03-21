<?php

namespace Cool_FormKit\Admin\Entries;

use Cool_FormKit\Admin\Entries\CFKEF_List_Table;
use Cool_FormKit\Admin\Register_Menu_Dashboard\CFKEF_Dashboard;
use Cool_FormKit\Admin\Entries\CFKEF_Post_Bulk_Actions;

/**
 * Entries Posts
 */     
class CFKEF_Entries_Posts {

    private static $instance = null;

    public static $post_type = 'cfkef-entries';

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
    public function __construct() {
        add_action( 'init', [ $this, 'register_post_type' ] );
        add_action('add_meta_boxes', [ $this, 'add_submission_meta_boxes' ]);
        add_action('admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ]);
        add_action('cfkef_render_menu_pages', [ $this, 'output_entries_list' ]);
        add_action( 'admin_head', [$this, 'add_screen_option'] );

        $bulk_actions = new CFKEF_Post_Bulk_Actions();
        $bulk_actions->init();

        remove_action( 'admin_head', 'wp_admin_bar_help_menu' );
    }

    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts() {
        wp_enqueue_style('cfkef-entries-posts', CFL_PLUGIN_URL . 'admin/assets/css/cfkef-entries-post.css', [], CFL_VERSION);
    }

    /**
     * Add admin menu
     */
    public function register_post_type() {
        
        $labels = array(
            'name'                  => esc_html_x( 'Entries', 'Post Type General Name', 'cool-formkit' ),
            'singular_name'         => esc_html_x( 'Entrie', 'Post Type Singular Name', 'cool-formkit' ),
            'menu_name'             => esc_html__( 'Entrie', 'cool-formkit' ),
            'name_admin_bar'        => esc_html__( 'Entrie', 'cool-formkit' ),
            'archives'              => esc_html__( 'Entrie Archives', 'cool-formkit' ),
            'attributes'            => esc_html__( 'Entrie Attributes', 'cool-formkit' ),
            'parent_item_colon'     => esc_html__( 'Parent Item:', 'cool-formkit' ),
            'all_items'             => esc_html__( 'Entries', 'cool-formkit' ),
            'add_new_item'          => esc_html__( 'Add New Item', 'cool-formkit' ),
            'add_new'               => esc_html__( 'Add New', 'cool-formkit' ),
            'new_item'              => esc_html__( 'New Item', 'cool-formkit' ),
            'edit_item'             => esc_html__( 'Edit Item', 'cool-formkit' ),
            'update_item'           => esc_html__( 'Update Item', 'cool-formkit' ),
            'view_item'             => esc_html__( 'View Item', 'cool-formkit' ),
            'view_items'            => esc_html__( 'View Items', 'cool-formkit' ),
            'search_items'          => esc_html__( 'Search Item', 'cool-formkit' ),
            'not_found'             => esc_html__( 'Not found', 'cool-formkit' ),
            'not_found_in_trash'    => esc_html__( 'Not found in Trash', 'cool-formkit' ),
            'featured_image'        => esc_html__( 'Featured Image', 'cool-formkit' ),
            'set_featured_image'    => esc_html__( 'Set featured image', 'cool-formkit' ),
            'remove_featured_image' => esc_html__( 'Remove featured image', 'cool-formkit' ),
            'use_featured_image'    => esc_html__( 'Use as featured image', 'cool-formkit' ),
            'insert_into_item'      => esc_html__( 'Insert into item', 'cool-formkit' ),
            'uploaded_to_this_item' => esc_html__( 'Uploaded to this item', 'cool-formkit' ),
            'items_list'            => esc_html__( 'Form entries list', 'cool-formkit' ),
            'items_list_navigation' => esc_html__( 'Form entries list navigation', 'cool-formkit' ),
            'filter_items_list'     => esc_html__( 'Filter from entry list', 'cool-formkit' ),
        );

        $args = array(
            'label'                 => esc_html__( 'Form Entries', 'cool-formkit' ),
            'description'           => esc_html__( 'cool-formkit-entry', 'cool-formkit' ),
            'labels'                => $labels,
            'supports'              => false,
            'capabilities'          => ['create_posts' => 'do_not_allow'],
            'map_meta_cap'          => true,
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true, // Hide from dashboard
            'show_in_menu'          => false,
            'show_in_admin_bar'     => false,
            'show_in_nav_menus'     => false,
            'can_export'            => true,
            'has_archive'           => false,
            'publicly_queryable'    => false,
            'rewrite'               => false,
            'query_var'             => true,
            'exclude_from_search'   => true,
            'show_in_rest'          => true,
        );

        register_post_type( self::$post_type, $args );
        
    }

    public static function get_view() {
        return isset($_GET['view']) && in_array($_GET['view'], ['all', 'trash']) ? sanitize_text_field($_GET['view']) : 'all';
    }

    public function output_entries_list(CFKEF_Dashboard $dashboard) {
        if($dashboard->current_screen(self::$post_type)){
            echo "<div class='wrap'>";
            echo "<h1 class='wp-heading-inline cfkef-entries-list-title'>" . esc_html__( 'Entries', 'cool-formkit' ) . "</h1>";
            echo "<div id='cfkef-entries-list-wrapper'>";
            $list_table = CFKEF_List_Table::get_instance(self::$post_type);
            $list_table->prepare_items();
            $list_table->views();
            // echo '<form method="get">';
            echo '<form method="get" action="'.esc_url( admin_url( 'admin.php?page=cfkef-entries' ) ).'">';
            echo '<input type="hidden" name="page" value="'.self::$post_type.'">';
            echo '<input type="hidden" name="view" value="'.self::get_view().'">';
            $list_table->search_box( esc_html__( 'Search Forms', 'wpforms-lite' ), 'cfkef-entries-search' );
            $list_table->display();
            echo "</form>";
            echo "</div>";
            echo "</div>";
        }
    }

    public function add_screen_option() {
        if(CFKEF_Dashboard::current_screen(self::$post_type)){
            $args = array(
                'label'   => 'Items per page',
                'default' => 20,
                'option'  => 'edit_'.self::$post_type.'_per_page',
            );
            
            add_screen_option( 'per_page', $args );
        }
    }
    

    /**
     * Add submission meta boxes
     */
    public function add_submission_meta_boxes() {
        remove_meta_box('submitdiv', self::$post_type, 'side');
    
        add_meta_box( 'cfkef-entries-meta-box', 'Entrie Details', [ $this, 'render_submission_meta_box' ], self::$post_type, 'normal', 'high' );
        add_meta_box( 'cfkef-form-info-meta-box', 'Form Info', [ $this, 'render_form_info_meta_box' ], self::$post_type, 'side', 'high' );
    }

    /**
     * Render submission meta box
     */
    public function render_submission_meta_box() {
        $form_data = get_post_meta(get_the_ID(), '_cfkef_form_data', true);
        
        $this->render_field_html("cfkef-entries-form-data", $form_data);
    }

    /**
     * Render form info meta box
     */
    public function render_form_info_meta_box() {
        $meta = get_post_meta(get_the_ID(), '_cfkef_form_meta', true);

          // Update the form entry id in post meta
        $submission_number = get_post_meta(get_the_ID(), '_cfkef_form_entry_id', true);
  
        // Update the form name in post meta
        $form_name = get_post_meta(get_the_ID(), '_cfkef_form_name', true);
  
        // Update the element id in post meta
        $element_id = get_post_meta(get_the_ID(), '_cfkef_element_id', true);

        $post_id= isset($meta['page_url']['value']) ? url_to_postid(isset($meta['page_url']['value'])) : '';

        $data=[
            'Form Name' => array('value' => $form_name),
            'Entry No.' => array('value' => $submission_number),
            'Page Url' => array('value' => isset($meta['page_url']['value']) ? $meta['page_url']['value'] : ''),
        ];

        $this->render_field_html("cfkef-form-info", $data);
    }

    private function render_field_html($type, $data) {
        echo '<div id="' . esc_attr($type) . '" class="cfkef-entries-field-wrapper">';
        echo '<table class="cfkef-entries-data-table">';
        echo '<tbody>';
        
        foreach ($data as $key => $value) {
            if(empty($value['value'])) {
                continue;
            }
            $label = $value['title'] ?? $key;
            echo '<tr class="cfkef-entries-data-table-key">';
            echo '<td>' . esc_html($label) . '</td>';
            echo '</tr>';
            echo '<tr class="cfkef-entries-data-table-value">';
            echo '<td>' . esc_html($value['value']) . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    }
}

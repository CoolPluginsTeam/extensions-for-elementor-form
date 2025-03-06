<?php

namespace Cool_FormKit\Admin\Submission_Posts;

/**
 * Submission Posts
 */     
class CFKEF_Submission_Posts {

    private static $instance = null;

    /**
     * Get instance
     * 
     * @return CFKEF_Submission_Posts
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
    }

    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts() {
        wp_enqueue_style('cfkef-submission-posts', CFL_PLUGIN_URL . 'admin/submission-posts/class-cfkef-submission-post.css', [], CFL_VERSION);
    }

    /**
     * Add admin menu
     */
    public function register_post_type() {
        
        $labels = array(
            'name'                  => esc_html_x( 'Submissions', 'Post Type General Name', 'cool-formkit' ),
            'singular_name'         => esc_html_x( 'Submission', 'Post Type Singular Name', 'cool-formkit' ),
            'menu_name'             => esc_html__( 'Submission', 'cool-formkit' ),
            'name_admin_bar'        => esc_html__( 'Submission', 'cool-formkit' ),
            'archives'              => esc_html__( 'Submission Archives', 'cool-formkit' ),
            'attributes'            => esc_html__( 'Submission Attributes', 'cool-formkit' ),
            'parent_item_colon'     => esc_html__( 'Parent Item:', 'cool-formkit' ),
            'all_items'             => esc_html__( 'Submissions', 'cool-formkit' ),
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
            'label'                 => esc_html__( 'Form Submissions', 'cool-formkit' ),
            'description'           => esc_html__( 'cool-formkit-entry', 'cool-formkit' ),
            'labels'                => $labels,
            'supports'              => ['title'],
            'capabilities'          => ['create_posts' => 'do_not_allow'],
            'map_meta_cap'          => true,
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true,
            // 'show_in_menu'          => "cool-formkit-menu",
            'menu_icon'             => 'dashicons-format-aside',
            'menu_position'         => 5,
            'show_in_admin_bar'     => false,
            'show_in_nav_menus'     => false,
            'can_export'            => true,
            'has_archive'           => false,
            'publicly_queryable'    => false,
            'rewrite'               => false,
            'query_var'             => true,
            'exclude_from_search'   => true,
            'capability_type'       => 'page',
            'show_in_rest'          => true,
            // 'rest_base'             => $this->get_name(),
        );

        register_post_type( 'cfkef-submission', $args );
    }

    /**
     * Add submission meta boxes
     */
    public function add_submission_meta_boxes() {
        // var_dump(get_the_ID());
        // if (get_post_type() === 'cfkef-submission') {
            add_meta_box( 'cfkef-submission-meta-box', 'Submission Details', [ $this, 'render_submission_meta_box' ], 'cfkef-submission', 'normal', 'high' );
            add_meta_box( 'cfkef-form-info-meta-box', 'Form Info', [ $this, 'render_form_info_meta_box' ], 'cfkef-submission', 'side', 'high' );
        // }
    }

    /**
     * Render submission meta box
     */
    public function render_submission_meta_box() {
        $form_data = get_post_meta(get_the_ID(), '_cfkef_form_data', true);
        
        $this->render_field_html("cfkef-form-data", $form_data);
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
            'Page Title' => array('value' => isset($meta['page_title']['value']) ? $meta['page_title']['value'] : ''),
            'Page Url' => array('value' => isset($meta['page_url']['value']) ? $meta['page_url']['value'] : ''),
            // 'Page Url' => array('value' => $post_id),
            'Entry No.' => array('value' => $submission_number),
            'Form Name' => array('value' => $form_name),
            'Element ID' => array('value' => $element_id),
        ];

        $this->render_field_html("cfkef-form-info", $data);
    }

    private function render_field_html($type, $data) {
        echo '<div id="' . esc_attr($type) . '" class="cfkef-field-wrapper">';
        echo '<table class="cfkef-data-table">';
        echo '<tbody>';
        
        foreach ($data as $key => $value) {
            if(empty($value['value'])) {
                continue;
            }
            $label = $value['title'] ?? $key;
            echo '<tr class="cfkef-data-table-key">';
            echo '<td>' . esc_html($label) . '</td>';
            echo '</tr>';
            echo '<tr class="cfkef-data-table-value">';
            echo '<td>' . esc_html($value['value']) . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    }
}

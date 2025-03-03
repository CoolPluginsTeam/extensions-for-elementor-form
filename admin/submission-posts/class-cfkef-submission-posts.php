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
        add_action('init', [ $this, 'add_submission_meta_boxes' ]);
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
        if (get_post_type() === 'cfkef-submission') {
            add_meta_box( 'cfkef-submission-meta-box', 'Submission Details', [ $this, 'render_submission_meta_box' ], 'cfkef-submission', 'normal', 'high' );
        }
    }

    /**
     * Render submission meta box
     */
    public function render_submission_meta_box() {
        echo '<h1>Submission Details</h1>';
    }
}

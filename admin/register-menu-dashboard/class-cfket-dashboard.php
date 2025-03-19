<?php

namespace Cool_FormKit\Admin\Register_Menu_Dashboard;
use Cool_FormKit\Admin\Register_Menu_Dashboard\CFKEF_Menu_Pages;

class CFKEF_Dashboard {
    private $parent_slug = 'elementor';
    private $page_title='Cool Form Kit';
    private $capability='manage_options';
    private $menu_slug='cool-form-kit';
    private $icon_url='';
    private static $allowed_pages=array(
        'cool-formkit',
        'cfkef-entries',
    );

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

       /**
     * Constructor for the class.
     * 
     * @param callable $dashboard_callback The callback function for the dashboard page.
     */
    public function __construct() {
        $dashboard_pages=array(
            'cool-formkit' => array(
                'title' => 'Cool FormKit Lite',
                'position' => 45,
                'slug' => 'cool-formkit',
            ),
            'cfkef-entries' => array(
                'title' => '↳ Entries',
                'position' => 46,
                'slug' => 'edit.php?post_type=cfkef-entries', // Retained the original slug with post-new.php?post_type=
                'callback' => false,
            )
        );

        foreach (self::$allowed_pages as $page) {
            if(isset($dashboard_pages[$page]['slug']) && isset($dashboard_pages[$page]['title']) && isset($dashboard_pages[$page]['position'])) {  
                $this->add_menu_page($dashboard_pages[$page]['slug'], $dashboard_pages[$page]['title'], isset($dashboard_pages[$page]['callback']) ? $dashboard_pages[$page]['callback'] : [$this, 'render_page'], $dashboard_pages[$page]['position']);
            }
        }
    }

    public function add_menu_page($slug, $title, $callback, $position = 99 ) {
        add_action('admin_menu', function() use ($slug, $title, $callback, $position) {
            add_submenu_page(
                $this->parent_slug,
                esc_html($title),
                esc_html($title),
                $this->capability,
                $slug,
                $callback,
                $position
            );
        },999);
    }
    
    public static function cfkef_current_page($slug) {
        $current_page= isset($_REQUEST['page']) ? esc_html($_REQUEST['page']) : (isset($_REQUEST['post_type']) ? esc_html($_REQUEST['post_type']) : '');
        if(in_array($current_page, self::$allowed_pages)) {
            return $current_page === $slug;
        }

        return false;
    }

    public function render_page() {
        do_action('cfkef_render_menu_pages');
    }
}

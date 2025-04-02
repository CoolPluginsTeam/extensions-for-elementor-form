<?php

namespace Cool_FormKit\Admin\Register_Menu_Dashboard;

class CFKEF_Dashboard
{
    /**
     * The parent slug for the menu page.
     *
     * @var string
     */
    private $parent_slug = 'elementor';

    /**
     * The capability required to access the menu page.
     *
     * @var string
     */
    private $capability = 'manage_options';
    
    /**
     * The plugin name.
     *
     * @var string
     */
    private $plugin_name;
    
    /**
     * The version of the plugin.
     *
     * @var string
     */
    private $version;

    /**
     * The allowed pages.
     *
     * @var array
     */
    private static $allowed_pages = array(
        'cool-formkit',
        'cfkef-entries',
    );

    /**
     * The instance of the class.
     *
     * @var object
     */
    private static $instance = null;

    /**
     * Get the instance of the class.
     *
     * @param string $plugin_name The name of the plugin.
     * @param string $version The version of the plugin.
     * @return object The instance of the class.
     */
    public static function get_instance($plugin_name, $version)
    {
        if (null === self::$instance) {
            self::$instance = new self($plugin_name, $version);
        }
        return self::$instance;
    }

    /**
     * Constructor for the class.
     * 
     * @param callable $dashboard_callback The callback function for the dashboard page.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $dashboard_pages = array(
            'cool-formkit' => array(
                'title' => 'Cool FormKit Lite',
                'position' => 45,
                'slug' => 'cool-formkit',
            ),
            'cfkef-entries' => array(
                'title' => '↳ Entries',
                'position' => 46,
                // 'slug' => 'edit.php?post_type=cfkef-entries', // Retained the original slug with post-new.php?post_type=
                'slug' => 'cfkef-entries', // Retained the original slug with post-new.php?post_type=
            )
        );

        $dashboard_pages = apply_filters('cfkef_dashboard_pages', $dashboard_pages);

        foreach (self::$allowed_pages as $page) {
            if (isset($dashboard_pages[$page]['slug']) && isset($dashboard_pages[$page]['title']) && isset($dashboard_pages[$page]['position'])) {
                $this->add_menu_page($dashboard_pages[$page]['slug'], $dashboard_pages[$page]['title'], isset($dashboard_pages[$page]['callback']) ? $dashboard_pages[$page]['callback'] : [$this, 'render_page'], $dashboard_pages[$page]['position']);
            }
        }

        add_action('elementor/admin-top-bar/is-active', [$this, 'hide_elementor_top_bar']);
        add_action('admin_print_scripts', [$this, 'hide_unrelated_notices']);
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
    }

    /**
     * Add a menu page.
     *
     * @param string $slug The slug of the menu page.
     * @param string $title The title of the menu page.
     * @param callable $callback The callback function for the menu page.
     * @param int $position The position of the menu page.
     */
    public function add_menu_page($slug, $title, $callback, $position = 99)
    {
        add_action('admin_menu', function () use ($slug, $title, $callback, $position) {
            add_submenu_page(
                $this->parent_slug,
                str_replace('↳ ', '', $title),
                esc_html($title),
                $this->capability,
                $slug,
                $callback,
                $position
            );
        }, 999);
    }

    /**
     * Get the allowed pages.
     *
     * @return array The allowed pages.
     */
    public static function get_allowed_pages()
    {
        $allowed_pages = self::$allowed_pages;

        $allowed_pages = apply_filters('cfkef_dashboard_allowed_pages', $allowed_pages);

        return $allowed_pages;
    }

    /**
     * Check if the current screen is the given slug.
     *
     * @param string $slug The slug to check.
     * @return bool True if the current screen is the given slug, false otherwise.
     */ 
    public static function current_screen($slug)
    {
        $slug = sanitize_text_field($slug);
        return self::cfkef_current_page($slug);
    }

    /**
     * Check if the current page is the given slug.
     *
     * @param string $slug The slug to check.
     * @return bool True if the current page is the given slug, false otherwise.
     */
    private static function cfkef_current_page($slug)
    {
        $current_page = isset($_REQUEST['page']) ? esc_html($_REQUEST['page']) : (isset($_REQUEST['post_type']) ? esc_html($_REQUEST['post_type']) : '');
        $status=false;

        if (in_array($current_page, self::get_allowed_pages()) && $current_page === $slug) {
            $status=true;
        }

        if(function_exists('get_current_screen') && in_array($slug, self::get_allowed_pages())){
            $screen = get_current_screen();

            if($screen && property_exists($screen, 'id') && $screen->id && $screen->id === $slug){
                $status=true;
            }
        }

        return $status;
    }

    /**
     * Render the page.
     */
    public function render_page()
    {
        echo '<div class="cfkef-wrapper">
        <div class="cfkef-header">
            <div class="cfkef-header-logo">
                <img src="'.esc_url(CFL_PLUGIN_URL . 'assets/images/cool-formkit-logo.png').' ?>" alt="Cool FormKit Logo">
            </div>
            <div class="cfkef-header-buttons">
                <p>Cool FormKit Lite – Elementor Form Builder.</p>
                <a href="https://www.youtube.com/watch?v=u1PYFXv01Rc" class="button" target="_blank">'.esc_html__('Video Demo', 'cool-formkit').'</a>
            </div>
        </div>';

        $this->render_tabs();

        echo '<div class="cfkef-content">';

        do_action('cfkef_render_menu_pages', $this);
        
        echo '</div></div>';
    }

    public function render_tabs(){
        $tabs = $this->cfkef_get_tabs();
        
        echo '<div class="cfkef-dashboard-tabs">';
        foreach ($tabs as $tab) {
            $active_class = self::current_screen($tab['slug']) ? ' active' : '';

            echo '<div class="cfkef-dashboard-tab-wrapper' . esc_attr($active_class) . '">';
            echo '<a href="' . esc_url(admin_url('admin.php?page=' . $tab['slug'])) . '" class="cfkef-dashboard-tab">' . esc_html($tab['title']) . '</a>';
            echo '</div>';
        }
        echo '</div>';
    }

    public function cfkef_get_tabs(){
        $default_tabs = array(
            array(
                'title' => 'settings',
                'position' => 1,
                'slug' => 'cool-formkit',
            ),
        );

        $tabs = apply_filters('cfkef_dashboard_tabs', $default_tabs);
        // Set the index of tabs based on their position
        usort($tabs, function($a, $b) {
            return $a['position'] <=> $b['position'];
        });

        return $tabs;
    }

    /**
     * Enqueue admin styles and scripts.
     *
     * @since    1.0.0
     */
    public function enqueue_admin_styles() {
        if (isset($_GET['page']) && self::current_screen($_GET['page'])) {
            wp_enqueue_style('cfkef-admin-style', CFL_PLUGIN_URL . 'assets/css/admin-style.css', array(), $this->version, 'all');
            wp_enqueue_style('dashicons');
            wp_enqueue_script('cfkef-admin-script', CFL_PLUGIN_URL . 'assets/js/admin-script.js', array('jquery'), $this->version, true);
        }
    }

    /**
     * Hide the Elementor top bar.
     *
     * @param bool $is_active Whether the Elementor top bar is active.
     * @return bool Whether the Elementor top bar is active.
     */ 
    public function hide_elementor_top_bar($is_active)
    {
        foreach (self::$allowed_pages as $page) {
            if (self::current_screen($page)) {
                return false;
            }
        }

        return $is_active;
    }

    /**
     * Hide unrelated notices
     */
    public function hide_unrelated_notices()
    { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded, Generic.Metrics.NestingLevel.MaxExceeded
        $cfkef_pages = false;
        foreach (self::$allowed_pages as $page) {

            if (self::current_screen($page)) {
                $cfkef_pages = true;
                break;
            }
        }

        if ($cfkef_pages) {
            global $wp_filter;

            // Define rules to remove callbacks.
            $rules = [
                'user_admin_notices' => [], // remove all callbacks.
                'admin_notices'      => [],
                'all_admin_notices'  => [],
                'admin_footer'       => [
                    'render_delayed_admin_notices', // remove this particular callback.
                ],
            ];

            $notice_types = array_keys($rules);

            foreach ($notice_types as $notice_type) {
                if (empty($wp_filter[$notice_type]->callbacks) || ! is_array($wp_filter[$notice_type]->callbacks)) {
                    continue;
                }

                $remove_all_filters = empty($rules[$notice_type]);

                foreach ($wp_filter[$notice_type]->callbacks as $priority => $hooks) {
                    foreach ($hooks as $name => $arr) {
                        if (is_object($arr['function']) && is_callable($arr['function'])) {
                            if ($remove_all_filters) {
                                unset($wp_filter[$notice_type]->callbacks[$priority][$name]);
                            }
                            continue;
                        }

                        $class = ! empty($arr['function'][0]) && is_object($arr['function'][0]) ? strtolower(get_class($arr['function'][0])) : '';

                        // Remove all callbacks except WPForms notices.
                        if ($remove_all_filters && strpos($class, 'wpforms') === false) {
                            unset($wp_filter[$notice_type]->callbacks[$priority][$name]);
                            continue;
                        }

                        $cb = is_array($arr['function']) ? $arr['function'][1] : $arr['function'];

                        // Remove a specific callback.
                        if (! $remove_all_filters) {
                            if (in_array($cb, $rules[$notice_type], true)) {
                                unset($wp_filter[$notice_type]->callbacks[$priority][$name]);
                            }
                            continue;
                        }
                    }
                }
            }
        }

        add_action( 'admin_notices', [ $this, 'display_admin_notices' ], PHP_INT_MAX );
    }

    /**
     * Display admin notices.
     */
    public function display_admin_notices() {
        do_action('cfkef_admin_notices');
    }
}

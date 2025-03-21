<?php

namespace Cool_FormKit\Admin\Register_Menu_Dashboard;

class CFKEF_Dashboard
{
    private $parent_slug = 'elementor';
    private $capability = 'manage_options';
    private static $allowed_pages = array(
        'cool-formkit',
        'cfkef-entries',
    );

    private static $instance = null;

    public static function get_instance()
    {
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
    public function __construct()
    {
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
    }

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

    public static function get_allowed_pages()
    {
        $allowed_pages = self::$allowed_pages;

        $allowed_pages = apply_filters('cfkef_dashboard_allowed_pages', $allowed_pages);

        return $allowed_pages;
    }

    public static function current_screen($slug)
    {
        $slug = sanitize_text_field($slug);
        return self::cfkef_current_page($slug);
    }

    private static function cfkef_current_page($slug)
    {
        $current_page = isset($_REQUEST['page']) ? esc_html($_REQUEST['page']) : (isset($_REQUEST['post_type']) ? esc_html($_REQUEST['post_type']) : '');
        if (in_array($current_page, self::get_allowed_pages())) {
            return $current_page === $slug;
        }

        return false;
    }

    public function render_page()
    {
        do_action('cfkef_render_menu_pages', $this);
    }

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

            // Extra deny callbacks (will be removed for each hook tag defined in $rules).
            $common_deny_callbacks = [
                'wpformsdb_admin_notice', // 'Database for WPForms' plugin.
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

                        // Remove non-WPForms callbacks from `$common_deny_callbacks` denylist.
                        if (in_array($cb, $common_deny_callbacks, true)) {
                            unset($wp_filter[$notice_type]->callbacks[$priority][$name]);
                        }
                    }
                }
            }
        }

        add_action( 'admin_notices', [ $this, 'display_admin_notices' ], PHP_INT_MAX );
    }

    public function display_admin_notices() {
        do_action('cfkef_admin_notices');
    }
}

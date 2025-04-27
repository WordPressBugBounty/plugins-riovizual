<?php
/**
 * Dashboard Core functionality for RioVizual
 *
 * @since   1.0.0
 * @package RioVizual
 */
class Admin {

    private $menu_slug = 'riovizual';
    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', [$this, 'register_admin_menus']);
        add_action('admin_menu', [$this, 'highlight_submenu'], 999); // Add submenu highlight fix
        add_action('admin_enqueue_scripts', [$this, 'enqueue_dashboard_assets']);
        add_action('rest_api_init', [$this, 'register_rest_routes']);
        add_action('admin_head', [$this, 'hide_admin_notification']);
        add_action('admin_init', [$this, 'riovizual_redirect_after_activation'] );
    }

    /**
     * Define RioVizual Admin Dashboard Menus
     */
    public function register_admin_menus() {

        $menu_slug  = $this->menu_slug;
        $capability = 'manage_options';
        $icon = esc_url('https://riovizual.com/wp-content/uploads/2024/04/rv_dashboard-icon.png');

        $pro_active = $this->is_pro_active();

        add_menu_page(
            esc_html__('RioVizual', 'riovizual'),
            esc_html__('RioVizual', 'riovizual'),
            $capability,
            $menu_slug,
            [$this, 'render_dashboard_page'],
            $icon,
            26
        );

        add_submenu_page(
            $menu_slug,
            esc_html__('RioVizual', 'riovizual'),
            esc_html__('Dashboard', 'riovizual'),
            $capability,
            $menu_slug,
            [$this, 'render_dashboard_page']
        );

        add_submenu_page(
            $menu_slug,
            esc_html__('RioVizual', 'riovizual'),
            esc_html__('Blocks', 'riovizual'),
            $capability,
            $menu_slug . '&path=blocks',
            [$this, 'render_dashboard_page']
        );

        if ($pro_active) {
            add_submenu_page(
                $menu_slug,
                esc_html__('RioVizual', 'riovizual'),
                esc_html__('License', 'riovizual'),
                $capability,
                $menu_slug . '&path=license',
                [$this, 'render_dashboard_page']
            );
        }
        else {
            add_submenu_page(
                $menu_slug,
                esc_html__('RioVizual', 'riovizual'),
                esc_html__('Free vs Pro', 'riovizual'),
                $capability,
                $menu_slug . '&path=freeVsPro',
                [$this, 'render_dashboard_page']
            );

            // Upgrade to pro button
            add_submenu_page(
                $menu_slug,
                esc_html__('RioVizual', 'riovizual'),
                esc_html__('Get Riovizual Pro', 'riovizual'),
                $capability,
                'https://riovizual.com/pricing/',
                ''
            );
        }
    }

    /**
     * Ensure the correct submenu is highlighted in admin
     */
    public function highlight_submenu() {
        global $submenu_file, $parent_file;

        $menu_slug = $this->menu_slug;

        if (isset($_GET['page']) && strpos($_GET['page'], $menu_slug) === 0) {
            $parent_file = $menu_slug;

            // If `path` is set, match submenu accordingly
            if (isset($_GET['path'])) {
                $submenu_file = $menu_slug . '&path=' . sanitize_key($_GET['path']);
            } else {
                $submenu_file = $menu_slug;
            }
        }
    }

    /**
     * Enqueue Dashboard Scripts and Styles
     */
    public function enqueue_dashboard_assets() {

        $localized_data = [
            'dashboardData' => get_option('_rio_vizual_dashboard'),
            'version'       => esc_attr(RIO_VIZUAL_VERSION),
            'nonce'         => wp_create_nonce('wp_rest'),
            'isActive'      => $this->is_pro_active()
        ];

        if ($localized_data['isActive']) {
            $localized_data['pro_version'] = esc_attr(RIO_VIZUAL_PRO_VERSION);
        }

        $current_user = wp_get_current_user();

        if ($current_user->exists()) {
            $localized_data['username'] = esc_html($current_user->user_login);
        }

        wp_enqueue_script(
            'riodashboard-script',
            esc_url(RIO_VIZUAL_ADMIN_URL . '/dashboard/dashboard.js'),
            ['react', 'react-dom', 'wp-element', 'wp-components', 'wp-api-fetch'],
            esc_attr(RIO_VIZUAL_VERSION),
            true
        );

        wp_localize_script('riodashboard-script', 'rv_dashboard_data', $localized_data);

        // Check for the specific page
        if (isset($_GET['page']) && $_GET['page'] === 'riovizual') {
            wp_enqueue_style(
				'riodashboard-style',
				esc_url(RIO_VIZUAL_ADMIN_URL . '/dashboard/dashboard.css'),
				[],
				esc_attr(RIO_VIZUAL_VERSION)
			);
        }
    }

    /**
     * Register REST API Routes
     */
    public function register_rest_routes() {
        register_rest_route(
            'rio-vizual/v2',
            '/save_dashboard_blocks/',
            [
                'methods'             => 'POST',
                'callback'            => [$this, 'save_dashboard_blocks'],
                'permission_callback' => function () {
                    return current_user_can('edit_posts');
                },
            ]
        );
    }

    /**
     * Save RioVizual Dashboard Data
     *
     * @param WP_REST_Request $request
     * @return array
     */
    public function save_dashboard_blocks($request) {
        $dashboard_data = $request->get_param('dashboard_data');
        update_option('_rio_vizual_dashboard', $dashboard_data);
        return true;
    }

    /**
     * Render Dashboard Page
     */
    public function render_dashboard_page() {
        echo '<div id="rioDashboard"></div>';
    }

    /**
     * Check if the Pro plugin is active
     *
     * @return bool
     */
    private function is_pro_active() {
        return function_exists('is_plugin_active') && is_plugin_active('riovizual-pro/riovizual-pro.php');
    }

	/**
     * Hide admin notification
     */
    public function hide_admin_notification() {
        if (isset($_GET['page']) && strpos($_GET['page'], 'riovizual') === 0) {
			remove_all_actions('admin_notices');
			remove_all_actions('all_admin_notices');
		}
    }

    /**
     * Redirect to Dashboard on first time installation
     * */
	public function riovizual_redirect_after_activation() {
        if (get_option('_rio_vizual_redirect_on_activation')) {
            delete_option('_rio_vizual_redirect_on_activation');

            if (is_admin() && current_user_can('manage_options')) {
                wp_safe_redirect(admin_url('admin.php?page=riovizual'));
                exit;
            }
        }
    }
}

new Admin();

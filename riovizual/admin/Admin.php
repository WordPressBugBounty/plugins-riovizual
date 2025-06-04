<?php
namespace RioVizual\Admin;

use RioVizual\Helpers\Utils;
use RioVizual\PostTypes\TablesList;

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

        // menu filters
        add_filter('parent_file', [$this, 'custom_menuitem_activation'],10 ,1);
        add_filter('submenu_file', [$this, 'custom_submenu_file_filter'],10 ,1 );
    }

    /**
     * Define RioVizual Admin Dashboard Menus
     */
    public function register_admin_menus() {

        $tablesList = new TablesList();

        $capability = 'manage_options';
       // $capability_edit = 'edit_posts';
        $icon = esc_url('https://riovizual.com/wp-content/uploads/2024/04/rv_dashboard-icon.png');

        add_menu_page(
            esc_html__('RioVizual', 'riovizual'),
            esc_html__('RioVizual', 'riovizual'),
            $capability,
            $this->menu_slug,
            [$this, 'render_dashboard_page'],
            $icon,
            26
        );
        add_submenu_page(
            $this->menu_slug,
            esc_html__('Dashboard', 'riovizual'),
            esc_html__('Dashboard', 'riovizual'),
            $capability,
            $this->menu_slug,
            [$this, 'render_dashboard_page']
        );
        add_submenu_page(
            $this->menu_slug,
            esc_html__('Blocks', 'riovizual'),
            esc_html__('Blocks', 'riovizual'),
            $capability,
            $this->menu_slug . '&path=blocks',
            [$this, 'render_dashboard_page']
        );
        add_submenu_page(
            $this->menu_slug,
            __( 'Tables', 'riovizual' ),
            __( 'Tables', 'riovizual' ),
            $capability,
            'riovizualTables',
            [ $tablesList, 'render_riovizual_tables_page' ]
        );
        add_submenu_page(
            'edit.php?post_type=wp_block',
            __( 'Riovizual Tables', 'riovizual' ),
            __( 'Tables', 'riovizual' ),
            $capability,
            'riovizualTables',
            [ $tablesList, 'render_riovizual_tables_page' ]
        );

        if ( Utils::is_pro_plugin_active() ) {
            add_submenu_page(
                $this->menu_slug,
                esc_html__('License', 'riovizual'),
                esc_html__('License', 'riovizual'),
                $capability,
                $this->menu_slug . '&path=license',
                [$this, 'render_dashboard_page']
            );
        } else {
            add_submenu_page(
                $this->menu_slug,
                esc_html__('Free vs Pro', 'riovizual'),
                esc_html__('Free vs Pro', 'riovizual'),
                $capability,
                $this->menu_slug . '&path=freeVsPro',
                [$this, 'render_dashboard_page']
            );

            // Upgrade to pro button
            add_submenu_page(
                $this->menu_slug,
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
            'isActive'      => Utils::is_pro_plugin_active()
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
        if ( isset($_GET['page']) && strpos($_GET['page'], 'riovizual') === 0 ) {
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
     * Hide admin notification
     */
    public function hide_admin_notification() {
        if ( isset($_GET['page']) && $_GET['page'] === 'riovizual' ) {
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

    /**
     * Custom Menu item activation filter
    */
    public function custom_menuitem_activation($parent_file){
        if (isset($_GET['page'])) {
            if ($_GET['page'] === 'riovizual' || strpos($_GET['page'], 'riovizual&path=') === 0) {
                return 'riovizual'; // Highlight the main parent menu
            }
            if ($_GET['page'] === 'riovizualtables') {
                return 'riovizualtables'; // Custom menu for CPT page
            }
        }
        return $parent_file;
    }

    public function custom_submenu_file_filter($submenu_file) {
        if (isset($_GET['page'])) {
            if ($_GET['page'] === 'riovizual') {
                $path = $_GET['path'] ?? '';
                switch ($path) {
                    case 'blocks':
                        return 'riovizual&path=blocks';
                    case 'freeVsPro':
                        return 'riovizual&path=freeVsPro';
                    case 'license':
                        return 'riovizual&path=license';
                    default:
                        return 'riovizual'; // default dashboard
                }
            }
            if ($_GET['page'] === 'riovizualTables') {
                return 'riovizualTables';
            }
        }
        return $submenu_file;
    }
}

<?php

namespace RioVizual\PostTypes;

class TablesList {

    public function __construct() {
        add_action( 'parse_query', [$this, 'riovizual_parse_pattern_query'], 10, 1);
        add_action( 'load-admin_page_riovizualTables', [ $this, 'handle_bulk_actions' ] );
        
        add_filter( 'post_row_actions', [ $this, 'riovizual_remove_export_action_from_row' ], 10, 2 );
        add_filter( 'bulk_actions-edit-wp_block', [ $this, 'remove_bulk_edit_action' ] );
        add_filter( 'gettext', [ $this, 'riovizual_change_no_patterns_text' ], 10, 3 );
    }
    
    // we remove export options
    public function riovizual_remove_export_action_from_row( $actions, $post ) {
        if ( $post->post_type === 'wp_block' ) {
            unset( $actions['export'] );
        }
        return $actions;
    }

    /*
    * filtered posts based on custom meta key
    * note: we neglet auto-draft status
    */ 
    private function get_filtered_counts() {
        global $wpdb;
    
        $results = $wpdb->get_results("
            SELECT post_status, COUNT(*) as count
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} m ON p.ID = m.post_id
            WHERE p.post_type = 'wp_block'
            AND m.meta_key = '_riovizual_pattern'
            AND p.post_status NOT IN ('auto-draft')
            GROUP BY post_status
        ");
    
        $counts = [];
        foreach ($results as $row) {
            $counts[ $row->post_status ] = (int) $row->count;
        }
    
        return $counts;
    }    

    /*
    * custom query to fetch posts with custom meta
    */ 
    public function riovizual_parse_pattern_query($query) {
        if (
            is_admin()
            && isset($_GET['page']) && $_GET['page'] === 'riovizualTables'
        ) {
            $meta_query = [
                [
                    'key'     => '_riovizual_pattern',
                    'compare' => 'EXISTS',
                ]
            ];

            $query->set('meta_query', $meta_query);
        }
    }

    /*
    * Render page to display all tables
    */
    public function render_riovizual_tables_page() {
        $_GET['post_type'] = 'wp_block';

        $this->handle_quick_admin_notice(); // notice for bulk delete, move to trash and restore

        // Add this to fix screen context
        require_once ABSPATH . 'wp-admin/includes/screen.php';
        set_current_screen('edit-wp_block');
    
        echo '<div id="riovizual-table-entries"></div>';
        echo '<div class="wrap" id="riovizual-table-entries">';
        echo '<h1 class="wp-heading-inline">' . esc_html__('Riovizual Tables', 'riovizual') . '</h1>';
        echo '<a href="' . admin_url('post-new.php?post_type=wp_block&plugin=riovizual') . '" class="page-title-action rv-add-new-table-btn">' . esc_html__('Create New Table', 'riovizual') . '</a>';
        echo '<hr class="wp-header-end">';
    
        require_once ABSPATH . 'wp-admin/includes/class-wp-posts-list-table.php';

        // Get filtered counts for custom views
        $count = $this->get_filtered_counts();
        $this->display_posts_count_meta($count);

        ob_start();
    
        $list_table = new \WP_Posts_List_Table([
            'screen' => 'edit-wp_block'
        ]);
    
        $list_table->prepare_items();
    
        // Search box
        echo '<form method="get">';
        echo '<input type="hidden" name="page" value="riovizualTables">';
        echo '<input type="hidden" name="post_type" value="wp_block">';
        if (!empty($_GET['post_status'])) {
            echo '<input type="hidden" name="post_status" value="' . esc_attr($_GET['post_status']) . '">';
        }
        $list_table->search_box(__('Search Tables', 'riovizual'), 'block');
       // $this->handle_empty_trash();
        $list_table->display();

        // Replace the "No patterns found." text
        $html = ob_get_clean();
        // Replace the "No patterns found." text
        $html = str_replace(
            [
                'No patterns found.',
                'No patterns found in Trash.'
            ],
            [
                'No Tables Found.',
                'No Tables Found in Trash.'
            ],
            $html
        );
        

        // Output the final HTML
        echo $html;

        echo '</form>';
        echo '</div>';
        
    }

    private function display_posts_count_meta($count){
        // Custom Views
        echo '<ul class="subsubsub">';
        $current_status = $_GET['post_status'] ?? '';
        $total = array_sum($count);
        $statuses = [
            'all'     => ['label' => __('All'),      'count' => $total - ($count['trash'] ?? 0)],
            'publish' => ['label' => __('Published'), 'count' => $count['publish'] ?? 0],
            'draft'   => ['label' => __('Drafts'),    'count' => $count['draft'] ?? 0],
            'trash'   => ['label' => __('Trash'),     'count' => $count['trash'] ?? 0],
        ];
    
        $i = 0;
        foreach ( $statuses as $status => $data ) {
            $url = add_query_arg([
                'page'        => 'riovizualTables',
                'post_type'   => 'wp_block',
                'post_status' => $status !== 'all' ? $status : null,
            ], admin_url('admin.php'));
    
            $class = ($status === $current_status || ($status === 'all' && $current_status === '')) ? 'class="current"' : '';
            echo "<li><a href='" . esc_url($url) . "' $class>{$data['label']} <span class='count'>({$data['count']})</span></a>";
            echo (++$i < count($statuses)) ? ' | </li>' : '</li>';
        }
        echo '</ul>';
    }

    public function handle_bulk_actions() {
        if ( ! current_user_can( 'edit_posts' ) ) {
            return;
        }

        require_once ABSPATH . 'wp-admin/includes/class-wp-posts-list-table.php';
    
        $wp_list_table = new \WP_Posts_List_Table([
            'screen' => 'edit-wp_block'
        ]);
        $wp_list_table->prepare_items();
    
        $action = $wp_list_table->current_action();
        if ( ! $action ) return;
    
        $post_ids = array_map('intval', (array) ($_REQUEST['post'] ?? []));
        if ( empty( $post_ids ) ) return;
    
        switch ( $action ) {
            case 'trash':
                foreach ( $post_ids as $post_id ) {
                    wp_trash_post( $post_id );
                }
                $sendback = add_query_arg( [ 'trashed' => count( $post_ids ) ], wp_get_referer() );
                break;
    
            case 'delete':
                foreach ( $post_ids as $post_id ) {
                    wp_delete_post( $post_id, true );
                }
                $sendback = add_query_arg( [ 'deleted' => count( $post_ids ) ], wp_get_referer() );
                break;
    
            case 'untrash':
                foreach ( $post_ids as $post_id ) {
                    wp_untrash_post( $post_id );
                }
                $sendback = add_query_arg( [ 'untrashed' => count( $post_ids ) ], wp_get_referer() );
                break;
    
            default:
                return;
        }

        wp_redirect( $sendback );
        exit;
    }

    /*
    * Custom notice for bulk action
    */ 
    public function handle_quick_admin_notice(){
        if ( isset($_GET['trashed']) ) {
            // translators: %s is the number of items moved to Trash.
            echo '<div class="updated notice is-dismissible"><p>' . sprintf( __('%s item(s) moved to Trash.', 'riovizual'), intval($_GET['trashed']) ) . '</p></div>';
        }

        if ( isset($_GET['deleted']) ) {
            // translators: %s is the number of items permanently deleted.
            echo '<div class="updated notice is-dismissible"><p>' . sprintf( __('%s item(s) permanently deleted.', 'riovizual'), intval($_GET['deleted']) ) . '</p></div>';
        }

        if ( isset($_GET['untrashed']) ) {
            // translators: %s is the number of items restored from Trash.
            echo '<div class="updated notice is-dismissible"><p>' . sprintf( __('%s item(s) restored.', 'riovizual'), intval($_GET['untrashed']) ) . '</p></div>';
        }
       
    }

    public function remove_bulk_edit_action( $actions ) {
        if ( isset($_GET['page']) && $_GET['page'] === 'riovizualTables' ) {
            unset( $actions['edit'] ); // 'edit' is the key for Bulk Edit
        }
        return $actions;
    }

    public function riovizual_change_no_patterns_text( $translated_text, $text, $domain ) {
    
        // Match the exact default message
        if ( isset( $_GET['post_type'] ) && $_GET['post_type'] === 'wp_block' && isset( $_GET['page'] ) && $_GET['page'] === 'riovizualTables' ) {
            if ( strpos( $text, 'No patterns found.' ) !== false ) {
                return 'No Tables Found!';
            }
        }
    
        return $translated_text;
    }
}

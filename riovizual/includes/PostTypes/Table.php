<?php

namespace RioVizual\PostTypes;

use RioVizual\Helpers\Utils;

class Table {

	public function __construct() {
        add_action( 'init', [ $this, 'register_custom_category_and_meta' ] );
        add_action( 'save_post', [ $this, 'riovizual_assign_rio_pattern_category' ], 10, 3 );
        add_filter( 'allowed_block_types_all', [$this, 'restrict_blocks_for_riovizual_plugin'], 10, 2 );

        if( isset( $_GET['page'] ) && $_GET['page'] === 'riovizualTables' ){
            add_action( 'manage_wp_block_posts_custom_column', [ $this, 'display_shortcode' ], 10, 2 );
            add_filter( 'manage_wp_block_posts_columns', [ $this, 'add_shortcode_column' ], 10, 1 );
        }
    }

    /*
    * Register custom Category and Meta
    */
    public function register_custom_category_and_meta() {

        register_taxonomy_for_object_type( 'wp_pattern_category', 'wp_block' );

        if ( ! term_exists( 'rio', 'wp_pattern_category' ) ) {
            wp_insert_term( 'Riovizual', 'wp_pattern_category', [ 'slug' => 'rio' ] );
        }

        register_post_meta( 'wp_block', '_riovizual_pattern', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
            'auth_callback'=> '__return_false', // disallow REST editing
        ]);
    }

    public function riovizual_assign_rio_pattern_category( $post_id, $post, $update ) {
       
        if( self::is_riovizual_postType() ){
            // Always assign taxonomy for backwards compatibility
            wp_set_post_terms( $post_id, [ 'rio' ], 'wp_pattern_category', false );

            // Assign unique identifier if not already set
            if ( ! get_post_meta( $post_id, '_riovizual_pattern', true ) ) {
                update_post_meta( $post_id, '_riovizual_pattern', uniqid( 'rio_', true ) );
            }
        }
    }

    public function add_shortcode_column ( $columns ) {
        if ( isset( $columns['title'] ) ) {
            $columns['title'] = __( 'Name', 'riovizual' );
        }
        if ( isset( $columns['taxonomy-wp_pattern_category'] ) ) {
            unset( $columns['taxonomy-wp_pattern_category'] );
        }
        if ( isset( $columns['date'] ) ) {
            unset( $columns['date'] );
        }
        $columns['riovizual_shortcode'] = __( 'Shortcode', 'riovizual' );
        return $columns;
    }

    public function display_shortcode( $column, $post_id ) {
        if ( $column === 'riovizual_shortcode' ) {
            $shortcode = '[riovizual id="' . $post_id . '"]';
            echo '<input type="text" class="riovizual-shortcode-field" readonly value="' . esc_attr( $shortcode ) . '" /> <label class="riovizual-shortcode-field-label"></label>';
        }
    }

    public function restrict_blocks_for_riovizual_plugin( $allowed_block_types, $block_editor_context ) {

        if( self::is_riovizual_postType() || self::has_riovizual_table_meta() ){
            return Utils::default_blocks_list(); // Restrict to default blocks
        }

        return $allowed_block_types;
    }

    public static function is_riovizual_postType() {
        return ( isset( $_GET['post_type'] ) && $_GET['post_type'] === 'wp_block' && isset( $_GET['plugin'] ) && $_GET['plugin'] === 'riovizual' );
    }

    public static function has_riovizual_table_meta() {

        $post_id = null;

        if ( isset( $_GET['post'] ) ){
            $post_id = intval( $_GET['post'] );
        }

        if ( isset( $_GET['postId'] ) ){
            $post_id = intval( $_GET['postId'] );
        }
        
        if( !empty($post_id) ){
            $post    = get_post( $post_id );

            if ( $post && $post->post_type === 'wp_block' && get_post_meta( $post_id, '_riovizual_pattern', true ) ) {
                return true;
            }
        }
        return false;
    }
}
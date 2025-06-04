<?php

namespace RioVizual\PostTypes;

use RioVizual\StyleProcessor\StyleProcessor;

class Shortcode {

	public function __construct() {
        add_shortcode( 'riovizual', [ $this, 'render' ] );
    }

    public function render( $attrs ) {  

        $atts = shortcode_atts( [
            'id' => 0,
        ], $attrs ); 
    
        $post = get_post( intval( $atts['id'] ) );
    
        if ( $post && $post->post_type === 'wp_block' && $post->post_status === 'publish' ) {
           
            $blocks = parse_blocks( $post->post_content );
            
            ob_start();

            foreach ( $blocks as $block ) {
                if ( isset( $block['attrs']['styles'] ) ) {
                    StyleProcessor::add_inline_css( 'rv-styles', $block['attrs']['styles'] );
                }
        
                if ( isset( $block['attrs']['fontFamily'] ) ) {
                    StyleProcessor::add_fonts( $block['attrs']['fontFamily'] );
                }
        
                echo render_block( $block );
            }

            return ob_get_clean();
        }
    
        return '';
    }
}
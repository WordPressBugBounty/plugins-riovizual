<?php
namespace RioVizual\PostTypes;
use RioVizual\StyleProcessor\StyleProcessor;
class Shortcode {
    public function __construct() {
        add_shortcode( 'riovizual', [ $this, 'render' ] );
    }
    public function render( $attrs ) {
        global $riovizual_generated_css;
        $atts = shortcode_atts( [
            'id' => 0,
        ], $attrs );
        $post = get_post( intval( $atts['id'] ) );
        if ( $post && $post->post_type === 'wp_block' && $post->post_status === 'publish' ) {
            $blocks = parse_blocks( $post->post_content );
            ob_start();
            foreach ( $blocks as $block ) {
                if ( isset( $block['attrs']['styles'] ) ) {
                    // StyleProcessor::add_inline_css( 'rv-styles', $block['attrs']['styles'] );
                    echo '<style>'.$block['attrs']['styles'].'</style>';
                   // $riovizual_generated_css = $block['attrs']['styles'];
                }
                if ( isset( $block['attrs']['fontFamily'] ) ) {
                    $font_url = 'https://fonts.googleapis.com/css2?' . esc_html($block['attrs']['fontFamily']) . '&display=swap';
                    echo '<link href="' . esc_url($font_url) . '" rel="stylesheet">';
                    // StyleProcessor::add_fonts( $block['attrs']['fontFamily'] );
                }
                echo render_block( $block );
            }
            return ob_get_clean();
        }
        return '';
    }
}

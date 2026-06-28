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
                    // Strip any tag-breakout attempt before printing CSS.
                    // Valid CSS never contains "<", so removing it neutralises
                    // payloads like `</style><script>...`.
                    $css = str_replace( '<', '', (string) $block['attrs']['styles'] );
                    echo '<style>' . $css . '</style>';
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

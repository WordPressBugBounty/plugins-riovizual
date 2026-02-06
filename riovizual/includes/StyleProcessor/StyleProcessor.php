<?php

namespace RioVizual\StyleProcessor;

use RioVizual\Helpers\Utils;

class StyleProcessor {

	public $css = '';
	public $fonts = '';

	public function __construct() {
		add_filter( 'render_block_data', [ $this, 'render_blocks' ], 10 );
		add_action( 'the_content', [ $this, 'output_styles_in_head' ], 100 );
	}

	/**
	 * @param $block
	 *
	 * @return mixed
	 */
	public function render_blocks( $block ) {
		if ( isset( $block['blockName'] ) && in_array( $block['blockName'], Utils::default_blocks_list(), true ) ) {
			if ( isset( $block['attrs']['styles'] ) ) {
				$this->css .= $block['attrs']['styles'];
			}
			if ( isset( $block['attrs']['fontFamily'] ) ) {
				$this->fonts .= $block['attrs']['fontFamily'] ? $block['attrs']['fontFamily'].'&' : '';
			}
		}
		
		return $block;
	}

	/**
	 * Add Google Font Api to the head
	 *
	 * @param mixed $post_id post id.
	 */
	public static function rio_vizual_font_api( $post_id ) {
		$option_name   = '_rio_vizual_font';
		$save_font     = get_option( $option_name );
		$font_families = array();
		$font_varient  = array();

		if ( $save_font ) {
			$save_font_keys = array_keys( $save_font );

			// Loop thrugh all save font.
			foreach ( $save_font_keys as $key ) {
				$save_font_post_ids = array_keys( $save_font[ $key ]['post_id'] );

				// Generate font family for google apis call, if post id match with save font post id.
				if ( in_array( $post_id, $save_font_post_ids, false ) ) {

					$default_weight        = array();
					$default_italic_weight = array();
					$italic_weight         = array();

					// Generate font weight.
					foreach ( $save_font[ $key ]['weight'] as $weight ) {
						$index = strpos( $weight, ' Italic' );

						// Check for `italic` in font weight.
						if ( $index ) {
							$new_weight = str_replace( ' Italic', '', $weight );
							array_push( $italic_weight, '1,' . $new_weight );
						} else {
							array_push( $default_italic_weight, '0,' . $weight );
							array_push( $default_weight, $weight );
						}
					}

					// if true, generate font family with `italic` font weight.
					if ( count( $italic_weight ) > 0 ) {
						$font_varient = array_merge( $default_italic_weight, $italic_weight );

						$font_families[] = $key . ':ital,wght@' . implode( ';', $font_varient );
					} else {
						$font_families[] = $key . ':wght@' . implode( ';', $default_weight );
					}
				}
			}

			// If font family found, then enqueue font in head.
			if ( count( $font_families ) > 0 ) {
				$query_args = array(
					'family'  => implode( '&family=', $font_families ),
					'display' => 'swap',
				);

				$google_fonts_url = esc_url_raw( add_query_arg( $query_args, 'https://fonts.googleapis.com/css2' ) );

				if ( ! empty( $google_fonts_url ) ) {
					wp_enqueue_style( 'rv-fonts-' . $post_id . '', $google_fonts_url, array(), RIO_VIZUAL_VERSION );
				}
			}
		}
	}

	/**
	 * Generate style CSS
	 *
	 * @param  mixed  $content   The content to add styles for.
	 *
	 * @return string|null
	 */
	public function output_styles_in_head($content) {

		global $post;
		$post_id = (int) $post->ID;

		// generate style css
		if ( ! empty( $this->css ) ) {
			$this->add_inline_css( 'rv-styles' , $this->css );
		}
		else{
			$styles = get_post_meta( $post_id, '_rio_vizual_css', true );
			if( $styles ){
				$this->add_inline_css( 'rv-styles' . $post_id , $styles );
			}
		}
 
		// generate google fonts link
		if ( ! empty( $this->fonts ) ) {
			self::add_fonts($this->fonts);
		}else{
			self::rio_vizual_font_api( $post_id );
		}
		return $content;
	}

	public static function add_inline_css($handle, $css){
		wp_register_style( $handle, false, array(), RIO_VIZUAL_VERSION );
		wp_enqueue_style( $handle );
		wp_add_inline_style( $handle , $css );
	}

	public static function add_fonts($fonts){
		$fonts_url = 'https://fonts.googleapis.com/css2?'. esc_html($fonts) . '&display=swap';
		wp_enqueue_style( 'rv-fonts', esc_url_raw( $fonts_url ), array(), null );
	}

}
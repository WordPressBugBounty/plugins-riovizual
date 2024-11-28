<?php
/**
 * Style processor for rio vizual
 *
 * @since   1.0.0
 * @package riovizual
 */
class Rio_Viz_Style_Processor {

	public $css = '';
	public $fonts = '';

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'render_block_data', [ $this, 'render_blocks' ] );
		add_action( 'the_content', [ $this, 'output_styles_in_head' ] );
	}

	/**
	 * @param $block
	 *
	 * @return mixed
	 */
	public function render_blocks( $block ) {
		if ( isset( $block['blockName'] ) && (
			$block['blockName'] === 'riovizual/tablebuilder' ||
			$block['blockName'] === 'riovizual/prosandcons' ||
			$block['blockName'] === 'riovizual/pricingtable'
			) ) {
			if ( isset( $block['attrs']['styles'] ) ) {
				$this->css .= $block['attrs']['styles'];
			}
			if ( isset( $block['attrs']['fontFamily']) ) {
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
					wp_enqueue_style( 'rv-fonts-' . $post_id . '', $google_fonts_url, array(), null );
				}
			}
		}
	}

	/**
	 * @return void
	 */
	public function output_styles_in_head($content) {

		global $post;
		$post_id = (int) $post->ID;

		// generate style css
		if ( ! empty( $this->css ) ) {
			$handle = 'rv-styles';
			wp_register_style( $handle, false );
			wp_enqueue_style( $handle );
			wp_add_inline_style( $handle , $this->css );
		}
		else{
			$styles = get_post_meta( $post_id, '_rio_vizual_css', true );
			if( $styles ){
				$handle = 'rv-styles' . $post_id;
				wp_register_style( $handle, false );
				wp_enqueue_style( $handle );
				wp_add_inline_style( $handle, $styles );
			}
		}

		// generate google fonts link
		if ( ! empty( $this->fonts ) ) {

			$fonts_url = 'https://fonts.googleapis.com/css2?'. esc_html($this->fonts) . 'display=swap';
			wp_enqueue_style(
				'rv-fonts',
				$fonts_url,
				array(),
				null
			);
		}else{
			self::rio_vizual_font_api( $post_id );
		}
		return $content;
	}

}

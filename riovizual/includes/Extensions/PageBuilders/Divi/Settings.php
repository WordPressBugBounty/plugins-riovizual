<?php
/**
 * @package riovizual.
 * @since 2.2.2
 */

namespace Riovizual\Extensions\PageBuilders\Divi;

class Settings {

	public function __construct() {
		
		if ( ! class_exists( 'ET_Builder_Module' ) ) { return; }

       	add_action( 'et_builder_ready', [ $this, 'module_setup' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'load_scripts' ] );

		if ( wp_doing_ajax() ) {
			add_action( 'wp_ajax_riovizual_divi_preview', [ $this, 'preview' ] );
		}
	}
  
	public function load_scripts() {
		wp_enqueue_style( 'riovizual-divi-style', plugins_url( 'assets/editor.css', __FILE__ ), [], RIO_VIZUAL_VERSION, 'all' );
		wp_enqueue_script( 'rv-divi-loader', RIO_VIZUAL_BUILD_URL . '/divi_extn_frontend.js', array('wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'lodash' ), RIO_VIZUAL_VERSION );
		wp_localize_script(
			'rv-divi-loader',
			'riovizual_divi_builder',
			[
				'ajax_url'         => admin_url( 'admin-ajax.php' ),
				'nonce'            => wp_create_nonce( 'riovizual_divi_builder' ),
				'placeholder'      => 'https://riovizual.com/wp-content/uploads/2025/05/rio-icon-elementor.png',
				'block_empty_text' => wp_kses(
					__( 'RioVizual is the most versatile WordPress table plugin designed for creating comparison tables, pricing tables, and pros & cons boxes directly in the Gutenberg block editor.', 'riovizual' ),
					[
						'b' => [],
					]
				),
				'get_started_url'  => esc_url( admin_url( 'post-new.php?post_type=wp_block&plugin=riovizual' ) ),
				'get_started_text' => esc_html__( 'Create New', 'riovizual' )
			]
		);
	}

    /**
	 * Divi module register
	 *
	 * @param object $module_manager Divi module manager.
	 * @since 1.1.0
	 * @return void
	 */
	public function module_setup() {
		require_once RIO_VIZUAL_INC_PATH . '/Extensions/PageBuilders/Divi/Module.php';
        new Module();
    }

	public function preview() { 

		global $riovizual_generated_css;

		check_ajax_referer( 'riovizual_divi_builder', 'nonce' );

		add_action(
			'riovizual_frontend_output',
			static function () {
				echo '<fieldset disabled>';
			},
			3
		);

		add_action(
			'riovizual_frontend_output',
			static function () {

				echo '</fieldset>';

				echo "<img src='https://riovizual.com/wp-content/uploads/2025/05/rio-icon-elementor.png'
					height='0'
					width='0'
					onLoad=\"jQuery( document ).trigger( 'riovizualDiviModuleDisplay' );\"
				/>";
			},
			30
		);
		
		$table_id = absint( filter_input( INPUT_POST, 'riovizual', FILTER_SANITIZE_NUMBER_INT ) );
		$shortcode_output = do_shortcode( sprintf( '[riovizual id="%1$d"]', $table_id ) );
		
		wp_send_json_success(
			[
				'content' => $shortcode_output,
				'css'     => $riovizual_generated_css,
			]
		);
	}

}
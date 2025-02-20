<?php
/**
 * Load assets for rio vizual
 *
 * @since   1.0.0
 * @package riovizual
 */

/**
 * Load Assets
 */
class Rio_Viz_Load_Assets {
	/**
	 * Constructor
	 */
	public function __construct() {

		global $pagenow;
		// styles and scripts for public.
		add_action(
			'init',
			function() {
				$this->scripts();
			}
		);

		// styles and scripts for admin.
		if ( $pagenow == 'plugins.php' ) {
			add_action( 'admin_enqueue_scripts',
				function() {
					$this->rio_feedback_assets();
				}
			);
		}

		// Blocks: styles and scripts for public.
		add_action( 'enqueue_block_assets', function() {
			$this->rio_blocks_assets();
		} );

		// Blocks: styles and scripts for admin.
		add_action( 'enqueue_block_editor_assets', function() {
			$this->rio_blocks_editor_assets();
		} );
	}

	/**
	 * Enqueue and register scripts
	 *
	 * Only the styles for front-end should load here
	 */
	public function scripts() {
		// wp_register_script( 'rio-viz-res-table-scripts', RIO_VIZUAL_BUILD_URL . '/responsiveTable.js', array(), RIO_VIZUAL_VERSION, true );
		wp_register_script( 'rio-viz-pricing-table-scripts', RIO_VIZUAL_BUILD_URL . '/pricing-table.js', array('lodash'), RIO_VIZUAL_VERSION, true );
	}

	/**
	 * Enqueue and register feedback assets
	 *
	 * Only the scripts for admin block should load here
	 */
	private function rio_feedback_assets(){
		wp_enqueue_script(
			'rio-feedback-core-script',
			RIO_VIZUAL_BUILD_URL. '/assets/js/feedback.js',
			array(),
			RIO_VIZUAL_VERSION,
			false
		);

		wp_enqueue_style(
			'rio-feedback-style',
			RIO_VIZUAL_BUILD_URL. '/assets/css/feedback.css',
			array(),
			RIO_VIZUAL_VERSION
		);

	}

	/**
	 * Enqueue the block's assets for the frontend.
	 */
	public function rio_blocks_assets() {
		wp_enqueue_style(
				'riovizual-block-style',
				RIO_VIZUAL_BUILD_URL . '/style-index.css',
				array(),
				RIO_VIZUAL_VERSION
			);
	}

	/**
	 * Enqueue the block's assets for the editor.
	 */
	public function rio_blocks_editor_assets() {
		wp_enqueue_script(
			'riovizual-block-scripts',
			RIO_VIZUAL_BUILD_URL . '/index.js',
			array('wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'lodash' ),
			RIO_VIZUAL_VERSION,
			true
		);

		wp_enqueue_style(
			'riovizual-block-editor-style',
			RIO_VIZUAL_BUILD_URL . '/index.css',
			array( 'wp-edit-blocks' ),
			RIO_VIZUAL_VERSION
		);

		wp_enqueue_style(
			'riovizual-block-editor-common-style',
			RIO_VIZUAL_BUILD_URL . '/style-common.css',
			array( 'wp-edit-blocks', 'dashicons' ),
			RIO_VIZUAL_VERSION
		);
	}
}

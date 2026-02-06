<?php

namespace RioVizual\Assets;

use RioVizual\PostTypes\Table;

class Assets {

	public function __construct() {
		global $pagenow;

		add_action( 'init', [$this, 'scripts'] );
		add_action( 'enqueue_block_assets', [$this, 'rio_blocks_assets'] );
		add_action( 'enqueue_block_editor_assets', [$this, 'rio_blocks_editor_assets'] );
		add_action( 'admin_enqueue_scripts', function() { $this->admin_scripts(); } );
		if ( $pagenow === 'post-new.php' || $pagenow === 'post.php' ) { add_action( 'admin_enqueue_scripts', function() { $this->editor_panel_script(); } ); }
		if ( $pagenow === 'plugins.php' ) { add_action( 'admin_enqueue_scripts', [$this, 'rio_feedback_assets'] ); }
	}

	public function editor_panel_script(){
		wp_enqueue_script(
          'riovizual-editor-custom',
          RIO_VIZUAL_BUILD_URL . '/editor.js',
          [ 'wp-dom' ], // or any other dependencies
          '1.0',
          true
      );
	}

	public function admin_scripts(){
		wp_enqueue_script( 'rio-admin-core-script', RIO_VIZUAL_ASSETS_URL. '/js/rv-admin-script.min.js', array(), RIO_VIZUAL_VERSION, false );
		wp_enqueue_style( 'riovizual-admin-style', RIO_VIZUAL_ASSETS_URL. '/css/admin-style.css', array(), RIO_VIZUAL_VERSION );
	}

	/**
	 * Enqueue and register scripts
	 *
	 * Only the styles for front-end should load here
	 */
	public function scripts() {
		wp_register_script( 'rio-viz-pricing-table-scripts', RIO_VIZUAL_BUILD_URL . '/pricing-table.js', array('lodash'), RIO_VIZUAL_VERSION, true );
	}

	/**
	 * Enqueue and register feedback assets
	 *
	 * Only the scripts for admin block should load here
	 */
	public function rio_feedback_assets(){
		wp_enqueue_script( 'rio-feedback-core-script', RIO_VIZUAL_ASSETS_URL. '/js/feedback.min.js', array(), RIO_VIZUAL_VERSION, false );
		wp_localize_script( 'rio-feedback-core-script', 'feedback_ajax_object', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'deactivate_plugin_nonce' ),
		) );
		wp_enqueue_style( 'rio-feedback-style', RIO_VIZUAL_ASSETS_URL. '/css/feedback.css', array(), RIO_VIZUAL_VERSION );
	}

	/**
	 * Enqueue the block's assets for the frontend.
	 */
	public function rio_blocks_assets() {
		wp_enqueue_style( 'riovizual-block-style', RIO_VIZUAL_BUILD_URL . '/style-index.css', array(), RIO_VIZUAL_VERSION );
	}

	/**
	 * Enqueue the block's assets for the editor.
	 */
	public function rio_blocks_editor_assets() {
		wp_enqueue_script( 'riovizual-block-scripts', RIO_VIZUAL_BUILD_URL . '/index.js', array('wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'lodash' ), RIO_VIZUAL_VERSION );
		wp_enqueue_style( 'riovizual-block-editor-style', RIO_VIZUAL_BUILD_URL . '/index.css', array( 'wp-edit-blocks' ), RIO_VIZUAL_VERSION );
		wp_enqueue_style( 'riovizual-block-editor-common-style', RIO_VIZUAL_BUILD_URL . '/style-common.css', array( 'wp-edit-blocks', 'dashicons' ), RIO_VIZUAL_VERSION);

		// Enqueue the table editor style only when needed
		if ( Table::is_riovizual_postType() || Table::has_riovizual_table_meta() ) {
			wp_enqueue_style( 'riovizual-table-editor-style', RIO_VIZUAL_ASSETS_URL . '/css/table-editor.css', array(), RIO_VIZUAL_VERSION );
		}
	}
}

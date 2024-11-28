<?php
/**
 * Initialize file for rio vizual
 *
 * @since   1.0.0
 * @package riovizual
 */

/**
 * Initialize
 */
class Rio_Viz_Init {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->load_dependencies();
		$this->load_assets();
		$this->load_blocks();
		$this->load_categories();
		$this->process_block_styles();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		require_once RIO_VIZUAL_INC_PATH . '/class-rio-viz-load-assets.php';

		require_once RIO_VIZUAL_INC_PATH . '/load-blocks.php';

		require_once RIO_VIZUAL_INC_PATH . '/load-categories.php';

		require_once RIO_VIZUAL_INC_PATH . '/process-styles/class-rio-viz-style-processor.php';

		require_once RIO_VIZUAL_INC_PATH . '/feedback/feedback-main.php';

		require_once RIO_VIZUAL_INC_PATH . '/deactive_plugin.php';
	}

	/**
	 * Load all blocks
	 */
	public function load_blocks() {
		add_action( 'init', 'rio_viz_create_block_riovizual_block_init' );
	}

	/**
	 * Load categories
	 */
	public function load_categories() {
		add_filter( 'block_categories_all', 'rio_viz_crate_block_categories' );
	}

	/**
	 * Load all assets
	 * CSS and JavaScritps
	 */
	public function load_assets() {
		new Rio_Viz_Load_Assets();
	}

	/**
	 * Process block styles
	 */
	public function process_block_styles() {
		new Rio_Viz_Style_Processor();
	}
}


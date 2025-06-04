<?php
/**
 * Elementor service provider.
 *
 * @package riovizual.
 * @since 2.2.2
 */

namespace Riovizual\Extensions\PageBuilders\Elementor;

class Settings {

	public function __construct() {
		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			return;
		}
		add_action( 'elementor/widgets/register', [ $this, 'widget' ] );
		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'load_scripts' ] );
	}

	public function load_scripts() {
		wp_enqueue_script( 'riovizual-elementor-editor', plugins_url( 'assets/editor.js', __FILE__ ), [], RIO_VIZUAL_VERSION, true );
		wp_enqueue_style( 'riovizual-elementor-style', plugins_url( 'assets/editor.css', __FILE__ ), [], RIO_VIZUAL_VERSION, 'all' );
		wp_localize_script(
			'riovizual-elementor-editor',
			'riovizualEmentorData', 
			[
				'admin_url'        => admin_url(),
				'add_new_table_url' => admin_url( 'post-new.php?post_type=wp_block&plugin=riovizual' ),
			]
		);
	}

	
    /**
	 * Elementor widget register
	 *
	 * @param object $widgets_manager Elementor widget manager.
	 * @since 2.2.2
	 * @return void
	 */
	public function widget( $widgets_manager ) {
		require_once RIO_VIZUAL_INC_PATH . '/Extensions/PageBuilders/Elementor/Widget.php'; // If autoloading doesn't work
    	$widgets_manager->register( new \Riovizual\Extensions\PageBuilders\Elementor\Widget() );
	}

}
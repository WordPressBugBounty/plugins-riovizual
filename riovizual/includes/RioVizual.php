<?php

namespace RioVizual;

final class RioVizual {

	private static $instance = null;

	public function __construct() {
		add_action( 'plugin_loaded', [ $this, 'init_plugin' ] );
	}

	public static function get_instance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function init_plugin() {
		add_action( 'init', [ $this, 'load_dependencies' ], 2 );
	}

	public function load_dependencies() {
		new Assets\Assets();
		new Blocks\Blocks();
		new Admin\Admin();
		new PostTypes\Table();
		new PostTypes\TablesList();
		new PostTypes\Shortcode();
		new StyleProcessor\StyleProcessor();
		new Notices\Init();
		new Feedback\Feedback();
		new Extensions\Extensions();
	}
}

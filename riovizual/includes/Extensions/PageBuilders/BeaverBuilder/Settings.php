<?php
/**
 * Beaver service provider.
 *
 * @package riovizual.
 * @since 2.2.2
 */

namespace Riovizual\Extensions\PageBuilders\BeaverBuilder;

class Settings {

	public function __construct() {
		if ( ! class_exists( 'FLBuilder' ) ) {
			return;	
		}
		add_action('init', [ $this, 'riovizual_bb_module_load' ]);
	}

	function riovizual_bb_module_load() {
		require_once RIO_VIZUAL_INC_PATH . '/Extensions/PageBuilders/BeaverBuilder/Module.php'; 
		new Module();
	}

}
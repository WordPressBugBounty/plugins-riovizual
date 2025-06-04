<?php

namespace RioVizual\Core;

class I18n {
	public static function load_textdomain() {
		load_plugin_textdomain( 'riovizual', false, dirname( plugin_basename( __FILE__ ) ) . '/../../languages' );
	}
}

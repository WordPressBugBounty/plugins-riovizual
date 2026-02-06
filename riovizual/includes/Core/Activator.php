<?php

namespace RioVizual\Core;

class Activator {
	public static function activate() {
		if ( ! get_option( '_rio_vizual_plugin_installed_on' ) ) {
			update_option( '_rio_vizual_redirect_on_activation', true );
		}
	}
}

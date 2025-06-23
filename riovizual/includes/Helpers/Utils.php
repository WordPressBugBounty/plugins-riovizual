<?php

namespace RioVizual\Helpers;

class Utils {

	// Plugin paths
	const FREE_PLUGIN_PATH = 'riovizual/riovizual.php';
	const PRO_PLUGIN_PATH  = 'riovizual-pro/riovizual-pro.php';

	public static function default_blocks_list() {
		return [
			'riovizual/pricingtable',
			'riovizual/prosandcons',
			'riovizual/tablebuilder',
		];
	}

	public static function is_pro_plugin_active() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return is_plugin_active( self::PRO_PLUGIN_PATH );
	}

	public static function get_available_tables() {

		$riovizualTable = array();

		$args = array(
			'post_type'      => 'wp_block',
			'post_status'    => 'publish',
			'numberposts'    => -1,
			'meta_key'       => '_riovizual_pattern',
		);

		$blocks = get_posts($args);

		$riovizualTable['default'] = 'Choose a Table';
		foreach ( $blocks as $block ) {
			$riovizualTable[$block->ID] = $block->post_title;
		}

		return $riovizualTable;
	}


}

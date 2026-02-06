<?php

namespace RioVizual\Core;

class Uninstaller {
	public static function uninstall() {
		global $wpdb;

		delete_metadata( 'post', 0, '_rio_vizual_css', '', true );

		$all_options = wp_load_alloptions();
		foreach ( $all_options as $name => $value ) {
			if ( str_starts_with( $name, '_rio_vizual_' ) ) {
				delete_option( $name );
			}
		}

		$users = get_users( [ 'fields' => 'ID' ] );
		foreach ( $users as $user_id ) {
			$usermeta = get_user_meta( $user_id );
			foreach ( $usermeta as $key => $value ) {
				if ( str_starts_with( $key, '_rio_vizual_' ) ) {
					delete_user_meta( $user_id, $key );
				}
			}
		}
	}
}

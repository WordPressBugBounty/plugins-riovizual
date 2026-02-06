<?php
/**
 * Bricks service provider.
 *
 * @package riovizual.
 * @since 2.2.2
 */

namespace Riovizual\Extensions\PageBuilders\Bricks;

class Settings {

	public function __construct() {
		if ( ! class_exists( '\Bricks\Elements' ) ) {
			return;
		}
		add_action( 'init', [ $this, 'widget' ], 11 );
	}

	public function widget() {
		add_filter(
			'bricks/builder/i18n',
			[ $this, 'bricks_translatable_strings' ]
		);
		\Bricks\Elements::register_element( __DIR__ . '/Widget.php' );
	}
    
	public function bricks_translatable_strings( $i18n ) {
		$i18n['riovizual'] = __( 'Riovizual', 'riovizual' );
		return $i18n;
	}

}
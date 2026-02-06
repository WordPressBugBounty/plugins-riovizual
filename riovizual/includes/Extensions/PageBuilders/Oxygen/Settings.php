<?php
/**
 * Oxygen service provider.
 *
 * @package riovizual.
 * @since 2.2.2
 */

namespace Riovizual\Extensions\PageBuilders\Oxygen;

class Settings{

	public function __construct() {

		if(class_exists('OxyEl')) {
			try {
				include_once 'elements/class-oxyel-riovizual-table.php';
			} catch (Exception $e) {}
		}
	} 
}
	

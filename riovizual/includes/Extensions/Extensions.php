<?php

namespace RioVizual\Extensions;

use RioVizual\Extensions\PageBuilders\Elementor\Settings as Elementor_Widget_Settings;
use RioVizual\Extensions\PageBuilders\Bricks\Settings as Bricks_Widget_Settings;
use RioVizual\Extensions\PageBuilders\Divi\Settings as Divi_Widget_Settings;
use RioVizual\Extensions\PageBuilders\BeaverBuilder\Settings as Beaver_Widget_Settings;
use RioVizual\Extensions\PageBuilders\Oxygen\Settings as Oxygen_Widget_Settings;

class Extensions {
	public function __construct() {
		new Elementor_Widget_Settings();
		new Bricks_Widget_Settings();
		new Divi_Widget_Settings();
		new Beaver_Widget_Settings();
		new Oxygen_Widget_Settings();
	}
}
 
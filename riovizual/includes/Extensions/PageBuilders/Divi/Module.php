<?php
/**
 * Divi Riovizual Widget.
 *
 * @package riovizual.
 * @since 1.1.0
 */

namespace Riovizual\Extensions\PageBuilders\Divi;

use RioVizual\Helpers\Utils;
use ET_Builder_Module;
use DF_UTLS;

class Module extends ET_Builder_Module{
	public $slug       = 'riovizual_module';
	public $vb_support = 'on';

	function init() {
		$this->name = esc_html__( 'Riovizual', 'riovizual' );
		$this->icon_path = plugin_dir_path( __FILE__ ) . 'assets/rio-icon-elementor.svg';
	}

	function get_fields() {
		return array(
			'riovizual' => array(
				'label'             => esc_html__( 'Available Tables', 'riovizual' ),
				'type'              => 'select',
				'options'           => Utils::get_available_tables(),
				'default'           => 'default',
				'option_category'   => 'basic_option',
				'description'       => sprintf(
					// translators: %s is the URL to create a new table in the admin area.
					__( 'Select a table from the dropdown. You can also <a href="%s" target="_blank">create a new table here</a>.', 'riovizual' ),
					esc_url( admin_url( 'post-new.php?post_type=wp_block&plugin=riovizual' ) )
				),
			),
		
		);
	}

	public function render_riovizual() {
        if (empty($this->props['riovizual']) || $this->props['riovizual'] === 'default') {
            return '<div class="riovizual-divi-no-table-id"><p>Select the table that you would like to use for this Divi module.</p></div>';
        } else {
            return do_shortcode('[riovizual id="' . esc_attr($this->props['riovizual']) . '"]');
        }
    }

    public function render( $attrs, $content = null, $render_slug = '' ) {
      	return sprintf('<div class="df-riovizual-container">%1$s</div>', $this->render_riovizual());
    }
	
}
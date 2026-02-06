<?php
/**
 * Bricks element for Riovizual.
 *
 * @package riovizual.
 * @since 2.2.2
 */

namespace Riovizual\Extensions\PageBuilders\Bricks;
use WP_Query;
use WP_Post;
use RioVizual\Helpers\Utils;
 
class Widget extends \Bricks\Element {

	// public $category = 'riovizual';
	public $category = 'basic';
	public $name = 'riovizual';
	public $icon = 'rv-bricks-element-icon';

	public function __construct( $element = null ) {

		if ( bricks_is_builder() ) {
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_bricks_editor_styles' ] );
			add_action( 'bricks/preview/enqueue', [ $this, 'enqueue_bricks_editor_styles' ] );

			$this->scripts = [ 'handleBricksPreviewFormSubmission', 'loadPageBreak' ];
		}
		parent::__construct( $element );
	}

	public function enqueue_bricks_editor_styles() {
		wp_enqueue_style( 'riovizual-bricks-editor-style', plugins_url( 'assets/editor.css', __FILE__ ), [], RIO_VIZUAL_VERSION, 'all' );
	}

	public function get_label() {
		return __( 'Riovizual', 'riovizual' );
	}

	public function get_keywords() {
		return [
			'riovizual',
			'table',
			'block',
			'tables',
			'table builder',
			'pricing table',
		];
	}

	public function set_controls() {

		$this->controls['table-id'] = [
			'tab'         => 'content',
			'label'       => __( 'Table', 'riovizual' ),
			'type'        => 'select',
			'options'     => Utils::get_available_tables(),
			'default'	  => 'default',
			'placeholder' => __( 'Select Table', 'riovizual' ),
		];
		$this->controls['create-table'] = [
			'tab'         => 'content',
			'type'        => 'string',
			'description' => __( '<div class="rv-raw-element"><label>Create a new table</label><a href="'.admin_url( "post-new.php?post_type=wp_block&plugin=riovizual" ).'" target="_blank" class="rv-bricks-button">Create Table</a></div>', 'riovizual' ),
		];
	
	}

	public function render() {
		$settings   = $this->settings;
		$table_id    = isset( $settings['table-id'] ) ? $settings['table-id'] : '';
        
		if( ! $table_id || $table_id == 'default'){
			echo $this->render_element_placeholder(
				[
					'icon-class'  => $this->icon,
					'description' => esc_html__( 'Please select a table.', 'riovizual' ),
				]
			);
		}
		else {
			echo '<div ' . $this->render_attributes( '_root' ) . '>' . do_shortcode( sprintf( '[riovizual id="%s"]', $table_id ) ) . '</div>';
		} 
	}

}
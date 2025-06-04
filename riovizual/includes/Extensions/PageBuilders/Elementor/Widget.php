<?php
/**
 * Elementor Riovizual Widget.
 *
 * @package riovizual.
 * @since 2.2.2
 */

namespace Riovizual\Extensions\PageBuilders\Elementor;

use RioVizual\Helpers\Utils;
use Elementor\Widget_Base;
use Elementor\Plugin;
use WP_Query;
use WP_Post;

class Widget extends Widget_Base{

	public $is_preview_mode;

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
	}

	public function get_name() {
		return 'riovizual_widget';
	}

	public function get_title() {
		return __( 'Riovizual', 'riovizual' );
	}

	public function get_icon() {
		return 'rv-elementor-widget-icon';
	}

	public function get_categories() {
		return [ 'basic' ];
		// return [ 'riovizual-elementor' ];
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

    /**
	 * Register form widget controls.
	 */
	protected function register_controls() {

        // Section for the form selection and creation
		$this->start_controls_section(
			'riovizual_content_section',
			[
				'label' => __( 'Riovizual', 'riovizual' ),
			]
		);

        // Dropdown for selecting a table
		$this->add_control(
			'riovizual_table_block',
			[
				'label'   => __( 'Select a Table', 'riovizual' ),
				'type'    => \Elementor\Controls_Manager::SELECT2,
				'options' => Utils::get_available_tables(),
				'default' => 'default',
			]
		);
		$this->add_control(
			'riovizual_create_table',
			[
				'label'     => __( 'Create New Table', 'riovizual' ),
				'separator' => 'before',
				'type'      => \Elementor\Controls_Manager::BUTTON,
				'text'      => __( 'Create Table', 'riovizual' ),
				'event'     => 'riovizual:table:create',
				'condition' => [
					'riovizual_table_block!' => [ '' ],
				],
			]
		);
		$this->end_controls_section();
	}

    /**
	 * Render form widget output on the frontend.
	 *
	 * @since 1.1.0
	 * @return void|string
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( ! is_array( $settings ) ) {
			return;
		}

		$is_editor = Plugin::instance()->editor->is_edit_mode();

		if ( ! isset( $settings['riovizual_table_block'] ) ) {
			return;
		}

		if ( $is_editor && isset( $settings['riovizual_table_block'] ) && $settings['riovizual_table_block'] == 'default' ) {
			echo '<div style="color: #515151;border-left: 4px solid #1A73E8; padding: 12px; font-family: Roboto, sans-serif">' .
					esc_html__( 'Please select the table.', 'riovizual' ) .
				'</div>';
			return;
		}

		echo do_shortcode( '[riovizual id="' . $settings['riovizual_table_block'] . '"]' );

	}
}
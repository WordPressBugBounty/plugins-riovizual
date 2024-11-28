<?php
/**
 * Load blocks for rio vizual
 *
 * @since   1.0.0
 * @package riovizual
 */

/**
 * Register all block.
 */
function rio_viz_create_block_riovizual_block_init() {
	$args = [
		'editor_script' => 'riovizual-block-scripts',
		'editor_style'  => 'riovizual-block-editor-style',
		'style'         => 'riovizual-block-style',
	];

	register_block_type( RIO_VIZUAL_BUILD_DIR . '/blocks/tableBuilder/block.json', $args );
	register_block_type( RIO_VIZUAL_BUILD_DIR . '/blocks/pricingTable/block.json', $args );
	register_block_type( RIO_VIZUAL_BUILD_DIR . '/blocks/prosAndCons/block.json', $args );
}

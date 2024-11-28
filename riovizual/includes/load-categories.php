<?php
/**
 * Load blocks for rio vizual
 *
 * @since   1.0.0
 * @package riovizual
 */

/**
 * Add a block category if it doesn't exist already.
 *
 * @param array $categories Array of block categories.
 *
 * @return array
 */
function rio_viz_crate_block_categories( $categories ) {
	$category_slugs = wp_list_pluck( $categories, 'slug' );

	return in_array( 'riovizual', $category_slugs, true ) ? $categories : array_merge(
		array(
			array(
				'slug'  => 'riovizual',
				'title' => __( 'Riovizual', 'riovizual' ),
			),
		),
		$categories
	);
}

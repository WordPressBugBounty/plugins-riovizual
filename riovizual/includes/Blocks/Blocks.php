<?php

namespace RioVizual\Blocks;

class Blocks {

	public function __construct() {
        add_action( 'init', [ $this, 'rio_viz_create_block_riovizual_block_init' ] );
        add_filter( 'block_categories_all', [ $this, 'rio_viz_crate_block_categories' ] );
    }

    public function rio_viz_create_block_riovizual_block_init() {
        $stored_data = get_option('_rio_vizual_dashboard');
        $defaultBlocks = ['tableBuilder', 'pricingTable', 'prosAndCons'];
    
        foreach ($defaultBlocks as $blockName) {
            if( isset( $stored_data['inActiveBlocks'] ) && in_array( $blockName, $stored_data['inActiveBlocks'] ) ){
                continue;
            }
            // register blocks
            $this->rio_viz_block_register($blockName);
        }
    }

    /**
     * Registers a block with the given name.
     *
     * @param string $blockName Block name.
     */
    public function rio_viz_block_register($blockName) {
        register_block_type(
            RIO_VIZUAL_BUILD_DIR . '/blocks/' . $blockName . '/block.json',
            [
                'editor_script' => 'riovizual-block-scripts',
                'editor_style'  => 'riovizual-block-editor-style',
                'style'         => 'riovizual-block-style',
            ]
        );
    }

    public function rio_viz_crate_block_categories( $categories ) {
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
}

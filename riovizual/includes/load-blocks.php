<?php
/**
 * Load and register blocks for RioVizual
 *
 * @since   1.0.0
 * @package riovizual
 */
function rio_viz_create_block_riovizual_block_init() {

    $stored_data = get_option('_rio_vizual_dashboard');
    $defaultBlocks = ['tableBuilder', 'pricingTable', 'prosAndCons'];

    foreach ($defaultBlocks as $blockName) {
        if( isset( $stored_data['inActiveBlocks'] ) && in_array( $blockName, $stored_data['inActiveBlocks'] ) ){
            continue;
        }
        // register blocks
        rio_viz_block_register($blockName);
    }
}

/**
 * Registers a block with the given name.
 *
 * @param string $blockName Block name.
 */
function rio_viz_block_register($blockName) {
    register_block_type(
        RIO_VIZUAL_BUILD_DIR . '/blocks/' . $blockName . '/block.json',
        [
            'editor_script' => 'riovizual-block-scripts',
            'editor_style'  => 'riovizual-block-editor-style',
            'style'         => 'riovizual-block-style',
        ]
    );
}

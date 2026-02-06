<?php

namespace Riovizual\Extensions\PageBuilders\BeaverBuilder;

use FLBuilderModule;
use FLBuilder;
use RioVizual\Helpers\Utils;

class Module extends FLBuilderModule {
    
    public function __construct() {
        parent::__construct(array(
            'name'             => __('Riovizual Table Builder', 'riovizual'),
            'description'      => __('Displays a Riovizual table.', 'riovizual'),
            'group'            => 'standard', 
            'category'         => 'Riovizual',   
            'dir'              => __DIR__,
            'url'              => plugins_url( '/', __FILE__ ),
            'icon'             => ' ft-riovizual-module-icon',
            'editor_export'    => true,
            'enabled'          => true,
            'partial_refresh'  => false,
            'version'          => '1.0.0'
        ));
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_css' ] );
    }

    /**
     * Registers custom CSS.  Best practice to use this, rather than adding CSS directly.
     */
    public function enqueue_css() {
        wp_enqueue_style( 'riovizual-bb-module-admin', $this->url . 'assets/editor.css', array(), false, 'all' );
    }

    public function render() {
        // Get the selected table ID from the settings
        $table_id = $this->settings->riovizual;
        if ($table_id) {
            echo do_shortcode('[riovizual id="' . $table_id . '"]');
        } else {
            echo __('No table selected.', 'riovizual');
        }
    }

}

FLBuilder::register_module( 'Riovizual\Extensions\PageBuilders\BeaverBuilder\Module', array(
    'general'       => array( 
        'title'         => __('General', 'riovizual'), 
        'sections'      => array( 
            'table_selection'  => array( 
                'title'     => __('Table Selection', 'riovizual'),
                'fields'    => array( 
                    'riovizual' => array(
                        'type'          => 'select',
                        'default'       => 'default',
                        'label'         => __('Select a Table', 'riovizual'),
                        'options'       => Utils::get_available_tables(),
                    ),
                    'new_table_button' => array(
                        'type'    => 'raw',
                        'content' => '<div class="rv-raw-element"><label>Create a new table</label><a href="'.admin_url( "post-new.php?post_type=wp_block&plugin=riovizual" ).'" target="_blank" class="fl-builder-button">Create Table</a></div>',
                    ),
                    
                ),
            ),
        ),
    ),
));

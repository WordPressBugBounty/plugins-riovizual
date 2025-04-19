<?php
/**
 * Notice related message and additional Link
 *
 * @since   1.0.0
 * @package riovizual
 */
class Rio_Viz_Notice {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'plugin_action_links_riovizual/riovizual.php', [ $this, 'rv_additional_helper_links' ], 10, 4 );
	}

    function rv_additional_helper_links( $actions, $plugin_file, $plugin_data, $context ){
        // Define the links
        $settings_link = '<a href="' . admin_url('admin.php?page=riovizual') . '" >Settings</a>';
        $go_pro_link = '<a href="https://riovizual.com/pricing" target="_blank" style="color: #ff5722; font-weight: bold;">Get Riovizual Pro</a>';

        // Check if Pro is active to modify the deactivate link
        if ( is_plugin_active('riovizual-pro/riovizual-pro.php') ) {
            $deactivate_link = isset($actions['deactivate']) ? '<span style="color: #aaa; cursor: not-allowed;">Deactivate</span>' : '';
        } else {
            $deactivate_link = isset($actions['deactivate']) ? $actions['deactivate'] : '';
        }

        // Reorder actions
        $new_actions = [
            'settings'   => $settings_link,
            'deactivate' => $deactivate_link,
        ];

        // Add "Go Pro" only if Pro is not active
        if ( ! is_plugin_active('riovizual-pro/riovizual-pro.php') ) {
            $new_actions['go_pro'] = $go_pro_link;
        }

        return $new_actions;
    }

}
new Rio_Viz_Notice();

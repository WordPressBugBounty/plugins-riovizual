<?php 

// deactive plugin after taking feedback

function rio_viz_feedback_deactivate_plugin_callback() {
    if (current_user_can('activate_plugins')) {
        $plugin_file = 'riovizual/riovizual.php';
        deactivate_plugins($plugin_file);

        // Check if the plugin is still active
        if (is_plugin_active($plugin_file)) {
            wp_send_json_error(array('success' => false, 'data' => 'Plugin deactivation failed.'));
        } else {
            wp_send_json_success(array('success' => true, 'data' => 'Plugin deactivated successfully.'));
        }
    } else {
        wp_send_json_error(array('success' => false, 'data' => 'You do not have permission to deactivate plugins.'));
    }
    wp_die();
}

add_action('wp_ajax_deactivate_plugin', 'rio_viz_feedback_deactivate_plugin_callback');

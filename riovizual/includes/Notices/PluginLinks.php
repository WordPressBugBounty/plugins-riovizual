<?php
namespace RioVizual\Notices;

use RioVizual\Helpers\Utils;

class PluginLinks {
	public function register() {
		add_filter('plugin_action_links_riovizual/riovizual.php', [$this, 'modify_plugin_links'], 10, 4);
	}

	public function modify_plugin_links($actions, $plugin_file, $plugin_data, $context) {
		$settings_link = '<a href="' . admin_url('admin.php?page=riovizual') . '">Settings</a>';
		$go_pro_link = '<a href="https://riovizual.com/pricing" target="_blank" style="color: #ff5722; font-weight: bold;">Get Riovizual Pro</a>';
		$deactivate_link = isset($actions['deactivate']) 
			? (Utils::is_pro_plugin_active() ? '<span style="color: #aaa;">Deactivate</span>' : $actions['deactivate']) 
			: '';

		$new_actions = [
			'settings' => $settings_link,
			'deactivate' => $deactivate_link,
		];

		if ( ! Utils::is_pro_plugin_active() ) {
			$new_actions['go_pro'] = $go_pro_link;
		}

		return $new_actions;
	}
}

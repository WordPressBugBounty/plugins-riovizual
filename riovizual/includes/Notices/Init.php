<?php
namespace RioVizual\Notices;
use RioVizual\Helpers\Utils;

class Init {
	public function __construct() {
		add_action('admin_init', [$this, 'handle_dismiss_actions']);
		add_action('admin_notices', [$this, 'handle_main_notices']);

		(new PluginLinks())->register();
		(new TablePageNotice())->register();
	}
 
	public function handle_main_notices() {
		global $pagenow;
		if ( $pagenow === 'plugins.php' || ( isset($_GET['page']) && $_GET['page'] === 'riovizualTables' ) || ! current_user_can('manage_options') ) return;

		$user_id = get_current_user_id();
		$installed = get_option('_rio_vizual_plugin_installed_on') ?: time();
		update_option('_rio_vizual_plugin_installed_on', $installed);

		(new ReviewNotice())->handle($user_id, $installed);

		if ( ! Utils::is_pro_plugin_active() ) {
			(new UpgradeNotice())->handle($user_id, $installed);
		}
	}
 
	public function handle_dismiss_actions() {
		if ( ! current_user_can('manage_options') || ! isset($_GET['rv_notice_action'], $_GET['notice_type']) ) return;

		$action = sanitize_text_field($_GET['rv_notice_action']);
		$type   = sanitize_text_field($_GET['notice_type']);
		$user_id = get_current_user_id();
		$meta_key = $type === 'review' ? '_rio_vizual_review_dismissed' : '_rio_vizual_upgrade_dismissed';
		$time_val = $type === 'review' ? time() + 7 * DAY_IN_SECONDS : time() + 15 * DAY_IN_SECONDS;

		update_user_meta($user_id, $meta_key, $action === 'dismiss' ? 'forever' : $time_val);

		wp_redirect(remove_query_arg(['rv_notice_action', 'notice_type']));
		exit;
	}
}

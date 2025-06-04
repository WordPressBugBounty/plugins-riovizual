<?php
namespace RioVizual\Notices;

class UpgradeNotice {
	public function handle($user_id, $installed) {
		if ( time() - $installed < 15 * DAY_IN_SECONDS ) return;

		$dismissed = get_user_meta($user_id, '_rio_vizual_upgrade_dismissed', true);
		if ( $dismissed === 'forever' || (is_numeric($dismissed) && time() < $dismissed) ) return;

		$upgrade_url = 'https://riovizual.com/pricing/';
		$remind_url  = esc_url(add_query_arg(['rv_notice_action' => 'remind_later', 'notice_type' => 'upgrade']));
		$dismiss_url = esc_url(add_query_arg(['rv_notice_action' => 'dismiss', 'notice_type' => 'upgrade']));

		echo '<div class="notice notice-info"><p><strong>Go Pro with RioVizual!</strong> Unlock more features and layouts.</p>';
		echo '<p><a href="' . esc_url($upgrade_url) . '" class="button button-primary" target="_blank">Upgrade Now</a> ';
		echo '<a href="' . $remind_url . '">Remind Me Later</a> ';
		echo '<a href="' . $dismiss_url . '" class="rv_noti_dismiss_btn"><span class="dashicons dashicons-dismiss"></span></a></p></div>';
	}
}

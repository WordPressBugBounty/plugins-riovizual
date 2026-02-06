<?php
namespace RioVizual\Notices;

class ReviewNotice {
	public function handle($user_id, $installed) {
		if ( time() - $installed < 7 * DAY_IN_SECONDS ) return;

		$dismissed = get_user_meta($user_id, '_rio_vizual_review_dismissed', true);
		if ( $dismissed === 'forever' || (is_numeric($dismissed) && time() < $dismissed) ) return;

		$rate_url   = 'https://wordpress.org/support/plugin/riovizual/reviews/?rate=5';
		$remind_url = esc_url(add_query_arg(['rv_notice_action' => 'remind_later', 'notice_type' => 'review']));
		$dismiss_url = esc_url(add_query_arg(['rv_notice_action' => 'dismiss', 'notice_type' => 'review']));

		echo '<div class="notice notice-info"><p><strong>Enjoying RioVizual?</strong> Let the community know!</p>';
		echo '<p><a href="' . esc_url($rate_url) . '" class="button button-primary" target="_blank">Leave a Review</a> ';
		echo '<a href="' . $remind_url . '">Remind Me Later</a> ';
		echo '<a href="' . $dismiss_url . '" class="rv_noti_dismiss_btn"><span class="dashicons dashicons-dismiss"></span></a></p></div>';
	}
}

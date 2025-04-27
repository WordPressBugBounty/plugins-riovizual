<?php
class Rio_Viz_Review_Notice {

	public function __construct() {
		add_action( 'admin_notices', [ $this, 'rio_viz_custom_admin_notice' ] );
		add_action( 'admin_init', [ $this, 'rio_viz_handle_notice_actions' ] );
	}

	public function rio_viz_custom_admin_notice() {
		global $pagenow;

		if ( $pagenow === 'plugins.php' || ! current_user_can('manage_options') ) return;

		$user_id = get_current_user_id();
		$installed = get_option('_rio_vizual_plugin_installed_on');

		if ( ! $installed ) {
			$installed = time();
			update_option('_rio_vizual_plugin_installed_on', $installed);
		}

		$this->riovizual_get_review_notice($user_id, $installed);

		// Show upgrade notice ONLY if Pro is NOT active
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		if ( ! is_plugin_active('riovizual-pro/riovizual-pro.php') ) {
			$this->riovizual_general_upgrade_notice($user_id, $installed);
		}
	}

	public function rio_viz_handle_notice_actions() {
		if ( ! current_user_can('manage_options') || ! isset($_GET['rv_notice_action']) || ! isset($_GET['notice_type']) ) return;

		$action = sanitize_text_field($_GET['rv_notice_action']);
		$type   = sanitize_text_field($_GET['notice_type']);
		$user_id = get_current_user_id();
		$meta_key = $type === 'review' ? '_rio_vizual_review_dismissed' : '_rio_vizual_upgrade_dismissed';
		$times = $type === 'review' ? time() + 7 * DAY_IN_SECONDS : time() + 15 * DAY_IN_SECONDS;

		if ( $action === 'remind_later' ) {
			update_user_meta($user_id, $meta_key, $times );
		} elseif ( $action === 'dismiss' ) {
			update_user_meta($user_id, $meta_key, 'forever');
		}

		wp_redirect(remove_query_arg(['rv_notice_action', 'notice_type']));
		exit;
	}

	public function riovizual_get_review_notice($user_id, $installed) {
		if ( time() - $installed < 7 * DAY_IN_SECONDS ) return;

		$dismissed = get_user_meta($user_id, '_rio_vizual_review_dismissed', true);
		if ( $dismissed === 'forever' || (is_numeric($dismissed) && time() < $dismissed) ) {
			return;
		}

		$rate_url   = 'https://wordpress.org/support/plugin/riovizual/reviews/?rate=5';
		$remind_url = esc_url(add_query_arg(['rv_notice_action' => 'remind_later', 'notice_type' => 'review']));
		$dismiss_url = esc_url(add_query_arg(['rv_notice_action' => 'dismiss', 'notice_type' => 'review']));

		echo '<div class="notice notice-info" style="position: relative;">';
		echo '<p><strong>Enjoying RioVizual?</strong> Let the WordPress community know with a quick review — it only takes a minute!</p>';
		echo '<p>';
		echo '<a href="' . esc_url($rate_url) . '" class="button button-primary" target="_blank">Leave a 5-Star Review</a> ';
		echo '<a href="' . $remind_url . '" style="margin-left:8px;">Remind Me Later</a> ';
		echo '<a href="' . $dismiss_url . '" class="rv_noti_dismiss_btn"><span class="dashicons dashicons-dismiss"></span></a>';
		echo '</p>';
		echo '</div>';
	}

	public function riovizual_general_upgrade_notice($user_id, $installed) {
		if ( time() - $installed < 15 * DAY_IN_SECONDS ) return;

		$dismissed = get_user_meta($user_id, '_rio_vizual_upgrade_dismissed', true);
		if ( $dismissed === 'forever' || (is_numeric($dismissed) && time() < $dismissed) ) {
			return;
		}

		$upgrade_url = 'https://riovizual.com/pricing/';
		$remind_url  = esc_url(add_query_arg(['rv_notice_action' => 'remind_later', 'notice_type' => 'upgrade']));
		$dismiss_url = esc_url(add_query_arg(['rv_notice_action' => 'dismiss', 'notice_type' => 'upgrade']));

		echo '<div class="notice notice-info" style="position: relative;">';
		echo "<p><strong>Go beyond the basics with RioVizual Pro </strong> — unlock advanced blocks, exclusive layouts, powerful customization, enhanced features, and priority support to level up your tables.</p>";
		echo '<p>';
		echo '<a href="' . esc_url($upgrade_url) . '" class="button button-primary" target="_blank">Upgrade to RioVizual Pro</a> ';
		echo '<a href="' . $remind_url . '" style="margin-left:8px;">Remind Me Later</a> ';
		echo '<a href="' . $dismiss_url . '" class="rv_noti_dismiss_btn"><span class="dashicons dashicons-dismiss"></span></a>';
		echo '</p>';
		echo '</div>';
	}
}
new Rio_Viz_Review_Notice();

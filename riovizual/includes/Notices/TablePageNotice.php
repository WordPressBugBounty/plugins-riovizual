<?php
namespace RioVizual\Notices;

class TablePageNotice {
	public function register() {
		add_action('admin_notices', [$this, 'show_notice']);
	}

	public function show_notice() {
		if ( isset($_GET['page']) && $_GET['page'] === 'riovizualTables' ) {
			echo '<div class="notice notice-success">';
			echo '<h2>🎉 New Way to Use Riovizual Tables!</h2>';
			echo '<p><em>We’re making it easier than ever to use our tables across all builders.</em></p>';
			echo '<p>' . wp_kses_post(
				"Start by clicking <strong>Add New Table</strong>, choose the block you need, and go!<br><br>
				Copy the <strong>shortcode</strong> and paste it in Elementor, Divi, Bricks and more.<br><br>
				📢 <strong>Gutenberg user?</strong> Try the block version for a smooth experience."
			) . '</p></div>';
		}
	}
}

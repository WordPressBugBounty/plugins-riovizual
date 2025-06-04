<?php

/**
 * You have access to two variables in this file: 
 * 
 * $module An instance of your module class.
 * $settings The module's settings.
 */

?>
<div class="fl-riovizual-table">
<?php

	$table_id = isset($settings->riovizual) ? absint($settings->riovizual) : 0;

	if($table_id > 0) {
		echo do_shortcode(sprintf('[riovizual id="%s"]', $table_id));
	}else{
		echo esc_html__( 'Please select the table.', 'riovizual' );
	}
?>
</div>

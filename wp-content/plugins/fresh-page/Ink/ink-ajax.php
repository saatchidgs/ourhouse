<?php

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER ['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
	require( dirname(__FILE__) . '/../../../../wp-config.php' );
	if (!(is_user_logged_in() && current_user_can('edit_posts')))
		die("Athentication failed!");

	if(isset($_GET["element"]) && isset($_GET["definition"]) && isset($_GET["value"])) {
		$wpdb->query("UPDATE ".$table_prefix.'ink'." SET ".urldecode($_GET["definition"])." = '".urldecode($_GET["value"])."' WHERE element = '".urldecode($_GET["element"])."' AND theme = '".get_option('template')."'");
		write_ink_definitions();
	}
	
	if(isset($_GET["option"]) && isset($_GET["value"])) {
		update_option($_GET["option"], $_GET["value"]);
	}
	
	if(isset($_GET["restore"]) && $_GET["restore"] == 'true') {
		ink_clean_install();
		canvas_check_theme();
	}
}
?>
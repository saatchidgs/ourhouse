<?php

require( dirname(__FILE__) . '/../../../../wp-config.php' );
if (!(is_user_logged_in() && current_user_can('edit_posts')))
	die("Athentication failed!");
	
$blockID = $_POST['block_id'];
//FlutterLayoutBlock::UpdateAllModulesSettings();
FlutterLayout::GetModuleSettings($blockID);
?>
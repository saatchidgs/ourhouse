<?php

require( dirname(__FILE__) . '/../../../../wp-config.php' );
if (!(is_user_logged_in() && current_user_can('edit_posts')))
	die("Athentication failed!");
	
$currentPage = $_POST['current_page'];
$pageLayoutSettings = $_POST['page_layout_settings'];
$modulesPositions = $_POST['modules_positions'];

//TODO
FlutterLayout::SaveLayoutSettings($pageLayoutSettings, $currentPage);
FlutterLayout::SaveModulesPositions($modulesPositions, $currentPage);

//echo "\nCurrent Page: $currentPage\n";
//echo "\nTODO - save the following settings: \n$pageLayoutSettings";
//echo "\nTODO - save the following modules positions: \n$modulesPositions";

?>
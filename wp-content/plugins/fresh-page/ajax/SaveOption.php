<?php

/**
 * Ajax request to save some Flutter option, the option should be
 * passed in a POST parameter whose name corressponds to the option
 * name.
 */
require( dirname(__FILE__) . '/../../../../wp-config.php' );
if (!(is_user_logged_in() && is_admin()))
	die("Athentication failed!");

include_once('../RCCWP_Options.php');
if($_POST)
	foreach($_POST as $key => $value) {
		$key = trim(urldecode($key));
		$value = trim(urldecode($value));
		//if($value == 1) $value = 'true';
		//	elseif($value == 0) $value = 'false';
		RCCWP_Options::Set($key, $value);
	}

?>
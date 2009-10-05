<?php
/* 
Plugin Name: WP-Ads
Version: 0.2
Plugin URI: http://thesandbox.wordpress.com/wpads/
Description: AdServer for WP
Author: Nick Brady
Author URI: http://thesandbox.wordpress.com/
*/ 

//error_reporting(E_ERROR | E_WARNING | E_PARSE);

include_once( 'wpads-class.php' );

/**
* Print the html code for a random banner of the given zone
*/
function wpads( $the_zone ) {
	print get_wpads( $the_zone );
}

/**
* Get the html code for a random banner of the given zone
*/
function get_wpads( $the_zone ) {
	global $doing_rss;    

	if( $the_zone == "" ) {
		return;
	}
	// No ads in RSS feeds
	if( $doing_rss ) {
		return;
	}	
	// are we in wp-admin editing the post? 
	if( strstr($_SERVER['PHP_SELF'], 'post.php') ) {
		// **TODO**: show placeholders
		return;
	}
	$banners = new Banners();
	$donate = get_option('wpads_donate');
	$theBanner = $banners->getZoneBanner( $the_zone, $donate );
	$banners->addView( $theBanner->banner_id );
	return $theBanner->banner_html;
}


/**
* Content filter: replaces all ocurrences of
* <!-- wpads#zone_name -->
* in the post content for a random banner for that zone
*/
function wpads_content_filter( $data ) {
	if( preg_match_all( "|<!--\s*wpads#(.*?)\s*-->|", $data, $matches ) ) {
		for($i=0;$i<count($matches[0]);$i++) {
			$banner = get_wpads( $matches[1][$i] );
			$data = preg_replace( "|".$matches[0][$i]."|", $banner, $data );
		}
	}
	return $data;
}

// WPAds Menu
add_action('admin_menu', 'wpads_menu');

function wpads_menu() {
	if (function_exists('add_submenu_page')) {
		add_submenu_page('options-general.php', __('WPAds'), __('WPAds'), 'edit_themes', 'wpads/wpads-options.php');
	}
}

if( function_exists('add_filter') ) {
	add_filter('the_content', 'wpads_content_filter'); 
}

?>
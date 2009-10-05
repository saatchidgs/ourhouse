<?php


// Check if user can edit themes
if(!current_user_can('edit_themes')) {
	die('Access Denied');
}

require_once( 'wpads-class.php' );
checkInstall();

$base_name = plugin_basename('wpads/wpads-options.php');
$options_url = $_SERVER[PHP_SELF] . '?page='.$base_name;

// $options_url = $_SERVER[PHP_SELF] . '?page=' . basename(__FILE__);

if ( isset($_REQUEST['action']) ) {
	$action = $_REQUEST['action'];
} else {
	$action = "";
}

switch( $action ) {
	case 'edit':
		showEdit();
		break;
	case 'edit2';
		updateBanner();
		showMainMenu();
		break;
	case 'new':
		showNewBanner();
		break;
	case 'new2':
		addBanner();
		showMainMenu();
		break;
	case 'delete';
		deleteBanner();
		showMainMenu();
		break;
	case 'donate';
		updateDonate();
		showMainMenu();
		break;
	default:
		showMainMenu();
}

/**
* Show the main options menu
*/
function showMainMenu() {
	global $options_url;
	
	$donate = get_option('wpads_donate');

	$bannersManager = new Banners();
	$banners = $bannersManager->getBanners();
	$zones = $bannersManager->getZones( $banners, $donate );
	
?>
<div class="wrap"> 
	<h2><?php _e('WPAds'); ?></h2> 

	<h3><?php _e('Banners'); ?> (<a href="<?php echo $options_url; ?>&amp;action=new"><?php _e('Add new'); ?></a>)</h3>
	<?php if( is_array( $banners ) ) { ?>
	<table border="0" cellpadding="3" width="100%">
		<tr>
			<th><?php _e('ID'); ?></th>
			<th align='left'><?php _e('Description'); ?></th>
			<th align="left"><?php _e('Zones'); ?></th>
			<th><?php _e('Active'); ?></th>
			<th><?php _e('Weight'); ?></th>
			<th><?php _e('Max views'); ?></th>
			<th><?php _e('Views served'); ?></th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
		</tr>
		<?php foreach( $banners as $banner ) { 
				$class = ('alternate' == $class) ? '' : 'alternate';
		?>
		<tr class='<?php echo $class; ?>'>
			<td><?php echo $banner->banner_id;?></td>
			<td><?php echo $banner->banner_description;?></td>
			<td><?php echo $banner->banner_zones;?></td>
			<td align="center"><?php echo $banner->banner_active;?></td>
			<td align="center"><?php echo $banner->banner_weight;?></td>
			<td align="center"><?php echo $banner->banner_maxviews;?></td>
			<td align="center"><?php echo $banner->banner_views;?></td>
			<td><a href="<?php echo $options_url; ?>&amp;action=edit&amp;id=<?php echo $banner->banner_id;?>" class="edit"><?php _e('Edit'); ?></a></td>
			<td><a href="<?php echo $options_url; ?>&amp;action=delete&amp;id=<?php echo $banner->banner_id;?>" class="delete"><?php _e('Delete'); ?></a></td>
		</tr>	
		<?php }  ?>
	</table>
	
	<a href="<?php echo $options_url; ?>&amp;action=new"><?php _e('Add new banner'); ?></a><br />
	<?php } else { ?>
		You have not defined any banners yet. <a href="<?php echo $options_url; ?>&amp;action=new">Add a new banner</a> to begin using WPAds.
	<?php } ?>

	<h3><?php _e('Zones'); ?></h3>
	<?php if( count( $zones ) > 0 ) { ?>
	<p>These are the zones you have defined in your banners. Next to each zone you can see all the <b>banners associated with that zone</b>, together with the <b>probability</b> of each banner in that zone. The third and fourth column give you the <b>code</b> you have to copy and paste in your templates or inside your posts, wherever you want the zone to show up</p>
	<table border="0" cellpadding="3" width="100%">
		<tr>
			<th align="left" valign="top"><?php _e('Zone'); ?></th>
			<th align="left"><?php _e('Banners'); ?> (<?php _e('Probability'); ?>)</th>
			<th align="left" valign="top"><?php _e('Code in templates'); ?></th>
			<th align="left" valign="top"><?php _e('Code in posts'); ?></th>
		</tr>
		<?php foreach( $zones as $zone ) { 
			$class = ('alternate' == $class) ? '' : 'alternate';
		?>
		<tr class='<?php echo $class; ?>'>
			<td valign="top"><?php echo $zone->zone_name; ?></td>
			<td>
				<?php foreach( $zone->banners as $banner ) { ?>
					<?php if( $banner->banner_description != "WPAds Support" ) { ?>
						<a href="<?php echo $options_url; ?>&amp;action=edit&amp;id=<?php echo $banner->banner_id;?>">
							<?php echo $banner->banner_description; ?> 
						</a> (<?php echo sprintf("%d", $banner->banner_probability); ?>%)
					<?php } else { ?>
						<font color="#f00"><?php echo $banner->banner_description; ?> (<?php echo sprintf("%d", $banner->banner_probability); ?>%)</font>
					<?php } ?>
				<br/>
				<?php } ?>
			</td>
			<td valign="top">
				&lt;?php wpads('<?php echo $zone->zone_name; ?>'); ?&gt;<br />
			</td>
			<td valign="top">
				&lt;!--wpads#<?php echo $zone->zone_name; ?>--&gt;
			</td>
		</tr>
		<?php } ?>
	</table>
	<?php } else { ?>
	There are no zones because you have not defined any banners yet.
	<?php } ?>
<br /><br />
	<h3>Support this plugin</h3>
	<p>If you want to support the development of this plugin, we would appreciaty if you consider <b>donating some of your ad impressions to us</b>.<br/>
	If you check this option, <b>3% of you Google AdSense impressions will include our AdSense client-id</b> 
	(in case you're wondering, this is not against <a href="https://www.google.com/adsense/localized-terms?hl=en_US" target="_blank">Google AdSense Terms and Conditions</a>).
	This change will be completely transparent to your readers, who will not see anything different on your blog.<br />
	By checking this option, you will reward us for our efforts and help us release new versions. Of course, if you decide you don't want to
	donate, you're welcome to do so and this plugin will still be fully functional.<br/>
	</p>
	<form name="donate" method="post" action="<?php echo $options_url; ?>">
		<input type="hidden" name="action" value="donate" />
		<input name="wpads_donate" type="checkbox" value="Y" <?php echo ($donate > 0 ? "checked" : "");?> />
		Yes, I want to donate 3% of my ad impressions to the developers of this plugin<br />
		<input type="submit" name="submit" value="<?php _e('Update'); ?>" />
	</form>
		
</div>

<?php
} // function showMainMenu


/**
* Show the banner edit page
*/
function showEdit() {
	global $options_url;

	if ( isset($_REQUEST['id']) ) {
		$banner_id = $_REQUEST['id'];
	} else {
		$banner_id = "";
	}
	$bannersManager = new Banners();
	$banner = $bannersManager->getBanner( $banner_id );
?>
<div class="wrap"> 
	<h2><?php _e('WPAds - Edit banner'); ?></h2> 
	
	<form name="banner_edit" method="post" action="<?php echo $options_url; ?>">
	<input type="hidden" name="action" value="edit2" />
	<input type="hidden" name="banner_id" value="<?php echo $banner->banner_id;?>" />
	<table cellspacing="3">
		<tr>
			<td valign="top">ID</td>
			<td><?php echo $banner->banner_id;?></td>
		</tr>
		<tr>
			<td valign="top">Description</td>
			<td>
				<input name="banner_description" type="text" size="50" value="<?php echo htmlentities($banner->banner_description);?>" /><br />
				Any text that helps you identify this banner
			</td>
		</tr>
		<tr>
			<td valign="top">HTML Code</td>
			<td>
				<textarea name="banner_html" rows="6" cols="80"><?php echo htmlentities($banner->banner_html);?></textarea><br />
				Copy and paste the HTML code to show the ad (for example, the Google AdSense code)
			</td>
		</tr>
		<tr>
			<td valign="top">Zones</td>
			<td>
				<input name="banner_zones" type="text" size="50" value="<?php echo $banner->banner_zones;?>" /><br/>
				Enter names of the zones where this banner will show, separated by commas. Example: <em>sidebar1, sidebar2</em>
			</td>
		</tr>
		<tr>
			<td valign="top">Active</td>
			<td>
				<input name="banner_active" type="checkbox" value="Y" <?php echo ($banner->banner_active == "Y" ? "checked" : "");?> />
			</td>
		</tr>
		<tr>
			<td valign="top">Weight</td>
			<td>
				<input name="banner_weight" type="text" size="10" value="<?php echo $banner->banner_weight;?>" /><br />
				Sets how much a banner is displayed in relationship with other banners in the same zone. Default: 1
			</td>
		</tr>
		<tr>
			<td valign="top">Max views</td>
			<td>
				<input name="banner_maxviews" type="text" size="10" value="<?php echo $banner->banner_maxviews;?>" /><br />
				Maximum number of times this banner will be shown. Default: 0 (unlimited views)
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="submit" value="<?php _e('Save'); ?>" /></td>
		</tr>
	</table>		
	
	</form>
</div>
<?php
} // function showEdit

/**
* Update a banner, gets the input from the "edit" form
*/
function updateBanner() {
	global $options_url;

	$banner = array();
	$banner["banner_id"] = $_REQUEST["banner_id"];
	$banner["banner_description"] = $_REQUEST["banner_description"];
	$banner["banner_html"] = $_REQUEST["banner_html"];
	$banner["banner_zones"] = $_REQUEST["banner_zones"];
	$banner["banner_active"] = $_REQUEST["banner_active"];
	$banner["banner_weight"] = $_REQUEST["banner_weight"];
	$banner["banner_maxviews"] = $_REQUEST["banner_maxviews"];
	if (get_magic_quotes_gpc()) {
		foreach( $banner as $key => $value ) {
			$banner[$key] = stripslashes( $value );
		}
   	} 
   	
	$bannersManager = new Banners();
	$banners = $bannersManager->updateBanner( $banner );

	echo '<div id="message" class="updated fade"><p>Banner updated</p></div>';
}

/**
* Show the new banner page
*/
function showNewBanner() {
	global $options_url;

?>
<div class="wrap"> 
	<h2><?php _e('WPAds - New banner'); ?></h2> 
	
	<form name="banner_edit" method="post" action="<?php echo $options_url; ?>">
	<input type="hidden" name="action" value="new2" />
	<table cellspacing="3">
		<tr>
			<td valign="top">Description</td>
			<td>
				<input name="banner_description" type="text" size="50" value="" /><br />
				Any text that helps you identify this banner
			</td>
		</tr>
		<tr>
			<td valign="top">HTML Code</td>
			<td>
				<textarea name="banner_html" rows="6" cols="80"></textarea><br />
				Copy and paste the HTML code to show the ad (for example, the Google AdSense code)
			</td>
		</tr>
		<tr>
			<td valign="top">Zones</td>
			<td>
				<input name="banner_zones" type="text" size="50" value="" /><br/>
				Enter names of the zones where this banner will show, separated by commas. Example: <em>sidebar1, sidebar2</em>
			</td>
		</tr>
		<tr>
			<td valign="top">Active</td>
			<td>
				<input name="banner_active" type="checkbox" value="Y" checked />
			</td>
		</tr>
		<tr>
			<td valign="top">Weight</td>
			<td>
				<input name="banner_weight" type="text" size="10" value="1" /><br />
				Sets how much a banner is displayed in relationship with other banners in the same zone. Default: 1
			</td>
		</tr>
		<tr>
			<td valign="top">Max views</td>
			<td>
				<input name="banner_maxviews" type="text" size="10" value="0" /><br />
				Maximum number of times this banner will be shown. Default: 0 (unlimited views)
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="submit" value="<?php _e('Save'); ?>" /></td>
		</tr>
	</table>		
	
	</form>
</div>
<?php
} // function showNewBanner

/**
* Add a banner, gets the input from the "new banner" form
*/
function addBanner() {
	global $options_url;

	$banner = array();
	$banner["banner_id"] = $_REQUEST["banner_id"];
	$banner["banner_description"] = $_REQUEST["banner_description"];
	$banner["banner_html"] = $_REQUEST["banner_html"];
	$banner["banner_zones"] = $_REQUEST["banner_zones"];
	$banner["banner_active"] = $_REQUEST["banner_active"];
	$banner["banner_weight"] = $_REQUEST["banner_weight"];
	$banner["banner_maxviews"] = $_REQUEST["banner_maxviews"];
	if (get_magic_quotes_gpc()) {
		foreach( $banner as $key => $value ) {
			$banner[$key] = stripslashes( $value );
		}
   	} 
	
	$bannersManager = new Banners();
	$banners = $bannersManager->addBanner( $banner );

	echo '<div id="message" class="updated fade"><p>Banner added</p></div>';
}

/**
* Delete a banner
*/
function deleteBanner() {
	global $options_url;

	$banner_id = $_REQUEST["id"];
	
	if( $banner_id > 0 ) {
		$bannersManager = new Banners();
		$banners = $bannersManager->deleteBanner( $banner_id );
	
		echo '<div id="message" class="updated fade"><p>Banner deleted</p></div>';
	}
}

/**
* Update donate option
*/
function updateDonate() {
	global $wpdb;

	$wpads_donate = $_REQUEST["wpads_donate"];
	if( $wpads_donate == "Y" ) {
		update_option( 'wpads_donate', '3' );
		echo '<div id="message" class="updated fade"><p>Option updated. <font color="#f00"><strong>Thanks for your support!</strong></font></p></div>';
	} else {
		update_option( 'wpads_donate', '0' );
		echo '<div id="message" class="updated fade"><p>Option updated</p></div>';
	}
}

/**
* Check if WPAds is installed
*/
function checkInstall() {
	global $wpdb;
	global $table_prefix;		
	
	$version = get_option('wpads_version');
	if( $version == "" ) {
		$sql = <<<SQL
			CREATE TABLE `{$table_prefix}ads_banners` (
			  `banner_id` bigint(20) NOT NULL auto_increment,
			  `banner_active` char(1) NOT NULL default '',
			  `banner_description` varchar(255) NOT NULL default '',
			  `banner_html` mediumtext NOT NULL,
			  `banner_weight` int(11) NOT NULL default '0',
			  `banner_zones` varchar(255) NOT NULL default '',
			  `banner_maxviews` bigint(20) NOT NULL default '0',
			  `banner_views` bigint(20) NOT NULL default '0',
			  PRIMARY KEY  (`banner_id`),
			  KEY `banner_zones` (`banner_zones`)
			) ENGINE=MyISAM ;
SQL;
		$wpdb->query( $sql );
		add_option( 'wpads_version', '0.1', 'WPAds version number', 'yes' );
		add_option( 'wpads_donate', '0', 'WPAds support developers', 'yes' );
	}
}

?>
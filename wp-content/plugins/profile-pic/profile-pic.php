<?php
/*
Plugin Name: Profile Pic
Plugin URI: http://geekgrl.net/wordpress/wordpress-profile-pic-plugin/
Description: Allows authors to add a picture to their profile and automates the process of displaying profiles. Highly configurable via plugin and widget settings. Like the plugin? Show your support with a small <a href="http://geekgrl.net/wordpress/wordpress-profile-pic-plugin/">donation</a> and/or by <a href="http://wordpress.org/extend/plugins/profile-pic/">rating the plugin on wordpress.org</a>.
Version: 0.9.2
Author: Hannah Gray
Author URI: http://geekgrl.net
*/

// Add actions to appropriete hooks
register_activation_hook(__FILE__,'profilepic_internal_init');
add_action('show_user_profile', 'profilepic_gui_upload');
add_action('edit_user_profile', 'profilepic_gui_upload4admin');
add_action('profile_update','profilepic_internal_upload',1);
add_action('admin_menu', 'profilepic_config');
add_action('plugins_loaded', 'profilepic_widget_init');

//filter
// return apply_filters('get_avatar', $avatar, $id_or_email, $size, $default, $alt);


$profilepic_options = get_option("profilepic_options");
if ($profilepic_options['templateoverride'] == "1") {
	add_filter('author_template', 'profilepic_internal_authortemplate');
}

if ($profilepic_options['avataroverride'] == "yes") {
	add_filter('get_avatar', 'profilepic_internal_getavatar', 1,5);
}

if (function_exists('add_shortcode')) {
	add_shortcode('printprofile', 'profilepic_gui_printprofile');
	add_shortcode('printprofilepic', 'profilepic_gui_printprofilepic');
}

// 'global' variable - easier to define only once
$profilepic_datafields = array('First Name' => 'first_name', 'Last Name' => 'last_name', 'Nick Name' => 'nick_name', 'Email' => 'email', 'Web Site' => 'website', 'AIM' => 'im_aim', 'Yahoo IM' => 'im_yahoo', 'Jabber' => 'im_jabber', 'Bio' => 'bio');
$profilepic_fieldscodex = array (
'first_name' => 'first_name', 'last_name' => 'last_name', 'nick_name' => 'nickname', 'email' => 'user_email', 'website' => 'user_url', 'im_aim' => 'aim', 'im_yahoo' => 'yim', 'im_jabber' => 'jabber', 'bio' => 'user_description');


// legacy fnx mapping for backwards compatability w/older profile pic template tags
function author_gravatar_tag($authorID, $tags = '') {
	profilepic_internal_gravatar($authorID, $tags);
}
function author_image_tag($authorID, $tags = '', $display = true) {
	profilepic_internal_imagetag($authorID, $tags, $display);
}
function author_image_dimensions($path, $dimension, $display = false) {
	profilepic_internal_fingerdimensions($path, $dimension, $display);
}
function author_image_path($authorID, $display = true, $type = 'url'){
	profilepic_internal_picpath($authorID, $display, $type);
}


//*** GUI FUNCTION: add menu item for plugin config to Options page
function profilepic_config() {
	global $wpdb;
	if (function_exists('add_options_page')){
		add_options_page('Profile Pic', 'Profile Pic', 8, __FILE__, 'profilepic_gui_configpage');
	}
}


//*** GUI FUNCTION: Show config form
function profilepic_gui_configpage() {
	$profilepic_options = get_option("profilepic_options");
	global $user_ID;

	// if submit was pressed, process config data
	if ( isset($_POST['submit']) ) {
		// check user permissions
		if ( !current_user_can('manage_options') ) {
			die(__('Cheatin&#8217; huh?'));
		// if okay, store data
		} else {
		
			$profilepic_options['donated'] = $_POST['donated'];
			$profilepic_options['avataroverride'] = $_POST['avataroverride'];
			$profilepic_options['blogmode'] = $_POST['blogmode'];
			$profilepic_options['templateoverride'] = $_POST['templateoverride'];
			$profilepic_options['extensions'] = (isset($_POST['image_extensions']) ? strtolower($_POST['image_extensions']) : '');
			$profilepic_options['dir'] = (isset($_POST['image_dir']) ? $_POST['image_dir'] : '');
			$profilepic_options['gravatar_width'] = (isset($_POST['gravatar_width']) ? $_POST['gravatar_width'] : '');
			update_option('profilepic_options', $profilepic_options);
		?>
	<div id="message" class="updated fade" style="background-color: rgb(255, 251, 204);">
		<p><strong>Profile Pic Plugin Options Updated.</strong></p>
	</div>
		<?php
		}
	}
	
?>
	<div class="wrap">
	<h2>Profile Pic Plugin Global Options</h2>			
		<form action="" method="post" id="profilepic_config" style="margin: auto;">

<?php if ($profilepic_options['donated'] != "yes") { ?>
		<h3>Donate</h3>
		<p>Do you like this plugin? If so, please show some love! I'm a freelance web developer and student, a little support (just a dollar or two even) will go a long way in allowing me to put time into maintaining and improving this plugin.  <a href='http://geekgrl.net/wordpress/wordpress-profile-pic-plugin/'>Click here to donate now</a>. You can also help others find out about Profile Pic by blogging about it on your own blog, and/or by <a href="http://wordpress.org/extend/plugins/profile-pic/">rating it on wordpress.org</a> </p>
		<p><b><label>Have you donated: </label></b><input type="radio" name="donated" value="yes" <?php echo ($profilepic_options['donated'] == "yes") ? 'checked ' : "" ; ?>/> Yes &nbsp; &nbsp; &nbsp; <input type="radio" name="donated" value="no" <?php echo ($profilepic_options['donated'] == "no") ? 'checked ' : ""; ?>/> No <br/> This is an honor code kind of thing... by selecting yes, you get rid of the relatively mild nag here and on the profile editing page.  </p>
<?php } ?>		
		
		<h3>General Stuff</h3>
		<p><b><label>'Gravatar'/Avatar Override: </label></b><input type="radio" name="avataroverride" value="yes" <?php echo ($profilepic_options['avataroverride'] == "yes") ? 'checked ' : "" ; ?>/> Yes &nbsp; &nbsp; &nbsp; <input type="radio" name="avataroverride" value="no" <?php echo ($profilepic_options['avataroverride'] == "no") ? 'checked ' : ""; ?>/> No<br />
		Do you want the profile picture to be used in place of the standard gravatar/avatar in comments and elsewhere within the blog?</p>
		<p><b><label>Blog Mode: </label></b><input type="radio" name="blogmode" value="single" <?php echo ($profilepic_options['blogmode'] == "single") ? 'checked ' : "" ; ?>/> Single Author &nbsp; &nbsp; &nbsp; <input type="radio" name="blogmode" value="multiple" <?php echo ($profilepic_options['blogmode'] == "multiple") ? 'checked ' : ""; ?>/> Multiple Authors<br />
		The profile pic plugin will attempt to figure out the correct setting on its own.  If it guessed wrong, use this setting to override. </p>
		<p><b><label>Author Template Override: </label></b><input type="radio" name="templateoverride" value="1" <?php echo ($profilepic_options['templateoverride'] == "1") ? 'checked ' : "" ; ?>/> Yes &nbsp; &nbsp; &nbsp; <input type="radio" name="templateoverride" value="0" <?php echo ($profilepic_options['templateoverride'] == "0") ? 'checked ' : "" ; ?>/> No<br />
		If yes, when viewers click on an author link (yours is <a href="<?php echo get_bloginfo('url') ."/?author=" . $user_ID; ?>"><?php echo get_bloginfo('url') ."/?author=" . $user_ID; ?></a>) they will be taken to the standard profile page generated by Profile Pic.  Consequences: if you don't use a standard-ish 'kubric' style theme, then this page will likely not look very purdy.  </p>

		<h3>Advanced Stuff</h3>
		<p><b>Misconfiguration of the following options will cause Profile Pics to break!  Use with caution!</b></p>
		<p><b><label>Profile Pics Upload Directory: * </label></b><input size="45" name='image_dir' value='<?php echo($profilepic_options['dir']); ?>' style="font-family: 'Courier New', Courier, mono;" /><br />
		Recommended: wp-content/plugins/profile-pic/pics  &nbsp; *must be set to chmod 777 </p>

		<p><b><label>Allowed File Extensions: </label></b><input size="45" name='image_extensions' value='<?php echo(($profilepic_options['extensions'] == "") ? 'png gif jpg' : $profilepic_options['extensions']); ?>' style="font-family: 'Courier New', Courier, mono;" /><br />
		Seperate each three digit extension with a space; field is NOT case-sensitive</p>
		
		<p><b><label>Standard Width for Comment Author "Gravatar": ** </label></b><input size="45" name='gravatar_width' value='<?php echo(($profilepic_options['gravatar_width'] == "") ? '80' : $profilepic_options['gravatar_width']); ?>' style="font-family: 'Courier New', Courier, mono;" /><br />
		Width in px  ** DEPRECATED, only applicable with old templates using pre- WP 2.7 comment template tags (and/or profile pic template tags described in the readme)
		
		
		<p class="submit"><input type="submit" name="submit" value="<?php echo('Update Settings&raquo;'); ?>" /></p>
		</form>
		</div>
<?php

}

// make sure admins can add pics for other users as well
function profilepic_gui_upload4admin() {
	profilepic_gui_upload(TRUE); 
}


//*** GUI FUNCTION: displays "add picture" box when editing your profile
function profilepic_gui_upload($isAdmin = FALSE) {
	global $profilepic_datafields, $wp_version, $user_ID;
    
    $datafields = $profilepic_datafields;
    
    if (isset($_GET['user_id'])) {
	    if ($_GET['user_id'] == $user_ID) {
	    	//we're editing our own profile
	    	$profile_id = $user_ID;
	    } else {
	    	$profile_id = $_GET['user_id'];
	    }
	} else {
		$profile_id = $user_ID;
	}
	    
    
    $profilepic_displayoptions = get_option('profilepic_displayoptions_'.$profile_id);
    $profilepic_options = get_option("profilepic_options");

	// build extension check string for the js
	$extensions_array = explode(' ', $profilepic_options['extensions']);
	$checkstr = "";
	foreach ($extensions_array as $count => $exe) {
		$checkstr .= "(ext != '.$exe') && ";
	}
	$checkstr = rtrim($checkstr, ' && ');
	
	$directory = profilepic_internal_cleanpath(ABSPATH . '/' . $profilepic_options['dir']);
	
	// check permissions of pics dir and change if needed
	if (!file_exists($directory)) {
		$directory_exists = false;
	} else {
		if (substr(sprintf('%o', fileperms($directory)), -4) != "0777") { 
			$directory_is_writable = false;
		} else {
			$directory_is_writable = true;
		}
		$directory_exists = true;
	}
	
	$checkbox_string = '';
	
	//DEBUG
	// echo "<pre>datafields:" . print_r($profilepic_datafields, true) . "\r\r stored options:" . print_r($profilepic_displayoptions, true) . "</pre>";
	
	// prep checkbox display
	foreach ($datafields as $fieldname => $fieldid) {
		if ($profilepic_displayoptions[$fieldid] == 1) { $checked = " CHECKED "; } else { $checked = ''; }
		$checkbox_string .= "<label><input type='checkbox' name='profilepic_displayoptions[]' value='".$fieldid."'".$checked."/> ".$fieldname."  </label><br />";
	}
		
	// HTML GUI, js changes form encoding and adds error check
		// error checking fix v 0.2 -- thanks to http://serge-rauber.fr serge@kalyx.fr
		// heads up on how 2.7 breaks this -- thanks to Eric Simon 
	?>
		<script type="text/javascript" language="javascript">
		<!--
		function uploadPic() {
			var upload = jQuery("#profilepicture").val();
			upload = upload.toLowerCase();
			var ext = upload.substring((upload.length-4),(upload.length));
			if (<?php echo $checkstr ?>){
				alert('Please upload an image with one of the following extentions: <?php echo($profilepic_options['extensions']); ?>');
			}
			else {
				jQuery("#your-profile").attr('encoding','multipart/form-data');
			}
		}
		
		jQuery(document).ready(function(){
			jQuery("#picprofileinfo_link").click(function(){
				jQuery("#picprofileinfo_box").show("slow");
				return false;
			});
		});

		//-->
		</script>
		
<?php if (version_compare($wp_version, '2.5.dev') <= 0) { ?>
		<h3><?php echo("Profile Picture"); ?></h3>
<?php } else { ?>
	<fieldset><legend><?php echo("Profile Picture"); ?></legend>
<?php }  ?>
	<table class="form-table">
<?php if (!$directory_exists) { ?>
		<tr valign="top">
			<th scope="row" colspan=2><div class="error" style="padding: 5px;"><b>Warning:</b> the picture storage directory does not exist.  Use your FTP client or a command line interface to create the picture directory (<?php echo $profilepic_options['dir']; ?>), and chmod it to 777 to resolve this problem. </div></th>
		</tr>
<?php } ?>
<?php if (!$directory_is_writable) { ?>
		<tr valign="top">
			<th scope="row" colspan=2><div class="error" style="padding: 5px;"><b>Warning:</b> the picture storage directory is not writable by the server.  Picture upload will fail.  Use your FTP client or a command line interface to chmod the picture directory (<?php echo $profilepic_options['dir']; ?>) to 777 to resolve this problem.</div></th>
		</tr>
<?php } 
$pic = profilepic_internal_picpath($profile_id, false);
?>
		<tr valign="top">
			<th scope="row">Current: </th>
			<td><img src="<?php echo $pic; ?>" width="150" /><br />
<?php
	$array = explode('default.jpg', $pic);
	if (count($array)<2) {
?>
			Delete? <input type="checkbox" id="deletepicture" name="deletepicture">
<?php	} ?>
		</td>
		</tr>
		<tr valign="top">
			<th scope="row">Upload a new picture: </th>
			<td><label><input type="file" name="profilepicture" id="profilepicture" onchange="uploadPic();" /></label></td>
		</tr>
		<tr valign="top">
			<th scope="row">What profile information do you want to make public?<br />
			<a id="picprofileinfo_link" href="#">[click for more info]</a>
			<div style="display:none; font-weight:normal; font-size: 10px; background-color: #d8dbf2; border: 1px solid #bdd0d5; margin: 4px 0; padding: 4px;" id="picprofileinfo_box">these settings apply to the profile displayed wherever you use the shortcode [printprofile], template tag &lt;? printprofile(); ?&gt; or on the Author page (if your theme supports the Author page, an/or if you have the Author theme page over-ride activated in the Profile Pic options page).</div></th>
			<td>
			<div style="float:left; width: px;">
			<?php echo $checkbox_string; ?>
			<br/>Note that all email addresses, jabber handle &amp; yahoo IM  will be spam-bot proofed before they are displayed.

			</div>
			</td>
		</tr>
<?php if ($profilepic_options['donated'] != "yes") { ?>
	<tr valign="top">
			<th scope="row"><span style="font-size: 9px; color: #666; font-weight: bold;">Do you like the profile picture plugin?</span><br /></th>
			<td  valign="top" style="font-size: 9px; color: #666;">
If yes, please show some love! I'm a freelance web developer and student, a little support (just a dollar or two even) will go a long way in allowing me to put time into maintaining and improving this plugin.  <b><a href='http://geekgrl.net/wordpress/wordpress-profile-pic-plugin/'>Click here to donate now</a></b>. You can also help others find out about Profile Pic by blogging about it on your own blog, and/or by <a href="http://wordpress.org/extend/plugins/profile-pic/">rating it on wordpress.org</a>. <b>Go to Profile Pic options to remove this nag!</b>
			</td>
		</tr>
<?php } ?>		
	</table>
<?php if (version_compare($wp_version, '2.5.dev') <= 0) : ?>
	</fieldset>
<?php endif; ?>

	<?php
}

function profilepic_internal_init() {
	/*
	// for future versions
	global $wpdb;
	
	$table_name = $wpdb->prefix . "profilepicdata";
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "CREATE TABLE " . $table_name . " (
			  id mediumint(9) NOT NULL,
			  displaydata blob NOT NULL,
			  UNIQUE KEY id (id)
			);";
	}	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
	*/
		
	$plugin_data = get_plugin_data(WP_PLUGIN_DIR.'/profile-pic/profile-pic.php');
	
	// check for legacy profile pic options and migrate as needed
	$legacy_options = get_option("profile_picture_options");
	if (isset($legacy_options['image_dir'])) {
		// legacy options detected, migrate now
		$old_dir			= $legacy_options['image_dir'];
		$old_extensions		= $legacy_options['image_extensions'];
		$old_gravatar_width	= $legacy_options['gravatarwidth'];
		delete_option('profile_picture_options');
	}
	
	// check for current version, if none then create
	$profilepic_options = get_option("profilepic_options");
	if (!isset($profilepic_options['dir'])){   
		// define defaults
		$dir = (isset($old_dir)) ? $old_dir : '/wp-content/profile-pics/';
		$extensions = (isset($old_extensions)) ? $old_extensions : 'gif png jpg';
		$profilepic_options['gravatar_width'] = (isset($old_gravatar_width)) ? $old_gravatar_width : "80";
		$blogmode = (count(get_editable_user_ids()) > 1 ) ? "multiple" : "single";
		$profilepic_options = array(
			'donated'			=> 'no',
			'avataroverride'	=> 'yes',
			'dir'				=> $dir,
			'extensions'		=> $extensions,
			'gravatar_width'	=> $gravatar_width,
			'templateoverride'	=> 1,
			'blogmode'			=> $blogmode,
			'version'			=> $plugin_data['version']
		);
		add_option('profilepic_options', $profilepic_options);
		
		// check if dir exists, create if needed
		if (!file_exists(profilepic_internal_cleanpath(ABSPATH . '/' . $dir))) {
			@mkdir(profilepic_internal_cleanpath(ABSPATH . '/' . $dir), '0777');
		}
		
		// check permissions of pics dir and change if needed
		$directory = profilepic_internal_cleanpath(ABSPATH . '/' . $dir);
		if (substr(sprintf('%o', fileperms($directory)), -4) != "0777") { 
			@chmod($directory, 0777); 
		}
	}
}

//*** INTERNAL FUNCTION: stores user dat on what they want the plugin to show on the profile page
function profilepic_internal_storeuserdisplaysettings($settings, $option_name, $pic_filename = 'DEFAULT') {
	global $profilepic_datafields;
	
	$datafields = $profilepic_datafields;
	
	// checkbox data only exists for the boxes that were checked; but we really want an array of all checkboxes w/true or false data.  this function accomplishes that. 
	
	$profile_data = array();

	foreach ($datafields as $key => $val) {
		$profile_data[$val] = isset($settings[$val]) ? 1 : 0;  
	} 
	
	//echo print_r($profile_data);
	
	$profile_data['filename'] = $pic_filename;
	
	update_option($option_name, $profile_data);
}

//*** INTERNAL FUNCTION: stores pic submitted via profile editing page
function profilepic_internal_upload() {
	$profile_id = $_POST['user_id'];
	
	$profilepic_options = get_option("profilepic_options");
	
	if ($_POST['deletepicture']) {
		// physically delete picture
		$extensions_array = explode(' ', $profilepic_options['extensions']);
	foreach ($extensions_array as $image_extension) {
		$old_pic_path = profilepic_internal_cleanpath(ABSPATH . '/' . $profilepic_options['dir'] . '/' . $profile_id . '.' . $image_extension);
		if ( file_exists($old_pic_path) ) { 
			unlink($old_pic_path);
		}
	}
		// load default into settings DB
		$filename = 'DEFAULT';
		
	}
	

	// store image 
	$raw_name = (isset($_FILES['profilepicture']['name'])) ? $_FILES['profilepicture']['name'] : "";	
	// if file was sumbitted, continue
	if ($raw_name != "") {
		// delete previous image if it's there
		$extensions_array = explode(' ', $profilepic_options['extensions']);
		foreach ($extensions_array as $image_extension) {
			$old_pic_path = profilepic_internal_cleanpath(ABSPATH . '/' . $profilepic_options['dir'] . '/' . $profile_id . '.' . $image_extension);
			if ( file_exists($old_pic_path) ) { 
				unlink($old_pic_path);
			}
		}

		// build the path and filename 		
		$clean_name = ereg_replace("[^a-z0-9._]", "", ereg_replace(" ", "_", ereg_replace("%20", "_", strtolower($raw_name))));
		$fileext = substr(strrchr($clean_name, "."), 1);
		$directory = profilepic_internal_cleanpath(ABSPATH . '/' . $profilepic_options['dir']);
		$file_path = profilepic_internal_cleanpath($directory . '/' . $profile_id . '.' . $fileext);
		
		// store file
		move_uploaded_file($_FILES['profilepicture']['tmp_name'], $file_path);
	
		// Set correct file permissions -- some servers make img's 777
		$stat = @ stat($file_path);
		$perms = $stat['mode'] & 0007777;
		$perms = $perms & 0000666;
		@ chmod($file_path, $perms);	 
	
	$filename = isset($fileext) ? $profile_id . '.' . $fileext : '';

	}

	// store form data 
	$raw_data = $_POST['profilepic_displayoptions'];
	$reversed_data = array();
	foreach ($raw_data as $key => $val) {
		$reversed_data[$val] = $key;
	}

	profilepic_internal_storeuserdisplaysettings($reversed_data, 'profilepic_displayoptions_'.$profile_id, $filename);


}

//*** GUI FUNCTION: displays profile & pic 
function profilepic_gui_printprofile($atts) {
	
	$settings = shortcode_atts( array(
   'callmethod' => 'shortcode',
   'userid'		=> ''
   ), $atts );
	
	global $profilepic_datafields, $profilepic_fieldscodex, $wp_query;
	$author_id = $wp_query->post->post_author;
	$author_data = get_userdata(intval($author_id));
	$datafields = $profilepic_datafields;
	
	//if calling via shortcode use user prefs, otherwise use widget prefs
	if ($settings['callmethod'] == 'shortcode') {
		$author_prefs = get_option('profilepic_displayoptions_'.$author_id);
	} else {
		$author_prefs = get_option('profilepic_widget_displayoptions');
	}
	
	$profile = "<div id='profilepic_profile'>";

	// add picture
	if ($settings['callmethod'] == 'widget') {
		$widgetoptions = get_option('profilepic_widget_options');
		$profile .= '<img src="' . profilepic_internal_picpath($author_id, false, 'url') . '" width=' . $widgetoptions['width'] . ' id="authorpic" />';
	} else {
		$profile .= profilepic_internal_imagetag($author_id, 'align=right', false);
	}
	// debug
	/*
	$profile .= '<pre>' . print_r($datafields, true) . '\r' .
				print_r($profilepic_fieldscodex) .
				print_r($author_data, true) . 
				print_r($author_prefs, true) .
				print_r($profilepic_fieldscodex) . '</pre>';
	*/

	// $profile .= '<pre>' . print_r($author_data, true) . '</pre>';	
	// need to check to if there is an option for the the data, check if the option allows for the the data to be shown, and then check to see if the data exists
	

	// first 3 fields are name data - figure out which ones exist and are allowed to be displayed				
	$tick = 0;		
	foreach ($datafields as $name => $field) {	
		// does option data exist?
		if ($tick < 3) {
			if ( (isset($author_prefs[$field])) && ($author_prefs[$field] == 1) && (isset($author_data -> $profilepic_fieldscodex[$field])) ) {
				$authorname[$field] = $author_data -> $profilepic_fieldscodex[$field];
			}
			$tick++;
			// now that we've examined the field, trim it from field array
			array_shift($datafields);
		}
	}
	
	
	// if the above logic indicates we have a name to display, figure out how to format it
	if (count($authorname) > 0) {
		$namestring = (isset($authorname['first_name'])) ? $authorname['first_name'] : '';
		$$namestring .= (isset($authorname['last_name'])) ? ' ' . $authorname['last_name'] : '';
		if (isset($authorname['nick_name'])  && (isset($authorname['last_name']) || isset($authorname['first_name'])) ) {
			$namestring .= ', aka "'.$authorname['nick_name'].'"';
		} else if (isset($authorname['nick_name'])) {
			$namestring = $authorname['nick_name'];
		}
	}
	
	$profiledata = array();
	
	if ($namestring != '') {
		$profiledata['Name'] = $namestring;
	} 
	
	// now run the logic on the rest of the variables
	foreach ($datafields as $name => $field) {	
		// does option data exist?
		if (isset($author_prefs[$field])) {
			// does option allow for data to be displayad?
			if ($author_prefs[$field] == 1) {
				// does the data actually exist?
				if (isset($author_data -> $profilepic_fieldscodex[$field]) && $author_data -> $profilepic_fieldscodex[$field] != '') {
					$profiledata[$name] = $author_data -> $profilepic_fieldscodex[$field];
				}
			}
		}
	}
	
	if (isset($profiledata['Web Site'])) {		
		if ($profiledata['Web Site'] == 'http://' || $profiledata['Web Site'] == '') {
			unset($profiledata['Web Site']);
		} else {
			$profiledata['Web Site'] = "<a href='".$profiledata['Web Site']."'>".$profiledata['Web Site']."</a>";
		}
	}
	
	if (isset($profiledata['Email'])) {		
		$profiledata['Email'] = profilepic_internal_emailmask($profiledata['Email'], true);
	}
	if (isset($profiledata['Yahoo IM'])) {		
		$profiledata['Yahoo IM'] = profilepic_internal_emailmask($profiledata['Yahoo IM']);
	}
	if (isset($profiledata['Jabber'])) {		
		$profiledata['Jabber'] = profilepic_internal_emailmask($profiledata['Jabber']);
	}

	foreach ($profiledata as $name => $data) {	
		$profile .=  '<p><b>'. $name . '</b>: ' . $data . '</p>';	
	}

	$profile .= '</div>';

	return $profile;
}

function profilepic_gui_printprofilepic($atts) {
	
	$settings = shortcode_atts( array(
	'callmethod' => 'shortcode',
	'userid'		=> '',
	'tags'		=> ''
	), $atts );
	
	if ($settings['userid'] == '') {
		global $wp_query;
		$authorID = $wp_query->post->post_author;
	} else {
		$authorID = $settings['userid'];
	}
	
	return profilepic_internal_imagetag($authorID, $settings['tags'], false);
}

//*** INTERNAL FUNCTION: modifies get_avatar response by replacing the default with the author's profile pic (if he/she has one)
//    USAGE: 
function profilepic_internal_getavatar( $imgtag, $id_or_email, $size, $default, $alt) {
	
	// debug
	// return $imgtag . print_r($id_or_email, true) . $size . $default;

	if (is_object($id_or_email)){
		$email = $id_or_email->comment_author_email;
		unset($id_or_email);
		$id_or_email = $email;
	}
	// get user id
	if ( is_numeric($id_or_email) ) {
		$id = (int) $id_or_email; 
	} else {
	// look up id based on email
		global $wpdb;
		$id = $wpdb->get_var("SELECT ID FROM $wpdb->users WHERE user_email='$id_or_email'");
		if ($id) { 
			$id = (int) $id; 
		} else {
			// email not associated with a user
			return $imgtag;
		}
	}
	
	$new_image = profilepic_internal_pickimage($id); 
	
	// does user have image?
	$array = explode('default.jpg', $new_image);
	if (count($array)<2) {
		$imgtag = "<img alt='$alt' src='$new_image' class='avatar avatar-$size photo' height='$size' width='$size' />";
	}
	
	return $imgtag;
	
}	


//*** TEMPLATE FUNCTION: returns image for comment author
//    USAGE: 
//		authorID: id number of author
//		tags: attributes to include in img tag (optional, defaults to no tags)
function profilepic_internal_gravatar($authorID, $tags = '') {
	$profilepic_options = get_option("profilepic_options");
	if ($authorID != 0) {
		$path = profilepic_internal_picpath($authorID, false, 'absolute');
		$width = $profilepic_options['gravatar_width'];
		$height = profilepic_internal_fingerdimensions($path, 'height') * ($profilepic_options['gravatar_width'] / profilepic_internal_fingerdimensions($path, 'width'));
		$tag = '<img src="' . profilepic_internal_picpath($authorID, false, 'url') . '" width=' . $width . ' height=' . $height . ' '. $tags . ' />';
		return $tag;
	} else {
		return false;
	}
}

//*** INTERNAL FUNCTION: returns alternate template tag
//    USAGE: 
function profilepic_internal_authortemplate() {
	return profilepic_internal_cleanpath(WP_PLUGIN_DIR . '/profile-pic/author.php');
}

//*** TEMPLATE FUNCTION: returns image for author wrapped in image tag
//    USAGE: 
//		authorID: id number of author
//		tags: attributes to include in img tag (optional, defaults to no tags)
//		display: display results (ie. echo)? true or false (optional, defaults to true)
function profilepic_internal_imagetag($authorID, $tags = '', $display = true) {
	$path = profilepic_internal_picpath($authorID, false, 'absolute');
	$width = profilepic_internal_fingerdimensions($path, 'width');
	$height = profilepic_internal_fingerdimensions($path, 'height');
	$tag = '<img src="' . profilepic_internal_picpath($authorID, false, 'url') . '" width=' . $width . ' height=' . $height . ' '. $tags . ' ' . ' id="authorpic" />';
	if ($display) { echo $tag; } else { return $tag; }
}

//*** TEMPLATE FUNCTION: returns url or absolute path to author's picture
//    USAGE: 
//		authorID: id number of author
//		display: display results (ie. echo)? true or false (optional, defaults to true)
//		type: specify what kind of path requested: 'url' or 'absolute' (optional, defaults to url)
function profilepic_internal_picpath($authorID, $display = true, $type = 'url') {
	switch($type) {
		case 'url' :
			$ref =  profilepic_internal_cleanpath(get_settings('siteurl') . profilepic_internal_pickimage($authorID));
			if ($display) { echo $ref; } else { return $ref; }
			break;
		case 'absolute':
			$ref =  profilepic_internal_cleanpath(ABSPATH . profilepic_internal_pickimage($authorID));
			if ($display) { echo $ref; } else { return $ref; }
			break;
	}
} 

//*** TEMPLATE FUNCTION: returns requested dimension from specific image
//    USAGE: 
//		path: absolute path to image from server root', 
//		dimension: the dimension you want, can be either 'height' or width'
//		display: display results (ie. echo)? true or false
function profilepic_internal_fingerdimensions($path, $dimension, $display = false) {
	$size = getimagesize($path);
	$width = $size[0];
	$height = $size[1];
	
	switch ($dimension) {
		case 'width':
			if ($display) { echo $width; } else { return $width; }
			break;
		case 'height':
			if ($display) { echo $height; } else { return $height; }
			break;
	}
}



//*** INTERNAL FUNCTION: strips extra slashes from paths; means user-end 
//    configuration is not picky about leading and trailing slashes
function profilepic_internal_cleanpath($dirty_path) {
	$nasties = array(1 => "///", 2 => "//", 3 => "http:/");
	$cleanies = array(1 => "/", 2 => "/", 3 => "http://");
	$profilepic_internal_cleanpath = str_replace($nasties, $cleanies, $dirty_path);
	return $profilepic_internal_cleanpath;
}

//*** INTERNAL FUNCTION: finds the appropriete path to the author's picture
function profilepic_internal_pickimage($authorID) {
	$profilepic_options = get_option("profilepic_options");
    $profilepic_displayoptions = get_option('profilepic_displayoptions_'.$authorID);
	
	// new method: image name is cached in the user options
	if (isset($profilepic_displayoptions['filename'])) {
		if ($profilepic_displayoptions['filename'] == 'DEFAULT') {
			$path = '/wp-content/plugins/profile-pic/default.jpg';
		} else if (isset($profilepic_displayoptions['filename']) && $profilepic_displayoptions['filename'] != '') {
			$path = profilepic_internal_cleanpath('/' . $profilepic_options['dir'] . '/' . $profilepic_displayoptions['filename']);
		}
	// if no image name was found, use old method to check for stored pic (much slower)
	} 
	else {	
		$extensions_array = explode(' ', $profilepic_options['extensions']);
		// look for image file based on user id
		$path = "";
		foreach ($extensions_array as $image_extension) {
			$path_fragment = '/' . $profilepic_options['dir'] . '/' . $authorID . '.' . $image_extension;
			$path_to_check = profilepic_internal_cleanpath(ABSPATH . $path_fragment);
			if ( file_exists($path_to_check) ) { 
				$path = $path_fragment;
				
				// if found, store file name in user data to comply w/new method
				$profilepic_displayoptions['filename'] = $authorID . '.' . $image_extension;
				update_option('profilepic_displayoptions_'.$authorID, $profilepic_displayoptions);
				
				break;
			}
		}
		
		// if not found, use default
		if ($path == "") {
			$path = '/wp-content/plugins/profile-pic/default.jpg';
			// again, update user data for compliance
			$profilepic_displayoptions['filename'] = 'DEFAULT';
				update_option('profilepic_displayoptions_'.$authorID, $profilepic_displayoptions);
		}
	}
	return $path;
}

//*** INTERNAL FUNCTION: masks email so it can't be harvested by spambots
function profilepic_internal_emailmask ($mail, $isLink = false, $display = '') {

// author: Micke Johansson

	$domain = substr($mail,strpos($mail, '@')+1);
	$name = substr($mail,0, strpos($mail, '@'));
	$encodedDomain = '';
	$encodedName = '';
	$encodedDisplay = '';
	
	for ($i=0; $i < strlen($domain); $i++) {
		$encodedDomain .= '&#'.ord(substr($domain,$i)).';';
	} 
	for ($i=0; $i < strlen($name); $i++) {
		$encodedName .= '&#'.ord(substr($name,$i)).';';
	}
	for ($i=0; $i < strlen($display); $i++) {
		$encodedDisplay .= '&#'.ord(substr($display,$i)).';';
	}
	
	$script = "<script type=\"text/javascript\">";
	$script .= "d=\"".$encodedDomain."\";";
	$script .= "n=\"".$encodedName."\";";
	if ($isLink) {
		if ($display == '')
			$script .= "document.write('<a href=\"&#109;&#097;&#105;&#108;&#116;&#111;&#058;'+n+'&#64;'+d+'\">'+n+'&#64;'+d+'</a>');";
		else
			$script .= "document.write('<a href=\"&#109;&#097;&#105;&#108;&#116;&#111;&#058;'+n+'&#64;'+d+'\">".$encodedDisplay."</a>');";
	} else {
		$script .= "document.write(n+'&#64;'+d);";
	}
	$script .= "</script>";
	return $script;
}


function profilepic_widget_init() {

	// Check for the required plugin functions. This will prevent fatal
	// errors occurring when you deactivate the dynamic-sidebar plugin.
	if ( !function_exists('register_sidebar_widget') )
		return;

	// This is the function that outputs the widget
	function profilepic_widget_main($args) {
		
		// $args is an array of strings that help widgets to conform to
		// the active theme: before_widget, before_title, after_widget,
		// and after_title are the array keys. Default tags: li and h2.
		extract($args);

		$options = get_option('profilepic_widget_options');
		$title = $options['title'];
		

		echo $before_widget . $before_title . $title . $after_title;
		$options = array('callmethod' => 'widget');
		echo profilepic_gui_printprofile($options);
        echo $after_widget;
	}  

	// This is the function that outputs the form to let the users edit
	// the widget's options
	function profilepic_widget_control() {
		global $profilepic_datafields;
		$datafields = $profilepic_datafields;
		
		$profilepic_displayoptions = get_option('profilepic_widget_displayoptions');
		
		foreach ($datafields as $fieldname => $fieldid) {
			$profilepic_displayoptions[$fieldid] = 0; 
		}
		
	
		// prep checkbox display
		$count = count($datafields);
		foreach ($datafields as $fieldname => $fieldid) {
			if ($profilepic_displayoptions[$fieldid] == 1) { $checked = " CHECKED "; } else { $checked = ''; }
			$checkbox_string .= "<label>".$fieldname."  &nbsp; <input type='checkbox' name='profilepic_widget_displayoptions[]' value='".$fieldid."'".$checked."/> </label><br />";
			$tick++;
			if ((round($count/2)) == $tick) {
				$checkbox_string .= "</p><p style='padding: 4px; text-align:right; display: block;'>";
			} 
		}
		// Get our options and see if we're handling a form submission.
		$options = get_option('profilepic_widget_options');
		if (!is_array($options)) {
			$options = array('title'=>'About', 'width' => 120);
		}
		
		if ( $_POST['profilepic_widget'] ) {
			$options['title'] = strip_tags(stripslashes($_POST['profilepic_widget_title']));
			$options['width'] = strip_tags(stripslashes($_POST['profilepic_widget_width']));
			update_option('profilepic_widget_options', $options);
			
			// store form data 
			$raw_data = $_POST['profilepic_widget_displayoptions'];
			$reversed_data = array();
			foreach ($raw_data as $key => $val) {
				$reversed_data[$val] = $key;
			}
			profilepic_internal_storeuserdisplaysettings($reversed_data, 'profilepic_widget_displayoptions');
			
		}
	
		$title = $options['title'];	
		$width = $options['width'];
		
		echo '<p style="text-align:right;"><label for="profilepic_widget_title">' . __('Title:') . ' <input style="width: 200px;" id="profilepic_widget_title" name="profilepic_widget_title" type="text" value="'.$title.'" /></label>
		</p><p style="text-align:right;"><label for="profilepic_widget_width">' . __('Picture Width:') . ' <input style="width: 180px;" id="profilepic_widget_width" name="profilepic_widget_width" type="text" value="'.$width.'" /> px</label>
		</p><div style="clear: both;"><p style="padding: 4px; text-align:right; display: block; float: left;">
		Fields to display:<br/></p><p style="padding: 4px; text-align:right; display: block; float: left;">';
			
		echo $checkbox_string . '</p>';
		
		if ($profilepic_options['donated'] != "yes") { 
			echo '<p style="text-align: left; clear: both;"><span style="font-size: 9px; color: #666; font-weight: bold;">Do you like the profile picture plugin?</span><br /><span style="font-size: 9px; color: #666;">
	If yes, please  <b><a href="http://geekgrl.net/wordpress/wordpress-profile-pic-plugin/">click here to donate now</a></b>. I\'m a freelance web developer and student, a little support (just a dollar or two even) will go a long way in allowing me to put time into maintaining and improving this plugin. <b>Go to Profile Pic options to remove this nag!</b></span></p>';
		}	
		
		echo '<div style="clear: both;"></div></div><br/><input type="hidden" id="profilepic_widget" name="profilepic_widget" value="1" />';
	
	}
	
	// This registers our widget so it appears with the other available
	// widgets and can be dragged and dropped into any active sidebars.
	wp_register_sidebar_widget('profilepic_widget', 'Profile', 'profilepic_widget_main', array('classname' => 'profilepic_widget', 'description' => 'Display your picture and profile details'));


	// This registers our optional widget control form. Because of this
	// our widget will have a button that reveals a 300x100 pixel form.
	register_widget_control('profilepic_widget', 'profilepic_widget_control', 300, 100);
}


?>
<?php
function ink_admin() {
	global $wpdb, $table_prefix;
	$table = $table_prefix . 'ink';
	$theme = get_option('template');

	$freshPageFolderName = (dirname(plugin_basename(__FILE__)));

	// This isn't testing what you think it is...
	if($values = $wpdb->get_results("SELECT * FROM ".$table." WHERE theme = '$theme'")) {
		?>
	<div id="inkadmin">
		<div class="shelf_column" id="leftcolumn">
			<h5 class="title">Color Mixer <span>&#8212; After selecting a color field, create your own mix.</span></h5>
			<div class="colorfields">
				<div id="red_bar" class="color_bar">
					<img class="slider" id="red_slider" src="../<?php echo FLUTTER_URI_RELATIVE?>images/horiz_slider.png" alt=""/>
				</div>
				<div id="green_bar" class="color_bar">
					<img class="slider" id="green_slider" src="../<?php echo FLUTTER_URI_RELATIVE?>images/horiz_slider.png" alt=""/>
				</div>
				<div id="blue_bar" class="color_bar">
					<img class="slider" id="blue_slider" src="../<?php echo FLUTTER_URI_RELATIVE?>images/horiz_slider.png" alt=""/>
				</div>
			</div>
			<div class="colorbox">
				<div id="colorfield">
				</div>
				<input type="text" id="red" name="red" value="0" size="3"/><br/>
				<input type="text" id="green" name="green" value="0" size="3"/><br/>
				<input type="text" id="blue" name="blue" value="0" size="3"/>
			</div>
		</div>
		<div class="content_wrap">
		<form><label><input type="checkbox" name="enableInk" id="enableInk" <?php if(get_option('enableInk') == 'true') echo 'checked'; ?> /> Activate</label></form>
		<form name="ink" id="ink" action="">
		<p><strong>This portion of Flutter is experimental. This is not a final product and has not been tested extensively. Use it at your own risk.</strong></p>
		<p>Use the form below to modify your blog's font and color settings. To modify colors, select the color field you'd like to modify and then use the RGB color mixer in the shelf below to choose your color (you can use the hexadecimal field too). If you're unsure what you're modifying, click the '?' link next to the element name to view your blog with that element highlighted.</p>
		<input type="hidden" id="activeField" name="activeField" />
		<?php foreach($values as $value) { ?>
			<div class="ink_element">
			<h3><?php echo $value->element ?> <a title="Highlight this element" href="<?php ink_highlight_url($value->element) ?>">?</a></h3>
			<span>Background: <input class="color" onclick="toggleColorSwatch('body__background')" id="<?php echo urlencode($value->element).'__' ?>background" name="<?php echo urlencode($value->element).'__' ?>background" type="text" value="<?php echo $value->background ?>"/></span>
			<span>Border color: <input class="color" id="<?php echo urlencode($value->element).'__' ?>border" type="text" value="<?php echo $value->border ?>" name="<?php echo urlencode($value->element).'__' ?>border" /></span>
			<span>Font color: <input class="color" id="<?php echo urlencode($value->element).'__' ?>color" type="text" value="<?php echo $value->color ?>" name="<?php echo urlencode($value->element).'__' ?>color" /></span>
			<br/>
			<span>Font family: 
							<select id="<?php echo urlencode($value->element).'__' ?>font_family" name="<?php echo urlencode($value->element).'__' ?>font_family" />
								<option value="none">Default</option>
								<option value="'Century Gothic', 'Arial', 'Verdana', 'Helvetica', 'Avant Garde', sans-serif" <?php is_selected("'Century Gothic', 'Arial', 'Verdana', 'Helvetica', 'Avant Garde', sans-serif", $value->font_family) ?>>"Century Gothic", "Arial", "Verdana", "Helvetica", "Avant Garde", sans-serif</option>
								<option value="'Times New Roman', Roman, serif" <?php is_selected("'Times New Roman', Roman, serif", $value->font_family) ?>>"Times New Roman", Roman, serif</option>
								<option value="'Lucida Grand', Algerian, fantasy" <?php is_selected("'Lucida Grand', Algerian, fantasy", $value->font_family) ?>>"Lucida Grand", Algerian, fantasy</option>
								<option value="'Comic Sans MS', 'Brush Script MT', cursive" <?php is_selected("'Comic Sans MS', 'Brush Script MT', cursive", $value->font_family) ?>>"Comic Sans MS", "Brush Script MT", cursive</option>
								<option value="'Courier New', Courier, monospace" <?php is_selected("'Courier New', Courier, monospace", $value->font_family) ?>>"Courier New", Courier, monospace</option>
							</select>
			</span>
			<span>Font size (pixels): <input class="size" id="<?php echo urlencode($value->element).'__' ?>font_size" size="3" type="text" value="<?php echo $value->font_size ?>" name="<?php echo urlencode($value->element).'__' ?>font_size" /></span>
			<span>Font style: 
							<select id="<?php echo urlencode($value->element).'__' ?>font_style" name="<?php echo urlencode($value->element).'__' ?>font_style">
								<option value="none" <?php is_selected('none', $value->font_style) ?>>Default</option>
								<option value="normal" <?php is_selected('normal', $value->font_style) ?>>Normal</option>
								<option value="italic" <?php is_selected('italic', $value->font_style) ?>>Italic</option>
								<option value="bold" <?php is_selected('bold', $value->font_style) ?>>Bold</option>
								<option value="bold italic" <?php is_selected('bold italic', $value->font_style) ?>>Bold Italic</option>
							</select>
			</span>
			</div>
		<?php } ?>
		</form>
		</div>

	</div>
	<div class="content_wrap">
		<input type="button" id="restore" value="Restore Defaults" />
	</div>
	<img src="../<?php echo FLUTTER_URI_RELATIVE?>images/colorswatch-active.png" alt="" style="display: none;">
		<?php
	} else {
		echo '<div id="inkadmin"><div class="content_wrap">The Ink table doesn\'t exist in your database. Please try reinstalling the software.</div></div>';
	}
}

function is_selected($style, $value) {
	if($value == $style) echo 'selected="selected"';
}

function ink_highlight_url($element) {
	echo get_bloginfo('siteurl').'/?ink_highlight='.urlencode($element);
}

function add_ink_admin() { 
	add_submenu_page('themes.php', 'Ink', 'Ink', 8, basename(__FILE__), 'ink_admin');
}

function add_ink_js() { ?>
	<?php if($_GET["page"] == 'FlutterInk') : ?><script language="JavaScript" type="text/javascript" src="<?php bloginfo('wpurl') ?>/<?php echo FLUTTER_URI_RELATIVE?>Ink/ink.js"></script>
	<link rel="stylesheet" href="<?php bloginfo('wpurl') ?>/<?php echo FLUTTER_URI_RELATIVE?>Ink/ink.css" type="text/css" media="screen" /><?php endif; ?>
<?php
}

?>
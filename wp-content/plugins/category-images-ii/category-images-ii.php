<?php
/*
Plugin Name: Category Images II
Plugin URI: http://wordpress.org/extend/plugins/category-images-ii/
Description: This plugin allows you to upload images for categories, and provides a template tag to show the image(s) in your theme.
Author: Simon Wheatley
Version: 1.00
Author URI: http://simonwheatley.co.uk/wordpress/
*/

/*  Copyright 2008 Simon Wheatley

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

require_once ( dirname (__FILE__) . '/plugin.php' );

// Let me know if these seem useful: http://www.simonwheatley.co.uk/contact/
// SWTODO: Ability to specify that the image for the default category is never shown?

/**
 * A Class to allow authors to set default categories for their new posts.
 *
 * Extends John Godley's WordPress Plugin Class, which adds all sorts of functionality
 * like templating which can be overriden by the theme, etc.
 * 
 * @package default
 * @author Simon Wheatley
 **/
class CategoryImagesII extends CategoryImagesII_Plugin
{
	
	protected $error_codes = array();
	protected $error_strings = array();

	/**
	 * Constructor for this class. 
	 *
	 * @return void
	 * @author Simon Wheatley
	 **/
	function __construct() 
	{
		$this->register_plugin ( 'category-images-ii', __FILE__ );
		// Register Hooks
		if ( is_admin() ) {
			$this->register_activation ( __FILE__ );
			// Amend the form element
			$this->add_action( 'load-categories.php', 'start_buffer' );
			// Form fields
			$this->add_action( 'edit_category_form' );
			// CSS for categories.php
			$this->add_action( 'admin_print_styles-categories.php', 'edit_categories_styles' );
			// Process any uploads, etc
			$this->add_action( 'edited_category', null, null, 2 );
			// Encode any errors or notices to the redirect URL (what a pain)
			$this->add_filter( 'wp_redirect', 'append_error_codes' );
			// Show any errors or notices
			$this->add_action( 'admin_notices' );
			// Hooks involved with setting the categories on a new post
			$this->add_action( 'load-categories.php', 'manage_category_scripts' );
			// Options pages 'n stuff
			$this->add_action( 'admin_menu' );
			// Process form submissions from options page
			$this->add_action( 'load-settings_page_category_images_ii', 'options_form_submission' );
		}
		$this->init_error_strings();
	}
	
	function activate()
	{
		// Set a reasonable initial max side value
		$this->save_max_side( 50 );
	}
	
	protected function init_error_strings()
	{
 		$this->error_strings[ 0 ] = __( "Sorry, something went wrong with the image upload. Please try again, or contact the website administrator." );
 		$this->error_strings[ 1 ] = __( "Sorry, you cannot upload an image file of this type. Allowed image file types are: GIF, JPEG, or PNG." );
	}
	
	/* HOOKS */
	
	/**
	 * Start the Output Buffer so we can alter the form element.
	 *
	 * @author Viper007Bond
	 **/
	public function start_buffer() 
	{
		if ( empty( $_GET['page'] ) ) ob_start( array( & $this, 'modify_buffer' ) );
	}
	
	public function modify_buffer( $buffer )
	{
		// Amend the form
		$buffer = str_replace( '<form name="editcat"', '<form name="editcat" enctype="multipart/form-data"', $buffer );
		return $buffer;
	}

	public function edit_category_form( $category )
	{
		global $action;
		$cat_id = $this->admin_cat_id();
		$images_base_url = $this->cat_images_base_url();
		$vars = array();
		$vars[ 'action' ] = $action;
		$vars[ 'max_upload_size' ] = $this->max_file_upload();
		$cat_image_name = $this->get_category_image_names( $cat_id, 'original' );
		$vars[ 'has_image' ] = false;
		if ( $cat_image_name ) {
			$vars[ 'has_image' ] = true;
			$cat_image_thumb_name = $this->get_category_image_names( $cat_id, 'thumbnail' );
			$vars[ 'cat_image' ] = "$images_base_url/$cat_image_name";
			$vars[ 'cat_image_thumb' ] = "$images_base_url/$cat_image_thumb_name";
		}
		$this->render_admin( 'image-edit-and-upload', $vars );
	}
	
	public function edited_category( $term_id, $tt_id )
	{
		// SECURE-A-TEA: If the nonce isn't present, or incorrect, then we don't have anything to do
		if ( ! wp_verify_nonce( $_REQUEST[ '_ciii_nonce' ], 'category_images_ii') ) return;

		// Deal with any file deletion *before* any upload
		$this->process_deletion();
		// Deal with any file upload
		$this->process_upload();
	}
	
	public function edit_categories_styles()
	{
		$vars = array();
		$this->render_admin( 'edit-categories-styles', $vars );
	}
	
	/**
	 * WP Filter
	 * Append a serialised array of error codes to the redirect, so they are
	 * displayed on the next page.
	 *
	 * @param string $location The URL string to be redirected to
	 * @return string The amended URL string to be redirected to
	 **/
	public function append_error_codes( $location )
	{
		// If we've no errors, then just pass it straight through
		if ( ! $this->error_codes ) return $location;
		// Serialise and encode the errors array, and add to the location query string
		$serialised = serialize( $this->error_codes );
		$encoded = urlencode( $serialised );
		$location = add_query_arg( 'ciii_errors', $encoded, $location );
		// We don't want the anchor (#) element on the URL
		return $location;
	}
	
	/**
	 * Show any errors or messages in the standard style. Called on admin pages.
	 *
	 * @return void
	 **/
	public function admin_notices()
	{
		// Any errors in the errors property array?
		foreach ( $this->error_codes AS $error_code ) {
			$this->render_error( $this->error_strings[ $error_code ] );
		}

		// Get any errors from the GET params
		$slashed = @ $_GET[ 'ciii_errors' ];
		if ( ! $slashed ) {
			return;
		}
		// F'ing slashes
		$serialised = stripslashes( $slashed );
		$error_codes = (array) unserialize( $serialised );

		// After all that, any errors? Display them.
		foreach ( $error_codes AS $error_code ) {
			$this->render_error( $this->error_strings[ $error_code ] );
		}
	}
	
	public function manage_category_scripts()
	{
		wp_enqueue_script( 'jquery' ); // Just to be sure
		$manage_categories_js = $this->url() . '/js/manage-categories.js';
		wp_enqueue_script( 'dc_set_categories', $manage_categories_js );
	}
	
	public function admin_menu() {
		// ( $page_title, $menu_title, $access_level, $file, $function = '' ) {
		add_options_page( 'Category Images II', 'Category Images II', 'manage_options', 'category_images_ii', array( $this, 'options_page' ) );
	}
	
	public function options_page()
	{
		$vars = array();
		$vars[ 'max_side' ] = $this->get_max_side();
		$this->render_admin( 'options-page', $vars );
	}
	
	public function options_form_submission()
	{
		// SECURE-A-TEA: If the nonce isn't present, or incorrect, then we don't have anything to do
		if ( ! wp_verify_nonce( $_REQUEST[ '_ciii_nonce' ], 'ciii_options') ) return;
		$max_side = (int) $_POST[ 'ciii_max_side' ];
		$this->save_max_side( $max_side );
	}
	
	public function display_images( $cat_ids )
	{
		$category_image_names = $this->get_category_image_names();
		$images_base_url = $this->cat_images_base_url();
		$categories = array();
		foreach ( $cat_ids AS $cat_id ) {
			$category = array();
			$category[ 'name' ] = $this->cat_name( $cat_id );
			// If there's no image set, then skip this one
			if ( ! isset( $category_image_names[ $cat_id ] ) ) continue;
			$category[ 'thumbnail' ] = $images_base_url . "/" . $category_image_names[ $cat_id ][ 'thumbnail' ];
			$category[ 'original' ] = $images_base_url . "/" . $category_image_names[ $cat_id ][ 'original' ];
			$categories[] = $category;
			unset( $category );
		}
		// Did we end up with *any* categories with images?
		if ( empty( $categories ) ) return;
		// Otherwise, render the template
		$vars = array();
		$vars[ 'categories' ] = & $categories;
		$this->render( 'category-images', $vars );
	}
	
	/* UTILITIES */
	
	protected function process_deletion()
	{
		// Check that the delete button was actually pressed.
		if ( ! $this->was_button_pressed( 'ciii_delete' ) ) return;
		// Delete button was pressed, and we verified the Nonce earlier. Kindly proceed.
		
		$cat_id = $this->admin_cat_id();
		$this->delete_category_image_files( $cat_id );
		$this->delete_category_image_names( $cat_id );
	}
	
	protected function process_upload()
	{
		// Have we actually got an upload?
		if ( $_FILES[ 'category_images_ii' ][ 'error' ] == 4 ) return;

		// Is it an image?
		$img_info = getimagesize( $_FILES[ 'category_images_ii' ][ 'tmp_name' ] );
		if ( ! $img_info ) {
			$this->error_codes[] = 0;
			return;
		}
		if ( ! $this->valid_uploaded_file_type( $img_info ) ) {
			$this->error_codes[] = 1;
			return;
		}

		// Find the directory we want the file to live in
		$base_dir = $this->cat_images_base_dir();
		// Make a filename
		$cat_id = $this->admin_cat_id();
		$ext = $this->preferred_image_extension( $img_info );
		$original_name = "$cat_id.original.$ext";
		// Put it all together
		$original_file = $base_dir . '/' . $original_name;
		// Safely move the uploaded file (this func will return false if it's not a properly uploaded file, e.g. could be hack attack!)
		if ( ! move_uploaded_file( $_FILES[ 'category_images_ii' ][ 'tmp_name' ], $original_file ) ) wp_die( __( "Something went wrong. Could not move the uploaded file." ) );
		// Resize and save thumbnail
		$thumbnail_name = "$cat_id.thumb.$ext";
		$thumbnail_file = $base_dir . "/$cat_id.thumb.$ext";
		$this->save_thumbnail( $original_file, $thumbnail_file );
		// Save this info in the options
		$this->save_category_image_names( $cat_id, $original_name, $thumbnail_name );
	}
	
	protected function save_thumbnail( $original_file, $thumbnail_file )
	{
		// Scale the image.
		list( $w, $h, $format ) = getimagesize( $original_file );
		$max_side = $this->get_max_side();
		$xratio = $max_side / $w;
		$yratio = $max_side / $h;
		$ratio = min( $xratio, $yratio );
		$targetw = (int) $w * $ratio;
		$targeth = (int) $h * $ratio;

		$src_gd = $this->image_create_from_file( $original_file );
		assert( $src_gd );
		$target_gd = imagecreatetruecolor( $targetw, $targeth );
		imagecopyresampled ( $target_gd, $src_gd, 0, 0, 0, 0, $targetw, $targeth, $w, $h );
		// create the initial copy from the original file
		// also overwrite the filename (in case the extension isn't accurate)
		if ( $format == IMAGETYPE_GIF ) {
			imagegif( $target_gd, $thumbnail_file );
		} elseif ( $format == IMAGETYPE_JPEG ) {
			imagejpeg( $target_gd, $thumbnail_file, 90 );
		} elseif ( $format == IMAGETYPE_PNG ) {
			imagepng( $target_gd, $thumbnail_file );
		} else {
			wp_die( 'Unknown image type (I thought we had checked this? Ed). Please upload a JPEG, GIF or PNG.' );
		}
	}
	
	protected function delete_category_image_files( $cat_id )
	{
		// Find the directory the files live in
		$base_dir = $this->cat_images_base_dir();
		// Make the full filepaths
		$category_images = $this->get_category_image_names( $cat_id );
		$original_file = $base_dir . "/" . $category_images[ 'original' ];
		$thumbnail_file = $base_dir . "/" . $category_images[ 'thumbnail' ];
		// Unlink the files
		unlink( $original_file );
		unlink( $thumbnail_file );
	}
	
	// The following was lifted from:
	// http://uk.php.net/manual/en/ref.image.php
	// With minor mods: ON error now returns false.
	// No longer accepts xbms (silly format)
	protected function image_create_from_file( $filename )
	{
		static $image_creators;

		if (!isset($image_creators)) {
			$image_creators = array(
				1  => "imagecreatefromgif",
				2  => "imagecreatefromjpeg",
				3  => "imagecreatefrompng"
			);
		}

		list( $w, $h, $file_type ) = getimagesize($filename);
		if ( isset( $image_creators[$file_type] ) ) {
			$image_creator = $image_creators[ $file_type ];
			if ( function_exists( $image_creator ) ) {
				// Set artificially high because GD uses uncompressed images in memory
				@ini_set('memory_limit', '256M');
				return $image_creator( $filename );
			}
		}

		// Changed to return false on error
		return false;
	}

	protected function cat_images_base_dir()
	{
		// Where should the dir be? Get the base WP uploads dir
		$wp_upload_dir = wp_upload_dir();
		$base_dir = $wp_upload_dir[ 'basedir' ];
		// Append our subdir
		$dir = $base_dir . '/category-images-ii';
		// Does the dir exist? (If not, then make it)
		if ( ! file_exists( $dir ) ) {
			mkdir( $dir );
		}
		// Now return it
		return $dir;
	}

	protected function cat_images_base_url()
	{
		// Where should the dir be? Get the base WP uploads dir
		$wp_upload_dir = wp_upload_dir();
		$base_url = $wp_upload_dir[ 'baseurl' ];
		// Append our subdir
		$url = $base_url . '/category-images-ii';
		return $url;
	}
	
	protected function max_file_upload()
	{
		$upload_max_filesize = ini_get( 'upload_max_filesize' );
		$post_max_size = ini_get( 'post_max_size' );
		return min( $post_max_size, $upload_max_filesize );
	}

	protected function admin_cat_id()
	{
		$cat_ID = @ $_REQUEST[ 'cat_ID' ];
		if ( ! $cat_ID ) return false;
		return $cat_ID;
	}
	
	// Method will only with within the admin cat editing interface
	protected function cat_name( $cat_ID = false )
	{
		if ( $cat_ID === false ) $cat_ID = $this->admin_cat_id();
		if ( $cat_ID === false ) return false;
		$category = get_term_by( 'id', $cat_ID, 'category' );
		return $category->name;
	}
	
	protected function valid_uploaded_file_type( $img_info )
	{
		return (bool) $this->preferred_image_extension( $img_info );
	}

	protected function preferred_image_extension( $img_info )
	{
		switch( $img_info[ 2 ] ) {
			case IMAGETYPE_GIF:
				return 'gif';
			case IMAGETYPE_JPEG:
				return 'jpg';
			case IMAGETYPE_PNG:
				return 'png';
		}
		return false;
	}
	
	// Delete one set of category image names from the CIII WP option,
	// or just one name from a set of category image names,
	// or all category image names.
	protected function get_category_image_names( $cat_id = false, $type = false )
	{
		$category_image_names = get_option( 'ciii_image_names' );
		if ( $cat_id !== false && $type ) {
			return $category_image_names[ $cat_id ][ $type ];
		}
		if ( $cat_id !== false ) {
			if ( ! isset( $category_image_names[ $cat_id ] ) ) return false;
			return $category_image_names[ $cat_id ];
		}
		return $category_image_names;
	}
	
	// Save one category image names into the CIII WP option
	protected function save_category_image_names( $cat_id, $original_name, $thumbnail_name )
	{
		$category_image_names = $this->get_category_image_names();
		$category_image_names[ $cat_id ] = array();
		$category_image_names[ $cat_id ][ 'original' ] = $original_name;
		$category_image_names[ $cat_id ][ 'thumbnail' ] = $thumbnail_name;
		delete_option( 'ciii_image_names' );
		return update_option( 'ciii_image_names', $category_image_names );
	}

	// Delete one set of category image names from the CIII WP option
	protected function delete_category_image_names( $cat_id )
	{
		$category_image_names = $this->get_category_image_names();
		unset( $category_image_names[ $cat_id ] );
		delete_option( 'ciii_image_names' );
		return update_option( 'ciii_image_names', $category_image_names );
	}
	
	protected function get_max_side()
	{
		return get_option( 'ciii_max_side' );
	}
	
	protected function save_max_side( $max_side )
	{
		delete_option( 'ciii_max_side' );
		return update_option( 'ciii_max_side', $max_side );
	}
	
	protected function was_button_pressed( $name, $post_only = false )
 	{
		if ( $post_only ) {
			$test_array = & $_POST;
		} else {
			$test_array = & $_REQUEST;
		}
		if ( @ $test_array[ $name ] != '' ) {
			return true;
		}
		if ( @ $test_array[$name . '_x'] != '' ) {
			return true;
		}
		return false;
	 }

}

/**
 * Instantiate the plugin
 *
 * @global
 **/

$CategoryImagesII = new CategoryImagesII();

function ciii_category_images( $args = null )
{
	global $CategoryImagesII;

	// Traditional WP argument munging.
	$defaults = array(
		'category_ids' => false
	);
	$r = wp_parse_args( $args, $defaults );
	
	// Cat ID(s) passed?
	if ( $r[ 'category_ids' ] !== false ) {
		$cat_ids = explode( ',', $r['category_ids'] );
		$CategoryImagesII->display_images( $cat_ids );
		return;
	}

	// In the loop?
	$categories = get_the_category();
	$cat_ids = array();
	foreach ( $categories AS & $category ) {
		$cat_ids[] = $category->cat_ID;
	}
	$CategoryImagesII->display_images( $cat_ids );
}

function ciii_category_archive_image()
{
	global $CategoryImagesII;
	if ( ! is_category() ) return;
	$cat = (array) intval( get_query_var('cat') );
	$CategoryImagesII->display_images( $cat );
}


?>
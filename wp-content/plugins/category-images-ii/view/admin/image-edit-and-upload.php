<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?>
<?php if ( $action != 'addcat' ) { // The edit category form, click through from the manage categories listings ?>
	<?php wp_nonce_field( 'category_images_ii', '_ciii_nonce' ); ?>
	<table class="form-table" id="category_images_ii">
		<?php if ( $has_image ) { ?>
			<tr class="form-field current-category-image">
				<th scope="row" valign="top"><?php _e( 'Current Category Image' ) ?></th>
				<td>
					<a href="<?php echo $cat_image; ?>" target="_blank"><img src="<?php echo $cat_image_thumb; ?>" alt="current category image (select to view larger size)" /></a>
					<p><input type="submit" class="button-secondary action" name="ciii_delete" value="<?php _e( "Delete This Image" ); ?>" id="Delete" /></p>
				</td>
			</tr>
		<?php } ?>
		<tr class="form-field upload-category-image">
			<th scope="row" valign="top"><label for="category_image_ii"><?php _e( 'Upload a Category Image' ) ?></label></th>
			<td>
				<input name="category_images_ii" id="category_images_ii" type="file" /><br />
				<span class='field-hint'><?php printf( __( "(Will replace any previous image. The maximum uploadable file size is %s.)" ), $max_upload_size ); ?></span>
			</td>
		</tr>
	</table>
<?php } else { // Add category form, at the bottom of the manage categories page. ?>
	<p id="category_images_ii">To add a category image, edit the category after you've created it.</p>
<?php } ?>
<div class="wrap">
	<h2><?php _e( "Category Images II" ); ?></h2>
	<form method="post" action="">
		<?php wp_nonce_field( 'ciii_options', '_ciii_nonce' ); ?>
		<table class='form-table'>
			<tr valign="top">
				<th scope="row">
					<label for="ciii_max_side"><?php _e( "Maximum side dimension of thumbnail" ); ?></label>
				</th>
				<td>
					<input name="ciii_max_side" type="text" id="ciii_max_side" value="<?php echo $max_side; ?>" size="6" /><br />
					<span class="field-hint"><?php _e( "(Max height or width of the thumbnail image. Created when the image is uploaded, so you'll need to re-upload any previous images.)" ); ?></span>
				</td>
			<tr>
		</table>
		<p class="submit"><input type="submit" name="Submit" value="<?php _e( 'Save Changes' ); ?>" /></p>
	</form>
</div>
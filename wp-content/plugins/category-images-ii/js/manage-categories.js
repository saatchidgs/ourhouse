// Iterate over all the category checkboxes
jQuery( document ).ready( ciii_init );

function ciii_init()
{
	// Move the fields into the main table
	jQuery( 'form#addcat p.submit' ).before( jQuery( 'p#category_images_ii' ) );
	jQuery( 'form#editcat table.form-table:first' ).append( jQuery( 'table#category_images_ii tbody' ) );
}
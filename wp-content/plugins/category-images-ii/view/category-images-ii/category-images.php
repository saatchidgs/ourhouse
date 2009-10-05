<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?>
<ul class="category_images_ii">
<?php foreach( $categories AS & $category ) { ?>
	<li class="category_image"><img src="<?php echo $category[ 'thumbnail' ]; ?>" alt="<?php echo $category[ 'name' ]; ?>" /></li>
<?php } ?>
</ul>
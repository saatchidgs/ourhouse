<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header(); ?>
	 <ul class="catList"><?php wp_list_categories('title_li=&hide_empty=0&child_of=3'); ?> </ul>
		
	<ul class="catelogue-feature">
		<? query_posts($query_string.'x_featureCat=true&showposts=3'); ?>
		<?php if (have_posts()) : ?>

			<?php while (have_posts()) : the_post(); ?>
			
			<li>
				<h2><?php the_title(); ?></h2>
				<div class="imgHolder"><img src="<?php bloginfo('template_directory'); ?>/images/catImage2.jpg<?php // Replace test image with API code ?>" /></div>
				<ul class="catelogueLinks">
					<li><a href="<? echo get('catFlyer'); ?>">Flyer</a></li>
					<li><a href="<? echo get('catAd'); ?>">Press Advert</a></li>
					<li><a href="<? echo get('catWebsite'); ?>">Website Link</a></li>
					<li><a href="<? echo get('catRetailNotes'); ?>">Retail Notes</a></li>
					<li><a href="<? echo get('catForumLinks'); ?>">Forum</a></li>
				</ul>
			</li>
			
		<?php endwhile; ?>
		<?php endif; ?>
	</ul>
	<div class="catArchive"><a href="/category/destinations/catalogue/">Campaign Archive</a></div>
<?php get_footer(); ?>

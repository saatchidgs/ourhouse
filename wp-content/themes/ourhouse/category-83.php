<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header();
?>

		<?php $posts = query_posts($query_string . 
		'&orderby=title&order=asc&posts_per_page=-1'); ?>
		
		<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>
		<div <?php post_class() ?> style="background: url(<? echo get('headerImg'); ?>) no-repeat;">
			<? echo get_image('consultantImage'); ?>
			<h2 id="post-<?php the_ID(); ?>"><?php the_title(); ?></h2>
			
			<div class="entry">
				<?php the_content() ?>
			</div>
			
			<ul class="download">
				<li><a href="<? echo get('catFlyer'); ?>">Flyer</a></li>
				<li><a href="<? echo get('catAd'); ?>">Press Advert</a></li>
				<li><a href="<? echo get('catWebsite'); ?>">Website Link</a></li>
				<li><a href="<? echo get('catRetailNotes'); ?>">Retail Notes</a></li>
				<li><a href="<? echo get('catForumLinks'); ?>">Forum</a></li>
			</ul>
			
		</div>

		<?php endwhile; ?>

	<?php else :
	endif;
?>

<?php get_footer(); ?>
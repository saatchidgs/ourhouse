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
			
			<a href="<? echo get('additionalInfo'); ?>" class="additional-info">Additional info >></a>
			<div class="linksList">
			<ul class="download">
				<li><a href="#<?php // API CODE FEEDS IN HERE ?>"><img src="<?php bloginfo('template_directory'); ?>/images/icon-pdf.png" /> Current Brochure</a> <span class="expires"><strong>Expires:</strong> <? echo get('brochureExpires'); ?></span></li>
				<li><a href="#<?php // API CODE FEEDS IN HERE ?>"><img src="<?php bloginfo('template_directory'); ?>/images/icon-pdf.png" /> Current Campaign (Short Life)</a> <span class="expires"><strong>Expires:</strong> <? echo get('campaignExpires'); ?></span></li>
				<li><a href="#<?php // API CODE FEEDS IN HERE ?>"><img src="<?php bloginfo('template_directory'); ?>/images/icon-pdf.png" /> Current Flyers (Long Life)</a> <span class="expires"><strong>Expires:</strong> <? echo get('flyerExpires'); ?></span></li>
			</ul>
			
			<ul class="additionalLinks">
				<li><a href="<? echo get('propertyUpdate'); ?>"><img src="<?php bloginfo('template_directory'); ?>/images/icon-doc.png" /> DESTINATION / PROPERTY UPDATE >></a></li>
				<li><a href="<? echo get('retailNotes'); ?>"><img src="<?php bloginfo('template_directory'); ?>/images/icon-doc.png" /> RETAIL NOTES >></a></li>
			</ul>
			</div>
		</div>

		<?php endwhile; ?>

	<?php else :
	endif;
?>

<?php get_footer(); ?>
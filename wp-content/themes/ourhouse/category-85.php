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
				<?php echo get_image('consultantImage'); ?>
				<h2 id="post-<?php the_ID(); ?>"><?php the_title(); ?></h2>

				<div class="entry">
					<?php the_content() ?>
				</div>

				<a href="<? echo get('additionalInfo'); ?>" class="additional-info">Additional info >></a>
<div class="linksList">
				<ul class="download">
					<li><a href="#<?php // API CODE FEEDS IN HERE ?>"><img src="<?php bloginfo('template_directory'); ?>/images/icon-pdf.png" /> Download 1</a></li>
					<li><a href="#<?php // API CODE FEEDS IN HERE ?>"><img src="<?php bloginfo('template_directory'); ?>/images/icon-pdf.png" /> Download 2</a></li>
					<li><a href="#<?php // API CODE FEEDS IN HERE ?>"><img src="<?php bloginfo('template_directory'); ?>/images/icon-pdf.png" /> Download 3</a></li>
				</ul>

				<ul class="additionalLinks">
					
					<?php
					$total = getFieldDuplicates('moreLinks',1);
					for($i = 1; $i < $total+1; $i++){
					echo "<li>" .get('moreLinks',1,$i). "</li>";
					}?>
				
				
				</ul>
</div>
			</div>

		<?php endwhile; ?>

	<?php else :
	endif;
?>

<?php get_footer(); ?>
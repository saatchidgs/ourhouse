<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header(); ?>


	<?php if (have_posts()) : ?>


		<?php while (have_posts()) : the_post(); ?>

			<div <?php post_class() ?>>
				<h2 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				<p class="postmetadata"><?php the_category(', ') ?> | <?php edit_post_link('Edit', '', ' | '); ?></p>
			</div>

		<?php endwhile; ?>

	<?php else : ?>

		<h2 class="center">No posts found. Try a different search?</h2>
		<?php get_search_form(); ?>

	<?php endif; ?>

<?php get_footer(); ?>

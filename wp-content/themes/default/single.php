<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header();
?>

<?php if(in_category(83)){ // If the article is in the Catalogue Category ?>
		
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
<?php } elseif(in_category(84)){ // If the article is in the Marketing Material Category ?>
		
		<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>
				<div <?php post_class() ?> style="background: url(<? echo get('headerImg'); ?>) no-repeat;">
					<?php echo get_image('consultantImage'); ?>
					<h2 id="post-<?php the_ID(); ?>"><?php the_title(); ?></h2>

					<div class="entry">
						<?php the_content() ?>
					</div>
<div class="linksList">
						<ul class="download">
							<li><a href="#<?php // API CODE FEEDS IN HERE ?>"><img src="<?php bloginfo('template_directory'); ?>/images/icon-pdf.png" /> Current Brochure</a> <span class="expires"><strong>Expires:</strong> <? echo get('mm_currentBrochureExpires'); ?></span></li>
							<li><a href="#<?php // API CODE FEEDS IN HERE ?>"><img src="<?php bloginfo('template_directory'); ?>/images/icon-pdf.png" /> Current Campaign (Short Life)</a> <span class="expires"><strong>Expires:</strong> <? echo get('mm_currentCampaignsExpires'); ?></span></li>
							<li><a href="#<?php // API CODE FEEDS IN HERE ?>"><img src="<?php bloginfo('template_directory'); ?>/images/icon-pdf.png" /> Current Flyers (Long Life)</a> <span class="expires"><strong>Expires:</strong> <? echo get('mm_currentFlyersExpires'); ?></span></li>
							<li><a href="#<?php // API CODE FEEDS IN HERE ?>"><img src="<?php bloginfo('template_directory'); ?>/images/icon-pdf.png" /> eDM</a></li>
							<li><a href="#<?php // API CODE FEEDS IN HERE ?>"><img src="<?php bloginfo('template_directory'); ?>/images/icon-pdf.png" /> Direct Mail</a></li>
						</ul>

						<ul class="additionalLinks">
							<li><a href="<? echo get('mm_propertyUpdate'); ?>"><img src="<?php bloginfo('template_directory'); ?>/images/icon-doc.png" /> DESTINATION / PROPERTY UPDATE >></a></li>
							<li><a href="<? echo get('mm_retailNotes'); ?>"><img src="<?php bloginfo('template_directory'); ?>/images/icon-doc.png" /> RETAIL NOTES >></a></li>
							<li><a href="<? echo get('mm_forum'); ?>"><img src="<?php bloginfo('template_directory'); ?>/images/icon-doc.png" /> FORUM >></a></li>
						</ul>
</div>
				</div>

		<?php endwhile; ?>

	<?php else :
	endif;
?>
<?php } elseif(in_category(85)){ // If the article is in the Ideas Centre Category ?>
		
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
						<li><a href="#<?php // API CODE FEEDS IN HERE ?>"><img src="<?php bloginfo('template_directory'); ?>/images/doc-pdf.png" /> Download 1</a></li>
						<li><a href="#<?php // API CODE FEEDS IN HERE ?>"><img src="<?php bloginfo('template_directory'); ?>/images/doc-pdf.png" /> Download 2</a></li>
						<li><a href="#<?php // API CODE FEEDS IN HERE ?>"><img src="<?php bloginfo('template_directory'); ?>/images/doc-pdf.png" /> Download 3</a></li>
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
<?php } else { ?>
	
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
		<?php } ?>

<?php get_footer(); ?>
<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 * Destinations Category Template
 */

get_header();
?>

		 <ul class="catList">
			<li class="cat-item cat-item-4">
				<a href="http://ourhouse.pixelberry.co.nz/category/destinations/new-zealand/" title="View all posts filed under NEW ZEALAND">NEW ZEALAND</a>
				<ul>
					<?php $catlist = new WP_Query('cat=4'); ?>
					<?php while ($catlist->have_posts()) : $catlist->the_post(); ?>
					<?php setup_postdata($post); ?>
						<li><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></li>
					<?php endwhile; ?>
				</ul>
			</li>
			<li class="cat-item cat-item-7">
				<a href="http://ourhouse.pixelberry.co.nz/category/destinations/australia/" title="View all posts filed under AUSTRALIA">AUSTRALIA</a>
				<ul>
					<?php $catlist = new WP_Query('cat=7'); ?>
					<?php while ($catlist->have_posts()) : $catlist->the_post(); ?>
					<?php setup_postdata($post); ?>
						<li><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></li>
					<?php endwhile; ?>
				</ul>
			</li>
			<li class="cat-item cat-item-16">
				<a href="http://ourhouse.pixelberry.co.nz/category/destinations/south-pacific/" title="View all posts filed under SOUTH PACIFIC">SOUTH PACIFIC</a>
				<ul>
					<?php $catlist = new WP_Query('cat=16'); ?>
					<?php while ($catlist->have_posts()) : $catlist->the_post(); ?>
					<?php setup_postdata($post); ?>
						<li><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></li>
					<?php endwhile; ?>
				</ul>
			</li>
			<li class="cat-item cat-item-26">
				<a href="http://ourhouse.pixelberry.co.nz/category/destinations/asia/" title="View all posts filed under ASIA">ASIA</a>
				<ul>
					<?php $catlist = new WP_Query('cat=26'); ?>
					<?php while ($catlist->have_posts()) : $catlist->the_post(); ?>
					<?php setup_postdata($post); ?>
						<li><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></li>
					<?php endwhile; ?>
				</ul>
			</li>
		</ul>
		<ul class="catList">
			<li class="cat-item cat-item-35">
				<a href="http://ourhouse.pixelberry.co.nz/category/destinations/uk-europe/" title="View all posts filed under UK / EUROPE">UK / EUROPE</a>
				<ul>
					<?php $catlist = new WP_Query('cat=35'); ?>
					<?php while ($catlist->have_posts()) : $catlist->the_post(); ?>
					<?php setup_postdata($post); ?>
						<li><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></li>
					<?php endwhile; ?>
				</ul>
			</li>
			<li class="cat-item cat-item-44">
				<a href="http://ourhouse.pixelberry.co.nz/category/destinations/usa-canada/" title="View all posts filed under USA / CANADA">USA / CANADA</a>
				<ul>
					<?php $catlist = new WP_Query('cat=44'); ?>
					<?php while ($catlist->have_posts()) : $catlist->the_post(); ?>
					<?php setup_postdata($post); ?>
						<li><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></li>
					<?php endwhile; ?>
				</ul>
			</li>
			<li class="cat-item cat-item-49">
				<a href="http://ourhouse.pixelberry.co.nz/category/destinations/adventure/" title="View all posts filed under ADVENTURE">ADVENTURE</a>
				<ul>
					<?php $catlist = new WP_Query('cat=49'); ?>
					<?php while ($catlist->have_posts()) : $catlist->the_post(); ?>
					<?php setup_postdata($post); ?>
						<li><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></li>
					<?php endwhile; ?>
				</ul>
			</li>
			<li class="cat-item cat-item-60">
				<a href="http://ourhouse.pixelberry.co.nz/category/destinations/cruise/" title="View all posts filed under CRUISE">CRUISE</a>
				<ul>
					<?php $catlist = new WP_Query('cat=60'); ?>
					<?php while ($catlist->have_posts()) : $catlist->the_post(); ?>
					<?php setup_postdata($post); ?>
						<li><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></li>
					<?php endwhile; ?>
				</ul>
			</li>
		</ul>
		<ul class="catList">
			<li class="cat-item cat-item-69">
				<a href="http://ourhouse.pixelberry.co.nz/category/destinations/rugby/" title="View all posts filed under RUGBY">RUGBY</a>
				<ul>
					<?php $catlist = new WP_Query('cat=69'); ?>
					<?php while ($catlist->have_posts()) : $catlist->the_post(); ?>
					<?php setup_postdata($post); ?>
						<li><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></li>
					<?php endwhile; ?>
				</ul>
			</li>
			<li class="cat-item cat-item-73">
				<a href="http://ourhouse.pixelberry.co.nz/category/destinations/weddings/" title="View all posts filed under WEDDINGS">WEDDINGS</a>
				<ul>
					<?php $catlist = new WP_Query('cat=73'); ?>
					<?php while ($catlist->have_posts()) : $catlist->the_post(); ?>
					<?php setup_postdata($post); ?>
						<li><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></li>
					<?php endwhile; ?>
				</ul>
			</li>
			<li class="cat-item cat-item-78">
				<a href="http://ourhouse.pixelberry.co.nz/category/destinations/groups-events/" title="View all posts filed under GROUPS / EVENTS">GROUPS / EVENTS</a>
				<ul>
					<?php $catlist = new WP_Query('cat=78'); ?>
					<?php while ($catlist->have_posts()) : $catlist->the_post(); ?>
					<?php setup_postdata($post); ?>
						<li><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></li>
					<?php endwhile; ?>
				</ul>
			</li>
			<li class="cat-item cat-item-83">
				<a href="http://ourhouse.pixelberry.co.nz/category/destinations/catalogue/" title="View all posts filed under CATALOGUE">CATALOGUE</a>
				<ul>
					<?php $catlist = new WP_Query('cat=83'); ?>
					<?php while ($catlist->have_posts()) : $catlist->the_post(); ?>
					<?php setup_postdata($post); ?>
						<li><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></li>
					<?php endwhile; ?>
				</ul>
			</li>
	
		 </ul>
		

<?php get_footer(); ?>
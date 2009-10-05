<?php get_header(); ?>
   <div id="content" class="narrowcolumn">

<!-- This sets the $curauth & $authid variables -->
<?php
if(get_query_var('author_name')) :
$curauth = get_userdatabylogin(get_query_var('author_name'));
else :
$curauth = get_userdata(get_query_var('author'));
endif;
$authid = $curauth->ID;
?>

<div id="profilebox" style="min-height: <?php author_image_dimensions(author_image_path($authid, false, 'absolute'), 'height', true); ?>px;">

<!-- old school method 
<?php author_image_tag($authid, 'align=right'); ?>

<h2><?php _e($curauth->first_name); ?> <?php _e($curauth->last_name); ?></h2>

<p><b>E-Mail: </b> <?php _e($curauth->user_email); ?>
<p><b>Yahoo IM: </b><?php _e($curauth->yim); ?>
<p><b>AIM: </b><?php _e($curauth->aim); ?>
<p><b>Jabber: </b><?php _e($curauth->jabber); ?>
<p><b>Web Page: </b><a href="<?php _e($curauth->user_url); ?>"><?php _e($curauth->user_url); ?></a>
<p><b>Registered Since: </b><?php _e($curauth->user_registered); ?>
<p><b>Profile: </b><?php _e($curauth->description); ?>

-->

<?php 
$atts = array('callmethod' => 'shortcode', 'userid' => $authid);
echo profilepic_gui_printprofile($atts); 
?>

</div>

<h2>Posts by <?php echo $curauth->nickname; ?>:</h2>

<ul>
<!-- The Loop -->
<?php if (have_posts()) : ?>
   <?php while (have_posts()) : the_post(); ?>
      <h3>
    <a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h3>
      <small><?php the_time('F jS, Y') ?> </small>	
    <?php the_content('Read the rest of this entry "'); ?>
      <p>
<?php comments_popup_link('No Comments "', '1 Comment "', '% Comments "'); ?>
</p>

   <?php endwhile; ?>

   <?php else : ?>
      <p>No posts by this author</p>
   <?php endif; ?>
<!-- End Loop -->
</ul>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
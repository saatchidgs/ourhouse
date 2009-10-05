<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>


<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" >
<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>?=<?php the_time(); ?>" type="text/css" media="screen" />
<!--[if IE 7]>
<link href="<?php bloginfo('stylesheet_directory'); ?>/ie7.css" rel="stylesheet" type="text/css" media="screen" />
<![endif]-->

<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>

<?php wp_head(); ?>
</head>
<body class="<?php sandbox_body_class() ?>">
<div id="page">

<div id="header" role="banner">
	<div id="headerimg">
		<h1><a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a></h1>
		<div class="search"><?php get_search_form(); ?></div>
		<ul id="nav">
			<li class="nav-home"><a href="/">Home</a></li>
			<li class="nav-destiantions"><a href="/category/destinations">Destinations</a></li>
			<li class="nav-design-request"><a href="/design-request">Design request</a></li>
			<li class="nav-marketing-material"><a href="/category/marketing-material">Marketing Material</a></li>
			<li class="nav-merchandise"><a href="/merchandise">Merchandise</a></li>
			<li class="nav-ideas-centre"><a href="/category/ideas-centre">Ideas Centre</a></li>
			<li class="nav-visual-identity"><a href="/visual-identity">Visual Identity</a></li>
			<li class="nav-forum"><a href="/forum">Forum</a></li>
		</ul>
	</div>
</div>

<?php if(is_home()){ 
	} else { ?>
<div class="breadcrumb">
	<?php
	if(function_exists('bcn_display'))
	{
		bcn_display();
	}
	?>
</div>
<?php } ?>
<div id="contentGroup">

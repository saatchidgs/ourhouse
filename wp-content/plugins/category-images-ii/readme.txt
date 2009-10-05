=== Category Images II ===
Contributors: simonwheatley
Donate link: http://www.simonwheatley.co.uk/wordpress/
Tags: authors, users, profile
Requires at least: 2.6
Tested up to: 2.7
Stable tag: 1.00

This plugin allows you to upload images for categories, and provides a template tag to show the image(s) in your theme.

== Description ==

**This plugin requires PHP5 (see Other Notes > PHP4 for more).**

This plugin allows you to upload images for categories, and provides two template tags to show the image(s) in your theme.

=== Tag: ciii_category_images() ===

`<?php ciii_category_images(); ?>`

Used within the loop, the above template tag will show the thumbnails for all the category images for the category of that post. If some categories have no image, no image is shown for that category (i.e. there is no default image).

`<?php ciii_category_images( 'category_ids=37,27' ); ?>`

Used anywhere and provided with category IDs, the above template tag will show the thumbnails for all the categories specified. If some categories have no image, no image is shown for that category (i.e. there is no default image here either).

(Note that this tag will get confused if you use it outside the loop. If you want to add a single image to your category archive pages, please use `ciii_category_archive_image()` below.)

=== Tag: ciii_category_archive_image() ===

`<?php ciii_category_archive_image(); ?>`

This tag is designed to be used on the category archive page, either inside or outside the loop. It will show the image for the category in question.

=== Other notes ===

You can specify the maximum side of the category image thumbnail in "Settings > Category Images II". You can upload, and delete, images for each category from "Manage > Categories", click into each category you wish to edit and you'll see the uploading and deletion controls (deletion controls only show up if the category already has an image uploaded).

The HTML output is fairly well classed, but if you need to adapt it you can. Create a directory in your *theme* called "view", and a directory within that one called "category-images-ii". Then copy the template files `view/category-images-ii/category-images.php` from the plugin directory into your theme directory and amend as you need. If these files exist in these directories in your theme they will override the ones in the plugin directory. This is good because it means that when you update the plugin you can simply overwrite the old plugin directory as you haven't changed any files in it. All hail [John Godley](http://urbangiraffe.com/) for the code which allows this magic to happen. Can hook into the [User Photo plugin](http://wordpress.org/extend/plugins/user-photo/) to display the author photos.

Plugin initially produced on behalf of [Puffbox](http://www.puffbox.com).

Is this plugin lacking a feature you want? I'm happy to accept offers of feature sponsorship: [contact me](http://www.simonwheatley.co.uk/contact-me/) and we can discuss your ideas.

Any issues: [contact me](http://www.simonwheatley.co.uk/contact-me/).

== Installation ==

The plugin is simple to install:

1. Download `category-images-ii.zip`
1. Unzip
1. Upload `category-images-ii` directory to your `/wp-content/plugins` directory
1. Go to the plugin management page and enable the plugin
1. Give yourself a pat on the back

== PHP4 ==

Many of my plugin now require at least PHP5. I know that WordPress officially supports PHP4, but I don't. PHP4 is a mess and makes coding a lot less efficient, and when you're releasing stuff for free these things matter. PHP5 has been out for several years now and is fully production ready, as well as being naturally more secure and performant.

If you're still running PHP4, I strongly suggest you talk to your hosting company about upgrading your servers. All reputable hosting companies should offer PHP5 as well as PHP4.

Right, that's it. Grump over. ;)

== PHP4 ==

Many of my plugin now require at least PHP5. I know that WordPress officially supports PHP4, but I don't. PHP4 is a mess and makes coding a lot less efficient. PHP5 has been out for several years now and is fully production ready, as well as being naturally more secure and performant.

If you're still running PHP4, I strongly suggest you talk to your hosting company about upgrading your servers. All reputable hosting companies should offer PHP5 as well as PHP4.

Right, that's it. Grump over. ;)

== Change Log ==

= v1.00 2009/02/24 =

* RELEASE: Version 1.00

= v0.40b 2009/02/23 =

* ENHANCEMENT: Added ciii_category_archive_image after (Richard Strauss)[http://littlegreenblog.com] pointed out the flaws of ciii_category_images when used in archive.php outside the loop.

= v0.30b 2009/01/13 =

* ENHANCEMENT: Now compatible with both 2.6.5 AND 2.7

= v0.21b 2009/01/13 =

* FIXED: Through a triumph of copying and pasting the readme.txt, I managed to rename this plugin to "Author Listings"

= v0.20b 2009/01/12 =

* Category images can now be deleted.

= v0.10b 2009/01/12 =

* Plugin first sees the light of day.

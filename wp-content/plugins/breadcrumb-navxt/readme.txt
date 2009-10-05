=== Breadcrumb NavXT ===
Contributors: mtekk, hakre
Tags: breadcrumb, navigation
Requires at least: 2.6
Tested up to: 2.8
Stable tag: 3.3.0
Adds breadcrumb navigation showing the visitor's path to their current location.

== Description ==

Breadcrumb NavXT, the successor to the popular WordPress plugin Breadcrumb Navigation XT, was written from the ground up to be better than its ancestor. This plugin generates locational breadcrumb trails for your WordPress blog. These breadcrumb trails are highly customizable to suit the needs of just about any blog. The Administrative interface makes setting options easy, while a direct class access is available for theme developers and more adventurous users. Do note that Breadcrumb NavXT requires PHP5.

= Translations =

Breadcrumb NavXT distributes with translations for the following languages:

* English - default -
* German by Tom Klingenberg
* French by Laurent Grabielle
* Spanish by Karin Sequen
* Dutch by Stan Lenssen
* Russian by Yuri Gribov
* Swedish by Patrik Spathon

Don't see your language on the list? Feel free to translate Breadcrumb NavXT and send John Havlik the translations.

== Installation ==

Please visit [Breadcrumb NavXT's](http://mtekk.weblogs.us/code/breadcrumb-navxt/#installation "Go to Breadcrumb NavXT's project page's installation section.") project page for installation and usage instructions.

== Changelog ==

= 3.3.0 =
* Behavior change: The core plugin was removed, and administrative plugin renamed, direct class access still possible.
* New feature: Ability to trim the title length for all breadcrumbs in the trail.
* New feature: Ability to selectively include the "Blog" in addition to the "Home" breadcrumb in the trail (for static frontpage setups).
* New feature: Translations for Russian now included thanks to Yuri Gribov.
* New feature: Translations for Swedish now included thanks to Patrik Spathon.
* Bug fix: Minor tweaks to the settings link in the plugins listing page so that it fits better in WordPress 2.8.
* Bug fix: Now selects the first category hierarchy of a post instead of the last.
= 3.2.1 =
* New feature: Translations for Belorussian now included thanks to "Fat Cow".
* Bug fix: The `bcn_display()` and `bcn_display_list()` wrapper functions obey the
`$return parameter`.
* Bug fix: Anchors now will be valid HTML even when a page/category/post title has HTML tags in it.
* Bug fix: Revised `bcn_breadcrumb_trail::category_parents` to work around a bug in `get_category` that causes a WP_Error to be thrown.
* Bug fix: Importing settings XML files should no longer corrupt HTML entities.
* Bug fix: Can no longer import and reset options at the same time.
* Bug fix: WordPress 2.6 should be supported again.
= 3.2.0 =
* New feature: Now can output breadcrumbs in trail as list elements.
* New feature: Translations for Dutch now included thanks to Stan Lenssen.
* New feature: Now breadcrumb trails can be output in reverse order.
* New feature: Ability to reset to default option values in administrative interface.
* New feature: Ability to export settings to a XML file.
* New feature: Ability to import settings from a XML file.
* Bug fix: Anchor templates now protected against complete clearing.
* Bug fix: Administrative interface related styling and JavaScript no longer leaks to other admin pages.
* Bug fix: Calling `bcn_display()` works with the same inputs as `bcn_breadcrumb_trail::display()`.
* Bug fix: Calling `bcn_display()` multiple times will not place duplicate breadcrumbs into the trail.
= 3.1.0 =
* New feature: Tabular plugin integrated into the administrative interface/settings page plugin.
* New feature: Default options now are localized.
* New feature: Plugin uninstaller following the WordPress plugin uninstaller API.
* Bug fix: Administrative interface tweaked, hopefully more usable.
* Bug fix: Tabs work with WordPress 2.8-bleeding-edge.
* Bug fix: Translations for German, French, and Spanish are all updated.
* Bug fix: Paged archives, searches, and frontpage fixed.
= 3.0.2 =
* Bug fix: Default options are installed correctly now for most users.
* Bug fix: Now `bcn_breadcrumb_trail::fill()` is safe to call within the loop.
* Bug fix: In WPMU options now are properly separate/independent for each blog.
* Bug fix: WPMU settings page loads correctly after saving settings.
* Bug fix: Blog_anchor setting not lost on non-static frontpage blogs.
* Bug fix: Tabular add on no longer causes issues with WordPress 2.7.
* New feature: Spanish and French localization files are now included thanks to Karin Sequen and Laurent Grabielle.
= 3.0.1 =
* Bug fix: UTF-8 characters in the administrative interface now save/display correctly.
* Bug fix: Breadcrumb trails for attachments of pages no longer generate PHP errors.
* Bug fix: Administrative interface tweaks for installing default options.
* Bug fix: Changed handling of situation when Posts Page is not set and Front Page is set.
= 3.0.0 =
* New feature: Completely rewritten core and administrative interface.
* New feature: WordPress sidebar widget built in.
* New feature: Breadcrumb trail can output without links.
* New feature: Customizable anchor templates, allows things such as rel="nofollow".
* New feature: The home breadcrumb may now be excluded from the breadcrumb trail.
* Bug fix: 404 page breadcrumbs show up in static frontpage situations where the posts page is a child of the home page.
* Bug fix: Static frontpage situations involving the posts page being more than one level off of the home behave as expected.
* Bug fix: Compatible with all polyglot like plugins.
* Bug fix: Compatible with Viper007bond's Breadcrumb Titles for Pages plugin (but 3.0.0 can replace it as well)
* Bug fix: Author page support should be fixed on some setups where it did not work before.
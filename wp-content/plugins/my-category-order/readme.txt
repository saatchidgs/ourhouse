=== My Category Order ===
Contributors: froman118
Donate link: http://geekyweekly.com/mycategoryorder
Tags: categories, category, order, sidebar, widget
Requires at least: 2.5
Tested up to: 2.8.3
Stable tag: 2.8.3

My Category Order allows you to set the order in which categories will appear in the sidebar.

== Description ==

My Category Order allows you to set the order in which categories will appear in the sidebar. Uses a drag 
and drop interface for ordering. Adds a widget with additional options for easy installation on widgetized themes.

= Change Log =

2.8.3:

* Trying to fix Javascript onload issues. Settled on using the addLoadEvent function built into Wordpress. If the sorting does not initialize then you have a plugin that is incorrectly overriding the window.onload event. There is nothing I can do to help. 
* This will also be the last version of this plugin I will release. I am working on a new "2.0" version that will only support WP 2.8 and greater. This jump will finally include the ability to have multiple widgets.

2.8.1:

* Added Czech translation (Jan)

2.8:

* Updated for 2.8 compatibility

2.7.1:

* If your categories don't show up for ordering your DB user account must have ALTER permissions, the plugin adds columns to store the order
* Added a call to $wpdb->show_errors(); to help debug any issues
* Translations added and thanks: Spanish (Karin), German (Wolfgang and Mike), Swedish (Mans), Italian (Stefano)

2.7:

* Updated for 2.7, now under the the new Pages menu
* Moved to jQuery for drag and drop
* Removed finicky AJAX submission
* Translations added and thanks: Russian (Flector and Pink), Dutch (Anja), Polish (Zbigniew)
* Keep those translations coming

2.6.1a:

* The plugin has been modified to be fully translated
* The plugin patch no correctly patches taxonomy.php
* New translation added : French, by Brahim Machkouri (http://www.category-icons.com)
* The widget has now a description

2.6.1:

* Finally no more taxonomy.php overwriting, well kind of. After you upgrade Wordpress visit the My Category Order page and it will perform the edit automatically.
* Thanks to Submarine at http://www.category-icons.com for the code.
* Also added string localization, email me if you are interested in translating.


== Installation ==

1. Copy plugin contents to /wp-content/plugins/my-category-order
2. Activate the My Category Order plugin on the Plugins menu
3. Go to the "My Category Order" tab under Manage and specify your desired order for post categories
   
4. If you are using widgets then replace the standard "Category" widget with the "My Category Order" widget. That's it.

5. If you aren't using widgets, modify sidebar template to use correct orderby value:
	wp_list_categories('orderby=order&title_li=');

== Frequently Asked Questions ==

= Why modify a core file? =

The way categories can be ordered is hardcoded at a very low level. Adding an ordering option at that level
makes it easy for people to modify their themes and helps overall compatibility.

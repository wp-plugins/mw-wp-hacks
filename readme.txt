=== Plugin Name ===
Contributors: inc2734
Donate link:
Tags: plugin, hack, setting
Requires at least: 3.6
Tested up to: 3.6.1
Stable tag: 0.2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

MW WP Hacks is plugin to help with development in wordpress.

== Description ==

MW WP Hacks is plugin to help with development in wordpress.

* Add Google Plus ID field in user profile page. IF input, echo <link rel="author" /> in <head>;
* Custom Feed into any post types.
* Custom text after excerpt.
* Add Google Plus ID field in management page. IF save this, include <link rel="publisher" /> in <head>.
* Add Facebook AppID field in management page. IF save this, include <div id="fb-root"></div> any more.
* Add GA Tracking ID field in management page. IF save this, include Google Analytics tag.
* Include any social scripts. ( Facebook, Twitter, Hatena Bookmark, Google+1 )
* Define widget areas.
* Define custom thumbnail sizes.
* Fix wp_title in the case of japanese.
* Set $content_width same as large_size_w.
* Add useful method: mw_wp_hacks::pager();
* Add useful method: mw_wp_hacks::get_top_parent_id();
* Add useful method: mw_wp_hacks::is_custom_post_type();
* Add useful method: mw_wp_hacks::the_localNav();

== Installation ==

1. Upload `MW WP Hacks` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Do setting in settings page.

== Changelog ==

= 0.2.0 =
* Initial release.

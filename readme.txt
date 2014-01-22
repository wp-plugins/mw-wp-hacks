=== Plugin Name ===
Contributors: inc2734
Donate link: http://www.amazon.co.jp/registry/wishlist/39ANKRNSTNW40
Tags: plugin, hack, setting
Requires at least: 3.6
Tested up to: 3.8.0
Stable tag: 0.4.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

MW WP Hacks is plugin to help with development in WordPress.

== Description ==

MW WP Hacks is plugin to help with development in WordPress.

* Add Google Plus ID field in user profile page. IF input, echo &lt;link rel="author" /&gt; in &lt;head&gt;.
* Custom Feed into any post types.
* Custom text after excerpt.
* Add Google Plus ID field in management page. IF save this, include &lt;link rel="publisher" /&gt; in &lt;head&gt;.
* Add Facebook AppID field in management page. IF save this, include &lt;div id="fb-root"&gt;&lt;/div&gt; any more.
* Add GA Tracking ID field in management page. IF save this, include Google Analytics tag.
* Add OGP setting.
* Add Google Site Verification setting.
* Add Meta description setting.
* Include any social scripts. ( Facebook, Twitter, Hatena Bookmark, Google+1 )
* Define widget areas.
* Define custom thumbnail sizes.
* Fix wp_title in the case of japanese.
* Set $content_width same as large_size_w.
* Add useful method: mw_wp_hacks::pager();
* Add useful method: mw_wp_hacks::get_top_parent_id();
* Add useful method: mw_wp_hacks::is_custom_post_type();
* Add useful method: mw_wp_hacks::the_localNav();
etc...

== Installation ==

1. Upload `MW WP Hacks` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Do setting in settings page.

== Changelog ==

= 0.4.2 =
* Delete: Delete og:local setting.

= 0.4.1 =
* Bugfix: Fix japanese sentence mistake in settings page.

= 0.4.0 =
* Add   : Custom post archive page's meta description setting.

= 0.3.0 =
* Refactoring

= 0.2.7 =
* Add   : mw_wp_hacks::get_description();
* Add   : Filter hook mw-wp-hacks-description.
* Add   : OGP setting.
* Add   : Google Site Verification setting.
* Add   : Meta description setting.

= 0.2.6 =
* Bugfix: 404 page title.
* Add   : Hide link in custom post edit page when public => 'false'.

= 0.2.5 =
* Add   : excerpt_more settiing.
* Bugfix: 404 page title.
* Change: Delete excerpt_length
* Change: Added feed post type "post" when activate.

= 0.2.4 =
* Bugfix: Undefined $facebook_app_id error.
* Bugfix: Notice error in 404 page.
* Bugfix: Can't delete meta box error.

= 0.2.3 =
* Supported less than php 5.3.
* Display message at the update.

= 0.2.1 =
* Fix readme.txt

= 0.2.0 =
* Initial release.

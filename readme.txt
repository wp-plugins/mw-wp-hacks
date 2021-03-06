=== Plugin Name ===
Contributors: inc2734
Donate link: http://www.amazon.co.jp/registry/wishlist/39ANKRNSTNW40
Tags: plugin, hack, setting
Requires at least: 3.6
Tested up to: 4.1.0
Stable tag: 1.4.0
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
* Add UA Tracking ID field in management page. IF save this, include Universal Analytics tag.
* Add OGP & Twitter Cards setting.
* Add Google Site Verification setting.
* Add Meta description setting.
* Include any social scripts. ( Facebook, Twitter, Hatena Bookmark, Google+1 )
* Define widget areas.
* Define custom thumbnail sizes.
* Fix wp_title in the case of japanese.
* You can setting that custom post type to disable a single page.
* You can setting that posts per page for custom post type.
* Useful method: MW_WP_Hacks::pager();
* Useful method: MW_WP_Hacks::get_top_parent_id();
* Useful method: MW_WP_Hacks::is_custom_post_type();
* Useful method: MW_WP_Hacks::the_local_nav(); There is the shortcode [local_nav]
* Useful method: MW_WP_Hacks::the_bread_crumb();
* The Class that Register Custom Post Type and Custom taxonomy.  
  ‘‘‘
  $Manage_Custom_Post_Type = new MW_WP_Hacks_Manage_Custom_Post_Type();
  $Manage_Custom_Post_Type->custom_post_type( '新着情報', 'news',
      array( 'title', 'editor', 'author', 'excerpt', 'comments', 'revisions', 'thumbnail' ),
      array( 'has_archive' => false )
  );
  $Manage_Custom_Post_Type->custom_taxonomy( '新着カテゴリー', 'news-category', array( 'news' ),
    array( 'hierarchical' => true )
  );
  $Manage_Custom_Post_Type->init();
  ‘‘‘
* etc...

== Installation ==

1. Upload `MW WP Hacks` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Do setting in settings page.

== Changelog ==

= 1.3.6 =
* Refactoring bread crumb.
* Refactoring local navigation.
* Add shortcode [local_nav]
* Add Twitter Cards setting.

= 1.3.5 =
* Fixed a bug that posts per page setting are not reflected in taxonomy archive page.

= 1.3.4 =
* Add setting that taxonomy archive to be disabled.

= 1.3.3 =
* Fixed a bug that widget does not display.

= 1.3.2 =
* Fixed a bug that sometimes incorrect fb:app_id is output.

= 1.3.1 =
* Add "Use default" option in posts per page for custom post type settings.

= 1.3.0 =
* Remove feature: Taxonomy archive to be disabled
* Add setting that posts per page for custom post type.

= 1.2.0 =
* Add setting that taxonomy archive to be disabled.

= 1.1.0 =
* Add setting that custom post type to disable a single page.
* Fixed a japanese translation bug.

= 1.0.4 =
* Fixed a custom rss settings bug.

= 1.0.3 =
* Fixed a custom rss settings bug.

= 1.0.2 =
* Fixed a bug that is incorrect ogp image size.

= 1.0.1 =
* Fixed the Universal Analytics bug.

= 1.0.0 =
* Refactoring.
* Add Method: MW_WP_Hacks::the_bread_crumb()
* Add Class: MW_WP_Hacks_Manage_Custom_Post_Type

= 0.6.1 =
* Bugfix: Fix bug that display notice error in feed.

= 0.6.0 =
* Add   : Facebook social script locale setting.
* Add   : Google social script locale setting.

= 0.5.0 =
* Add   : Add Universal Analytics Tracking ID setting.

= 0.4.4 =
* Add   : Custom thumbnail size can be selected in media uploader.
* Change: Delete taxonomy name in wp_title() that to display tax name.

= 0.4.3 =
* Add   : In the case of the page where both is_author and is_post_tupe_archive are true, set is_author to a false.

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

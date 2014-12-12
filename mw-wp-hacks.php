<?php
/**
 * Plugin Name: MW WP Hacks
 * Plugin URI: https://github.com/inc2734/mw-wp-hacks
 * Description: MW WP Hacks is plugin to help with development in WordPress.
 * Version: 1.0.4
 * Author: Takashi Kitajima
 * Author URI: http://2inc.org
 * Text Domain: mw-wp-hacks
 * Domain Path: /languages/
 * Created : September 30, 2013
 * Modified: December 12, 2014
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
class MW_WP_Hacks {

	/**
	 * array $options
	 */
	protected $options;

	/**
	 * array $fields
	 */
	protected $fields = array();

	/**
	 * __construct
	 */
	public function __construct() {
		register_uninstall_hook( __FILE__, array( __CLASS__, 'uninstall' ) );
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
	}

	/**
	 * plugins_loaded
	 */
	public function plugins_loaded() {
		load_plugin_textdomain( 'mw-wp-hacks', false, basename( dirname( __FILE__ ) ) . '/languages' );

		include_once( plugin_dir_path( __FILE__ ) . 'classes/class.config.php' );
		include_once( plugin_dir_path( __FILE__ ) . 'classes/class.mw-wp-hacks-admin.php' );
		include_once( plugin_dir_path( __FILE__ ) . 'classes/class.abstract-setting.php' );
		include_once( plugin_dir_path( __FILE__ ) . 'classes/class.setting-general.php' );
		include_once( plugin_dir_path( __FILE__ ) . 'classes/class.setting-description.php' );
		include_once( plugin_dir_path( __FILE__ ) . 'classes/class.setting-excerpt.php' );
		include_once( plugin_dir_path( __FILE__ ) . 'classes/class.setting-excerptmore.php' );
		include_once( plugin_dir_path( __FILE__ ) . 'classes/class.setting-feed.php' );
		include_once( plugin_dir_path( __FILE__ ) . 'classes/class.setting-ogp.php' );
		include_once( plugin_dir_path( __FILE__ ) . 'classes/class.setting-script.php' );
		include_once( plugin_dir_path( __FILE__ ) . 'classes/class.setting-social.php' );
		include_once( plugin_dir_path( __FILE__ ) . 'classes/class.setting-thumbnail.php' );
		include_once( plugin_dir_path( __FILE__ ) . 'classes/class.setting-widget.php' );
		include_once( plugin_dir_path( __FILE__ ) . 'classes/class.model.php' );
		include_once( plugin_dir_path( __FILE__ ) . 'classes/class.local-nav.php' );
		include_once( plugin_dir_path( __FILE__ ) . 'classes/class.manage-custom-post-type.php' );
		include_once( plugin_dir_path( __FILE__ ) . 'classes/class.bread-crumb.php' );

		$Admin               = new MW_WP_Hacks_Admin();
		$Setting_General     = new MW_WP_Hacks_Setting_General();
		$Setting_Description = new MW_WP_Hacks_Setting_Description();
		$Setting_Excerpt     = new MW_WP_Hacks_Setting_Excerpt();
		$Setting_Excerptmore = new MW_WP_Hacks_Setting_Excerptmore();
		$Setting_Feed        = new MW_WP_Hacks_Setting_Feed();
		$Setting_Ogp         = new MW_WP_Hacks_Setting_Ogp();
		$Setting_Script      = new MW_WP_Hacks_Setting_Script();
		$Setting_Social      = new MW_WP_Hacks_Setting_Social();
		$Setting_Thumbnail   = new MW_WP_Hacks_Setting_Thumbnail();
		$Setting_Widget      = new MW_WP_Hacks_Setting_Widget();
		$Model = new MW_WP_Hacks_Model( array(
			'General'     => $Setting_General,
			'Description' => $Setting_Description,
			'Excerpt'     => $Setting_Excerpt,
			'Excerptmore' => $Setting_Excerptmore,
			'Feed'        => $Setting_Feed,
			'Ogp'         => $Setting_Ogp,
			'Script'      => $Setting_Script,
			'Social'      => $Setting_Social,
			'Thumbnail'   => $Setting_Thumbnail,
			'Widget'      => $Setting_Widget,
		) );

		// general
		$Settng_General_option = $Setting_General->get_option();
		if ( $Settng_General_option['wp_generator'] === 'true' ) {
			remove_action( 'wp_head', 'wp_generator' );
		}
		if ( $Settng_General_option['page_excerpt'] === 'true' ) {
			add_post_type_support( 'page', 'excerpt' );
		}
		if ( $Settng_General_option['display_only_self_uploaded'] === 'true' ) {
			add_action( 'pre_get_posts', array( $Model, 'display_only_self_uploaded_medias' ) );
			add_action( 'wp_ajax_query-attachments', array( $Model, 'define_doing_query_attachment_const' ), 0 );
		}
		if ( $Settng_General_option['fix_is_author'] === 'true' ) {
			add_action( 'pre_get_posts', array( $Model, 'fix_is_author_and_is_archive_post_type' ) );
		}
		if ( $Settng_General_option['fix_caption_width'] === 'true' ) {
			add_filter( 'img_caption_shortcode', array( $Model, 'set_img_caption' ), 10, 3 );
		}
		if ( $Settng_General_option['checked_ontop'] === 'true' ) {
			add_action( 'wp_terms_checklist_args', array( $Model, 'wp_category_terms_checklist_no_top' ) );
		}
		if ( $Settng_General_option['remove_updated_link'] === 'true' ) {
			add_action( 'admin_head', array( $Model, 'cpt_public_false' ) );
		}
		if ( $Settng_General_option['thumbnail_info'] === 'true' ) {
			add_filter( 'admin_post_thumbnail_html', array( $Model, 'admin_post_thumbnail_html' ) );
		}
		if ( $Settng_General_option['fix_ja_title'] === 'true' && get_locale() === 'ja' ) {
			add_filter( 'wp_title', array( $Model, 'wp_title' ), 10, 3 );
		}

		// description
		$Settng_Description_option = $Setting_Description->get_option();
		if ( $Settng_Description_option['view'] === 'true' ) {
			add_action( 'wp_head', array( $Model, 'display_description' ) );
		}

		// excerpt
		$Setting_Excerpt_option = $Setting_Excerpt->get_option();
		if ( !empty( $Setting_Excerpt_option ) ) {
			add_filter( 'wp_trim_excerpt', array( $Model, 'wp_trim_excerpt' ) );
		}

		// Excerptmore
		$Setting_Excerptmore_option = $Setting_Excerptmore->get_option();
		if ( !empty( $Setting_Excerptmore_option ) ) {
			add_filter( 'excerpt_more', array( $Model, 'excerpt_more' ) );
		}

		// feed
		$Setting_Feed_option = $Setting_Feed->get_option();
		if ( in_array( 'true', $Setting_Feed_option ) ) {
			add_filter( 'pre_get_posts', array( $Model, 'set_rss_post_types' ) );
		}

		// ogp
		$Setting_Ogp_option = $Setting_Ogp->get_option();
		if ( $Setting_Ogp_option['use_ogp'] === 'true' ) {
			add_action( 'wp_head', array( $Model, 'display_ogp_tags' ) );
		}
		if ( $Setting_Ogp_option['update_cache'] === 'true' ) {
			add_action( 'transition_post_status', array( $Model, 'update_facebook_cache' ), 10, 3 );
		}

		// script
		add_filter( 'wp_footer', array( $Model, 'social_button_footer' ) );

		// social
		add_action( 'wp_head', array( $Model, 'add_profile_for_google_plus' ) );
		add_action( 'wp_head', array( $Model, 'add_google_site_verification' ) );

		// thumbnail
		$Setting_Thumbnail_option = $Setting_Thumbnail->get_option();
		//if ( count( $Setting_Thumbnail_option ) > 1 ) {
			add_action( 'init', array( $Model, 'set_thumbnail' ) );
		//}

		// widget
		$Setting_Widget_option = $Setting_Widget->get_option();
		if ( count( $Setting_Widget_option ) > 1 ) {
			add_action( 'widgets_init', array( $Model, 'widgets_init' ) );
		}
	}

	/**
	 * uninstall
	 * アンインストールした時の処理
	 */
	public static function uninstall() {
		delete_option( 'mw-wp-hacks' );
		delete_option( 'mw-wp-hacks-general' );
		delete_option( 'mw-wp-hacks-description' );
		delete_option( 'mw-wp-hacks-excerpt' );
		delete_option( 'mw-wp-hacks-excerptmore' );
		delete_option( 'mw-wp-hacks-feed' );
		delete_option( 'mw-wp-hacks-ogp' );
		delete_option( 'mw-wp-hacks-script' );
		delete_option( 'mw-wp-hacks-social' );
		delete_option( 'mw-wp-hacks-thumbnail' );
		delete_option( 'mw-wp-hacks-widget' );
	}

	/**
	 * get_description
	 * deprecated
	 * @param int 文字数
	 * @return string description
	 */
	public static function get_description( $strnum = 200 ) {
		echo 'This is a deprecated function MW_WP_Hacks::get_description().'.
		include_once( plugin_dir_path( __FILE__ ) . 'classes/class.model.php' );
		$Model = new MW_WP_Hacks_Model();
		return $Model->get_description( $strnum );
	}

	/**
	 * is_custom_post_type
	 * 引数無いときはカスタム投稿タイプかどうか、あるときはそのカスタム投稿タイプかどうか
	 * @param string $pt カスタム投稿タイプ名
	 * @return bool
	 */
	public static function is_custom_post_type( $pt = '' ) {
		$default_post_type = get_post_types( array(
			'_builtin' => true
		) );
		$post_type = get_post_type();
		if ( !$post_type ) {
			global $wp_query;
			if ( isset( $wp_query->query['post_type'] ) ) {
				$post_type = $wp_query->query['post_type'];
			}
		}
		if ( !empty( $post_type ) && !in_array( $post_type, $default_post_type ) ) {
			if ( empty( $pt ) ) {
				return true;
			} elseif ( $pt === $post_type ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * get_top_parent_id
	 * 一番上位の親ページのIDを取得。自分が一番上だったら自分のIDを返す
	 * @param object $post
	 * @return int post_id
	 */
	public static function get_top_parent_id( $post ) {
		if ( empty( $post->ID ) )
			return;
		$ancectors = get_post_ancestors( $post );
		$ancestor = 0;
		if ( $ancectors )
			$ancestor = @array_pop( get_post_ancestors( $post ) );
		if ( !$ancestor )
			return $post->ID;
		return $ancestor;
	}

	/**
	 * pager
	 */
	public static function pager() {
		global $wp_rewrite;
		global $wp_query;
		global $paged;
		$paginate_base = get_pagenum_link( 1 );
		if ( strpos( $paginate_base, '?' ) || ! $wp_rewrite->using_permalinks() ) {
			$paginate_format = '';
			$paginate_base = add_query_arg( 'paged', '%#%' );
		} else {
			$paginate_format = ( substr( $paginate_base, -1 ,1 ) == '/' ? '' : '/' ) .
			user_trailingslashit( 'page/%#%/', 'paged' );
			$paginate_base .= '%_%';
		}
		$paginate_links = paginate_links( array(
			'base'      => $paginate_base,
			'format'    => $paginate_format,
			'total'     => $wp_query->max_num_pages,
			'mid_size'  => 5,
			'current'   => ( $paged ? $paged : 1 ),
			'prev_text' => '&lt;',
			'next_text' => '&gt;',
		) );
		if ( $paginate_links ) {
			?>
			<div class="pager">
				<p>
					<?php echo $paginate_links; ?>
				</p>
			<!-- end .pager --></div>
			<?php
		}
	}

	/**
	 * the_localNav
	 * localNav を表示
	 * @param array $args
	 */
	public static function the_localNav( array $args = array() ) {
		$Local_Nav = new MW_WP_Hacks_Local_Nav( $args );
		$Local_Nav->display();
	}

	/**
	 * the_bread_crumb
	 * パンくずリストを表示
	 * @param array $args
	 */
	public static function the_bread_crumb( array $args = array() ) {
		$Bread_Crumb = new MW_WP_Hacks_Bread_Crumb();
		$Bread_Crumb->display( $args );
	}
}
$MW_WP_Hacks = new MW_WP_Hacks();

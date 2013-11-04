<?php
/**
 * Plugin Name: MW WP Hacks
 * Plugin URI: http://2inc.org
 * Description: MW WP Hacks is plugin to help with development in WordPress.
 * Version: 0.2.2
 * Author: Takashi Kitajima
 * Author URI: http://2inc.org
 * Text Domain: mw-wp-hacks
 * Domain Path: /languages/
 * Created : September 30, 2013
 * Modified: December 4, 2013
 * License: GPL2
 *
 * Copyright 2013 Takashi Kitajima (email : inc@2inc.org)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
$mw_wp_hacks = new mw_wp_hacks();
class mw_wp_hacks {

	const NAME = 'mw-wp-hacks';
	const DOMAIN = 'mw-wp-hacks';
	private $option;

	/**
	 * __construct
	 */
	public function __construct() {
		// 有効化した時の処理
		register_activation_hook( __FILE__, array( __CLASS__, 'activation' ) );
		// アンインストールした時の処理
		register_uninstall_hook( __FILE__, array( __CLASS__, 'uninstall' ) );

		$this->option['feed']      = get_option( self::NAME . '-feed' );
		$this->option['excerpt']   = get_option( self::NAME . '-excerpt' );
		$this->option['social']    = get_option( self::NAME . '-social' );
		$this->option['thumbnail'] = get_option( self::NAME . '-thumbnail' );
		$this->option['widget']    = get_option( self::NAME . '-widget' );
		$this->option['script']    = get_option( self::NAME . '-script' );

		add_action( 'init', array( $this, 'init' ) );
		add_action( 'init', array( $this, 'set_content_width' ) );
		add_action( 'init', array( $this, 'set_thumbnail' ) );
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
	}

	/**
	 * activation
	 * 有効化した時の処理
	 */
	public static function activation() {
	}

	/**
	 * uninstall
	 * アンインストールした時の処理
	 */
	public static function uninstall() {
		delete_option( self::NAME . '-feed' );
		delete_option( self::NAME . '-excerpt' );
		delete_option( self::NAME . '-social' );
		delete_option( self::NAME . '-thumbnail' );
		delete_option( self::NAME . '-widget' );
		delete_option( self::NAME . '-script' );
		$users = get_users();
		foreach ( $users as $user ) {
			delete_user_meta( $user->ID, self::NAME . '_google_plus_id' );
		}
	}

	/**
	 * init
	 */
	public function init() {
		load_plugin_textdomain( self::DOMAIN, false, basename( dirname( __FILE__ ) ) . '/languages' );

		remove_action( 'wp_head', 'wp_generator' );
		add_post_type_support( 'page', 'excerpt' );
		//add_editor_style();

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		add_filter( 'pre_get_posts', array( $this, 'set_rss_post_types' ) );
		add_filter( 'excerpt_mblength', array( $this, 'excerpt_length' ) );
		add_filter( 'excerpt_more', array( $this, 'excerpt_more' ) );
		add_filter( 'wp_trim_excerpt', array( $this, 'wp_trim_excerpt' ) );
		add_action( 'pre_get_posts', array( $this, 'display_only_self_uploaded_medias' ) );
		add_action( 'wp_ajax_query-attachments', array( $this, 'define_doing_query_attachment_const' ), 0 );
		add_filter( 'img_caption_shortcode', array( $this, 'set_img_caption' ), 10, 3 );
		add_action( 'wp_terms_checklist_args', array( $this, 'wp_category_terms_checklist_no_top' ) );
		add_filter( 'admin_post_thumbnail_html', array( $this, 'admin_post_thumbnail_html' ) );
		add_action( 'wp_head', array( $this, 'add_profile_for_google_plus' ) );
		add_filter( 'wp_footer', array( $this, 'social_button_footer' ) );
		add_filter( 'wp_footer', array( $this, 'facebook_root' ) );
		add_filter( 'user_contactmethods', array( $this, 'add_custom_contactmethods' ) );
		add_action( 'profile_update', array( $this, 'profile_update' ), 10, 2 );
		if ( defined( 'WPLANG' ) && WPLANG === 'ja' ) {
			add_filter( 'wp_title', array( $this, 'wp_title' ), 10, 3 );
		}
	}

	public function admin_menu() {
		include_once( plugin_dir_path( __FILE__ ) . 'system/mw_wp_hacks_admin_page.php' );
		new mw_wp_hacks_admin_page();
	}

	public function facebook_root() {
		$ga_tracking_id = '';
		if ( !empty( $this->option['social']['facebook_app_id'] ) ) {
			$facebook_app_id = $this->option['social']['facebook_app_id'];
		}
		if ( $facebook_app_id ) {
			?>
			<div id="fb-root"></div>
			<script type="text/javascript">
			window.fbAsyncInit = function() {
				FB.init({
					appId	: <?php echo esc_html( $facebook_app_id ); ?>, // App ID
					status	: true, // check login status
					cookie	: true, // enable cookies to allow the server to access the session
					xfbml	: true  // parse XFBML
				});
			};
			</script>
			<?php
		}
	}

	/**
	 * ソーシャル等のスクリプトの非同期読込
	 */
	public function social_button_footer() {
		$ga_tracking_id = '';
		if ( !empty( $this->option['social']['ga_tracking_id'] ) ) {
			$ga_tracking_id = $this->option['social']['ga_tracking_id'];
		}
		$scripts = array();
		if ( !empty( $this->option['script'] ) && is_array( $this->option['script'] ) ) {
			$scripts = $this->option['script'];
		}
		?>
		<?php if ( $scripts || $ga_tracking_id ) : ?>
		<script type="text/javascript">
		( function( doc, script ) {
			var js;
			var fjs = doc.getElementsByTagName( script )[0];
			var add = function( url, id, o ) {
				if ( doc.getElementById( id ) ) { return; }
				js = doc.createElement( script );
				js.src = url; js.async = true; js.id = id;
				fjs.parentNode.insertBefore( js, fjs );
				if ( window.ActiveXObject && o != null ) {
					js.onreadystatechange = function() {
						if ( js.readyState == 'complete' ) o();
						if ( js.readyState == 'loaded' ) o();
					};
				} else {
					js.onload = o;
				}
			};
			<?php if ( $ga_tracking_id ) : ?>
			add( ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js', 'google-analytics', function() {
				window._gaq = _gaq || [];
				_gaq.push(['_setAccount', '<?php echo $ga_tracking_id; ?>']);
				_gaq.push(['_trackPageview']);
			} );
			<?php endif; ?>
			<?php if ( !empty( $scripts['facebook'] ) && $scripts['facebook'] == 'facebook' ) : ?>
			add( '//connect.facebook.net/ja_JP/all.js', 'facebook-jssdk' );
			<?php endif; ?>
			<?php if ( !empty( $scripts['twitter'] ) && $scripts['twitter'] == 'twitter' ) : ?>
			add( '//platform.twitter.com/widgets.js', 'twitter-wjs' );
			<?php endif; ?>
			<?php if ( !empty( $scripts['hatena'] ) && $scripts['hatena'] == 'hatena' ) : ?>
			add( 'http://b.st-hatena.com/js/bookmark_button.js', 'hatena-js' );
			<?php endif; ?>
			<?php if ( !empty( $scripts['google'] ) && $scripts['google'] == 'google' ) : ?>
			window.___gcfg = { lang: "ja" };
			add( 'https://apis.google.com/js/plusone.js' );
			<?php endif; ?>
		}( document, 'script' ) );
		</script>
		<?php endif; ?>
	<?php
	}

	/****************************************************************************/

	/**
	 * set_content_width
	 */
	public function set_content_width() {
		global $content_width;
		$width = apply_filters( self::NAME . '-content_width', get_option( 'large_size_w' ) );
		$content_width = $width;
	}

	/**
	 * set_thumbnail
	 */
	public function set_thumbnail() {
		if ( !empty( $this->option['thumbnail'] ) && is_array( $this->option['thumbnail'] ) ) {
			add_theme_support( 'post-thumbnails' );
			$thumbnails = $this->option['thumbnail'];
			foreach ( $thumbnails as $thumbnail ) {
				add_image_size( $thumbnail['name'], $thumbnail['width'], $thumbnail['height'], $thumbnail['crop'] );
			}
			//var_dump( get_intermediate_image_sizes() );
		}
	}

	/**
	 * set_rss_post_types
	 */
	public function set_rss_post_types( $query ) {
		if ( is_feed() ) {
			$post_type = $query->get( 'post_type' );
			if ( empty( $post_type ) ) {
				if ( !empty( $this->option['feed'] ) && is_array( $this->option['feed'] ) ) {
					$query->set( 'post_type', $this->option['feed'] );
				}
			}
			return $query;
		}
	}

	/**
	 * excerpt_length
	 * returnが抜粋に表示される文字数
	 */
	public function excerpt_length( $length ) {
		return 150;
	}

	/**
	 * excerpt_more
	 * 抜粋もしくは本文が一定の文字数を超えたときに実行される
	 */
	public function excerpt_more( $post ) {
		return '...';
	}

	/**
	 * wp_trim_excerpt
	 * the_excerpt実行時に実行される
	 */
	public function wp_trim_excerpt( $excerpt ) {
		global $post;
		$more = '';
		if ( !empty( $this->option['excerpt'] ) && !is_array( $this->option['excerpt'] ) ) {
			$more = $this->option['excerpt'];
			$more = preg_replace( '/%link%/', get_permalink(), $more );
		}
		return $excerpt . $more;
	}

	/**
	 * widgets_init
	 * 後で管理画面作成
	 */
	public function widgets_init() {
		$widgets = array();
		if ( !empty( $this->option['widget'] ) && is_array( $this->option['widget'] ) ) {
			$widgets = $this->option['widget'];
			foreach ( $widgets as $widget ) {
				register_sidebar( $widget );
			}
		}
	}

	/**
	 * wp_title
	 * ページタイトルを出力
	 */
	public function wp_title( $title, $sep, $seplocation ) {
		global $page, $paged;

		$title = trim( $title );
		$_sep = $sep;
		$_sep = ' ' . $_sep . ' ';
		/*
		if ( !$sep && !is_front_page() ) {
			if ( $seplocation == 'right' ) {
				$title .= $_sep;
			} else {
				$title = $_sep . $title;
			}
		}
		*/

		if ( is_date() ) {
			$title = '';
			if ( $y = intval( get_query_var( 'year' ) ) )
				$title .= sprintf( '%4d年', $y );
			if ( $m = intval( get_query_var( 'monthnum' ) ) )
				$title .= sprintf( '%2d月', $m );
			if ( $d = intval( get_query_var( 'day' ) ) ) {
				$title .= sprintf( '%2d日', $d );
			}
		}
		if ( is_search() ) {
			$title = str_replace( ':   ', ':', $title );
			$title = str_replace( $sep, '', $title );
			$title = preg_replace( '/検索結果\:/', '', $title );
			$title = trim( $title );
			$title = '検索結果: ' . $title;
		}
		if ( $paged >= 2 || $page >= 2 ) {
			if ( $seplocation == 'right' ) {
				$title .= ' ページ' . max( $paged, $page );
			} else {
				$title = 'ページ' . max( $paged, $page ) . ' ' . $title;
			}
		}
		if ( is_singular() || ( is_archive() && !is_paged() ) ) {
			$title = str_replace( $sep, '', $title );
			$title = trim( $title );
		}
		if ( $seplocation == 'right' ) {
			if ( $sep && !is_front_page() )
				$title .= $_sep;
		} else {
			if ( $sep && !is_front_page() )
				$title = $_sep . $title;
		}
		return $title;
	}

	/**
	 * display_only_self_uploaded_medias
	 */
	public function display_only_self_uploaded_medias( $wp_query ) {
		global $userdata;
		if ( is_admin() && ( $wp_query->is_main_query() || ( defined( 'DOING_QUERY_ATTACHMENT' ) && DOING_QUERY_ATTACHMENT ) ) && $wp_query->get( 'post_type' ) == 'attachment' && !current_user_can( 'manage_options' ) ) {
			$user = wp_get_current_user();
			$wp_query->set( 'author', $user->ID );
		}
	}

	/**
	 * define_doing_query_attachment_const
	 */
	public function define_doing_query_attachment_const() {
		if ( ! defined( 'DOING_QUERY_ATTACHMENT' ) ) {
			define( 'DOING_QUERY_ATTACHMENT', true );
		}
	}

	/**
	 * set_img_caption
	 * デフォルトでdiv.wp-captionのwidthが+10pxされるのを無くす
	 */
	public function set_img_caption( $output, $attr, $content ) {
		extract( shortcode_atts( array(
			'id'		=> '',
			'align'		=> 'alignnone',
			'width'		=> '',
			'caption'	=> ''
		), $attr) );
		if ( 1 > (int) $width || empty( $caption ) )
			return $content;
		if ( $id ) $id = 'id="' . esc_attr( $id ) . '" ';
		return '<div ' . $id . 'class="wp-caption ' . esc_attr( $align )
			. '" style="width: ' . (int) $width . 'px">'
			. do_shortcode( $content )
			. '<p class="wp-caption-text">' . $caption . '</p>'
			. '</div>';
	}

	/**
	 * wp_category_terms_checklist_no_top
	 * カテゴリーの並び順を調整
	 */
	public function wp_category_terms_checklist_no_top( $args, $post_id = null ) {
		$args['checked_ontop'] = false;
		return $args;
	}

	/**
	 * admin_post_thumbnail_html
	 * アイキャッチ画像の設定部分に説明を追加
	 */
	public function admin_post_thumbnail_html( $content ) {
		global $_wp_additional_image_sizes;
		if ( isset( $_wp_additional_image_sizes['post-thumbnail'] ) ) {
			$postThumbnail = $_wp_additional_image_sizes['post-thumbnail'];
			if ( isset( $postThumbnail['height'], $postThumbnail['width'] ) ) {
				$height = $postThumbnail['height'];
				$width  = $postThumbnail['width'];
				$content .= '<p class="howto">推奨サイズ：' . $width . ' x ' . $height . '<br />※これより大きいサイズの画像を指定した場合は自動的にリサイズ&amp;トリミングされます。</p>';
			}
		}
		return $content;
	}

	/**
	 * add_profile_for_google_plus
	 * Google+ 用の link タグを出力
	 */
	public function add_profile_for_google_plus() {
		global $post;
		if ( !empty( $this->option['social']['google_plus_id'] ) && !is_singular() ) {
			?>
			<link rel="publisher" href="https://plus.google.com/<?php echo esc_attr(  $this->option['social']['google_plus_id'] ); ?>/" />
			<?php
		} elseif ( get_user_meta( $post->post_author, self::NAME . '_google_plus_id', true ) != '' && is_singular() ) {
			?>
			<link rel="author" href="https://plus.google.com/<?php echo esc_attr( get_user_meta( $post->post_author, self::NAME . '_google_plus_id', true ) ); ?>/" />
			<?php
		}
	}

	/**
	 * add_custom_contactmethods
	 * 連絡先情報からAIM, YIM, Jabberを削除、Google+ IDを追加
	 */
	public function add_custom_contactmethods( $user_contactmethods ) {
		return array(
			self::NAME . '_google_plus_id' => 'Google+ ID'
		);
	}
	public function profile_update( $user_id, $old_user_data ) {
		if ( isset( $_POST[self::NAME . '_google_plus_id'] ) && preg_match( '/^\d+$/', $_POST[self::NAME . '_google_plus_id'] ) ) {
			update_user_meta( $user_id, self::NAME . '_google_plus_id', $_POST[self::NAME . '_google_plus_id'] );
		} else {
			update_user_meta( $user_id, self::NAME . '_google_plus_id', '' );
		}
	}

	/****************************************************************************/

	/**
	 * is_custom_post_type
	 * 引数無いときはカスタム投稿タイプかどうか、あるときはそのカスタム投稿タイプかどうか
	 * @param    String    カスタム投稿タイプ
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
	 * @return    Int    ページID
	 */
	public static function get_top_parent_id( $post ) {
		if ( empty( $post->ID ) )
			return;
		$ancectors = get_post_ancestors( $post );
		$ancestor = 0;
		if ( $ancectors )
			$ancestor = array_pop( get_post_ancestors( $post ) );
		if ( !$ancestor )
			return $post->ID;
		return $ancestor;
	}

	/**
	 * pager
	 * @return    String    html
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
	 * localNavを表示
	 */
	public static function the_localNav( Array $args = array() ) {
		global $post;
		$defaults = array(
			'title' => true,		// 親ページタイトル表示
			'hide_empty' => true,	// 子がいないときは表示しない
		);
		$args = array_merge( $defaults, $args );
		if ( is_front_page() ) return;

		$postTypeObject = get_post_type_object( get_post_type() );

		$localNavs = array();

		// 固定ページのとき
		if ( is_page() ) {
			$parentId = self::get_top_parent_id( $post );
			$children = wp_list_pages( array(
				'title_li' => '',
				'sort_column' => 'menu_order',
				'child_of' => $parentId,
				'echo' => false
			) );
			$title = get_the_title( $parentId );
			$titleLink = get_permalink( $parentId );

			$localNavs[] = array(
				'children' => $children,
				'title' => $title,
				'titleLink' => $titleLink,
			);

		// ブログもしくはカスタム投稿タイプのとき
		} elseif ( is_blog() || static::is_custom_post_type() ) {

			// カスタム投稿タイプ（固定ページ）のとき
			if ( static::is_custom_post_type() && !empty( $postTypeObject->hierarchical ) ) {
				$children = wp_list_pages( array(
					'title_li' => '',
					'sort_column' => 'menu_order',
					'post_type' => get_post_type(),
					'echo' => false
				) );
				$title = $postTypeObject->labels->name;

				$localNavs[] = array(
					'children' => $children,
					'title' => $title,
				);
			}

			if ( !empty( $postTypeObject->taxonomies ) ) {
				foreach ( $postTypeObject->taxonomies as $taxonomy_name ) {
					$children = wp_list_categories( array(
						'title_li' => '',
						'show_count' => false,
						'hide_empty' => true,
						'echo' => false,
						'taxonomy' => $taxonomy_name,
					) );
					$taxonomy = get_taxonomy( $taxonomy_name );
					if ( isset( $taxonomy->label ) )
						$title = $taxonomy->label;

					$localNavs[] = array(
						'children' => $children,
						'title' => $title,
					);
				}
			}
		}
		if ( $args['hide_empty'] && empty( $children ) ) return;
		?>
		<?php foreach ( $localNavs as $localNav ) : ?>
		<div class="localnav">
			<dl>
				<?php if ( !empty( $args['title'] ) ) : ?>
				<dt class="localnav-parent-title">
					<?php if ( empty( $localNav['titleLink'] ) ) : ?>
					<?php echo esc_html( $localNav['title'] ); ?>
					<?php else : ?>
					<a href="<?php echo esc_url( $localNav['titleLink'] ); ?>"><?php echo esc_html( $localNav['title'] ); ?></a>
					<?php endif; ?>
				</dt>
				<?php endif; ?>
				<dd class="localnav-sub-pages">
					<ul>
						<?php echo $localNav['children']; ?>
					</ul>
				</dd>
			</dl>
		<!-- end .widget-container --></div>
		<?php endforeach; ?>
		<?php
	}
}


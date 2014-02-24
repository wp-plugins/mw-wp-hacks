<?php
/**
 * Plugin Name: MW WP Hacks
 * Plugin URI: http://2inc.org
 * Description: MW WP Hacks is plugin to help with development in WordPress.
 * Version: 0.4.4
 * Author: Takashi Kitajima
 * Author URI: http://2inc.org
 * Text Domain: mw-wp-hacks
 * Domain Path: /languages/
 * Created : September 30, 2013
 * Modified: February 22, 2013
 * License: GPL2
 *
 * Copyright 2014 Takashi Kitajima (email : inc@2inc.org)
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

	private $option;
	private $fields = array();

	/**
	 * __construct
	 */
	public function __construct() {
		// 有効化した時の処理
		register_activation_hook( __FILE__, array( __CLASS__, 'activation' ) );
		// アンインストールした時の処理
		register_uninstall_hook( __FILE__, array( __CLASS__, 'uninstall' ) );

		include_once( plugin_dir_path( __FILE__ ) . 'system/mwhacks_config.php' );
		$this->fields = self::load_fields_classes();

		add_action( 'init', array( $this, 'init' ) );
		add_action( 'init', array( $this, 'set_content_width' ) );
	}

	public static function load_fields_classes() {
		$fields = array();
		include_once( plugin_dir_path( __FILE__ ) . 'system/abstract_mwhacks_base.php' );
		foreach ( glob( plugin_dir_path( __FILE__ ) . 'field/*.php' ) as $field ) {
			include_once $field;
			$className = basename( $field, '.php' );
			if ( class_exists( $className ) ) {
				$fields[] = new $className();
			}
		}
		return $fields;
	}

	/**
	 * activation
	 * 有効化した時の処理
	 */
	public static function activation() {
		$fields = self::load_fields_classes();
		foreach ( $fields as $field ) {
			$field->activation();
		}
	}

	/**
	 * uninstall
	 * アンインストールした時の処理
	 */
	public static function uninstall() {
		$fields = self::load_fields_classes();
		foreach ( $fields as $field ) {
			$field->uninstall();
		}
	}

	/**
	 * init
	 */
	public function init() {
		load_plugin_textdomain( MWHACKS_Config::DOMAIN, false, basename( dirname( __FILE__ ) ) . '/languages' );

		remove_action( 'wp_head', 'wp_generator' );
		add_post_type_support( 'page', 'excerpt' );
		//add_editor_style();

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		add_action( 'pre_get_posts', array( $this, 'display_only_self_uploaded_medias' ) );
		add_action( 'pre_get_posts', array( $this, 'fix_is_author_and_is_archive_post_type' ) );
		add_action( 'wp_ajax_query-attachments', array( $this, 'define_doing_query_attachment_const' ), 0 );
		add_filter( 'img_caption_shortcode', array( $this, 'set_img_caption' ), 10, 3 );
		add_action( 'wp_terms_checklist_args', array( $this, 'wp_category_terms_checklist_no_top' ) );
		add_action( 'admin_head', array( $this, 'cpt_public_false' ) );
		add_filter( 'admin_post_thumbnail_html', array( $this, 'admin_post_thumbnail_html' ) );

		if ( defined( 'WPLANG' ) && WPLANG === 'ja' ) {
			add_filter( 'wp_title', array( $this, 'wp_title' ), 10, 3 );
		}
	}

	public function admin_menu() {
		include_once( plugin_dir_path( __FILE__ ) . 'system/mw_wp_hacks_admin_page.php' );
		new mw_wp_hacks_admin_page();
	}

	/****************************************************************************/

	/**
	 * set_content_width
	 */
	public function set_content_width() {
		global $content_width;
		$width = apply_filters( MWHACKS_Config::NAME . '-content_width', get_option( 'large_size_w' ) );
		$content_width = $width;
	}

	/**
	 * wp_title
	 * ページタイトルを出力
	 */
	public function wp_title( $title, $sep, $seplocation ) {
		global $page, $paged;
		if ( is_date() ) {
			$title = '';
			if ( $y = intval( get_query_var( 'year' ) ) )
				$title .= sprintf( '%4d年', $y );
			if ( $m = intval( get_query_var( 'monthnum' ) ) )
				$title .= sprintf( '%2d月', $m );
			if ( $d = intval( get_query_var( 'day' ) ) ) {
				$title .= sprintf( '%2d日', $d );
			}
			if ( $seplocation == 'right' ) {
				$title .= ' ' . $sep . ' ';
			} else {
				$title = ' ' . $sep . ' ' . $title;
			}
		}
		elseif ( is_search() ) {
			if ( $seplocation == 'right' ) {
				$pattern = '/(.*?) ' . preg_quote( $sep ) . ' (検索結果\:)  (' . preg_quote( $sep ) . ' )$/';
				$title = preg_replace( $pattern, '$2$1 $3', $title );
			} else {
				$pattern = '/^( ' . preg_quote( $sep ) . ' )(検索結果\:)  ' . preg_quote( $sep ) . '  (.*?)$/';
				$title = preg_replace( $pattern, '$1$2 $3', $title );
			}
		}
		elseif ( is_tax() ) {
			$term_obj = get_queried_object();
			$title = $term_obj->name;
			if ( $seplocation == 'right' ) {
				$title .= ' ' . $sep . ' ';
			} else {
				$title = ' ' . $sep . ' ' . $title;
			}
		}
		elseif ( $paged >= 2 || $page >= 2 ) {
			if ( $seplocation == 'right' ) {
				$title .= 'ページ' . max( $paged, $page );
			} else {
				$title = 'ページ' . max( $paged, $page ) . $title;
			}
			if ( $seplocation == 'right' ) {
				$title .= ' ' . $sep . ' ';
			} else {
				$title = ' ' . $sep . ' ' . $title;
			}
		}
		if ( !$sep )
			$title = trim( $title );
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
	 * fix_is_author_and_is_archive_post_type
	 */
	public function fix_is_author_and_is_archive_post_type( $wp_query ) {
		if ( is_admin() || !$wp_query->is_main_query() )
			return;
		if ( $wp_query->is_author && $wp_query->is_post_type_archive ) {
			$wp_query->is_author = false;
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
	 * cpt_public_false
	 * public => false なカスタム投稿タイプの場合は更新しましたのリンクを消す
	 */
	public function cpt_public_false() {
		// カスタム投稿タイプのときだけ実行
		$cpts = get_post_types( array(
			'_builtin' => false,
		) );
		$pt = get_post_type();
		if ( ! in_array( $pt, $cpts ) )
			return;
		// カスタム投稿タイプオブジェクトを取得
		$pto = get_post_type_object( get_post_type() );
		// public => false のとき
		if ( isset( $pto->public ) && $pto->public == false ) {
			?>
			<style type="text/css">
			.post-php #message a {
				display: none;
			}
			.wp-list-table .post-title span.more-link {
				display: none;
			}
			</style>
			<?php
		}
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
			$ancestor = @array_pop( get_post_ancestors( $post ) );
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
		} elseif ( is_blog() || self::is_custom_post_type() ) {

			// カスタム投稿タイプ（固定ページ）のとき
			if ( self::is_custom_post_type() && !empty( $postTypeObject->hierarchical ) ) {
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
		<!-- end .localNav --></div>
		<?php endforeach; ?>
		<?php
	}

	/**
	 * descriptionを取得
	 * @param	Int 文字数
	 * @return	String description
	 */
	public static function get_description( $strnum = 200 ) {
		global $post;
		$description = get_bloginfo( 'description' );
		$site_description = $description;
		if ( is_singular() && empty( $post->post_password ) ) {
			if ( !empty( $post->post_excerpt ) ) {
				$description = $post->post_excerpt;
			} elseif ( !empty( $post->post_content ) ) {
				$description = $post->post_content;
			}
		}
		$description = strip_shortcodes( $description );
		$description = str_replace( ']]>', ']]&gt;', $description );
		$description = strip_tags( $description );
		$description = str_replace( array( "\r\n","\r","\n" ), '', $description );
		$description = mb_strimwidth( $description, 0, $strnum, "...", 'utf8' );
		if ( empty( $description ) ) {
			$description = $site_description;
		}
		return apply_filters( MWHACKS_Config::NAME . '-description', $description );
	}
}



<?php
/**
 * Name       : MW WP Hacks Model
 * Description: 管理画面
 * Version    : 1.0.1
 * Author     : Takashi Kitajima
 * Author URI : http://2inc.org
 * Create     : November 13, 2014
 * Modified   : November 24, 2014
 * License    : GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
class MW_WP_Hacks_Model {

	/**
	 * $settings
	 */
	private $settings = array();

	/**
	 * __construct
	 */
	public function __construct( array $settings ) {
		$this->settings = apply_filters( MW_WP_Hacks_Config::NAME . '-setting-objects', $settings );
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
	 * $this->display_only_self_uploaded_medias() で使用する定数を定義
	 */
	public function define_doing_query_attachment_const() {
		if ( ! defined( 'DOING_QUERY_ATTACHMENT' ) ) {
			define( 'DOING_QUERY_ATTACHMENT', true );
		}
	}

	/**
	 * display_only_self_uploaded_medias
	 * メディア管理画面で自分のアップロードしたメディアだけを表示
	 * @param WP_Query $wp_query
	 */
	public function display_only_self_uploaded_medias( $wp_query ) {
		global $userdata;
		if ( is_admin() && ( $wp_query->is_main_query() || ( defined( 'DOING_QUERY_ATTACHMENT' ) && DOING_QUERY_ATTACHMENT ) ) && $wp_query->get( 'post_type' ) == 'attachment' && !current_user_can( 'edit_others_pages' ) ) {
			$user = wp_get_current_user();
			$wp_query->set( 'author', $user->ID );
		}
	}

	/**
	 * set_img_caption
	 * デフォルトで div.wp-caption の widthが + 10px されるのを無くす
	 * @param string $empty
	 * @param array $attr
	 * @param string $content
	 * @return string $content
	 */
	public function set_img_caption( $empty, $attr, $content ) {
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
	 * @param array $args
	 * @return array $args
	 */
	public function wp_category_terms_checklist_no_top( $args ) {
		$args['checked_ontop'] = false;
		return $args;
	}

	/**
	 * cpt_public_false
	 * public => false なカスタム投稿タイプの場合は「更新しました」のリンクを消す
	 */
	public function cpt_public_false() {
		// カスタム投稿タイプのときだけ実行
		$post_types = get_post_types( array(
			'_builtin' => false,
		) );
		$post_type = get_post_type();
		if ( ! in_array( $post_type, $post_types ) )
			return;
		// カスタム投稿タイプオブジェクトを取得
		$post_type_object = get_post_type_object( $post_type );
		// public => false のとき
		if ( isset( $post_type_object->public ) && $post_type_object->public === false ) {
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
	 * アイキャッチ画像の設定部分にサムネイルサイズを表示
	 * @param string $content
	 * @return string $content
	 */
	public function admin_post_thumbnail_html( $content ) {
		global $_wp_additional_image_sizes;
		$width = '';
		$height = '';
		if ( isset( $_wp_additional_image_sizes['post-thumbnail'] ) ) {
			$post_thumbnail = $_wp_additional_image_sizes['post-thumbnail'];
			if ( isset( $post_thumbnail['height'], $post_thumbnail['width'] ) ) {
				$width = $post_thumbnail['width'];
				$height = $post_thumbnail['height'];
			}
		}
		$width = apply_filters(
			MW_WP_Hacks_Config::NAME . '-thumbnail-html-height',
			$width
		);
		$height = apply_filters(
			MW_WP_Hacks_Config::NAME . '-thumbnail-html-height',
			$height
		);
		if ( $width && $height ) {
			$content .= '<p class="howto">' . sprintf(
				esc_html__( 'Recommended image size of %d x %d', 'mw-wp-hacks' ),
				$width,
				$height
			) . '</p>';
		}
		return $content;
	}

	/**
	 * wp_title
	 * 日本語のページタイトルを改変
	 * @param string $title wp_title で出力されるタイトル
	 * @param string $sep セパレーター文字
	 * @param string $seplocation right or left
	 * @return string $title
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
	 * get_description
	 * @param int $strnum description の長さ
	 * @return string $description
	 */
	public function get_description( $strnum = 200 ) {
		global $post;
		$description = '';
		if ( is_singular() && empty( $post->post_password ) ) {
			if ( !empty( $post->post_excerpt ) ) {
				$description = $post->post_excerpt;
			} elseif ( !empty( $post->post_content ) ) {
				$description = $post->post_content;
			}
			$description = strip_shortcodes( $description );
			$description = str_replace( ']]>', ']]&gt;', $description );
			$description = strip_tags( $description );
			$description = str_replace( array( "\r\n","\r","\n" ), '', $description );
			$description = mb_strimwidth( $description, 0, $strnum, "...", 'utf8' );
		} elseif ( !is_singular() ) {
			$option = $this->settings['Description']->get_option();
			$post_type = get_post_type();
			if ( !empty( $option['pt_' . $post_type] ) ) {
				$description = $option['pt_' . $post_type];
			} elseif ( !empty( $option['basic'] ) ) {
				$description = $option['basic'];
			}
		}
		if ( empty( $description ) ) {
			$description = get_bloginfo( 'description' );
		}
		$description = trim( $description );
		return apply_filters( MW_WP_Hacks_Config::NAME . '-description', $description );
	}

	/**
	 * display_description
	 */
	public function display_description() {
		$description = $this->get_description();
		?>
		<meta name="description" content="<?php echo esc_attr( $description ); ?>" />
		<?php
	}

	/**
	 * wp_trim_excerpt
	 * the_excerpt実行時に実行される
	 * @param string $excerpt
	 * @return string
	 */
	public function wp_trim_excerpt( $excerpt ) {
		global $post;
		$more = '';
		$option = $this->settings['Excerpt']->get_option();
		if ( !empty( $option ) && !is_array( $option ) ) {
			$more = $option;
			$more = preg_replace( '/%link%/', get_permalink(), $more );
		}
		return $excerpt . $more;
	}

	/**
	 * excerpt_more
	 * 抜粋もしくは本文が一定の文字数を超えたときに実行される
	 * @param string $more
	 * @return string
	 */
	public function excerpt_more( $more ) {
		$option = $this->settings['Excerptmore']->get_option();
		if ( $option ) {
			return $option;
		}
	}

	/**
	 * set_rss_post_types
	 * @param object $query
	 * @return object $query
	 */
	public function set_rss_post_types( $query ) {
		if ( $query->is_feed ) {
			$post_type = $query->get( 'post_type' );
			if ( empty( $post_type ) ) {
				$option = $this->settings['Feed']->get_option();
				if ( is_array( $option ) ) {
					$query->set( 'post_type', $option );
				}
			}
			return $query;
		}
	}

	/**
	 * display_ogp_tags
	 */
	public function display_ogp_tags() {
		$ogp = $this->settings['Ogp']->get_option();
		$social = $this->settings['Social']->get_option();

		if ( empty( $ogp ) ) {
			return;
		}
		$image = '';
		if ( !empty( $ogp['image'] ) ) {
			$image = home_url() . $ogp['image'];
		}
		if ( is_singular() && !is_front_page() ) {
			$type = 'article';
			$title = get_the_title();
			$url = get_permalink();
			if ( $_image = $this->get_first_image() )
				$image = $_image;
		}
		elseif ( is_tax() || is_category() || is_tag() ) {
			$term_obj = get_queried_object();
			$type = 'article';
			$title = $term_obj->name;
			$url = get_term_link( $term_obj, $term_obj->taxonomy );
		}
		elseif ( is_author() ) {
			$author_obj = get_queried_object();
			$title = $author_obj->display_name;
			$type = 'author';
			$url = get_author_posts_url( $author_obj->ID );
		}
		elseif ( is_post_type_archive() ) {
			$post_type_obj = get_queried_object();
			$title = $post_type_obj->labels->name;
			$type = 'article';
			$url = get_post_type_archive_link( $post_type_obj->name );
		}
		else {
			$title = get_bloginfo( 'name' );
			$type = ( empty( $ogp['type'] ) ) ? 'blog' : $ogp['type'];
			if ( is_singular() && is_front_page() ) {
				$url = get_permalink();
			} else {
				$url = home_url();
			}
		}
		$parse_url = parse_url( $url );
		if ( count( $_GET ) ) {
			$get = $_GET;
			$query = array();
			if ( isset( $parse_url['query'] ) ) {
				parse_str( $parse_url['query'], $query );
				foreach ( $get as $key => $value ) {
					if ( array_key_exists( $key, $query ) ) {
						unset( $get[$key] );
					}
				}
			}
			$url .= '?' . http_build_query( $get, null, '&' );
		}

		if ( !empty( $social['facebook_app_id'] ) ) {
			printf(
				'<meta property="fb:app_id" content="%d" />',
				apply_filters( MW_WP_Hacks_Config::NAME . '-ogp-app_id', $social['facebook_app_id'] )
			);
		}
		printf( '
			<meta property="og:type" content="%s" />
			<meta property="og:site_name" content="%s" />
			<meta property="og:image" content="%s" />
			<meta property="og:title" content="%s" />
			<meta property="og:url" content="%s" />
			<meta property="og:description" content="%s" />
			',
			esc_attr( apply_filters( MW_WP_Hacks_Config::NAME . '-ogp-type', $type ) ),
			esc_attr( apply_filters( MW_WP_Hacks_Config::NAME . '-ogp-site_name', get_bloginfo( 'name' ) ) ),
			esc_attr( apply_filters( MW_WP_Hacks_Config::NAME . '-ogp-image', $image ) ),
			esc_attr( apply_filters( MW_WP_Hacks_Config::NAME . '-ogp-title', $title ) ),
			esc_url( apply_filters( MW_WP_Hacks_Config::NAME . '-ogp-url', $url ) ),
			esc_attr( apply_filters( MW_WP_Hacks_Config::NAME . '-ogp-description', $this->get_description() ) )
		);
	}

	/**
	 * update_facebook_cache
	 * @param string $new_status
	 * @param string $old_status
	 * @param object $post
	 */
	public function update_facebook_cache( $new_status, $old_status, $post ) {
		if ( $new_status === 'publish' ) {
			$api = 'https://graph.facebook.com?id=%s&scrape=true';
			$t = wp_remote_post( sprintf( $api, get_permalink( $post->ID ) ) );
		}
	}

	/**
	 * get_first_image
	 * ogp_image > thumbnail > first image
	 * @return string 画像 URL
	 */
	public function get_first_image() {
		global $post;
		$first_img = '';
		$_image_id = get_post_meta( $post->ID, MW_WP_Hacks_Config::NAME . '-ogp', true );
		if ( !empty( $_image_id['ogp_image_id'] ) ) {
			$image_id = $_image_id['ogp_image_id'];
			$image_url = wp_get_attachment_image_src( $image_id, MW_WP_Hacks_Config::NAME . '_ogp_image', false );
		} elseif ( function_exists( 'get_post_thumbnail_id' ) ) {
			$image_id = get_post_thumbnail_id();
			$image_url = wp_get_attachment_image_src( $image_id, 'full', false );
		}

		if ( !empty( $image_url[0] ) ) {
			$first_img = $image_url[0];
		} else {
			if ( preg_match( '/<img.+?src=[\'"]([^\'"]+?)[\'"].*?>/msi', $post->post_content, $matches ) )
				$first_img = do_shortcode( $matches[1] );
		}
		if ( !empty( $first_img ) && preg_match( '/^\/.+$/', $first_img ) )
			$first_img = home_url() . $first_img;
		return $first_img;
	}

	/**
	 * ソーシャル等のスクリプトの非同期読込
	 */
	public function social_button_footer() {
		$scripts = $this->settings['Script']->get_option();
		$social  = $this->settings['Social']->get_option();
		?>
		<?php if ( $social['ua_tracking_id'] ) : ?>
		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		  ga('create', '<?php echo esc_js( $social["ua_tracking_id"] ); ?>', 'auto');
		  ga('send', 'pageview');
		</script>
		<?php elseif ( $social['ga_tracking_id'] ) : ?>
		<script type="text/javascript">
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', '<?php echo esc_js( $social["ga_tracking_id"] ); ?>']);
		_gaq.push(['_trackPageview']);
		(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true; 
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();
		</script>
		<?php endif; ?>

		<?php if ( $scripts['facebook'] === 'true' ) : ?>
		<div id="fb-root"></div>
		<script id="facebook-jssdk" src="//connect.facebook.net/<?php echo esc_attr( $scripts["facebook_locale"] ); ?>/sdk.js#xfbml=1&amp;appId=<?php echo esc_attr( $social['facebook_app_id'] ); ?>&amp;version=v2.0" async></script>
		<?php endif; ?>

		<?php if ( $scripts['twitter'] === 'true' ) : ?>
		<script id="twitter-wjs" src="//platform.twitter.com/widgets.js" async></script>
		<?php endif; ?>

		<?php if ( $scripts['hatena'] === 'true' ) : ?>
		<script src="//b.st-hatena.com/js/bookmark_button.js" async></script>
		<?php endif; ?>

		<?php if ( $scripts['google'] === 'true' ) : ?>
		<script src="https://apis.google.com/js/plusone.js" async defer></script>
		<script>
		window.___gcfg = { lang: "<?php echo esc_js( $scripts['google_locale'] ); ?>" };
		</script>
		<?php endif; ?>
		<?php
	}

	/**
	 * add_profile_for_google_plus
	 * Google+ 用の link タグを出力
	 */
	public function add_profile_for_google_plus() {
		global $post;
		$option = $this->settings['Social']->get_option();
		if ( empty( $post->post_author ) )
			return;
		$user_meta = get_user_meta( $post->post_author, MW_WP_Hacks_Config::NAME_GOOGLEPLUSID, true );
		if ( !empty( $option['google_plus_id'] ) && !is_singular() ) {
			?>
			<link rel="publisher" href="https://plus.google.com/<?php echo esc_attr( $option['google_plus_id'] ); ?>/" />
			<?php
		} elseif ( isset( $post->post_author ) && $user_meta != '' && is_singular() ) {
			?>
			<link rel="author" href="https://plus.google.com/<?php echo esc_attr( $user_meta ); ?>/" />
			<?php
		}
	}

	/**
	 * add_google_site_verification
	 * Googleウェブマスターツール サイトの確認用のタグを出力
	 */
	public function add_google_site_verification() {
		$option = $this->settings['Social']->get_option();
		if ( !empty( $option['google_site_verification'] ) && is_front_page() ) {
			?>
			<meta name="google-site-verification" content="<?php echo esc_attr( $option['google_site_verification'] ); ?>" />
			<?php
		}
	}

	/**
	 * set_thumbnail
	 * オリジナルサムネイルサイズを登録
	 */
	public function set_thumbnail() {
		$option = $this->settings['Thumbnail']->get_option();
		if ( count( $option ) > 1 && is_array( $option ) ) {
			add_theme_support( 'post-thumbnails' );
			foreach ( $option as $key => $thumbnail ) {
				if ( $thumbnail['name'] ) {
					add_image_size(
						$thumbnail['name'],
						$thumbnail['width'],
						$thumbnail['height'],
						$thumbnail['crop']
					);
				}
			}
		}
	}

	/**
	 * widgets_init
	 * ウィジェットエリアを登録
	 */
	public function widgets_init() {
		$option = $this->settings['Widget']->get_option();
		if ( count( $option ) > 1 && is_array( $option ) ) {
			foreach ( $option as $widget ) {
				if ( $widget['name'] ) {
					$widget_args = array();
					foreach ( $widget as $key => $value ) {
						if ( $value ) {
							$widget_args[$key] = $value;
						}
					}
					register_sidebar( $widget_args );
				}
			}
		}
	}
}
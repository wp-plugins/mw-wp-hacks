<?php
/**
 * Name       : MW WP Hacks Bread Crumb
 * Description: パンくずリスト生成
 * Version    : 1.0.0
 * Author     : Takashi Kitajima
 * Author URI : http://2inc.org
 * Create     : November 13, 2014
 * Modified   :
 * License    : GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
class MW_WP_Hacks_Bread_Crumb {

	/**
	 * $bread_crumb
	 */
	protected $bread_crumb = array();

	/**
	 * __construct
	 */
	public function __construct() {
	}

	/**
	 * display
	 * @param array $params
	 */
	public function display( $params ) {
		global $wp_query;

		$page_on_front = get_option( 'page_on_front' );
		$home_label = esc_html__( 'Home', 'mw-wp-hacks' );
		if ( $page_on_front ) {
			$home_label = get_the_title( $page_on_front );
		}

		$defaults = array(
			'home_label' => $home_label,
		);
		$params = shortcode_atts( $defaults, $params );

		if ( is_404() ) {
			$this->set( esc_html__( 'Page Not Found', 'mw-wp-hacks' ) );
		}
		elseif ( is_search() ) {
			$this->set( sprintf( esc_html__( 'Search results for "%s"', 'mw-wp-hacks' ), get_search_query() ) );
		}
		elseif ( is_tax() ) {
			$taxonomy = get_query_var( 'taxonomy' );
			$term = get_term_by( 'slug', get_query_var( 'term' ), $taxonomy );
			$ancestor = $term;

			$taxonomy_objects = get_taxonomy( $taxonomy );
			$post_types = $taxonomy_objects->object_type;
			foreach ( $post_types as $post_type ) {
				$template_page = $this->get_template_used_page( $post_type );
				if ( !empty( $template_page->ID ) ) {
					$label = get_the_title( $template_page->ID );
				} else {
					$post_type_object = get_post_type_object( $post_type );
					$label = $post_type_object->labels->singular_name;
				}
				$this->set( $label, $this->get_post_type_archive_link( $post_type ) );
				break;
			}

			if ( is_taxonomy_hierarchical( $taxonomy ) && $term->parent ) {
				$ancestors = get_ancestors( $term->term_id, $taxonomy );
				foreach ( $ancestors as $ancestor_id ) {
					$ancestor = get_term( $ancestor_id, $taxonomy );
					$this->set( $ancestor->name, get_term_link( $ancestor ) );
				}
			}
			$this->set( $ancestor->name );
		}
		elseif ( is_attachment() ) {
			$this->set( get_the_title() );
		}
		elseif ( is_page() && !is_front_page() ) {
			$ancestors = get_ancestors( get_the_ID(), 'page' );
			krsort( $ancestors );
			foreach ( $ancestors as $ancestor_id ) {
				$this->set( get_the_title( $ancestor_id ), get_permalink( $ancestor_id ) );
			}
			$this->set( get_the_title() );
		}
		elseif ( is_single() ) {
			$post_type = $wp_query->get( 'post_type' );
			if ( $post_type ) {
				$post_type_object = get_post_type_object( $post_type );
				$template_page = $this->get_template_used_page( $post_type );
				if ( !empty( $template_page->ID ) ) {
					$label = get_the_title( $template_page->ID );
				} else {
					$label = $post_type_object->labels->singular_name;
				}
				$this->set( $label, $this->get_post_type_archive_link( $post_type ));
				$taxonomies = $post_type_object->taxonomies;
				if ( $taxonomies ) {
					foreach ( $taxonomies as $taxonomy ) {
						$terms = get_the_terms( get_the_ID(), $taxonomy );
						break;
					}
					if ( $terms ) {
						$term =  array_shift( $terms );
						$ancestors = get_ancestors( $term->term_id, $taxonomy );
						$ancestors[] = $term;
						foreach ( $ancestors as $ancestor_id ) {
							$ancestor = get_term( $ancestor_id, $taxonomy );
							$this->set( $ancestor->name, get_term_link( $ancestor ) );
							break;
						}
					}
				}
			}
			else {
				$categories = get_the_category( get_the_ID() );
				if ( $categories ) {
					$category = array_shift( $categories );
					$ancestors = get_ancestors( $category->term_id, 'category' );
					$ancestors[] = $category;
					foreach ( $ancestors as $ancestor_id ) {
						$ancestor = get_term( $ancestor_id, 'category' );
						$this->set( $ancestor->name, get_term_link( $ancestor ) );
					}
				}
			}
			$this->set( get_the_title() );
		}
		elseif ( is_category() ) {
			$category_name = single_cat_title( '', false );
			$category_id = get_cat_ID( $category_name );
			$ancestors = get_ancestors( $category_id, 'category' );
			foreach ( $ancestors as $ancestor_id ) {
				$ancestor = get_term( $ancestor_id, 'category' );
				$this->set( $ancestor->name, get_term_link( $ancestor ) );
			}
			$this->set( $category_name );
		}
		elseif ( is_tag() ) {
			$this->set( single_tag_title( '', false ) );
		}
		elseif ( is_author() ) {
			$author_id = get_query_var( 'author' );
			$this->set( get_the_author_meta( 'display_name', $author_id ) );
		}
		elseif ( is_day() ) {
			$year = get_query_var( 'year' );
			if ( !$year ) {
				$m = get_query_var( 'm' );
				$year = substr( $m, 0, 4 );
				$month = substr( $m, 4, 2 );
				$day = substr( $m, -2 );
			} else {
				$month = get_query_var( 'monthnum' );
				$day = get_query_var( 'day' );
			}
			$this->set( $this->year( $year ), get_year_link( $year ) );
			$this->set( $this->month( $month ), get_month_link( $year, $month ) );
			$this->set( $this->day( $day ) );
		}
		elseif ( is_month() ) {
			$year = get_query_var( 'year' );
			if ( !$year ) {
				$m = get_query_var( 'm' );
				$year = substr( $m, 0, 4 );
				$month = substr( $m, -2 );
			} else {
				$month = get_query_var( 'monthnum' );
			}
			$this->set( $this->year( $year ), get_year_link( $year ) );
			$this->set( $this->month( $month ) );
		}
		elseif ( is_year() ) {
			$year = get_query_var( 'year' );
			if ( !$year ) {
				$m = get_query_var( 'm' );
				$year = $m;
			}
			$this->set( $this->year( $year ) );
		}
		else {
			if ( !is_front_page() ) {
				$this->set( wp_title( '', false, '' ) );
			}
		}

		$bread_crumb = array();
		$bread_crumb[] = sprintf( '<a href="%s">%s</a>',
			esc_url( home_url() ),
			esc_html( $params['home_label'] )
		);
		if ( ( is_category() || is_tag() || is_date() || is_author() ) ||
			 ( is_archive() && !get_post_type() ) ||
			 ( is_single() && get_post_type() === 'post' ) ) {
			if ( $page_for_posts = get_option( 'page_for_posts' ) ) {
				$bread_crumb[] = sprintf( '<a href="%s">%s</a>',
					esc_url( get_permalink( $page_for_posts ) ),
					esc_html( get_the_title( $page_for_posts ) )
				);
			}
		}
		foreach ( $this->bread_crumb as $_bread_crumb ) {
			if ( !empty( $_bread_crumb['link'] ) ) {
				$bread_crumb[] = sprintf( '<a href="%s">%s</a>',
					esc_url( $_bread_crumb['link'] ),
					esc_html( $_bread_crumb['title'] )
				);
			} else {
				$bread_crumb[] = sprintf( '%s',
					esc_html( $_bread_crumb['title'] )
				);
			}
		}
		echo '<div class="bread-crumb">' . implode( ' &gt; ', $bread_crumb ) . '</div>';
	}

	/**
	 * set
	 * @param string $title リンクタイトル
	 * @param string $link リンクURL
	 */
	protected function set( $title, $link = '' ) {
		$this->bread_crumb[] = array(
			'title' => $title,
			'link'  => $link,
		);
	}

	/**
	 * get_post_type_archive_link
	 * @param string $post_type カスタム投稿タイプ名
	 * @return string カスタム投稿アーカイブURL
	 */
	protected function get_post_type_archive_link( $post_type ) {
		$post_type_archive_link = get_post_type_archive_link( $post_type );
		if ( !$post_type_archive_link ) {
			$template_page = $this->get_template_used_page( $post_type );
			if ( !empty( $template_page->ID ) ) {
				$post_type_archive_link = get_permalink( $template_page->ID );
			}
		}
		return $post_type_archive_link;
	}

	/**
	 * get_template_used_page
	 * これに一致するテンプレートを使っている固定ページはカスタム投稿アーカイブ用とみなす
	 * @param string $post_type
	 * @return int Post ID
	 */
	protected function get_template_used_page( $post_type ) {
		$template_pages = get_posts( array(
			'post_type' => 'page',
			'meta_query' => array(
				array(
					'key' => '_wp_page_template',
					'value' => 'template-archive-' . $post_type . '.php',
				),
			),
		) );
		if ( !empty( $template_pages[0] ) ) {
			return $template_pages[0];
		}
	}

	/**
	 * year
	 * @param string $year
	 * @return string $year
	 */
	protected function year( $year ) {
		if ( get_locale() === 'ja' ) {
			$year .= '年';
		}
		return $year;
	}

	/**
	 * month
	 * @param string $month
	 * @return string $month
	 */
	protected function month( $month ) {
		if ( get_locale() === 'ja' ) {
			$month .= '月';
		} else {
			$monthes = array(
				1  => 'January',
				2  => 'February',
				3  => 'March',
				4  => 'April',
				5  => 'May',
				6  => 'June',
				7  => 'July',
				8  => 'August',
				9  => 'September',
				10 => 'October',
				11 => 'November',
				12 => 'December',
			);
			$month = $monthes[$month];
		}
		return $month;
	}

	/**
	 * day
	 * @param string $day
	 * @return string $day
	 */
	protected function day( $day ) {
		if ( get_locale() === 'ja' ) {
			$day .= '日';
		}
		return $day;
	}
}

<?php
/**
 * Name       : MW WP Hacks Local Nav
 * Description: ローカルナビゲーション
 * Version    : 1.1.0
 * Author     : Takashi Kitajima
 * Author URI : http://2inc.org
 * Create     : November 17, 2014
 * Modified   : February 16, 2015
 * License    : GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
class MW_WP_Hacks_Local_Nav {

	/**
	 * $defaults
	 */
	private $defaults = array(
		// 親ページタイトル表示
		'title'      => true,
		// 子が存在しないときはナビゲーションを表示しない
		'hide_empty' => true,
	);

	/**
	 * $args
	 */
	private $args = array();

	/**
	 * __construct
	 */
	public function __construct( array $args = array() ) {
		$this->args = shortcode_atts( $this->defaults, $args );
	}

	/**
	 * get
	 * @return string
	 */
	public function get() {
		// 固定ページのとき
		if ( is_page() ) {
			return $this->display_for_page();
		}
		// カスタム投稿タイプのとき
		elseif ( MW_WP_Hacks::is_custom_post_type() ) {
			return $this->display_for_custom_post();
		}
		// （カスタム投稿タイプでない）カスタムタクソノミーのとき
		elseif ( is_tax() ) {
			return $this->display_for_custom_taxonomy();
		}
	}

	/**
	 * display
	 */
	public function display() {
		echo $this->get();
	}

	/**
	 * display_for_page
	 * @return string
	 */
	protected function display_for_page() {
		global $post;
		$parent_id = MW_WP_Hacks::get_top_parent_id( $post );
		return $this->pages( $parent_id );
	}

	/**
	 * display_for_custom_post
	 * @return string
	 */
	protected function display_for_custom_post() {
		$post_type = $this->get_post_type();
		$post_type_object = get_post_type_object( $post_type );
		if ( !empty( $post_type_object->taxonomies ) ) {
			foreach ( $post_type_object->taxonomies as $taxonomy_name ) {
				return $this->terms( $post_type, $taxonomy_name );
			}
		} elseif ( !empty( $post_type_object->hierarchical ) ) {
			return $this->custom_post_pages( $post_type );
		}
	}

	/**
	 * display_for_custom_taxonomy
	 * @return string
	 */
	protected function display_for_custom_taxonomy() {
		$post_type = $this->get_post_type();
		if ( !empty( $post_type_object->taxonomies ) ) {
			foreach ( $post_type_object->taxonomies as $taxonomy_name ) {
				return $this->terms( $post_type, $taxonomy_name );
			}
		}
	}

	/**
	 * pages
	 * @param int $parent_id 一番親の Post ID
	 * @return string
	 */
	protected function pages( $parent_id ) {
		$title = get_the_title( $parent_id );
		$title_link = get_permalink( $parent_id );
		$children = wp_list_pages( array(
			'title_li'    => '',
			'sort_column' => 'menu_order',
			'child_of'    => $parent_id,
			'echo'        => false
		) );
		return $this->template( $title, $title_link, $children );
	}

	/**
	 * custom_post_pages
	 * @param string $post_type
	 * @return string
	 */
	protected function custom_post_pages( $post_type ) {
		$post_type_object = get_post_type_object( $post_type );
		$title = $post_type_object->labels->name;
		$title_link = get_post_type_archive_link( $post_type );
		$children = wp_list_pages( array(
			'title_li'    => '',
			'sort_column' => 'menu_order',
			'post_type'   => $post_type,
			'echo'        => false
		) );
		return $this->template( $title, $title_link, $children );
	}

	/**
	 * terms
	 * @param string $post_type
	 * @param string $taxonomy
	 */
	protected function terms( $post_type, $taxonomy ) {
		$post_type_object = get_post_type_object( $post_type );
		$title = $post_type_object->labels->name;
		$title_link = get_post_type_archive_link( $post_type );
		$children = wp_list_categories( array(
			'title_li'   => '',
			'show_count' => false,
			'hide_empty' => true,
			'echo'       => false,
			'taxonomy'   => $taxonomy,
		) );
		return $this->template( $title, $title_link, $children );
	}

	/**
	 * template
	 * @param string $title
	 * @param string $title_link
	 * @param string $children
	 */
	protected function template( $title, $title_link = '', $children = '' ) {
		$show = $this->args['title'];
		if ( $this->args['hide_empty'] === true && empty( $children ) ) {
			return;
		}
		if ( $show === false && empty( $children ) ) {
			return;
		}

		$_title = '';
		if ( $show === true ) {
			if ( !empty( $title_link ) ) {
				$_title = sprintf(
					'<dt class="localnav-parent-title"><a href="%s">%s</a></dt>',
					esc_attr( $title_link ),
					esc_html( $title )
				);
			} else {
				$_title = sprintf(
					'<dt class="localnav-parent-title">%s</dt>',
					esc_html( $title )
				);
			}
		}
		$title = $_title;

		$_children = '';
		if ( !empty( $children ) ) {
			$_children = sprintf(
				'<dd class="localnav-sub-pages"><ul>%s</ul></dd>',
				$children
			);
		}
		$children = $_children;

		return sprintf(
			'<div class="localnav">' .
			'<dl>%s%s</dl>' .
			'</div>',
			$title,
			$children
		);
	}

	/**
	 * get_post_type
	 * @return string
	 */
	private function get_post_type() {
		global $wp_query;
		$post_type = get_post_type();
		if ( !$post_type ) {
			if ( isset( $wp_query->query['post_type'] ) ) {
				$post_type = $wp_query->query['post_type'];
			}
		}
		return $post_type;
	}
}
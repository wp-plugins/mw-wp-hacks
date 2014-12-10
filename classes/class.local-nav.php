<?php
/**
 * Name       : MW WP Hacks Local Nav
 * Description: ローカルナビゲーション
 * Version    : 1.0.0
 * Author     : Takashi Kitajima
 * Author URI : http://2inc.org
 * Create     : November 17, 2014
 * Modified   :
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
	 * display
	 */
	public function display() {
		// 固定ページのとき
		if ( is_page() ) {
			$this->display_for_page();
		}
		// カスタム投稿タイプのとき
		elseif ( MW_WP_Hacks::is_custom_post_type() ) {
			$post_type = $this->get_post_type();
			$post_type_object = get_post_type_object( $post_type );

			$this->display_for_custom_post_page();
			$this->display_for_custom_post();
		}
		// （カスタム投稿タイプでない）カスタムタクソノミーのとき
		elseif ( is_tax() ) {
			$this->display_for_custom_taxonomy();
		}
	}

	/**
	 * display_for_page
	 */
	protected function display_for_page() {
		global $post;
		$parent_id = MW_WP_Hacks::get_top_parent_id( $post );
		
		$title = get_the_title( $parent_id );
		$title_link = get_permalink( $parent_id );
		$children = wp_list_pages( array(
			'title_li'    => '',
			'sort_column' => 'menu_order',
			'child_of'    => $parent_id,
			'echo'        => false
		) );

		$this->template( $title, $title_link, $children );
	}

	/**
	 * display_for_custom_post
	 */
	protected function display_for_custom_post() {
		$post_type = $this->get_post_type();
		$post_type_object = get_post_type_object( $post_type );

		if ( !empty( $post_type_object->taxonomies ) ) {
			foreach ( $post_type_object->taxonomies as $taxonomy_name ) {
				$children = wp_list_categories( array(
					'title_li'   => '',
					'show_count' => false,
					'hide_empty' => true,
					'echo'       => false,
					'taxonomy'   => $taxonomy_name,
				) );
				$taxonomy = get_taxonomy( $taxonomy_name );
				if ( !isset( $taxonomy->label ) )
					continue;

				$this->template( $taxonomy->label, '', $children );
			}
		}
	}

	/**
	 * display_for_custom_post_page
	 */
	protected function display_for_custom_post_page() {
		$post_type = $this->get_post_type();
		$post_type_object = get_post_type_object( $post_type );

		$title = $post_type_object->labels->name;
		$title_link = get_post_type_archive_link( $post_type );
		$children = wp_list_pages( array(
			'title_li'    => '',
			'sort_column' => 'menu_order',
			'post_type'   => $post_type,
			'echo'        => false
		) );

		$this->template( $title, $title_link, $children );
	}

	/**
	 * display_for_custom_taxonomy
	 */
	protected function display_for_custom_taxonomy() {
		$post_type = get_post_type();
		$post_type_object = get_post_type_object( $post_type );

		if ( !empty( $post_type_object->taxonomies ) ) {
			foreach ( $post_type_object->taxonomies as $taxonomy_name ) {
				$children = wp_list_categories( array(
					'title_li'   => '',
					'show_count' => false,
					'hide_empty' => true,
					'echo'       => false,
					'taxonomy'   => $taxonomy_name,
				) );
				$taxonomy = get_taxonomy( $taxonomy_name );
				if ( !isset( $taxonomy->label ) )
					continue;

				$this->template( $taxonomy->label, '', $children );
			}
		}
	}

	/**
	 * template
	 */
	protected function template( $title, $title_link = '', $children = '' ) {
		$show = $this->args['title'];
		if ( $this->args['hide_empty'] === true && empty( $children ) )
			return;
		?>
		<?php if ( true === $show || !empty( $children ) ) : ?>
		<div class="localnav">
			<dl>
				<?php if ( true === $show ) : ?>
				<?php if ( !empty( $title_link ) ) : ?>
				<dt class="localnav-parent-title"><a href="<?php echo esc_attr( $title_link ); ?>"><?php echo esc_html( $title ); ?></a></dt>
				<?php else : ?>
				<dt class="localnav-parent-title"><?php echo esc_html( $title ); ?></dt>
				<?php endif; ?>
				<?php endif; ?>
				<?php if ( !empty( $children ) ) : ?>
				<dd class="localnav-sub-pages">
					<ul>
						<?php echo $children; ?>
					</ul>
				</dd>
				<?php endif; ?>
			</dl>
		<!-- end .localNav --></div>
		<?php endif; ?>
		<?php
	}

	/**
	 * get_post_type
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
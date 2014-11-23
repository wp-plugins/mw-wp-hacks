<?php
/**
 * Name       : MW WP Hacks Manage Custom Post Type
 * Description: カスタム投稿タイプの管理
 * Version    : 1.0.0
 * Author     : Takashi Kitajima
 * Author URI : http://2inc.org
 * Create     : November 13, 2014
 * Modified   :
 * License    : GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
class MW_WP_Hacks_Manage_Custom_Post_type {

	private $custom_post_type = array();
	private $custom_post_dashboard = array();
	private $custom_taxonomy = array();

	/**
	 * init
	 * 実行
	 */
	public function init() {
		if ( !empty( $this->custom_post_type ) ) {
			add_action( 'init', array( $this, 'register_post_type' ) );
		}
		if ( !empty( $this->custom_post_dashboard ) ) {
			add_action( 'right_now_content_table_end', array( $this, 'right_now_content_table_end' ) );
			add_filter( 'dashboard_glance_items', array( $this, 'dashboard_glance_items' ) );
			add_action( 'admin_print_styles', array( $this, 'admin_print_styles' ) );
		}
		if ( !empty( $this->custom_taxonomy ) ) {
			add_action( 'init', array( $this, 'register_taxonomy' ) );
		}
	}

	/**
	 * custom_post_type
	 * カスタム投稿タイプの登録
	 * http://codex.wordpress.org/Function_Reference/register_post_type
	 * @param string $name 表示名
	 * @param string $slug スラッグ（登録名）
	 * @param array $supports サポートタイプ
	 * @param array $options オプション項目
	 */
	public function custom_post_type( $name, $slug, array $supports = array(), array $options = array() ) {
		$custom_post_type = array(
			'name'     => $name,
			'slug'     => $slug,
			'supports' => $supports,
			'options'  => $options
		);
		$this->custom_post_type[] = $custom_post_type;
		$this->custom_post_dashboard( $slug );
	}

	/**
	 * register_post_type
	 * カスタム登録タイプの登録を実行
	 */
	public function register_post_type() {
		foreach ( $this->custom_post_type as $cpt ) {
			if ( empty( $cpt['supports'] ) ) {
				$cpt['supports'] = array( 'title', 'editor' );
			}
			$labels = array(
				'name'               => $cpt['name'],
				'singular_name'      => $cpt['name'],
				'add_new_item'       => sprintf( esc_html__( 'Add New %s', 'mw-wp-hacks' ), esc_html( $cpt['name'] ) ),
				'add_new'            => esc_html__( 'Add New', 'mw-wp-hacks' ),
				'new_item'           => esc_html__( 'Add New', 'mw-wp-hacks' ),
				'edit_item'          => sprintf( esc_html__( 'Edit %s', 'mw-wp-hacks' ), $cpt['name'] ),
				'view_item'          => sprintf( esc_html__( 'View %s', 'mw-wp-hacks' ), $cpt['name'] ),
				'not_found'          => sprintf( esc_html__( 'No %s found.', 'mw-wp-hacks' ), $cpt['name'] ),
				'not_found_in_trash' => sprintf( esc_html__( 'No %s found in trash.', 'mw-wp-hacks' ), $cpt['name'] ),
				'search_items'       => sprintf( esc_html__( 'Search %s', 'mw-wp-hacks' ), $cpt['name'] ),
			);
			$default_options = array(
				'public'        => true,
				'has_archive'   => true,
				'hierarchical'  => false,
				'labels'        => $labels,
				'menu_position' => null,
				'supports'      => $cpt['supports'],
				'rewrite' => array(
					'slug'       => $cpt['slug'],
					'with_front' => false
				),
			);
			$args = $this->array_merge( $default_options, $cpt['options'] );

			if ( isset( $args['capability_type'] ) ) {
				$default_capability_type = array( 'post', 'page', 'attachment', 'mediapage' );
				// capability_type が配列の場合は管理者に権限を付与
				if ( is_array( $args['capability_type'] ) && count( $args['capability_type'] ) == 2 ) {
					$capability_type = $args['capability_type'][0];
					$capability_types = $args['capability_type'][1];
					if ( !in_array( $capability_type, $default_capability_type ) ) {
						$capabilities = array(
							'edit_posts'             => 'edit_' . $capability_types,
							'edit_others_posts'      => 'edit_others_' . $capability_types,
							'publish_posts'          => 'publish_' . $capability_types,
							'read_private_posts'     => 'read_private_' . $capability_types,
							'delete_posts'           => 'delete_' . $capability_types,
							'delete_private_posts'   => 'delete_private_' . $capability_types,
							'delete_published_posts' => 'delete_published_' . $capability_types,
							'delete_others_posts'    => 'delete_others_' . $capability_types,
							'edit_private_posts'     => 'edit_private_' . $capability_types,
							'edit_published_posts'   => 'edit_published_' . $capability_types,
						);
					}
				}
				// capability_type が配列じゃない場合（capabilitiesが設定されているはず）は
				// capabiliteisを管理者に付与
				elseif ( !is_array( $args['capability_type'] ) ) {
					$capability_type = $args['capability_type'];
					if ( !in_array( $capability_type, $default_capability_type ) && isset( $args['capabilities'] ) ) {
						$capabilities = $args['capabilities'];
					}
				}
				$args['map_meta_cap'] = true;
				$role = get_role( 'administrator' );
				foreach ( $capabilities as $cap ) {
					$role->add_cap( $cap );
				}
			}

			// 関連するカスタムタクソノミーがある場合は配列に持たせる
			$_taxonomies = array();
			foreach ( $this->custom_taxonomy as $custom_taxonomy ) {
				if ( in_array( $cpt['slug'], $custom_taxonomy['post_type'] ) ) {
					$_taxonomies[] = $custom_taxonomy['slug'];
				}
			}
			if ( !empty( $_taxonomies ) ) {
				$args_taxonomies = array(
					'taxonomies' => $_taxonomies
				);
				$args = array_merge( $args, $args_taxonomies );
			}
			register_post_type( $cpt['slug'], $args );
		}
	}

	/**
	 * custom_post_dashboard
	 * ダッシュボードに表示したいカスタム登録タイプを登録
	 * @param string $custom post_type カスタム投稿タイプ名
	 */
	private function custom_post_dashboard( $custom_post_type ) {
		$this->custom_post_dashboard[] = $custom_post_type;
	}

	/**
	 * right_now_content_table_end
	 * ダッシュボードにカスタム登録タイプの情報を表示
	 */
	public function right_now_content_table_end() {
		foreach ( $this->custom_post_dashboard as $custom_post_type ) {
			global $wp_post_types;
			$num_post_type = wp_count_posts( $custom_post_type );
			$num = number_format_i18n( $num_post_type->publish );
			$text = _n( $wp_post_types[$custom_post_type]->labels->singular_name, $wp_post_types[$custom_post_type]->labels->name, $num_post_type->publish );
			$capability = $wp_post_types[$custom_post_type]->cap->edit_posts;

			if ( current_user_can( $capability ) ) {
				$num = '<a href="edit.php?post_type=' . esc_attr( $custom_post_type ) . '">' . $num . '</a>';
				$text = '<a href="edit.php?post_type=' . esc_attr( $custom_post_type ) . '">' . $text . '</a>';
			}

			echo '<tr>';
			echo '<td class="first b b_' . esc_attr( $custom_post_type ) . '">' . $num . '</td>';
			echo '<td class="t ' . esc_attr( $custom_post_type ) . '">' . $text . '</td>';
			echo '</tr>';
		}
	}

	/**
	 * dashboard_glance_items
	 * @param array $elements
	 * @return array $elements
	 */
	public function dashboard_glance_items( $elements ) {
		foreach ( $this->custom_post_dashboard as $custom_post_type ) {
			$num_post_type = wp_count_posts( $custom_post_type );
			$post_type_object = get_post_type_object( $custom_post_type );
			if ( $num_post_type && !empty( $num_post_type->publish ) ) {
				$text = sprintf( esc_html_x( '%s %s', 'dashboard-glance-items', 'mw-wp-hacks' ),
					number_format_i18n( $num_post_type->publish ),
					esc_html( $post_type_object->labels->name )
				);
				$elements[] = sprintf(
					'<a href="edit.php?post_type=%1$s" class="%1$s-count">%2$s</a>',
					esc_attr( $custom_post_type ),
					$text
				);
			}
		}
		return $elements;
	}

	/**
	 * admin_print_styles
	 */
	public function admin_print_styles() {
		foreach ( $this->custom_post_dashboard as $custom_post_type ) {
			?>
			<style>
			#dashboard_right_now li a.<?php echo esc_html( $custom_post_type ); ?>-count:before {
				content: '\f105';
			}
			</style>
			<?php
		}
	}

	/**
	 * custom_taxonomy 
	 * カスタムタクソノミーの登録
	 * http://codex.wordpress.org/Function_Reference/register_taxonomy
	 * @param string $name 表示名
	 * @param string $slug スラッグ（登録名）
	 * @param array $post_type ポストタイプ
	 * @param array $options オプション項目
	 */
	public function custom_taxonomy( $name, $slug, array $post_type, array $options = array() ) {
		$custom_taxonomy = array(
			'name'      => $name,
			'slug'      => $slug,
			'post_type' => $post_type,
			'options'   => $options
		);
		$this->custom_taxonomy[] = $custom_taxonomy;
	}

	/**
	 * register_taxonomy
	 * カスタムタクソノミーの登録を実行
	 */
	public function register_taxonomy() {
		foreach ( $this->custom_taxonomy as $ct ) {
			$default_options = array(
				'label'         => $ct['name'],
				'singular_name' => $ct['name'],
				'query_var'     => true,
				'hierarchical'  => false,
				'rewrite' => array(
					'with_front' => false
				),
			);
			$options = $this->array_merge( $default_options, $ct['options'] );
			register_taxonomy(
				$ct['slug'],
				$ct['post_type'],
				$options
			);
		}
	}

	/**
	 * array_merge
	 * 多次元配列をマージ
	 * @param array $a
	 * @param array $b
	 * @return array
	 */
	protected function array_merge( array $a, array $b ) {
		foreach ( $a as $key => $val ) {
			if ( isset( $b[$key] ) ) {
				if ( is_array( $val ) ) {
					$b[$key] = $this->array_merge( $val, $b[$key] );
				}
			} else {
				$b[$key] = $val;
			}
		}
		return $b;
	}
}
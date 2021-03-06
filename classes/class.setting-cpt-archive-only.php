<?php
/**
 * Name       : MW WP Hacks Setting CPT Archive Only
 * Description: カスタム投稿タイプの詳細ページを無効にする
 * Version    : 1.0.3
 * Author     : Takashi Kitajima
 * Author URI : http://2inc.org
 * Create     : December 22, 2014
 * Modified   : January 7, 2015
 * License    : GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
class MW_WP_Hacks_Setting_CPT_Archive_Only extends MW_WP_Hacks_Abstract_Setting {

	/**
	 * set_key
	 */
	protected function set_key() {
		$this->key = 'cpt-archive-only';
	}

	/**
	 * set_defaults
	 */
	protected function set_defaults() {
		return array();
	}

	/**
	 * settings_page
	 */
	public function settings_page() {
		$option = $this->get_option();
		$post_types = $this->get_post_types();
		if ( $post_types ) :
		?>
		<tr>
			<th><?php _e( 'Custom post type to disable a single page', 'mw-wp-hacks' ); ?></th>
			<td>
				<div id="<?php echo esc_attr( $this->get_name() ); ?>">
					<p>
						<?php foreach ( $post_types as $post_type => $name ) : ?>
						<?php $_option = ( isset( $option[$post_type] ) ) ? $option[$post_type] : false; ?>
						<label><input type="checkbox" name="<?php echo esc_attr( $this->get_name() ); ?>[<?php echo esc_attr( $post_type ); ?>]" value="true" <?php checked( 'true', $_option ); ?>> <?php echo esc_html( $name ); ?></label><br />
						<?php endforeach; ?>
					</p>
				</div>
			</td>
		</tr>
		<?php
		endif;
	}

	/**
	 * validate
	 * @param array $values
	 * @return array $valuees
	 */
	public function validate( $values ) {
		if ( is_array( $values ) === false ) {
			$values = array();
		}
		$post_types = $this->get_post_types();
		foreach ( $post_types as $post_type => $name ) {
			if ( !isset( $values[$post_type] ) ) {
				$values[$post_type] = 'false';
			}
		}
		return $values;
	}

	/**
	 * get_post_types
	 * アーカイブページだけ見せたいものが対象。
	 * アーカイブもいらない場合は public => false, has_archive => true でカスタム投稿タイプを定義すれば良い。
	 * @return array $post_types;
	 */
	private function get_post_types() {
		$_post_types = get_post_types( array(
			'public'      => true,
			'has_archive' => true,
		) );
		$post_types = array();
		foreach ( $_post_types as $post_type ) {
			$archive_link = get_post_type_archive_link( $post_type );
			if ( !$archive_link )
				continue;
			$post_type_object = get_post_type_object( $post_type );
			$post_types[$post_type] = $post_type_object->label;
		}
		return $post_types;
	}
}
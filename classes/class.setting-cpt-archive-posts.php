<?php
/**
 * Name       : MW WP Hacks Setting CPT Archive Posts
 * Description: カスタム投稿タイプ及び関連するタクソノミーのアーカイブの表示件数を設定
 * Version    : 1.1.0
 * Author     : Takashi Kitajima
 * Author URI : http://2inc.org
 * Create     : January 6, 2015
 * Modified   : January 7, 2015
 * License    : GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
class MW_WP_Hacks_Setting_CPT_Archive_Posts extends MW_WP_Hacks_Abstract_Setting {

	/**
	 * set_key
	 */
	protected function set_key() {
		$this->key = 'cpt-archive-posts';
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
		$posts_per_page = get_option( 'posts_per_page' );
		if ( $post_types ) :
		?>
		<tr>
			<th><?php _e( 'The maximum number of posts', 'mw-wp-hacks' ); ?></th>
			<td>
				<div id="<?php echo esc_attr( $this->get_name() ); ?>">
					<?php foreach ( $post_types as $post_type => $label ) : ?>
					<?php
					$use_default_value = ( isset( $option[$post_type] ) ) ? $option[$post_type] : 'false';
					$number_value = ( $use_default_value === 'false' ) ? $posts_per_page : $use_default_value;
					?>
					<p>
						<?php echo esc_html( $label ); ?>
						&nbsp;<input name="<?php echo esc_attr( $this->get_name() ); ?>[<?php echo esc_attr( $post_type ); ?>]" type="number" step="1" min="-1" value="<?php echo esc_attr( $number_value ); ?>" class="small-text" /> <?php _e( 'posts' ); ?>
						&nbsp;&nbsp;
						<label>
							<input type="checkbox" name="<?php echo esc_attr( $this->get_name() ); ?>[<?php echo esc_attr( $post_type ); ?>]" value="false" <?php checked( 'false', $use_default_value ); ?> class="<?php echo esc_attr( $this->get_name() ); ?>-use-default" />
							<?php esc_html_e( 'Use default', 'mw-wp-hacks' ); ?>
						</label>
					</p>
					<?php endforeach; ?>
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
		foreach ( $values as $post_type => $value ) {
			if ( ( isset( $post_types[$post_type] ) && preg_match( '/^\-?\d+$/', $value ) ) === false ) {
				unset( $values[$post_type] );
			}
		}
		return $values;
	}

	/**
	 * get_post_types
	 * @return array $post_types;
	 */
	private function get_post_types() {
		$_post_types = get_post_types( array(
			'public'      => true,
			'has_archive' => true,
		) );
		$post_types = array();
		foreach ( $_post_types as $post_type ) {
			$post_type_object = get_post_type_object( $post_type );
			$post_types[$post_type] = $post_type_object->label;
		}
		return $post_types;
	}
}
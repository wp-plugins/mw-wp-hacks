<?php
/**
 * Name       : MW WP Hacks Setting Feed
 * Description: 管理画面
 * Version    : 1.0.0
 * Author     : Takashi Kitajima
 * Author URI : http://2inc.org
 * Create     : November 13, 2014
 * Modified   :
 * License    : GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
class MW_WP_Hacks_Setting_Feed extends MW_WP_Hacks_Abstract_Setting {

	/**
	 * set_key
	 */
	protected function set_key() {
		$this->key = 'feed';
	}

	/**
	 * set_defaults
	 */
	protected function set_defaults() {
		$this->defaults = array(
			'post' => 'true',
		);
	}

	/**
	 * settings_page
	 */
	public function settings_page() {
		$option = $this->get_option();
		$post_types = $this->get_post_types();
		?>
		<tr>
			<th><?php _e( 'Included Post Types in Main Feed', 'mw-wp-hacks' ); ?></th>
			<td>
				<div id="<?php echo esc_attr( $this->get_name() ); ?>">
					<p>
						<?php foreach ( $post_types as $post_type => $name ) : ?>
						<label><input type="checkbox" name="<?php echo esc_attr( $this->get_name() ); ?>[<?php echo esc_attr( $post_type ); ?>]" value="true" <?php checked( 'true', $option[$post_type] ); ?>> <?php echo esc_html( $name ); ?></label><br />
						<?php endforeach; ?>
					</p>
				</div>
			</td>
		</tr>
		<?php
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
	 * get_option
	 * Ver 1.0.0 未満の保存形式に対応
	 */
	public function get_option() {
		$option = parent::get_option();
		$post_types = $this->get_post_types();
		foreach ( $post_types as $post_type => $name ) {
			$key = array_search( $post_type, $option );
			if ( $key !== false ) {
				unset( $option[$key] );
				$option[$post_type] = 'true';
			} else {
				if ( !isset( $option[$post_type] ) ) {
					$option[$post_type] = 'false';
				}
			}
		}
		return $option;
	}

	/**
	 * get_post_types
	 * @return array $post_types;
	 */
	private function get_post_types() {
		$_post_types = get_post_types( array(
			'public' => true,
		) );
		$post_types = array();
		foreach ( $_post_types as $post_type ) {
			if ( $post_type === 'attachment' )
				continue;
			$post_type_object = get_post_type_object( $post_type );
			$post_types[$post_type] = $post_type_object->label;
		}
		return $post_types;
	}
}
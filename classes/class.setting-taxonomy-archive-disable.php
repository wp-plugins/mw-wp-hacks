<?php
/**
 * Name       : MW WP Hacks Setting Taxonomy Archive Disable
 * Description: カスタムタクソノミーアーカイブを無効にする
 * Version    : 1.0.1
 * Author     : Takashi Kitajima
 * Author URI : http://2inc.org
 * Create     : January 19, 2015
 * Modified   : January 19, 2015
 * License    : GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
class MW_WP_Hacks_Setting_Taxonomy_Archive_Disable extends MW_WP_Hacks_Abstract_Setting {

	/**
	 * set_key
	 */
	protected function set_key() {
		$this->key = 'taxonomy-archive-disable';
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
		$taxonomies = $this->get_taxonomies();
		if ( $taxonomies ) :
		?>
		<tr>
			<th><?php _e( 'Taxonomy archive to be disabled', 'mw-wp-hacks' ); ?></th>
			<td>
				<div id="<?php echo esc_attr( $this->get_name() ); ?>">
					<p>
						<?php foreach ( $taxonomies as $taxonomy => $name ) : ?>
						<?php $_option = ( isset( $option[$taxonomy] ) ) ? $option[$taxonomy] : false; ?>
						<label><input type="checkbox" name="<?php echo esc_attr( $this->get_name() ); ?>[<?php echo esc_attr( $taxonomy ); ?>]" value="true" <?php checked( 'true', $_option ); ?>> <?php echo esc_html( $name ); ?></label><br />
						<?php endforeach; ?>
					</p>
					<p class="description">
						<?php esc_html_e( 'Can be set only taxonomy is "public => false".', 'mw-wp-hacks' ); ?>
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
		$taxonomies = $this->get_taxonomies();
		foreach ( $taxonomies as $taxonomy => $name ) {
			if ( !isset( $values[$taxonomy] ) ) {
				$values[$taxonomy] = 'false';
			}
		}
		return $values;
	}

	/**
	 * get_taxonomies
	 * @return array $taxonomies;
	 */
	private function get_taxonomies() {
		$_taxonomies = get_taxonomies( array(
			'public'   => false,
			'_builtin' => false,
		) );
		$taxonomies = array();
		foreach ( $_taxonomies as $taxonomy ) {
			$taxonomy_object = get_taxonomy( $taxonomy );
			$taxonomies[$taxonomy] = $taxonomy_object->label;
		}
		return $taxonomies;
	}
}
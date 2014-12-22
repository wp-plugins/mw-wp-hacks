<?php
/**
 * Name       : MW WP Hacks Setting Taxonomy Archive Disable
 * Description: カスタムタクソノミーアーカイブを無効にする
 * Version    : 1.0.0
 * Author     : Takashi Kitajima
 * Author URI : http://2inc.org
 * Create     : December 22, 2014
 * Modified   :
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
		$taxonomies = $this->get_taxonomies();
		foreach ( $taxonomies as $taxonomy => $name ) {
			$this->defaults[$taxonomy] = false;
		}
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
						<label><input type="checkbox" name="<?php echo esc_attr( $this->get_name() ); ?>[<?php echo esc_attr( $taxonomy ); ?>]" value="true" <?php checked( 'true', $option[$taxonomy] ); ?>> <?php echo esc_html( $name ); ?></label><br />
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
			'public'   => true,
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
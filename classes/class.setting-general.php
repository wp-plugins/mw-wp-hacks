<?php
/**
 * Name       : MW WP Hacks Setting General
 * Description: 管理画面
 * Version    : 1.0.0
 * Author     : Takashi Kitajima
 * Author URI : http://2inc.org
 * Create     : November 13, 2014
 * Modified   :
 * License    : GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
class MW_WP_Hacks_Setting_General extends MW_WP_Hacks_Abstract_Setting {

	/**
	 * set_key
	 */
	protected function set_key() {
		$this->key = 'general';
	}

	/**
	 * set_defaults
	 */
	protected function set_defaults() {
		$this->defaults = array(
			'wp_generator'               => 'true',
			'page_excerpt'               => 'true',
			'display_only_self_uploaded' => 'true',
			'fix_is_author'              => 'true',
			'fix_caption_width'          => 'true',
			'checked_ontop'              => 'true',
			'remove_updated_link'        => 'true',
			'thumbnail_info'             => 'true',
			'fix_ja_title'               => 'true',
		);
	}

	/**
	 * settings_page
	 */
	public function settings_page() {
		$option = $this->get_option();
		?>
		<tr>
			<th><?php esc_html_e( 'General', 'mw-wp-hacks' ); ?></th>
			<td>
				<p>
					<label><input type="checkbox" name="<?php echo esc_attr( $this->get_name() ); ?>[wp_generator]" value="true" <?php echo checked( 'true', $option['wp_generator'] ); ?> />
					<?php esc_html_e( 'Remove WordPress Version in head.', 'mw-wp-hacks' ); ?></label>
				</p>
				<p>
					<label><input type="checkbox" name="<?php echo esc_attr( $this->get_name() ); ?>[page_excerpt]" value="true" <?php echo checked( 'true', $option['page_excerpt'] ); ?> />
					<?php esc_html_e( 'Add support for excerpts in Pages.', 'mw-wp-hacks' ); ?></label>
				</p>
				<p>
					<label><input type="checkbox" name="<?php echo esc_attr( $this->get_name() ); ?>[display_only_self_uploaded]" value="true" <?php echo checked( 'true', $option['display_only_self_uploaded'] ); ?> />
					<?php esc_html_e( 'Display only the files that the author have uploaded in media library.', 'mw-wp-hacks' ); ?></label>
				</p>
				<p>
					<label><input type="checkbox" name="<?php echo esc_attr( $this->get_name() ); ?>[fix_is_author]" value="true" <?php echo checked( 'true', $option['fix_is_author'] ); ?> />
					<?php esc_html_e( 'The is_author flag to "false" when the is_archive_post_type flag is "true".', 'mw-wp-hacks' ); ?></label>
				</p>
				<p>
					<label><input type="checkbox" name="<?php echo esc_attr( $this->get_name() ); ?>[fix_caption_width]" value="true" <?php echo checked( 'true', $option['fix_caption_width'] ); ?> />
					<?php esc_html_e( 'Stop that width of .wp-caption is added 10px.', 'mw-wp-hacks' ); ?></label>
				</p>
				<p>
					<label><input type="checkbox" name="<?php echo esc_attr( $this->get_name() ); ?>[checked_ontop]" value="true" <?php echo checked( 'true', $option['checked_ontop'] ); ?> />
					<?php esc_html_e( 'Stop checked categories moving to top', 'mw-wp-hacks' ); ?></label>
				</p>
				<p>
					<label><input type="checkbox" name="<?php echo esc_attr( $this->get_name() ); ?>[remove_updated_link]" value="true" <?php echo checked( 'true', $option['remove_updated_link'] ); ?> />
					<?php esc_html_e( 'Remove updated link in edit page when public flag is false.', 'mw-wp-hacks' ); ?></label>
				</p>
				<p>
					<label><input type="checkbox" name="<?php echo esc_attr( $this->get_name() ); ?>[thumbnail_info]" value="true" <?php echo checked( 'true', $option['thumbnail_info'] ); ?> />
					<?php esc_html_e( 'Add thumbnail information in thumbnail meta box.', 'mw-wp-hacks' ); ?></label>
				</p>
				<?php if ( get_locale() === 'ja' ) : ?>
				<p>
					<label><input type="checkbox" name="<?php echo esc_attr( $this->get_name() ); ?>[fix_ja_title]" value="true" <?php echo checked( 'true', $option['fix_ja_title'] ); ?> />
					<?php esc_html_e( 'Fixed the bug of the "the_title".', 'mw-wp-hacks' ); ?></label>
				</p>
				<?php endif; ?>
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
		foreach ( $this->defaults as $key => $value ) {
			if ( !isset( $values[$key] ) ) {
				$values[$key] = 'false';
			}
		}
		return $values;
	}
}
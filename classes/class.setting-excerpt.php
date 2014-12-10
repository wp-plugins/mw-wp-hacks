<?php
/**
 * Name       : MW WP Hacks Setting Excerpt
 * Description: 管理画面
 * Version    : 1.0.0
 * Author     : Takashi Kitajima
 * Author URI : http://2inc.org
 * Create     : November 13, 2014
 * Modified   :
 * License    : GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
class MW_WP_Hacks_Setting_Excerpt extends MW_WP_Hacks_Abstract_Setting {

	/**
	 * set_key
	 */
	protected function set_key() {
		$this->key = 'excerpt';
	}

	/**
	 * set_defaults
	 */
	protected function set_defaults() {
		$this->defaults = '';
	}

	/**
	 * settings_page
	 */
	public function settings_page() {
		$option = $this->get_option();
		?>
		<tr>
			<th><?php _e( 'Text After Excerpt ( HTML )', 'mw-wp-hacks' ); ?></th>
			<td>
				<div id="<?php echo esc_attr( $this->get_name() ); ?>">
					<input type="text" name="<?php echo esc_attr( $this->get_name() ); ?>" value="<?php echo esc_attr( $option ); ?>" size="50">
					<p class="description">
						<?php _e( '%link% is converted to &lt;?php echo get_permalink(); ?&gt;', 'mw-wp-hacks' ); ?>
					</p>
				</div>
			</td>
		</tr>
		<?php
	}

	/**
	 * validate
	 * @param string $values
	 * @return string $valuees
	 */
	public function validate( $values ) {
		if ( !empty( $values ) && !is_array( $values ) ) {
			return $values;
		} else {
			return $this->defaults;
		}
	}
}
<?php
/**
 * Name       : MW WP Hacks Setting Thumbnail
 * Description: 管理画面
 * Version    : 1.0.0
 * Author     : Takashi Kitajima
 * Author URI : http://2inc.org
 * Create     : November 13, 2014
 * Modified   :
 * License    : GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
class MW_WP_Hacks_Setting_Thumbnail extends MW_WP_Hacks_Abstract_Setting {

	/**
	 * $crop
	 */
	protected $crop = array(
		0 => 'False',
		1 => 'True',
	);

	/**
	 * set_key
	 */
	protected function set_key() {
		$this->key = 'thumbnail';
	}

	/**
	 * set_defaults
	 */
	protected function set_defaults() {
		$this->defaults = array(
			array(
				'name'   => '',
				'width'  => 0,
				'height' => 0,
				'crop'   => 0,
			),
		);
	}

	/**
	 * settings_page
	 */
	public function settings_page() {
		$option = $this->get_option();
		?>
		<tr>
			<th><?php esc_html_e( 'Custom Thumbnail Sizes', 'mw-wp-hacks' ); ?></th>
			<td>
				<div id="<?php echo esc_attr( $this->get_name() ); ?>">
					<p>
						<span class="mwhacks-add button">Add</span>
					</p>
					<table border="0" cellpadding="0" cellspacing="0" class="data">
						<tr>
							<th style="width:1%">&nbsp;</th>
							<th>Thumbnail Size Name</th>
							<th style="width:20%">Width</th>
							<th style="width:20%">Height</th>
							<th style="width:20%">Crop</th>
						</tr>
						<?php foreach ( $option as $key => $value ) : ?>
						<tr class="add-box" <?php if ( $key === 0 ) : ?> style="display:none"<?php endif; ?>>
							<td><span class="mwhacks-remove">x</span></td>
							<td><input type="text" name="<?php echo esc_attr( $this->get_name() ); ?>[<?php echo esc_attr( $key ); ?>][name]" value="<?php echo esc_attr( $value['name'] ); ?>" size="20" /></td>
							<td><input type="text" name="<?php echo esc_attr( $this->get_name() ); ?>[<?php echo esc_attr( $key ); ?>][width]" value="<?php echo esc_attr( $value['width'] ); ?>" size="4" />px</td>
							<td><input type="text" name="<?php echo esc_attr( $this->get_name() ); ?>[<?php echo esc_attr( $key ); ?>][height]" value="<?php echo esc_attr( $value['height'] ); ?>" size="4" />px</td>
							<td>
								<select name="<?php echo esc_attr( $this->get_name() ); ?>[<?php echo esc_attr( $key ); ?>][crop]">
									<?php foreach ( $this->crop as $crop_key => $crop ) : ?>
									<option value="<?php echo esc_attr( $crop_key ); ?>" <?php selected( $value['crop'], $crop_key ); ?>><?php echo esc_html( $crop ); ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
						<?php endforeach; ?>
					</table>
					<p class="description">
						<?php _e( 'When you want to use thumbnails and you dont\' t define "post-thumbnail", firstly add "post-thumbnail".', 'mw-wp-hacks' ); ?>
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
			$values = $this->defaults;
		}
		foreach ( $values as $key => $value ) {
			if ( $key === 0 ) {
				unset( $values[0] );
				continue;
			}
			$value = shortcode_atts( $this->defaults[0], $value );
			if ( !preg_match( '/^[a-zA-z0-9_\-]+$/', $value['name'] ) ) {
				unset( $values[$key] );
				continue;
			}
			if ( !preg_match( '/^\d+$/', $value['width'] ) ) {
				$value['width'] = $this->defaults[0]['width'];
			}
			if ( !preg_match( '/^\d+$/', $value['height'] ) ) {
				$value['height'] = $this->defaults[0]['height'];
			}
			if ( !preg_match( '/^[0-1]$/', $value['crop'] ) ) {
				$value['crop'] = $this->defaults[0]['crop'];
			}
			$values[$key] = $value;
		}
		return $values;
	}
}
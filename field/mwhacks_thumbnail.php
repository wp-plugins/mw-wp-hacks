<?php
/**
 * Name: MW Hacks Thumbnail
 * URI: http://2inc.org
 * Description: thumbnail
 * Version: 1.0.0
 * Author: Takashi Kitajima
 * Author URI: http://2inc.org
 * Created : December 15, 2013
 * Modified:
 * License: GPL2
 *
 * Copyright 2013 Takashi Kitajima (email : inc@2inc.org)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
class mwhacks_thumbnail extends abstract_mwhacks_base {
	protected $base_name = 'thumbnail';

	/**
	 * activation
	 */
	public function activation() {
	}

	/**
	 * init
	 */
	public function init() {
		add_action( 'init', array( $this, 'set_thumbnail' ) );
	}

	/**
	 * sanitize
	 * @param mixed $data
	 */
	public function sanitize( $data ) {
		$thumbnails = array();
		if ( !is_array( $data ) )
			return;
		foreach ( $data as $value ) {
			if ( !isset( $value['name'], $value['width'], $value['height'], $value['crop'] ) )
				continue;
			if ( !preg_match( '/^[a-zA-z0-9_\-]+$/', $value['name'] ) )
				continue;
			if ( !preg_match( '/^\d+$/', $value['width'] ) )
				continue;
			if ( !preg_match( '/^\d+$/', $value['height'] ) )
				continue;
			if ( !( $value['crop'] === '1' || $value['crop'] === '0' ) )
				continue;
			$key = $value['name'];
			$thumbnails[$key] = $value;
		}
		if ( !empty( $thumbnails ) )
			return $thumbnails;
	}

	/**
	 * settings_page
	 */
	public function settings_page() {
		$thumbnails = $this->get_option();
		// 空の隠れバリデーションフィールド（コピー元）を挿入
		$thumbnail_keys = array(
			'name' => '',
			'width' => 0,
			'height' => 0,
			'crop' => 0,
		);
		if ( empty( $thumbnails ) )
			$thumbnails[] = $thumbnail_keys;
		array_unshift( $thumbnails, $thumbnail_keys );
		?>
		<tr>
			<th><?php _e( 'Custom Thumbnail Sizes', MWHACKS_Config::DOMAIN ); ?></th>
			<td>
				<div id="<?php echo $this->name; ?>">
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
						<?php foreach ( $thumbnails as $key => $value ) : ?>
						<tr class="add-box" <?php if ( $key === 0 ) : ?> style="display:none"<?php endif; ?>>
							<td><span class="mwhacks-remove">x</span></td>
							<td><input type="text" name="<?php echo $this->name; ?>[<?php echo $key; ?>][name]" value="<?php echo esc_attr( $value['name'] ); ?>" size="20" /></td>
							<td><input type="text" name="<?php echo $this->name; ?>[<?php echo $key; ?>][width]" value="<?php echo esc_attr( $value['width'] ); ?>" size="4" />px</td>
							<td><input type="text" name="<?php echo $this->name; ?>[<?php echo $key; ?>][height]" value="<?php echo esc_attr( $value['height'] ); ?>" size="4" />px</td>
							<td>
								<select name="<?php echo $this->name; ?>[<?php echo $key; ?>][crop]">
									<?php
									$options = array( 'False', 'True' );
									?>
									<?php foreach ( $options as $option_key => $option_value ) : ?>
									<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $value['crop'], $option_key ); ?>><?php echo esc_html( $option_value ); ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
						<?php endforeach; ?>
					</table>
					<p class="description">
						<?php _e( 'When you want to use a thumbnail, firstly add "Thumbnail Size Name" is a thumbnail size of "post-thumbnail".', MWHACKS_Config::DOMAIN ); ?>
					</p>
				<!-- end #<?php echo $this->name; ?> --></div>
			</td>
		</tr>
		<?php
	}

	/**
	 * set_thumbnail
	 */
	public function set_thumbnail() {
		$option = $this->get_option();
		if ( !empty( $option ) && is_array( $option ) ) {
			add_theme_support( 'post-thumbnails' );
			$thumbnails = $option;
			foreach ( $thumbnails as $thumbnail ) {
				add_image_size( $thumbnail['name'], $thumbnail['width'], $thumbnail['height'], $thumbnail['crop'] );
			}
			//var_dump( get_intermediate_image_sizes() );
		}
	}
}
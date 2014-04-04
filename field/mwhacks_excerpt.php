<?php
/**
 * Name: MW Hacks Excerpt
 * URI: http://2inc.org
 * Description: excerpt
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
class mwhacks_excerpt extends abstract_mwhacks_base {
	protected $base_name = 'excerpt';

	/**
	 * activation
	 */
	public function activation() {
	}

	/**
	 * init
	 */
	public function init() {
		add_filter( 'wp_trim_excerpt', array( $this, 'wp_trim_excerpt' ) );
	}

	/**
	 * sanitize
	 * @param mixed $data
	 */
	public function sanitize( $data ) {
		if ( !empty( $data ) && !is_array( $data ) )
			return $data;
	}

	/**
	 * settings_page
	 */
	public function settings_page() {
		$excerpt = $this->get_option();
		?>
		<tr>
			<th><?php _e( 'Text After Excerpt ( HTML )', MWHACKS_Config::DOMAIN ); ?></th>
			<td>
				<div id="<?php echo $this->name; ?>">
					<input type="text" name="<?php echo $this->name; ?>" value="<?php echo esc_attr( $excerpt ); ?>" size="50">
					<p class="description">
						<?php _e( '%link% is converted to &lt;?php echo get_permalink(); ?&gt;', MWHACKS_Config::DOMAIN ); ?>
					</p>
				<!-- end #<?php echo $this->name; ?> --></div>
			</td>
		</tr>
		<?php
	}

	/**
	 * wp_trim_excerpt
	 * the_excerpt実行時に実行される
	 * @param string $excerpt
	 * @return string
	 */
	public function wp_trim_excerpt( $excerpt ) {
		global $post;
		$more = '';
		$option = $this->get_option();
		if ( !empty( $option ) && !is_array( $option ) ) {
			$more = $option;
			$more = preg_replace( '/%link%/', get_permalink(), $more );
		}
		return $excerpt . $more;
	}
}
<?php
/**
 * Name: MW Hacks Feed
 * URI: http://2inc.org
 * Description: feed
 * Version: 1.0.1
 * Author: Takashi Kitajima
 * Author URI: http://2inc.org
 * Created : December 15, 2013
 * Modified: July 24, 2014
 * License: GPL2
 *
 * Copyright 2014 Takashi Kitajima (email : inc@2inc.org)
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
class mwhacks_feed extends abstract_mwhacks_base {
	protected $base_name = 'feed';

	/**
	 * activation
	 */
	public function activation() {
		add_option( $this->name, array( 'post' ) );
	}

	/**
	 * init
	 */
	public function init() {
		add_filter( 'pre_get_posts', array( $this, 'set_rss_post_types' ) );
	}

	/**
	 * sanitize
	 * @param mixed $data
	 */
	public function sanitize( $data ) {
		if ( !empty( $data ) && is_array( $data ) )
			return $data;
	}

	/**
	 * settings_page
	 */
	public function settings_page() {
		$post_types = $this->get_post_types();
		$feed = $this->get_option();
		if ( !$feed ) {
			$feed = array();
		}
		?>
		<tr>
			<th><?php _e( 'Included Post Types in Main Feed', MWHACKS_Config::DOMAIN ); ?></th>
			<td>
				<div id="<?php echo $this->name; ?>">
					<p>
						<?php foreach ( $post_types as $post_type => $name ) : ?>
						<input type="checkbox" name="<?php echo $this->name; ?>[]" value="<?php echo esc_attr( $post_type ); ?>" <?php checked( in_array( $post_type, $feed ), true ); ?>> <?php echo esc_html( $name ); ?><br />
						<?php endforeach; ?>
					</p>
				<!-- end #<?php echo $this->name; ?> --></div>
			</td>
		</tr>
		<?php
	}

	/**
	 * set_rss_post_types
	 * @param object $query
	 * @param object $query
	 */
	public function set_rss_post_types( $query ) {
		if ( $query->is_feed ) {
			$post_type = $query->get( 'post_type' );
			if ( empty( $post_type ) ) {
				$option = $this->get_option();
				if ( is_array( $option ) ) {
					$query->set( 'post_type', $option );
				}
			}
			return $query;
		}
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
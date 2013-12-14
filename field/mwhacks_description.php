<?php
/**
 * Name: MW Hacks Description
 * URI: http://2inc.org
 * Description: description
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
class mwhacks_description extends abstract_mwhacks_base {
	protected $base_name = 'description';

	/**
	 * activation
	 */
	public function activation() {
	}

	/**
	 * init
	 */
	public function init() {
		add_action( 'wp_head', array( $this, 'display_description' ) );
	}

	/**
	 * sanitize
	 * @param mixed $data
	 */
	public function sanitize( $data ) {
		if ( $data === 'true' ) {
			return $data;
		} else {
			return false;
		}
	}

	/**
	 * settings_page
	 */
	public function settings_page() {
		$option = $this->get_option();
		?>
		<tr>
			<th><?php _e( 'Description', MWHACKS_Config::DOMAIN ); ?></th>
			<td>
				<div id="<?php echo $this->name; ?>">
					<p>
						<input type="checkbox" name="<?php echo $this->name; ?>" value="true" <?php checked( $option, "true" ); ?>> <?php _e( 'Output meta description', MWHACKS_Config::DOMAIN ); ?>
					</p>
				<!-- end #<?php echo $this->name; ?> --></div>
			</td>
		</tr>
		<?php
	}

	/**
	 * display_description
	 */
	public function display_description() {
		$option = $this->get_option();
		if ( $option === 'true' ) :
		?>
		<meta name="description" content="<?php echo esc_attr( mw_wp_hacks::get_description() ); ?>" />
		<?php
		endif;
	}
}
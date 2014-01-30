<?php
/**
 * Name: MW Hacks Excerptmore
 * URI: http://2inc.org
 * Description: excerptmore
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
class mwhacks_excerptmore extends abstract_mwhacks_base {
	protected $base_name = 'excerptmore';

	/**
	 * activation
	 */
	public function activation() {
		add_option( $this->name, '[...]' );
	}

	/**
	 * init
	 */
	public function init() {
		add_filter( 'excerpt_more', array( $this, 'excerpt_more' ) );
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
		$excerptmore = $this->get_option();
		?>
		<tr>
			<th><?php _e( 'Text After Excerpt when an Excerpt was Cut ( HTML )', MWHACKS_Config::DOMAIN ); ?></th>
			<td>
				<div id="<?php echo $this->name; ?>">
					<input type="text" name="<?php echo $this->name; ?>" value="<?php echo esc_attr( $excerptmore ); ?>" size="20">
				<!-- end #<?php echo $this->name; ?> --></div>
			</td>
		</tr>
		<?php
	}

	/**
	 * excerpt_more
	 * 抜粋もしくは本文が一定の文字数を超えたときに実行される
	 * @param string $more
	 * @return string
	 */
	public function excerpt_more( $more ) {
		return $this->get_option();
	}
}
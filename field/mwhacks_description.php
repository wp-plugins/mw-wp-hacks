<?php
/**
 * Name: MW Hacks Description
 * URI: http://2inc.org
 * Description: description
 * Version: 1.0.1
 * Author: Takashi Kitajima
 * Author URI: http://2inc.org
 * Created : December 15, 2013
 * Modified: January 10, 2014
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
		$description = array();
		// Ver 1.0.1 未満用
		if ( !is_array( $data ) ) {
			$_data = $data;
			$data['view'] = $_data;
		}
		// end Ver 1.0.1 未満用
		$description = $data;
		if ( $description['view'] !== 'true' ) {
			$description['view'] = false;
		}
		return $description;
	}

	/**
	 * settings_page
	 */
	public function settings_page() {
		$post_types = $this->get_post_types();
		$post_type_objects = array();
		foreach ( $post_types as $post_type ) {
			$object = get_post_type_object( $post_type );
			$post_type_objects[$object->name] = $object;
		}

		$defaults = array();
		foreach ( $post_type_objects as $post_type_name => $post_type_object ) {
			$defaults['pt_' . $post_type_name] = '';
		}
		$defaults['view'] = false;
		$defaults['basic'] = '';

		$option = $this->get_option();
		$option = array_merge( $defaults, $option );
		?>
		<tr>
			<th><?php _e( 'Description', MWHACKS_Config::DOMAIN ); ?></th>
			<td>
				<div id="<?php echo $this->name; ?>">
					<p>
						<input type="checkbox" name="<?php echo $this->name; ?>[view]" value="true" <?php checked( $option['view'], "true" ); ?>> <?php _e( 'Output meta description', MWHACKS_Config::DOMAIN ); ?>
					</p>
					<div class="<?php echo $this->name; ?>-setting">
						<p>
							<?php _e( 'If this fields is empty, Tagline is used.', MWHACKS_Config::DOMAIN ); ?>
						</p>
						<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<th><?php _e( 'Basic', MWHACKS_Config::DOMAIN ); ?></th>
								<td><input type="text" name="<?php echo $this->name; ?>[basic]" value="<?php echo esc_attr( $option['basic'] ); ?>" size="50" /></td>
							</tr>
							<?php foreach ( $post_type_objects as $post_type_name => $post_type_object ) : ?>
							<tr>
								<th><?php echo esc_html( $post_type_object->labels->singular_name ); ?></th>
								<td><input type="text" name="<?php echo $this->name; ?>[pt_<?php echo esc_attr( $post_type_name ); ?>]" value="<?php echo esc_attr( $option['pt_' . $post_type_name] ); ?>" size="50" /></td>
							</tr>
							<?php endforeach; ?>
						</table>
					</div>
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

		if ( $option['view'] === 'true' ) :
			add_filter( MWHACKS_Config::NAME . '-description', array( $this, 'custom_description' ), 9 );
		?>
		<meta name="description" content="<?php echo esc_attr( mw_wp_hacks::get_description() ); ?>" />
		<?php
		endif;
	}

	/**
	 * custom_description
	 * @param string $description
	 * @return string $description
	 */
	public function custom_description( $description ) {
		$option = $this->get_option();
		$post_types = $this->get_post_types();
		foreach( $post_types as $post_type ) {
			if ( $post_type === get_post_type() && !empty( $option['pt_' . $post_type] ) && !is_singular() ) {
				return $option['pt_' . $post_type];
			}
		}
		if ( !empty( $option['basic'] ) ) {
			return $option['basic'];
		}
		return $description;
	}

	/**
	 * get_post_types
	 * @return array
	 */
	public function get_post_types() {
		return get_post_types( array(
			'public' => true,
			'has_archive' => true,
		) );
	}

	/**
	 * get_option
	 * Ver 1.0.1 未満対応
	 * @return array $option
	 */
	public function get_option() {
		$option = parent::get_option();
		if ( !is_array( $option ) ) {
			$_option = $option;
			$option = array();
			$option['view'] = $_option;
		}
		return $option;
	}
}
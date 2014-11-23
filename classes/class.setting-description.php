<?php
/**
 * Name       : MW WP Hacks Setting Description
 * Description: 管理画面
 * Version    : 1.0.0
 * Author     : Takashi Kitajima
 * Author URI : http://2inc.org
 * Create     : November 13, 2014
 * Modified   :
 * License    : GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
class MW_WP_Hacks_Setting_Description extends MW_WP_Hacks_Abstract_Setting {

	/**
	 * set_key
	 */
	protected function set_key() {
		$this->key = 'description';
	}

	/**
	 * set_defaults
	 */
	protected function set_defaults() {
		$this->defaults = array(
			'view'  => 'false',
			'basic' => '',
		);
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

		$option = $this->get_option();
		$option = array_merge( $defaults, $option );
		?>
		<tr>
			<th><?php esc_html_e( 'Description', 'mw-wp-hacks' ); ?></th>
			<td>
				<div id="<?php echo esc_attr( $this->get_name() ); ?>">
					<p>
						<input type="checkbox" name="<?php echo esc_attr( $this->get_name() ); ?>[view]" value="true" <?php checked( $option['view'], "true" ); ?>> <?php _e( 'Output meta description', 'mw-wp-hacks' ); ?>
					</p>
					<div class="<?php echo esc_attr( $this->get_name() ); ?>-setting">
						<p>
							<?php _e( 'If this fields is empty, Tagline is used.', 'mw-wp-hacks' ); ?>
						</p>
						<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<th style="width:1px; white-space:nowrap;"><?php _e( 'Basic', 'mw-wp-hacks' ); ?></th>
								<td><input type="text" name="<?php echo esc_attr( $this->get_name() ); ?>[basic]" value="<?php echo esc_attr( $option['basic'] ); ?>" size="50" /></td>
							</tr>
							<?php foreach ( $post_type_objects as $post_type_name => $post_type_object ) : ?>
							<tr>
								<th><?php echo esc_html( $post_type_object->labels->singular_name ); ?></th>
								<td><input type="text" name="<?php echo esc_attr( $this->get_name() ); ?>[pt_<?php echo esc_attr( $post_type_name ); ?>]" value="<?php echo esc_attr( $option['pt_' . $post_type_name] ); ?>" size="50" /></td>
							</tr>
							<?php endforeach; ?>
						</table>
					</div>
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
		// Ver 1.0.1 未満用
		if ( !is_array( $values ) ) {
			$values['view'] = $values;
		}
		// end
		$values = shortcode_atts( $this->defaults, $values );
		if ( $values['view'] !== 'true' ) {
			$values['view'] = 'false';
		}
		return $values;
	}

	/**
	 * get_post_types
	 * @return array
	 */
	protected function get_post_types() {
		return get_post_types( array(
			'public' => true,
			'has_archive' => true,
		) );
	}
}
<?php
/**
 * Name       : MW WP Hacks Setting Widget
 * Description: 管理画面
 * Version    : 1.0.0
 * Author     : Takashi Kitajima
 * Author URI : http://2inc.org
 * Create     : November 13, 2014
 * Modified   :
 * License    : GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
class MW_WP_Hacks_Setting_Widget extends MW_WP_Hacks_Abstract_Setting {

	/**
	 * set_key
	 */
	protected function set_key() {
		$this->key = 'widget';
	}

	/**
	 * set_defaults
	 */
	protected function set_defaults() {
		$this->defaults = array(
			array(
				'name'          => '',
				'id'            => '',
				'description'   => '',
				'before_widget' => '',
				'after_widget'  => '',
				'before_title'  => '',
				'after_title'   => '',
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
			<th><?php _e( 'Widget Areas', 'mw-wp-hacks' ); ?></th>
			<td>
				<p>
					<span class="mwhacks-add button">Add</span>
				</p>
				<div id="<?php echo esc_attr( $this->get_name() ); ?>">
					<?php foreach ( $option as $key => $value ) : ?>
					<div class="add-box"<?php if ( $key === 0 ) : ?> style="display:none"<?php endif; ?>>
						<table border="0" cellpadding="0" cellspacing="0" class="data">
							<tr>
								<td style="width:1%;text-align:center" rowspan="7"><span class="mwhacks-remove">x</span></td>
								<th style="width:15%">ID *</th>
								<td><input type="text" name="<?php echo esc_attr( $this->get_name() ); ?>[<?php echo esc_attr( $key ); ?>][id]" value="<?php echo esc_attr( $value['id'] ); ?>" size="30" placeholder="Sidebar Name" /></td>
							</tr>
							<tr>
								<th>Name *</th>
								<td><input type="text" name="<?php echo esc_attr( $this->get_name() ); ?>[<?php echo esc_attr( $key ); ?>][name]" value="<?php echo esc_attr( $value['name'] ); ?>" size="30" placeholder="sidebar" /></td>
							</tr>
							<tr>
								<th>Description</th>
								<td><input type="text" name="<?php echo esc_attr( $this->get_name() ); ?>[<?php echo esc_attr( $key ); ?>][description]" value="<?php echo esc_attr( $value['description'] ); ?>" size="30" /></td>
							</tr>
							<tr>
								<th>before_widget</th>
								<td><input type="text" name="<?php echo esc_attr( $this->get_name() ); ?>[<?php echo esc_attr( $key ); ?>][before_widget]" value="<?php echo esc_attr( $value['before_widget'] ); ?>" size="30" placeholder="<?php echo esc_attr( '<li id="%1$s" class="widget %2$s">' ); ?>" />
									<p class="description">
										<?php esc_html_e( '%1$s is converted to ID, %2$s is converted to Class.', 'mw-wp-hacks' ); ?>
									</p>
								</td>
							</tr>
							<tr>
								<th>after_widget</th>
								<td><input type="text" name="<?php echo esc_attr( $this->get_name() ); ?>[<?php echo esc_attr( $key ); ?>][after_widget]" value="<?php echo esc_attr( $value['after_widget'] ); ?>" size="30" placeholder="<?php echo esc_attr( '</li>' ); ?>" /></td>
							</tr>
							<tr>
								<th>before_title</th>
								<td><input type="text" name="<?php echo esc_attr( $this->get_name() ); ?>[<?php echo esc_attr( $key ); ?>][before_title]" value="<?php echo esc_attr( $value['before_title'] ); ?>" size="30" placeholder="<?php echo esc_attr( '<h2 class="widgettitle">' ); ?>" /></td>
							</tr>
							<tr>
								<th>after_title</th>
								<td><input type="text" name="<?php echo esc_attr( $this->get_name() ); ?>[<?php echo esc_attr( $key ); ?>][after_title]" value="<?php echo esc_attr( $value['after_title'] ); ?>" size="30" placeholder="<?php echo esc_attr( '</h2>' ); ?>" /></td>
							</tr>
						</table>
					<!-- end .mwhacks-widget-box --></div>
					<?php endforeach; ?>
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
			if ( empty( $value['name'] ) ) {
				unset( $values[$key] );
				continue;
			}
			if ( empty( $value['id'] ) ) {
				unset( $values[$key] );
				continue;
			}
			$values[$key] = $value;
		}
		return $values;
	}
}
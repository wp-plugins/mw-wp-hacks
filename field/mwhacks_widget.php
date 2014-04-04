<?php
/**
 * Name: MW Hacks Widget
 * URI: http://2inc.org
 * Description: widget
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
class mwhacks_widget extends abstract_mwhacks_base {
	protected $base_name = 'widget';

	/**
	 * activation
	 */
	public function activation() {
	}

	/**
	 * init
	 */
	public function init() {
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
	}

	/**
	 * sanitize
	 * @param mixed $data
	 */
	public function sanitize( $data ) {
		$widgets = array();
		if ( !is_array( $data ) )
			return;
		foreach ( $data as $value ) {
			if ( !isset( $value['name'], $value['id'], $value['description'], $value['before_widget'], $value['after_widget'], $value['before_title'], $value['after_title'] ) )
				continue;
			if ( empty( $value['name'] ) )
				continue;
			if ( empty( $value['id'] ) )
				continue;
			if ( empty( $value['description'] ) )
				continue;
			if ( empty( $value['before_widget'] ) )
				continue;
			if ( empty( $value['after_widget'] ) )
				continue;
			if ( empty( $value['before_title'] ) )
				continue;
			if ( empty( $value['after_title'] ) )
				continue;
			$key = $value['id'];
			$widgets[$key] = $value;
		}
		if ( !empty( $widgets ) )
			return $widgets;
	}

	/**
	 * settings_page
	 */
	public function settings_page() {
		$widgets = $this->get_option();
		// 空の隠れバリデーションフィールド（コピー元）を挿入
		$widget_keys = array(
			'name' => '',
			'id' => '',
			'description' => '',
			'before_widget' => '',
			'after_widget' => '',
			'before_title' => '',
			'after_title' => '',
		);
		if ( empty( $widgets ) ) {
			$widgets[] = $widget_keys;
		}
		array_unshift( $widgets, $widget_keys );
		?>
		<tr>
			<th><?php _e( 'Widget Areas', MWHACKS_Config::DOMAIN ); ?></th>
			<td>
				<p>
					<span class="mwhacks-add button">Add</span>
				</p>
				<div id="<?php echo $this->name; ?>">
					<?php foreach ( $widgets as $key => $value ) : ?>
					<div class="add-box"<?php if ( $key === 0 ) : ?> style="display:none"<?php endif; ?>>
						<table border="0" cellpadding="0" cellspacing="0" class="data">
							<tr>
								<td style="width:1%;text-align:center" rowspan="7"><span class="mwhacks-remove">x</span></td>
								<th style="width:15%">ID</th>
								<td><input type="text" name="<?php echo $this->name; ?>[<?php echo $key; ?>][id]" value="<?php echo esc_attr( $value['id'] ); ?>" size="30" /></td>
							</tr>
							<tr>
								<th>Name</th>
								<td><input type="text" name="<?php echo $this->name; ?>[<?php echo $key; ?>][name]" value="<?php echo esc_attr( $value['name'] ); ?>" size="30" /></td>
							</tr>
							<tr>
								<th>Description</th>
								<td><input type="text" name="<?php echo $this->name; ?>[<?php echo $key; ?>][description]" value="<?php echo esc_attr( $value['description'] ); ?>" size="30" /></td>
							</tr>
							<tr>
								<th>before_widget</th>
								<td><input type="text" name="<?php echo $this->name; ?>[<?php echo $key; ?>][before_widget]" value="<?php echo esc_attr( $value['before_widget'] ); ?>" size="30" />
									<p class="description">
										<?php _e( '%1$s is converted to ID, %2$s is converted to Class.', MWHACKS_Config::DOMAIN ); ?>
									</p>
								</td>
							</tr>
							<tr>
								<th>after_widget</th>
								<td><input type="text" name="<?php echo $this->name; ?>[<?php echo $key; ?>][after_widget]" value="<?php echo esc_attr( $value['after_widget'] ); ?>" size="30" /></td>
							</tr>
							<tr>
								<th>before_title</th>
								<td><input type="text" name="<?php echo $this->name; ?>[<?php echo $key; ?>][before_title]" value="<?php echo esc_attr( $value['before_title'] ); ?>" size="30" /></td>
							</tr>
							<tr>
								<th>after_title</th>
								<td><input type="text" name="<?php echo $this->name; ?>[<?php echo $key; ?>][after_title]" value="<?php echo esc_attr( $value['after_title'] ); ?>" size="30" /></td>
							</tr>
						</table>
					<!-- end .mwhacks-widget-box --></div>
					<?php endforeach; ?>
				<!-- end #<?php echo $this->name; ?> --></div>
			</td>
		</tr>
		<?php
	}

	/**
	 * widgets_init
	 */
	public function widgets_init() {
		$option = $this->get_option();
		$widgets = array();
		if ( !empty( $option ) && is_array( $option ) ) {
			$widgets = $option;
			foreach ( $widgets as $widget ) {
				register_sidebar( $widget );
			}
		}
	}
}
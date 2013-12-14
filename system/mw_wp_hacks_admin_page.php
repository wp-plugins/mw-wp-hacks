<?php
/**
 * Name: MW WP Hacks Admin Page
 * URI: http://2inc.org
 * Description: 管理画面
 * Version: 1.0.3
 * Author: Takashi Kitajima
 * Author URI: http://2inc.org
 * Created : September 30, 2013
 * Modified: December 15, 2013
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
class mw_wp_hacks_admin_page {

	private $fields = array();

	/**
	 * __construct
	 * 本体の admin_menu フックから呼ばれる
	 */
	public function __construct() {
		$this->fields = mw_wp_hacks::load_fields_classes();

		$hook = add_menu_page(
			'MW WP Hacks',
			'MW WP Hacks',
			'manage_options',
			basename( __FILE__ ),
			array( $this, 'settings_page' )
		);
		add_action( 'admin_print_styles-' . $hook, array( $this, 'admin_style' ) );
		add_action( 'admin_print_scripts-' . $hook, array( $this, 'admin_scripts' ) );
	}

	/**
	 * admin_style
	 * CSS適用
	 */
	public function admin_style() {
		$url = plugin_dir_url( __FILE__ );
		wp_register_style( MWHACKS_Config::DOMAIN . '-admin', $url . '../css/admin.css' );
		wp_enqueue_style( MWHACKS_Config::DOMAIN . '-admin' );
	}

	/**
	 * admin_scripts
	 * JavaScript適用
	 */
	public function admin_scripts() {
		$url = plugin_dir_url( __FILE__ );
		wp_register_script( MWHACKS_Config::DOMAIN . '-admin', $url . '../js/admin.js' );
		wp_enqueue_script( MWHACKS_Config::DOMAIN . '-admin' );
	}

	public function settings_page() {
		?>
		<div class="wrap">
			<h2>MW WP Hacks</h2>

			<?php if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] === 'true' ) : ?>
			<div id="message" class="updated">
				<p>
					<?php _e( 'Updated.', MWHACKS_Config::DOMAIN ); ?>
				</p>
			<!-- end #message --></div>
			<?php endif; ?>

			<form method="post" action="options.php">
				<?php settings_fields( MWHACKS_Config::NAME . '-group' ); ?>
				<table class="form-table">
					<?php
					foreach ( $this->fields as $field ) {
						$field->settings_page();
					}
					?>
				</table>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', MWHACKS_Config::DOMAIN ) ?>" />
				</p>
			</form>
		<!-- end .wrap --></div>
		<?php
	}
}
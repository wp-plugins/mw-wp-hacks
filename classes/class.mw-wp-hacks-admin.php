<?php
/**
 * Name       : MW WP Hacks Admin
 * Description: 管理画面
 * Version    : 1.0.0
 * Author     : Takashi Kitajima
 * Author URI : http://2inc.org
 * Create     : November 11, 2014
 * Modified   :
 * License    : GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
class MW_WP_Hacks_Admin {

	/**
	 * __construct
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	/**
	 * admin_menu
	 */
	public function admin_menu() {
		$hook = add_menu_page(
			'MW WP Hacks',
			'MW WP Hacks',
			'manage_options',
			basename( __FILE__ ),
			array( $this, 'display' )
		);
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	/**
	 * admin_enqueue_scripts
	 */
	public function admin_enqueue_scripts( $hook ) {
		if ( $hook === 'toplevel_page_class.mw-wp-hacks-admin' ) {
			$url = plugin_dir_url( __FILE__ );
			wp_register_style(
				MW_WP_Hacks_Config::NAME . '-admin',
				$url . '../css/admin.css'
			);
			wp_enqueue_style( MW_WP_Hacks_Config::NAME . '-admin' );
			wp_register_script(
				MW_WP_Hacks_Config::NAME . '-admin',
				$url . '../js/admin.js',
				array( 'jquery' ),
				false,
				true
			);
			wp_enqueue_script( MW_WP_Hacks_Config::NAME . '-admin' );
		}
	}

	/**
	 * display
	 */
	public function display() {
		?>
		<div class="wrap">
			<h2>MW WP Hacks</h2>

			<?php if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] === 'true' ) : ?>
			<div id="message" class="updated">
				<p>
					<?php esc_html_e( 'Updated.', 'mw-wp-hacks' ); ?>
				</p>
			<!-- end #message --></div>
			<?php endif; ?>

			<form method="post" action="options.php">
				<?php settings_fields( MW_WP_Hacks_Config::NAME . '-group' ); ?>
				<table class="form-table">
					<?php
					do_action( MW_WP_Hacks_Config::NAME . '-settings-page' );
					?>
				</table>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php esc_html_e( 'Save Changes', 'mw-wp-hacks' ) ?>" />
				</p>
			</form>
		<!-- end .wrap --></div>
		<?php
	}
}
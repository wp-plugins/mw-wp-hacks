<?php
/**
 * Name       : MW WP Hacks Setting Social
 * Description: 管理画面
 * Version    : 1.0.0
 * Author     : Takashi Kitajima
 * Author URI : http://2inc.org
 * Create     : November 13, 2014
 * Modified   :
 * License    : GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
class MW_WP_Hacks_Setting_Social extends MW_WP_Hacks_Abstract_Setting {

	/**
	 * __construct
	 */
	public function __construct() {
		parent::__construct();
		add_filter( 'user_contactmethods', array( $this, 'add_custom_contactmethods' ) );
		add_action( 'profile_update', array( $this, 'profile_update' ), 10, 2 );
	}

	/**
	 * set_key
	 */
	protected function set_key() {
		$this->key = 'social';
	}

	/**
	 * set_defaults
	 */
	protected function set_defaults() {
		$this->defaults = array(
			'facebook_app_id'          => '',
			'ga_tracking_id'           => '',
			'ua_tracking_id'           => '',
			'google_plus_id'           => '',
			'google_site_verification' => '',
		);
	}

	/**
	 * settings_page
	 */
	public function settings_page() {
		$option = $this->get_option();
		?>
		<tr>
			<th><?php _e( 'Social Account', 'mw-wp-hacks' ); ?></th>
			<td>
				<div id="<?php echo esc_attr( $this->get_name() ); ?>">
					<table border="0" cellpadding="0" cellspacing="0" class="data">
						<tr>
							<th style="width:33%">Facebook AppID</th>
							<td>
								<input type="text" name="<?php echo esc_attr( $this->get_name() ); ?>[facebook_app_id]" value="<?php echo esc_attr( $option['facebook_app_id'] ); ?>" size="50" />
								<p class="description">
									<?php esc_html_e( 'IF you use setting about "include facebook script" or "OGP", you shoud input this field.', 'mw-wp-hacks' ); ?>
								</p>
							</td>
						</tr>
						<tr>
							<th>GA Tracking ID</th>
							<td>
								<input type="text" name="<?php echo esc_attr( $this->get_name() ); ?>[ga_tracking_id]" value="<?php echo esc_attr( $option['ga_tracking_id'] ); ?>" size="50" />
								<p class="description">
									<?php esc_html_e( 'IF you input this, to generate GA tracking script.', 'mw-wp-hacks' ); ?>
								</p>
							</td>
						</tr>
						<tr>
							<th>UA Tracking ID</th>
							<td>
								<input type="text" name="<?php echo esc_attr( $this->get_name() ); ?>[ua_tracking_id]" value="<?php echo esc_attr( $option['ua_tracking_id'] ); ?>" size="50" />
								<p class="description">
									<?php esc_html_e( 'IF you input this, to generate UA tracking script.', 'mw-wp-hacks' ); ?>
								</p>
							</td>
						</tr>
						<tr>
							<th>Google+ ID</th>
							<td>
								<input type="text" name="<?php echo esc_attr( $this->get_name() ); ?>[google_plus_id]" value="<?php echo esc_attr( $option['google_plus_id'] ); ?>" size="50" />
								<p class="description">
									<?php esc_html_e( 'IF you input this, to generate publisher tag of Google+ profile page.', 'mw-wp-hacks' ); ?>
								</p>
							</td>
						</tr>
						<tr>
							<th>Google Site Verification</th>
							<td>
								<input type="text" name="<?php echo esc_attr( $this->get_name() ); ?>[google_site_verification]" value="<?php echo esc_attr( $option['google_site_verification'] ); ?>" size="50" />
								<p class="description">
									<?php esc_html_e( 'IF you input this, to generate meta tag for Google Site Verification.', 'mw-wp-hacks' ); ?>
								</p>
							</td>
						</tr>
					</table>
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
			$values = array();
		}
		$values = shortcode_atts( $this->defaults, $values );

		if ( !preg_match( '/^\d+$/', $values['facebook_app_id'] ) ) {
			$values['facebook_app_id'] = $this->defaults['facebook_app_id'];
		}

		if ( !preg_match( '/^UA\-\d+?\-\d+$/', $values['ga_tracking_id'] ) ) {
			$values['ga_tracking_id'] = $this->defaults['ga_tracking_id'];
		}
		
		if ( !preg_match( '/^UA\-\d+?\-\d+$/', $values['ua_tracking_id'] ) ) {
			$values['ua_tracking_id'] = $this->defaults['ua_tracking_id'];
		}
		
		if ( !preg_match( '/^\d+$/', $values['google_plus_id'] ) ) {
			$values['google_plus_id'] = $this->defaults['google_plus_id'];
		}
		
		if ( !isset( $values['google_site_verification'] ) ) {
			$values['google_site_verification'] = $this->defaults['google_site_verification'];
		}
		return $values;
	}

	/**
	 * add_custom_contactmethods
	 * プロフィールにGoogle+ IDを追加
	 */
	public function add_custom_contactmethods( $user_contactmethods ) {
		return array(
			MW_WP_Hacks_Config::NAME_GOOGLEPLUSID => 'Google+ ID'
		);
	}

	/**
	 * profile_update
	 */
	public function profile_update( $user_id, $old_user_data ) {
		if ( isset( $_POST[MW_WP_Hacks_Config::NAME_GOOGLEPLUSID] ) &&
			 preg_match( '/^\d+$/', $_POST[MW_WP_Hacks_Config::NAME_GOOGLEPLUSID] ) ) {
			update_user_meta(
				$user_id,
				MW_WP_Hacks_Config::NAME_GOOGLEPLUSID,
				$_POST[MW_WP_Hacks_Config::NAME_GOOGLEPLUSID]
			);
		} else {
			update_user_meta( $user_id, MW_WP_Hacks_Config::NAME_GOOGLEPLUSID, '' );
		}
	}
}
<?php
/**
 * Name: MW Hacks Social
 * URI: http://2inc.org
 * Description: social
 * Version: 1.0.1
 * Author: Takashi Kitajima
 * Author URI: http://2inc.org
 * Created : December 15, 2013
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
class mwhacks_social extends abstract_mwhacks_base {
	protected $base_name = 'social';
	private $name_google_plus_id;

	/**
	 * __construct
	 */
	public function __construct() {
		parent::__construct();
		$this->name_google_plus_id = MWHACKS_Config::NAME . '_google_plus_id';
	}

	/**
	 * activation
	 */
	public function activation() {
	}

	/**
	 * uninstall
	 */
	public function uninstall() {
		parent::uninstall();
		$users = get_users();
		foreach ( $users as $user ) {
			delete_user_meta( $user->ID, $this->name_google_plus_id );
		}
	}

	/**
	 * init
	 */
	public function init() {
		add_filter( 'wp_footer', array( $this, 'facebook_root' ) );
		add_action( 'wp_head', array( $this, 'add_profile_for_google_plus' ) );
		add_action( 'wp_head', array( $this, 'add_google_site_verification' ) );
		add_filter( 'user_contactmethods', array( $this, 'add_custom_contactmethods' ) );
		add_action( 'profile_update', array( $this, 'profile_update' ), 10, 2 );
	}

	/**
	 * sanitize
	 * @param mixed $data
	 */
	public function sanitize( $data ) {
		$socials = array();
		if ( !is_array( $data ) )
			return;
		if ( isset( $data['facebook_app_id'] ) ) {
			if ( preg_match( '/^\d+$/', $data['facebook_app_id'] ) )
				$socials['facebook_app_id'] = $data['facebook_app_id'];
		}
		if ( isset( $data['ga_tracking_id'] ) ) {
			if ( preg_match( '/^UA\-\d+?\-\d+$/', $data['ga_tracking_id'] ) )
				$socials['ga_tracking_id'] = $data['ga_tracking_id'];
		}
		if ( isset( $data['google_plus_id'] ) ) {
			if ( preg_match( '/^\d+$/', $data['google_plus_id'] ) )
				$socials['google_plus_id'] = $data['google_plus_id'];
		}
		if ( isset( $data['google_site_verification'] ) ) {
			$socials['google_site_verification'] = $data['google_site_verification'];
		}
		if ( !empty( $socials ) )
			return $socials;
	}

	/**
	 * settings_page
	 */
	public function settings_page() {
		$socials_default = array(
			'facebook_app_id' => '',
			'ga_tracking_id' => '',
			'google_plus_id' => '',
			'google_site_verification' => '',
		);
		$socials = $this->get_option();
		if ( $socials && is_array( $socials ) ) {
			$socials = array_merge( $socials_default, $socials );
		} else {
			$socials = $socials_default;
		}
		?>
		<tr>
			<th><?php _e( 'Social Account', MWHACKS_Config::DOMAIN ); ?></th>
			<td>
				<div id="<?php echo $this->name; ?>">
					<table border="0" cellpadding="0" cellspacing="0" class="data">
						<tr>
							<th style="width:25%">Facebook AppID</th>
							<td><input type="text" name="<?php echo $this->name; ?>[facebook_app_id]" value="<?php echo esc_attr( $socials['facebook_app_id'] ); ?>" size="50"></td>
						</tr>
						<tr>
							<th>GA Tracking ID</th>
							<td><input type="text" name="<?php echo $this->name; ?>[ga_tracking_id]" value="<?php echo esc_attr( $socials['ga_tracking_id'] ); ?>" size="50"></td>
						</tr>
						<tr>
							<th>Google+ ID</th>
							<td><input type="text" name="<?php echo $this->name; ?>[google_plus_id]" value="<?php echo esc_attr( $socials['google_plus_id'] ); ?>" size="50"></td>
						</tr>
						<tr>
							<th>Google Site Verification</th>
							<td><input type="text" name="<?php echo $this->name; ?>[google_site_verification]" value="<?php echo esc_attr( $socials['google_site_verification'] ); ?>" size="50"></td>
						</tr>
					</table>
				<!-- end #<?php echo $this->name; ?> --></div>
			</td>
		</tr>
		<?php
	}

	/**
	 * facebook_root
	 */
	public function facebook_root() {
		$option = $this->get_option();
		$facebook_app_id = '';
		if ( !empty( $option['facebook_app_id'] ) ) {
			$facebook_app_id = $option['facebook_app_id'];
		}
		if ( $facebook_app_id ) {
			?>
			<div id="fb-root"></div>
			<script type="text/javascript">
			window.fbAsyncInit = function() {
				FB.init({
					appId	: <?php echo esc_html( $facebook_app_id ); ?>, // App ID
					status	: true, // check login status
					cookie	: true, // enable cookies to allow the server to access the session
					xfbml	: true  // parse XFBML
				});
			};
			</script>
			<?php
		}
	}

	/**
	 * add_profile_for_google_plus
	 * Google+ 用の link タグを出力
	 */
	public function add_profile_for_google_plus() {
		global $post;
		$option = $this->get_option();
		if ( empty( $post->post_author ) )
			return;
		$user_meta = get_user_meta( $post->post_author, $this->name_google_plus_id, true );
		if ( !empty( $option['google_plus_id'] ) && !is_singular() ) {
			?>
			<link rel="publisher" href="https://plus.google.com/<?php echo esc_attr( $option['google_plus_id'] ); ?>/" />
			<?php
		} elseif ( isset( $post->post_author ) && $user_meta != '' && is_singular() ) {
			?>
			<link rel="author" href="https://plus.google.com/<?php echo esc_attr( $user_meta ); ?>/" />
			<?php
		}
	}

	/**
	 * add_google_site_verification
	 * Googleウェブマスターツール サイトの確認用のタグを出力
	 */
	public function add_google_site_verification() {
		$option = $this->get_option();
		if ( !empty( $option['google_site_verification'] ) && is_front_page() ) {
			?>
			<meta name="google-site-verification" content="<?php echo esc_attr( $option['google_site_verification'] ); ?>" />
			<?php
		}
	}

	/**
	 * add_custom_contactmethods
	 * プロフィールにGoogle+ IDを追加
	 */
	public function add_custom_contactmethods( $user_contactmethods ) {
		return array(
			$this->name_google_plus_id => 'Google+ ID'
		);
	}

	/**
	 * profile_update
	 */
	public function profile_update( $user_id, $old_user_data ) {
		if ( isset( $_POST[$this->name_google_plus_id] ) && preg_match( '/^\d+$/', $_POST[$this->name_google_plus_id] ) ) {
			update_user_meta( $user_id, $this->name_google_plus_id, $_POST[$this->name_google_plus_id] );
		} else {
			update_user_meta( $user_id, $this->name_google_plus_id, '' );
		}
	}
}
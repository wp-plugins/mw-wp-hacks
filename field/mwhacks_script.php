<?php
/**
 * Name: MW Hacks Script
 * URI: http://2inc.org
 * Description: script
 * Version: 1.1.0
 * Author: Takashi Kitajima
 * Author URI: http://2inc.org
 * Created : December 15, 2013
 * Modified: April 4, 2014
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
class mwhacks_script extends abstract_mwhacks_base {
	protected $base_name = 'script';

	/**
	 * activation
	 */
	public function activation() {
	}

	/**
	 * init
	 */
	public function init() {
		add_filter( 'wp_footer', array( $this, 'social_button_footer' ) );
	}

	/**
	 * sanitize
	 * @param mixed $data
	 */
	public function sanitize( $data ) {
		return $data;
	}

	/**
	 * settings_page
	 */
	public function settings_page() {
		$scripts = $this->get_option();
		if ( !$scripts )
			$scripts = array();
		?>
		<tr>
			<th><?php _e( 'Include Social Script', MWHACKS_Config::DOMAIN ); ?></th>
			<td>
				<div id="<?php echo $this->name; ?>">
					<p>
						<?php
						$services = array(
							'Facebook' => 'facebook',
							'Twitter' => 'twitter',
							'Hatena Bookmark' => 'hatena',
							'Google+1' => 'google',
						);
						?>
						<?php foreach ( $services as $key => $service ) : ?>
						<input type="checkbox" name="<?php echo $this->name; ?>[<?php echo esc_attr( $service ); ?>]" value="<?php echo esc_attr( $service ); ?>" <?php checked( in_array( $service, $scripts ) ); ?> /> <?php echo esc_html( $key ); ?><br />
						<?php endforeach; ?>
					</p>
				<!-- end #<?php echo $this->name; ?> --></div>
			</td>
		</tr>
		<?php
	}

	/**
	 * ソーシャル等のスクリプトの非同期読込
	 */
	public function social_button_footer() {
		$script = $this->get_option();
		$social = get_option( MWHACKS_Config::NAME . '-social' );

		$ga_tracking_id = '';
		if ( !empty( $social['ga_tracking_id'] ) ) {
			$ga_tracking_id = $social['ga_tracking_id'];
		}
		$ua_tracking_id = '';
		if ( !empty( $social['ua_tracking_id'] ) ) {
			$ua_tracking_id = $social['ua_tracking_id'];
		}
		$scripts = array();
		if ( !empty( $script ) && is_array( $script ) ) {
			$scripts = $script;
		}
		?>
		<?php if ( $scripts || $ga_tracking_id || $ua_tracking_id ) : ?>
		<script type="text/javascript">
		( function( doc, script ) {
			var js;
			var fjs = doc.getElementsByTagName( script )[0];
			var add = function( url, id, o ) {
				if ( doc.getElementById( id ) ) { return; }
				js = doc.createElement( script );
				js.src = url; js.async = true; js.id = id;
				fjs.parentNode.insertBefore( js, fjs );
				if ( window.ActiveXObject && o != null ) {
					js.onreadystatechange = function() {
						if ( js.readyState == 'complete' ) o();
						if ( js.readyState == 'loaded' ) o();
					};
				} else {
					js.onload = o;
				}
			};
			<?php if ( $ga_tracking_id ) : ?>
			add( ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js', 'google-analytics', function() {
				window._gaq = _gaq || [];
				_gaq.push(['_setAccount', '<?php echo $ga_tracking_id; ?>']);
				_gaq.push(['_trackPageview']);
			} );
			<?php endif; ?>
			<?php if ( $ua_tracking_id ) : ?>
			add( '//www.google-analytics.com/analytics.js', 'ga', function() {
				ga('create', '<?php echo $ua_tracking_id; ?>', 'auto');
				ga('send', 'pageview');
			} );
			<?php endif; ?>
			<?php if ( !empty( $scripts['facebook'] ) && $scripts['facebook'] == 'facebook' ) : ?>
			add( '//connect.facebook.net/ja_JP/all.js', 'facebook-jssdk' );
			<?php endif; ?>
			<?php if ( !empty( $scripts['twitter'] ) && $scripts['twitter'] == 'twitter' ) : ?>
			add( '//platform.twitter.com/widgets.js', 'twitter-wjs' );
			<?php endif; ?>
			<?php if ( !empty( $scripts['hatena'] ) && $scripts['hatena'] == 'hatena' ) : ?>
			add( 'http://b.st-hatena.com/js/bookmark_button.js', 'hatena-js' );
			<?php endif; ?>
			<?php if ( !empty( $scripts['google'] ) && $scripts['google'] == 'google' ) : ?>
			window.___gcfg = { lang: "ja" };
			add( 'https://apis.google.com/js/plusone.js' );
			<?php endif; ?>
		}( document, 'script' ) );
		</script>
		<?php endif; ?>
	<?php
	}
}
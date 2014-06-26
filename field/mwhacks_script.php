<?php
/**
 * Name: MW Hacks Script
 * URI: http://2inc.org
 * Description: script
 * Version: 1.2.0
 * Author: Takashi Kitajima
 * Author URI: http://2inc.org
 * Created : December 15, 2013
 * Modified: June 24, 2014
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
		$scripts_default = array(
			'facebook_locale' => 'ja_JP',
			'google_locale' => 'ja',
		);
		$scripts = $this->get_option();
		if ( $scripts && is_array( $scripts ) ) {
			$scripts = array_merge( $scripts_default, $scripts );
		} else {
			$scripts = $scripts_default;
		}
		?>
		<tr>
			<th><?php _e( 'Include Social Script', MWHACKS_Config::DOMAIN ); ?></th>
			<td>
				<div id="<?php echo $this->name; ?>">
					<p>
						<?php
						$google_locales = array(
							'Afrikaans' => 'af',
							'Amharic' => 'am',
							'Arabic' => 'ar',
							'Basque' => 'eu',
							'Bengali' => 'bn',
							'Bulgarian' => 'bg',
							'Catalan' => 'ca',
							'Traditional Chinese (Hong Kong)' => 'zh-HK',
							'Simplified Chinese (China)' => 'zh-CN',
							'Traditional Chinese (Taiwan)' => 'zh-TW',
							'Croatian' => 'hr',
							'Czech' => 'cs',
							'Danish' => 'da',
							'Dutch' => 'nl',
							'English (UK)' => 'en-GB',
							'English (US)' => 'en-US',
							'Estonian' => 'et',
							'Filipino' => 'fil',
							'Finnish' => 'fi',
							'French (France)' => 'fr',
							'French (Canada)' => 'fr-CA',
							'Galician' => 'gl',
							'German' => 'de',
							'Greek' => 'el',
							'Gujarati' => 'gu',
							'Hebrew' => 'iw',
							'Hindi' => 'hi',
							'Hungarian' => 'hu',
							'Icelandic' => 'is',
							'Indonesian' => 'id',
							'Italian' => 'it',
							'Japanese' => 'ja',
							'Kannada' => 'kn',
							'Korean' => 'ko',
							'Latvian' => 'lv',
							'Lithuanian' => 'lt',
							'Malay' => 'ms',
							'Malayalam' => 'ml',
							'Marathi' => 'mr',
							'Norwegian' => 'no',
							'Persian' => 'fa',
							'Polish' => 'pl',
							'Portuguese (Brazil)' => 'pt-BR',
							'Portuguese (Portugal)' => 'pt-PT',
							'Romanian' => 'ro',
							'Russian' => 'ru',
							'Serbian' => 'sr',
							'Slovak' => 'sk',
							'Slovenian' => 'sl',
							'Spanish' => 'es',
							'Spanish (Latin America)' => 'es-419',
							'Swahili' => 'sw',
							'Swedish' => 'sv',
							'Tamil' => 'ta',
							'Telugu' => 'te',
							'Thai' => 'th',
							'Turkish' => 'tr',
							'Ukrainian' => 'uk',
							'Urdu' => 'ur',
							'Vietnamese' => 'vi',
							'Zulu' => 'zu',
						);

						$facebook_locales = array(
							'Afrikaans' => 'af_ZA',
							'Arabic' => 'ar_AR',
							'Azeri' => 'az_AZ',
							'Belarusian' => 'be_BY',
							'Bulgarian' => 'bg_BG',
							'Bengali' => 'bn_IN',
							'Bosnian' => 'bs_BA',
							'Catalan' => 'ca_ES',
							'Czech' => 'cs_CZ',
							'Welsh' => 'cy_GB',
							'Danish' => 'da_DK',
							'German' => 'de_DE',
							'Greek' => 'el_GR',
							'English (UK)' => 'en_GB',
							'English (Pirate)' => 'en_PI',
							'English (Upside Down)' => 'en_UD',
							'English (US)' => 'en_US',
							'Esperanto' => 'eo_EO',
							'Spanish (Spain)' => 'es_ES',
							'Spanish' => 'es_LA',
							'Estonian' => 'et_EE',
							'Basque' => 'eu_ES',
							'Persian' => 'fa_IR',
							'Leet Speak' => 'fb_LT',
							'Finnish' => 'fi_FI',
							'Faroese' => 'fo_FO',
							'French (Canada)' => 'fr_CA',
							'French (France)' => 'fr_FR',
							'Frisian' => 'fy_NL',
							'Irish' => 'ga_IE',
							'Galician' => 'gl_ES',
							'Hebrew' => 'he_IL',
							'Hindi' => 'hi_IN',
							'Croatian' => 'hr_HR',
							'Hungarian' => 'hu_HU',
							'Armenian' => 'hy_AM',
							'Indonesian' => 'id_ID',
							'Icelandic' => 'is_IS',
							'Italian' => 'it_IT',
							'Japanese' => 'ja_JP',
							'Georgian' => 'ka_GE',
							'Khmer' => 'km_KH',
							'Korean' => 'ko_KR',
							'Kurdish' => 'ku_TR',
							'Latin' => 'la_VA',
							'Lithuanian' => 'lt_LT',
							'Latvian' => 'lv_LV',
							'Macedonian' => 'mk_MK',
							'Malayalam' => 'ml_IN',
							'Malay' => 'ms_MY',
							'Norwegian (bokmal)' => 'nb_NO',
							'Nepali' => 'ne_NP',
							'Dutch' => 'nl_NL',
							'Norwegian (nynorsk)' => 'nn_NO',
							'Punjabi' => 'pa_IN',
							'Polish' => 'pl_PL',
							'Pashto' => 'ps_AF',
							'Portuguese (Brazil)' => 'pt_BR',
							'Portuguese (Portugal)' => 'pt_PT',
							'Romanian' => 'ro_RO',
							'Russian' => 'ru_RU',
							'Slovak' => 'sk_SK',
							'Slovenian' => 'sl_SI',
							'Albanian' => 'sq_AL',
							'Serbian' => 'sr_RS',
							'Swedish' => 'sv_SE',
							'Swahili' => 'sw_KE',
							'Tamil' => 'ta_IN',
							'Telugu' => 'te_IN',
							'Thai' => 'th_TH',
							'Filipino' => 'tl_PH',
							'Turkish' => 'tr_TR',
							'Ukrainian' => 'uk_UA',
							'Vietnamese' => 'vi_VN',
							'Simplified Chinese (China)' => 'zh_CN',
							'Traditional Chinese (Hong Kong)' => 'zh_HK',
							'Traditional Chinese (Taiwan)' => 'zh_TW'
						);

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

					<table border="0" cellpadding="0" cellspacing="0" class="data">
						<tr>
							<th style="width:25%">Facebook locale</th>
							<td>
								<select name="<?php echo $this->name; ?>[facebook_locale]">
									<?php foreach ( $facebook_locales as $facebook_locale_name => $facebook_locale ) : ?>
									<option value="<?php echo esc_attr( $facebook_locale ); ?>"<?php selected( $scripts['facebook_locale'], $facebook_locale ); ?>><?php echo esc_html( $facebook_locale_name ); ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
						<tr>
							<th>Google locale</th>
							<td>
								<select name="<?php echo $this->name; ?>[google_locale]">
									<?php foreach ( $google_locales as $google_locale_name => $google_locale ) : ?>
									<option value="<?php echo esc_attr( $google_locale ); ?>"<?php selected( $scripts['google_locale'], $google_locale ); ?>><?php echo esc_html( $google_locale_name ); ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
					</table>
				<!-- end #<?php echo $this->name; ?> --></div>
			</td>
		</tr>
		<?php
	}

	/**
	 * ソーシャル等のスクリプトの非同期読込
	 */
	public function social_button_footer() {
		$script_default = array(
			'facebook_locale' => 'ja_JP',
			'google_locale' => 'ja',
		);
		$script = $this->get_option();
		if ( $script && is_array( $script ) ) {
			$script = array_merge( $script_default, $script );
		} else {
			$script = $script_default;
		}
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
			add( '//connect.facebook.net/<?php echo esc_html( $scripts["facebook_locale"] ); ?>/all.js', 'facebook-jssdk' );
			<?php endif; ?>
			<?php if ( !empty( $scripts['twitter'] ) && $scripts['twitter'] == 'twitter' ) : ?>
			add( '//platform.twitter.com/widgets.js', 'twitter-wjs' );
			<?php endif; ?>
			<?php if ( !empty( $scripts['hatena'] ) && $scripts['hatena'] == 'hatena' ) : ?>
			add( 'http://b.st-hatena.com/js/bookmark_button.js', 'hatena-js' );
			<?php endif; ?>
			<?php if ( !empty( $scripts['google'] ) && $scripts['google'] == 'google' ) : ?>
			window.___gcfg = { lang: "<?php echo esc_html( $scripts['google_locale'] ); ?>" };
			add( 'https://apis.google.com/js/plusone.js' );
			<?php endif; ?>
		}( document, 'script' ) );
		</script>
		<?php endif; ?>
	<?php
	}
}
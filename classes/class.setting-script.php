<?php
/**
 * Name       : MW WP Hacks Setting Script
 * Description: 管理画面
 * Version    : 1.0.0
 * Author     : Takashi Kitajima
 * Author URI : http://2inc.org
 * Create     : November 13, 2014
 * Modified   :
 * License    : GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
class MW_WP_Hacks_Setting_Script extends MW_WP_Hacks_Abstract_Setting {

	/**
	 * $google_locales
	 */
	private $google_locales = array(
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

	/**
	 * $facebook_locales
	 */
	private $facebook_locales = array(
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

	/**
	 * $services
	 */
	private $services = array(
		'facebook' => 'Facebook',
		'twitter'  => 'Twitter',
		'hatena'   => 'Hatena Bookmark',
		'google'   => 'Google+1',
	);

	/**
	 * set_key
	 */
	protected function set_key() {
		$this->key = 'script';
	}

	/**
	 * set_defaults
	 */
	protected function set_defaults() {
		$this->defaults = array(
			'facebook_locale' => 'ja_JP',
			'google_locale'   => 'ja',
		);
	}

	/**
	 * settings_page
	 */
	public function settings_page() {
		$option = $this->get_option();
		?>
		<tr>
			<th><?php _e( 'Include Social Script', 'mw-wp-hacks' ); ?></th>
			<td>
				<div id="<?php echo esc_attr( $this->get_name() ); ?>">
					<p>
						<?php foreach ( $this->services as $key => $service ) : ?>
						<input type="checkbox" name="<?php echo esc_attr( $this->get_name() ); ?>[<?php echo esc_attr( $key ); ?>]" value="true" <?php checked( 'true', $option[$key] ); ?> /> <?php echo esc_html( $service ); ?><br />
						<?php endforeach; ?>
					</p>

					<table border="0" cellpadding="0" cellspacing="0" class="data">
						<tr>
							<th style="width:25%">Facebook locale</th>
							<td>
								<select name="<?php echo esc_attr( $this->get_name() ); ?>[facebook_locale]">
									<?php foreach ( $this->facebook_locales as $facebook_locale_name => $facebook_locale ) : ?>
									<option value="<?php echo esc_attr( $facebook_locale ); ?>"<?php selected( $option['facebook_locale'], $facebook_locale ); ?>><?php echo esc_html( $facebook_locale_name ); ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
						<tr>
							<th>Google locale</th>
							<td>
								<select name="<?php echo esc_attr( $this->get_name() ); ?>[google_locale]">
									<?php foreach ( $this->google_locales as $google_locale_name => $google_locale ) : ?>
									<option value="<?php echo esc_attr( $google_locale ); ?>"<?php selected( $option['google_locale'], $google_locale ); ?>><?php echo esc_html( $google_locale_name ); ?></option>
									<?php endforeach; ?>
								</select>
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
			$values = $this->defaults;
		}

		$is_valid_facebook_locale = false;
		foreach ( $this->facebook_locales as $facebook_locale_name => $facebook_locale ) {
			if ( $values['facebook_locale'] === $facebook_locale ) {
				$is_valid_facebook_locale = true;
				break;
			}
		}
		if ( $is_valid_facebook_locale !== true ) {
			$values['facebook_locale'] = $defaults['facebook_locale'];
		}

		$is_valid_google_locale = false;
		foreach ( $this->google_locales as $google_locale_name => $google_locale ) {
			if ( $values['google_locale'] === $google_locale ) {
				$is_valid_google_locale = true;
				break;
			}
		}
		if ( $is_valid_google_locale !== true ) {
			$values['google_locale'] = $defaults['google_locale'];
		}

		foreach ( $this->services as $service => $service_name ) {
			if ( !isset( $values[$service] ) ) {
				$values[$service] = 'false';
			}
		}
		return $values;
	}

	/**
	 * get_option
	 * Ver 1.0.0 未満の保存形式に対応
	 */
	public function get_option() {
		$option = parent::get_option();
		foreach ( $this->services as $key => $service ) {
			if ( isset( $option[$key] ) ) {
				if ( $option[$key] === $key ) {
					$option[$key] = 'true';
				}
			} else {
				$option[$key] = 'false';
			}
		}
		return $option;
	}
}
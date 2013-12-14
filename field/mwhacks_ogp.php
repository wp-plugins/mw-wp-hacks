<?php
/**
 * Name: MW Hacks OGP
 * URI: http://2inc.org
 * Description: OGP
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
class mwhacks_ogp extends abstract_mwhacks_base {
	protected $base_name = 'ogp';

	/**
	 * activation
	 */
	public function activation() {
		add_option( $this->name, array( 'post' ) );
	}

	/**
	 * uninstall
	 */
	public function uninstall() {
		parent::uninstall();
		delete_post_meta_by_key( MWHACKS_Config::NAME . '-ogp' );
	}

	/**
	 * init
	 */
	public function init() {
		add_action( 'wp_head', array( $this, 'display_ogp_tags' ) );
		add_action( 'admin_head', array( $this, 'add_ogp_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_ogp' ) );
		add_action( 'admin_print_styles', array( $this, 'admin_ogp_style' ) );
		add_action( 'admin_print_scripts', array( $this, 'admin_ogp_scripts' ) );
		add_image_size( MWHACKS_Config::NAME . '_ogp_image', 1200, 627, true );
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
		$ogp_default = array(
			'type' => 'blog',
			'image' => '',
			'locale' => 'ja_JP',
		);
		$ogp = $this->get_option();
		if ( $ogp && is_array( $ogp ) ) {
			$ogp = array_merge( $ogp_default, $ogp );
		} else {
			$ogp = $ogp_default;
		}
		?>
		<tr>
			<th><?php _e( 'OGP', MWHACKS_Config::DOMAIN ); ?></th>
			<td>
				<div id="<?php echo $this->name; ?>">
					<?php
					$types = array(
						'Activities' => array(
							'activity',
							'sport'
						),
						'Businesses' => array(
							'bar',
							'company',
							'cafe',
							'hotel',
							'restaurant'
						),
						'Groups' => array(
							'cause',
							'sports_league',
							'sports_team'
						),
						'Organizations' => array(
							'band',
							'government',
							'non_profit',
							'school',
							'university'
						),
						'People' => array(
							'actor',
							'athlete',
							'author',
							'director',
							'musician',
							'politician',
							'public_figure'
						),
						'Places' => array(
							'city',
							'country',
							'landmark',
							'state_province'
						),
						'Products and Entertainment' => array(
							'album',
							'book',
							'drink',
							'food',
							'game',
							'product',
							'song',
							'movie',
							'tv_show'
						),
						'Websites' => array(
							'blog',
							'website',
							'article'
						),
					);
					$locales = array(
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
					?>
					<table border="0" cellpadding="0" cellspacing="0" class="data">
						<tr>
							<th style="width:25%">og:type( Front Page )</th>
							<td>
								<select name="<?php echo $this->name; ?>[type]">
									<?php foreach ( $types as $optgroupLbl => $optgroup ) : ?>
									<optgroup label="<?php echo esc_attr( $optgroupLbl ); ?>">
										<?php foreach ( $optgroup as $type ) : ?>
										<option value="<?php echo esc_attr( $type ); ?>"<?php selected( $ogp['type'], $type ); ?>><?php echo esc_html( $type ); ?></option>
										<?php endforeach; ?>
									</optgroup>
									<?php endforeach; ?>
								</select>
						</tr>
						<tr>
							<th>og:image</th>
							<td><?php echo home_url(); ?><input type="text" name="<?php echo $this->name; ?>[image]" value="<?php echo esc_attr( $ogp['image'] ); ?>" size="30" /></td>
						</tr>
						<tr>
							<th>og:locale</th>
							<td>
								<select name="<?php echo $this->name; ?>[locale]">
									<?php foreach ( $locales as $localeLbl => $locale ) : ?>
									<option value="<?php echo esc_attr( $locale ); ?>"<?php selected( $ogp['locale'], $locale ); ?>><?php echo esc_html( $localeLbl ); ?></option>
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
	 * display_ogp_tags
	 */
	public function display_ogp_tags() {
		$ogp = $this->get_option();
		$social = get_option( MWHACKS_Config::NAME . '-social' );

		if ( empty( $social['facebook_app_id'] ) || empty( $ogp ) ) {
			return;
		}
		$image = '';
		$facebook_app_id = $social['facebook_app_id'];
		if ( !empty( $ogp['image'] ) ) {
			$image = home_url() . $ogp['image'];
		}
		if ( is_singular() && !is_front_page() ) {
			$type = 'article';
			$title = get_the_title();
			$url = get_permalink();
			if ( $_image = $this->catch_that_image() )
				$image = $_image;
		}
		elseif ( is_tax() || is_category() || is_tag() ) {
			$term_obj = get_queried_object();
			$type = 'article';
			$title = $term_obj->name;
			$url = get_term_link( $term_obj, $term_obj->taxonomy );
		}
		elseif ( is_author() ) {
			$author_obj = get_queried_object();
			$title = $author_obj->display_name;
			$type = 'author';
			$url = get_author_posts_url( $author_obj->ID );
		}
		elseif ( is_post_type_archive() ) {
			$post_type_obj = get_queried_object();
			$title = $post_type_obj->labels->name;
			$type = 'article';
			$url = get_post_type_archive_link( $post_type_obj->name );
		}
		else {
			$title = get_bloginfo( 'name' );
			$type = ( empty( $ogp['type'] ) ) ? 'blog' : $ogp['type'];
			if ( is_singular() && is_front_page() ) {
				$url = get_permalink();
			} else {
				$url = home_url();
			}
		}
		$parse_url = parse_url( $url );
		if ( count( $_GET ) ) {
			$get = $_GET;
			$query = array();
			if ( isset( $parse_url['query'] ) ) {
				parse_str( $parse_url['query'], $query );
				foreach ( $get as $key => $value ) {
					if ( array_key_exists( $key, $query ) ) {
						unset( $get[$key] );
					}
				}
			}
			$url .= '?' . http_build_query( $get, null, '&' );
		}

		echo sprintf( '
			<meta property="fb:app_id" content="%s" />
			<meta property="og:type" content="%s" />
			<meta property="og:site_name" content="%s" />
			<meta property="og:image" content="%s" />
			<meta property="og:title" content="%s" />
			<meta property="og:url" content="%s" />
			<meta property="og:description" content="%s" />
			<meta property="og:locale" content="%s" />
			',
			esc_attr( apply_filters( MWHACKS_Config::NAME . '-ogp-app_id', $facebook_app_id ) ),
			esc_attr( apply_filters( MWHACKS_Config::NAME . '-ogp-type', $type ) ),
			esc_attr( apply_filters( MWHACKS_Config::NAME . '-ogp-site_name', get_bloginfo( 'name' ) ) ),
			esc_attr( apply_filters( MWHACKS_Config::NAME . '-ogp-image', $image ) ),
			esc_attr( apply_filters( MWHACKS_Config::NAME . '-ogp-title', $title ) ),
			esc_url( apply_filters( 'mw_ogp_url', $url ) ),
			esc_attr( apply_filters( MWHACKS_Config::NAME . '-ogp-description', mw_wp_hacks::get_description() ) ),
			esc_attr( apply_filters( MWHACKS_Config::NAME . '-ogp-locale', strtolower( $ogp['locale'] ) ) )
		);
	}

	/**
	 * add_ogp_meta_box
	 */
	public function add_ogp_meta_box() {
		global $post;
		if ( !is_admin() )
			return;
		$post_types = get_post_types();
		foreach ( $post_types as $post_type ) {
			if ( current_theme_supports( 'post-thumbnails' ) && post_type_supports( $post_type, 'thumbnail' ) ) {
				add_meta_box(
					MWHACKS_Config::NAME . '_add_ogp_image_metabox',
					__( 'OGP Image', MWHACKS_Config::DOMAIN ),
					array( $this, 'add_ogp_image' ),
					$post_type,
					'side',
					'low'
				);
			}
		}
	}

	/**
	 * add_ogp_image
	 * og:image 用の画像をアップロード
	 */
	public function add_ogp_image() {
		global $post;
		$post_meta = get_post_meta( $post->ID, MWHACKS_Config::NAME . '-ogp', true );
		$ogp_image_id = '';
		if ( !empty( $post_meta['ogp_image_id'] ) ) {
			$ogp_image_id = $post_meta['ogp_image_id'];
		}
		$add_button_class = 'mwogp-image-hide';
		$delete_button_class = 'mwogp-image-hide';
		if ( !empty( $post_meta['ogp_image_id'] ) ) {
			$delete_button_class = 'mwogp-image-show';
		} else {
			$add_button_class = 'mwogp-image-show';
		}
		?>
		<a id="mwogp-media" href="javascript:void( 0 )" class="<?php echo esc_attr( $add_button_class ); ?>"><?php _e( 'Set OGP Image', MWHACKS_Config::DOMAIN ); ?></a>
		<div id="mwogp-images">
			<?php
			if ( !empty( $ogp_image_id ) ) {
				$ogp_image = wp_get_attachment_image( $ogp_image_id, MWHACKS_Config::NAME . '_ogp_image' );
				echo $ogp_image;
			}
			?>
		</div>
		<a id="mwogp-delete" href="javascript:void( 0 )" class="<?php echo esc_attr( $delete_button_class ); ?>">
			<?php _e( 'Delete OGP Image', MWHACKS_Config::DOMAIN ); ?>
		</a>
		<input type="hidden" id="mwogp-hidden" name="<?php echo MWHACKS_Config::NAME; ?>-ogp[ogp_image_id]" value="<?php echo esc_attr( $ogp_image_id ); ?>" />
		<p class="howto">
			<?php _e( 'Recommended image size of 1200 x 627', MWHACKS_Config::DOMAIN ); ?>
		</p>
		<?php
	}

	/**
	 * admin_ogp_style
	 */
	public function admin_ogp_style() {
		$url = plugin_dir_url( __FILE__ );
		wp_register_style( MWHACKS_Config::NAME . '-ogp-admin', $url . '../css/admin_ogp.css' );
		wp_enqueue_style( MWHACKS_Config::NAME . '-ogp-admin' );
	}

	/**
	 * admin_ogp_scripts
	 */
	public function admin_ogp_scripts() {
		wp_enqueue_media();
		wp_enqueue_script(
			MWHACKS_Config::NAME . '-ogp-admin',
			plugins_url( '../js/media-uploader.js', __FILE__ ),
			array( 'jquery' ),
			false,
			true
		);
		wp_localize_script( MWHACKS_Config::NAME . '-ogp-admin', 'mwogp', array(
			'title' => __( 'Set OGP Image', MWHACKS_Config::DOMAIN ),
		) );
	}

	/**
	 * save_ogp
	 * @param numeric $post_ID
	 */
	public function save_ogp( $post_ID ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_ID;
		if ( !current_user_can( 'edit_posts' ) )
			return $post_ID;
		if ( !isset( $_POST[MWHACKS_Config::NAME . '-ogp'] ) )
			return $post_ID;

		$accepts = array(
			'ogp_image_id',
		);
		$data = array();
		foreach ( $accepts as $accept ) {
			if ( isset( $_POST[MWHACKS_Config::NAME . '-ogp'][$accept] ) )
				$data[$accept] = $_POST[MWHACKS_Config::NAME . '-ogp'][$accept];
		}
		$old_data = get_post_meta( $post_ID, MWHACKS_Config::NAME . '-ogp', true );
		update_post_meta( $post_ID, MWHACKS_Config::NAME . '-ogp', $data, $old_data );
	}

	/**
	 * catch_that_image
	 * ogp_image > thumbnail > first image
	 */
	public function catch_that_image() {
		global $post;
		$first_img = '';
		$_image_id = get_post_meta( $post->ID, MWHACKS_Config::NAME . '-ogp', true );
		if ( !empty( $_image_id['ogp_image_id'] ) ) {
			$image_id = $_image_id['ogp_image_id'];
		} elseif ( function_exists( 'get_post_thumbnail_id' ) ) {
			$image_id = get_post_thumbnail_id();
		}
		if ( !empty( $image_id ) )
			$image_url = wp_get_attachment_image_src( $image_id, MWHACKS_Config::NAME . '_ogp_image', false );

		if ( !empty( $image_url[0] ) ) {
			$first_img = $image_url[0];
		} else {
			if ( preg_match( '/<img.+?src=[\'"]([^\'"]+?)[\'"].*?>/msi', $post->post_content, $matches ) )
				$first_img = do_shortcode( $matches[1] );
		}
		if ( !empty( $first_img ) && preg_match( '/^\/.+$/', $first_img ) )
			$first_img = home_url() . $first_img;
		return $first_img;
	}
}
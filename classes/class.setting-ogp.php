<?php
/**
 * Name       : MW WP Hacks Setting OGP
 * Description: 管理画面
 * Version    : 1.2.0
 * Author     : Takashi Kitajima
 * Author URI : http://2inc.org
 * Create     : November 13, 2014
 * Modified   : February 18, 2015
 * License    : GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
class MW_WP_Hacks_Setting_Ogp extends MW_WP_Hacks_Abstract_Setting {

	/**
	 * $types
	 */
	private $types = array(
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

	/**
	 * $card_types
	 */
	protected $card_types = array(
		'summary'             => 'Summary Card',
		'summary_large_image' => 'Summary Card with Large Images',
		'photo'               => 'Photo Card',

	);

	/**
	 * __construct()
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'admin_head', array( $this, 'add_ogp_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_ogp' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_image_size( MW_WP_Hacks_Config::NAME . '_ogp_image', 1200, 630, true );
	}

	/**
	 * set_key
	 */
	protected function set_key() {
		$this->key = 'ogp';
	}

	/**
	 * set_defaults
	 */
	protected function set_defaults() {
		$this->defaults = array(
			'update_cache' => 'true',
			'use_ogp'      => 'true',
			'type'         => 'blog',
			'image'        => '',
			'twitter_card' => 'summary',
			'twitter_site' => '',
		);
	}

	/**
	 * settings_page
	 */
	public function settings_page() {
		$option = $this->get_option();
		?>
		<tr>
			<th><?php _e( 'OGP & Twitter Cards', 'mw-wp-hacks' ); ?></th>
			<td>
				<div id="<?php echo esc_attr( $this->get_name() ); ?>">
					<p>
						<label><input type="checkbox" name="<?php echo esc_attr( $this->get_name() ); ?>[update_cache]" value="true" <?php echo checked( 'true', $option['update_cache'] ); ?> />
						<?php esc_html_e( 'To update cache in facebook when publish or update.', 'mw-wp-hacks' ); ?></label>
					</p>
					<p>
						<label><input type="checkbox" name="<?php echo esc_attr( $this->get_name() ); ?>[use_ogp]" value="true" <?php echo checked( 'true', $option['use_ogp'] ); ?> />
						<?php esc_html_e( 'To generate OGP tags and use OGP settings.', 'mw-wp-hacks' ); ?></label>
					</p>
					<table border="0" cellpadding="0" cellspacing="0" class="data">
						<tr>
							<th style="width:25%">og:type ( Front Page )</th>
							<td>
								<select name="<?php echo esc_attr( $this->get_name() ); ?>[type]">
									<?php foreach ( $this->types as $optgroup_label => $optgroup ) : ?>
									<optgroup label="<?php echo esc_attr( $optgroup_label ); ?>">
										<?php foreach ( $optgroup as $type ) : ?>
										<option value="<?php echo esc_attr( $type ); ?>"<?php selected( $option['type'], $type ); ?>><?php echo esc_html( $type ); ?></option>
										<?php endforeach; ?>
									</optgroup>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
						<tr>
							<th>og:image</th>
							<td><?php echo home_url(); ?><input type="text" name="<?php echo esc_attr( $this->get_name() ); ?>[image]" value="<?php echo esc_attr( $option['image'] ); ?>" size="30" /></td>
						</tr>
						<tr>
							<th>twitter:card</th>
							<td>
								<select name="<?php echo esc_attr( $this->get_name() ); ?>[twitter_card]">
									<?php foreach ( $this->card_types as $card_key => $card_value ) : ?>
									<option value="<?php echo esc_attr( $card_key ); ?>"<?php selected( $option['twitter_card'], $card_key ); ?>><?php echo esc_html( $card_value ); ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
						<tr>
							<th>twitter:site</th>
							<td>
								@<input type="text" name="<?php echo esc_attr( $this->get_name() ); ?>[twitter_site]" value="<?php echo esc_attr( $option['twitter_site'] ); ?>" size="20" style="width: auto" />
								<p class="description">
									<?php esc_html_e( 'If this field is not entered , meta tags for Twitter Cards is not generated.', 'mw-wp-hacks' ); ?>
								</p>
							</td>
						</tr>
					</table>
					<p class="description">
						<?php
						printf(
							'<a href="https://cards-dev.twitter.com/validator" target="_blank">%s</a>',
							esc_html__( 'Authentication to the use of Twitter Cards is required.', 'mw-wp-hacks' )
						);
						?>
					</p>
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

		if ( $values['update_cache'] !== 'true' ) {
			$values['update_cache'] = 'false';
		}

		if ( $values['use_ogp'] !== 'true' ) {
			$values['use_ogp'] = 'false';
		}

		$is_valid_type = false;
		foreach ( $this->types as $optgroup_label => $optgroup ) {
			foreach ( $optgroup as $type ) {
				if ( $values['type'] === $type ) {
					$is_valid_type = true;
					break;
				}
			}
		}
		if ( $is_valid_type !== true ) {
			$values['type'] = $defaults['type'];
		}

		if ( !isset( $values['image'] ) ) {
			$values['image'] = $defaults['image'];
		}

		$is_valid_card_type = false;
		foreach ( $this->card_types as $card_key => $card_value ) {
			if ( $values['twitter_card'] === $card_key ) {
				$is_valid_card_type = true;
				break;
			}
		}
		if ( $is_valid_card_type !== true ) {
			$values['twitter_card'] = $defaults['twitter_card'];
		}

		if ( !isset( $values['twitter_site'] ) ) {
			$values['twitter_site'] = $defaults['twitter_site'];
		}
		return $values;
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
					MW_WP_Hacks_Config::NAME . '_add_ogp_image_metabox',
					esc_html__( 'OGP Image', 'mw-wp-hacks' ),
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
		$post_meta = get_post_meta( $post->ID, MW_WP_Hacks_Config::NAME . '-ogp', true );
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
		<a id="mwogp-media" href="javascript:void( 0 )" class="<?php echo esc_attr( $add_button_class ); ?>"><?php esc_html_e( 'Set OGP Image', 'mw-wp-hacks' ); ?></a>
		<div id="mwogp-images">
			<?php
			if ( !empty( $ogp_image_id ) ) {
				$ogp_image = wp_get_attachment_image( $ogp_image_id, MW_WP_Hacks_Config::NAME . '_ogp_image' );
				echo $ogp_image;
			}
			?>
		</div>
		<a id="mwogp-delete" href="javascript:void( 0 )" class="<?php echo esc_attr( $delete_button_class ); ?>">
			<?php esc_html_e( 'Delete OGP Image', 'mw-wp-hacks' ); ?>
		</a>
		<input type="hidden" id="mwogp-hidden" name="<?php echo MW_WP_Hacks_Config::NAME; ?>-ogp[ogp_image_id]" value="<?php echo esc_attr( $ogp_image_id ); ?>" />
		<p class="howto">
			<?php esc_html_e( 'Recommended image size of 1200 x 630', 'mw-wp-hacks' ); ?>
		</p>
		<?php
	}

	/**
	 * admin_enqueue_scripts
	 */
	public function admin_enqueue_scripts() {
		$url = plugin_dir_url( __FILE__ );
		wp_register_style( MW_WP_Hacks_Config::NAME . '-ogp-admin', $url . '../css/admin_ogp.css' );
		wp_enqueue_style( MW_WP_Hacks_Config::NAME . '-ogp-admin' );
		
		wp_enqueue_media();
		wp_enqueue_script(
			MW_WP_Hacks_Config::NAME . '-ogp-admin',
			plugins_url( '../js/media-uploader.js', __FILE__ ),
			array( 'jquery' ),
			false,
			true
		);
		wp_localize_script( MW_WP_Hacks_Config::NAME . '-ogp-admin', 'mwogp', array(
			'title' => esc_html__( 'Set OGP Image', 'mw-wp-hacks' ),
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
		if ( !isset( $_POST[MW_WP_Hacks_Config::NAME . '-ogp'] ) )
			return $post_ID;

		$accepts = array(
			'ogp_image_id',
		);
		$data = array();
		foreach ( $accepts as $accept ) {
			if ( isset( $_POST[MW_WP_Hacks_Config::NAME . '-ogp'][$accept] ) )
				$data[$accept] = $_POST[MW_WP_Hacks_Config::NAME . '-ogp'][$accept];
		}
		update_post_meta( $post_ID, MW_WP_Hacks_Config::NAME . '-ogp', $data );
	}
}
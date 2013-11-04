<?php
class mw_wp_hacks_admin_page {

	const NAME = 'mw-wp-hacks';
	const DOMAIN = 'mw-wp-hacks';

	/**
	 * __construct
	 * 本体の admin_menu フックから呼ばれる
	 */
	public function __construct() {
		$hook = add_menu_page(
			'MW WP Hacks',
			'MW WP Hacks',
			'manage_options',
			basename( __FILE__ ),
			array( $this, 'settings_page' )
		);
		add_action( 'admin_print_styles-' . $hook, array( $this, 'admin_style' ) );
		add_action( 'admin_print_scripts-' . $hook, array( $this, 'admin_scripts' ) );
		add_action( 'admin_init', array( $this, 'setting' ) );
	}

	private function get_post_types() {
		$_post_types = get_post_types( array(
			'public' => true,
		) );
		$post_types = array();
		foreach ( $_post_types as $post_type ) {
			if ( $post_type === 'attachment' )
				continue;
			$post_type_object = get_post_type_object( $post_type );
			$post_types[$post_type] = $post_type_object->label;
		}
		return $post_types;
	}

	/**
	 * admin_style
	 * CSS適用
	 */
	public function admin_style() {
		$url = plugin_dir_url( __FILE__ );
		wp_register_style( self::DOMAIN . '-admin', $url . '../css/admin.css' );
		wp_enqueue_style( self::DOMAIN . '-admin' );
	}

	/**
	 * admin_scripts
	 * JavaScript適用
	 */
	public function admin_scripts() {
		$url = plugin_dir_url( __FILE__ );
		wp_register_script( self::DOMAIN . '-admin', $url . '../js/admin.js' );
		wp_enqueue_script( self::DOMAIN . '-admin' );
	}

	public function setting() {
		register_setting( self::NAME . '-group', self::NAME . '-feed',      array( $this, 'sanitize_feed' ) );
		register_setting( self::NAME . '-group', self::NAME . '-excerpt',   array( $this, 'sanitize_excerpt' ) );
		register_setting( self::NAME . '-group', self::NAME . '-social',    array( $this, 'sanitize_social' ) );
		register_setting( self::NAME . '-group', self::NAME . '-script',    array( $this, 'sanitize_script' ) );
		register_setting( self::NAME . '-group', self::NAME . '-thumbnail', array( $this, 'sanitize_thumbnail' ) );
		register_setting( self::NAME . '-group', self::NAME . '-widget',    array( $this, 'sanitize_widget' ) );
	}

	public function settings_page() {
		?>
		<div class="wrap">
			<h2>MW WP Hacks</h2>
			<form method="post" action="options.php">
				<?php
				settings_fields( self::NAME . '-group' );
				$post_types = $this->get_post_types();

				// Feed
				$feed = get_option( self::NAME . '-feed' );
				$feed = (array) $feed;

				// Excerpt
				$excerpt = get_option( self::NAME . '-excerpt' );

				// Thumbnail
				$thumbnails = get_option( self::NAME . '-thumbnail' );
				$thumbnails = (array) $thumbnails;
				// 空の隠れバリデーションフィールド（コピー元）を挿入
				$thumbnail_keys = array(
					'name' => '',
					'width' => 0,
					'height' => 0,
					'crop' => 0,
				);
				array_unshift( $thumbnails, $thumbnail_keys );

				// Widget
				$widgets = get_option( self::NAME . '-widget' );
				$widgets = (array) $widgets;
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
				array_unshift( $widgets, $widget_keys );

				// Social Account
				$socials_default = array(
					'facebook_app_id' => '',
					'ga_tracking_id' => '',
					'google_plus_id' => '',
				);
				$socials = get_option( self::NAME . '-social' );
				$socials = (array) $socials;
				if ( $socials ) {
					$socials = array_merge( $socials_default, $socials );
				}

				// Social Script
				$scripts = get_option( self::NAME . '-script' );
				$scripts = (array) $scripts;
				?>
				<table class="form-table">
					<tr>
						<th>Included Post Types in Main Feed</th>
						<td>
							<div id="mwhacks-feed">
								<p>
									<?php foreach ( $post_types as $post_type => $name ) : ?>
									<input type="checkbox" name="<?php echo self::NAME; ?>-feed[]" value="<?php echo esc_attr( $post_type ); ?>" <?php checked( in_array( $post_type, $feed ), true ); ?>> <?php echo esc_html( $name ); ?><br />
									<?php endforeach; ?>
								</p>
							<!-- end #mwhacks-feed --></div>
						</td>
					</tr>
					<tr>
						<th>Text After Excerpt ( HTML )</th>
						<td>
							<div id="mwhacks-excerpt">
								<input type="text" name="<?php echo self::NAME; ?>-excerpt" value="<?php echo esc_attr( $excerpt ); ?>" size="50">
								<p class="description">
									%link% is converted to &lt;?php echo get_permalink(); ?&gt;
								</p>
							<!-- end #mwhacks-excerpt --></div>
						</td>
					</tr>
					<tr>
						<th>Social Account</th>
						<td>
							<div id="mwhacks-social">
								<table border="0" cellpadding="0" cellspacing="0" class="data">
									<tr>
										<th style="width:25%">Facebook AppID</th>
										<td><input type="text" name="<?php echo self::NAME; ?>-social[facebook_app_id]" value="<?php echo esc_attr( $socials['facebook_app_id'] ); ?>" size="50"></td>
									</tr>
									<tr>
										<th>GA Tracking ID</th>
										<td><input type="text" name="<?php echo self::NAME; ?>-social[ga_tracking_id]" value="<?php echo esc_attr( $socials['ga_tracking_id'] ); ?>" size="50"></td>
									</tr>
									<tr>
										<th>Google+ ID</th>
										<td><input type="text" name="<?php echo self::NAME; ?>-social[google_plus_id]" value="<?php echo esc_attr( $socials['google_plus_id'] ); ?>" size="50"></td>
									</tr>
								</table>
							<!-- end #mwhacks-social --></div>
						</td>
					</tr>
					<tr>
						<th>Include Social Script</th>
						<td>
							<div id="mwhacks-script">
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
									<input type="checkbox" name="<?php echo self::NAME; ?>-script[<?php echo esc_attr( $service ); ?>]" value="<?php echo esc_attr( $service ); ?>" <?php checked( in_array( $service, $scripts ) ); ?> /> <?php echo esc_html( $key ); ?><br />
									<?php endforeach; ?>
								</p>
							<!-- end #mwhacks-script --></div>
						</td>
					</tr>
					<tr>
						<th>Custom Thumbnail Sizes</th>
						<td>
							<div id="mwhacks-thumbnail">
								<p>
									<span class="mwhacks-add button">Add</span>
								</p>
								<table border="0" cellpadding="0" cellspacing="0" class="data">
									<tr>
										<th style="width:1%">&nbsp;</th>
										<th>Thumbnail Size Name</th>
										<th style="width:20%">Width</th>
										<th style="width:20%">Height</th>
										<th style="width:20%">Crop</th>
									</tr>
									<?php foreach ( $thumbnails as $key => $value ) : ?>
									<tr class="add-box" <?php if ( $key === 0 ) : ?> style="display:none"<?php endif; ?>>
										<td><span class="mwhacks-remove">x</span></td>
										<td><input type="text" name="<?php echo self::NAME; ?>-thumbnail[<?php echo $key; ?>][name]" value="<?php echo esc_attr( $value['name'] ); ?>" size="20" /></td>
										<td><input type="text" name="<?php echo self::NAME; ?>-thumbnail[<?php echo $key; ?>][width]" value="<?php echo esc_attr( $value['width'] ); ?>" size="4" />px</td>
										<td><input type="text" name="<?php echo self::NAME; ?>-thumbnail[<?php echo $key; ?>][height]" value="<?php echo esc_attr( $value['height'] ); ?>" size="4" />px</td>
										<td>
											<select name="<?php echo self::NAME; ?>-thumbnail[<?php echo $key; ?>][crop]">
												<?php
												$options = array( 'False', 'True' );
												?>
												<?php foreach ( $options as $option_key => $option_value ) : ?>
												<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $value['crop'], $option_key ); ?>><?php echo esc_html( $option_value ); ?></option>
												<?php endforeach; ?>
											</select>
										</td>
									</tr>
									<?php endforeach; ?>
								</table>
							<!-- end #mwhacks-thumbnail --></div>
						</td>
					</tr>
					<tr>
						<th>Widget Areas</th>
						<td>
							<p>
								<span class="mwhacks-add button">Add</span>
							</p>
							<div id="mwhacks-widget">
								<?php foreach ( $widgets as $key => $value ) : ?>
								<div class="add-box"<?php if ( $key === 0 ) : ?> style="display:none"<?php endif; ?>>
									<table border="0" cellpadding="0" cellspacing="0" class="data">
										<tr>
											<td style="width:1%;text-align:center" rowspan="7"><span class="mwhacks-remove">x</span></td>
											<th style="width:15%">ID</th>
											<td><input type="text" name="<?php echo self::NAME; ?>-widget[<?php echo $key; ?>][id]" value="<?php echo esc_attr( $value['id'] ); ?>" size="30" /></td>
										</tr>
										<tr>
											<th>Name</th>
											<td><input type="text" name="<?php echo self::NAME; ?>-widget[<?php echo $key; ?>][name]" value="<?php echo esc_attr( $value['name'] ); ?>" size="30" /></td>
										</tr>
										<tr>
											<th>Description</th>
											<td><input type="text" name="<?php echo self::NAME; ?>-widget[<?php echo $key; ?>][description]" value="<?php echo esc_attr( $value['description'] ); ?>" size="30" /></td>
										</tr>
										<tr>
											<th>before_widget</th>
											<td><input type="text" name="<?php echo self::NAME; ?>-widget[<?php echo $key; ?>][before_widget]" value="<?php echo esc_attr( $value['before_widget'] ); ?>" size="30" />
												<p class="description">
													%1$s is converted to ID, %2$s is converted to Class.
												</p>
											</td>
										</tr>
										<tr>
											<th>after_widget</th>
											<td><input type="text" name="<?php echo self::NAME; ?>-widget[<?php echo $key; ?>][after_widget]" value="<?php echo esc_attr( $value['after_widget'] ); ?>" size="30" /></td>
										</tr>
										<tr>
											<th>before_title</th>
											<td><input type="text" name="<?php echo self::NAME; ?>-widget[<?php echo $key; ?>][before_title]" value="<?php echo esc_attr( $value['before_title'] ); ?>" size="30" /></td>
										</tr>
										<tr>
											<th>after_title</th>
											<td><input type="text" name="<?php echo self::NAME; ?>-widget[<?php echo $key; ?>][after_title]" value="<?php echo esc_attr( $value['after_title'] ); ?>" size="30" /></td>
										</tr>
									</table>
								<!-- end .mwhacks-widget-box --></div>
								<?php endforeach; ?>
							<!-- end #mwhacks-widget --></div>
						</td>
					</tr>
				</table>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', self::DOMAIN ) ?>" />
				</p>
			</form>
		<!-- end .wrap --></div>
		<?php
	}
	public function sanitize_feed( $data ) {
		if ( !empty( $data ) && is_array( $data ) )
			return $data;
	}
	public function sanitize_excerpt( $data ) {
		if ( !empty( $data ) && !is_array( $data ) )
			return $data;
	}
	public function sanitize_thumbnail( $data ) {
		$thumbnails = array();
		foreach ( $data as $value ) {
			if ( !isset( $value['name'], $value['width'], $value['height'], $value['crop'] ) )
				continue;
			if ( !preg_match( '/^[a-zA-z0-9_\-]+$/', $value['name'] ) )
				continue;
			if ( !preg_match( '/^\d+$/', $value['width'] ) )
				continue;
			if ( !preg_match( '/^\d+$/', $value['height'] ) )
				continue;
			if ( !( $value['crop'] === '1' || $value['crop'] === '0' ) )
				continue;
			$key = $value['name'];
			$thumbnails[$key] = $value;
		}
		if ( !empty( $thumbnails ) )
			return $thumbnails;
	}
	public function sanitize_widget( $data ) {
		$widgets = array();
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
	public function sanitize_social( $data ) {
			$socials = array();
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
		if ( !empty( $socials ) )
			return $socials;
	}
	public function sanitize_script( $data ) {
		return $data;
	}
}
<?php
/**
 * Name       : MW WP Hacks Abstract Setting
 * Description: 管理画面
 * Version    : 1.0.0
 * Author     : Takashi Kitajima
 * Author URI : http://2inc.org
 * Create     : November 13, 2014
 * Modified   :
 * License    : GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
abstract class MW_WP_Hacks_Abstract_Setting {

	/**
	 * $key
	 */
	protected $key;

	/**
	 * $defaults
	 */
	protected $defaults = array();

	/**
	 * $option
	 * 設定のキャッシュ用
	 */
	protected $option;

	/**
	 * __construct
	 */
	public function __construct() {
		$this->set_key();
		$this->set_defaults();
		add_action( 'admin_init', array( $this, 'register_setting' ) );
		add_action( MW_WP_Hacks_Config::NAME . '-settings-page', array( $this, 'settings_page' ) );
		//add_filter( MW_WP_Hacks_Config::NAME . '-validate', array( $this, 'validate' ) );
	}

	/**
	 * register_setting
	 * name属性が mw-wp-hacks の項目だけ許可。さらに $this->_valdidate でフィルタリング
	 */
	public function register_setting() {
		register_setting(
			MW_WP_Hacks_Config::NAME . '-group',
			$this->get_name(),
			array( $this, 'validate' )
		);
	}

	/**
	 * set_key
	 * name属性の mw-wp-hacks-{$this->key} の部分を設定する
	 */
	abstract protected function set_key();

	/**
	 * set_defaults
	 * 初期値の設定
	 */
	abstract protected function set_defaults();

	/**
	 * settings_page
	 */
	abstract public function settings_page();

	/**
	 * validate
	 * @param array $values
	 * @return array $valuees
	 */
	abstract public function validate( $values );

	/**
	 * get_option
	 */
	public function get_option() {
		$option = get_option( MW_WP_Hacks_Config::NAME . '-' . $this->key );
		if ( is_array( $this->defaults ) ) {
			if ( $option === '' || $option === false ) {
				$option = $this->defaults;
			} elseif ( is_array( $option ) ) {
				$option = array_merge( $this->defaults, $option );
			}
		} else {
			if ( $option === false ) {
				$option = $this->defaults;
			}
		}
		return $option;
	}

	/**
	 * get_name
	 */
	protected function get_name() {
		$name = MW_WP_Hacks_Config::NAME . '-' . $this->key;
		return $name;
	}
}
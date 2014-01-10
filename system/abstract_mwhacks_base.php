<?php
/**
 * Name: abstract_mwhacks_base
 * URI: http://2inc.org
 * Description: 管理画面
 * Version: 1.0.0
 * Author: Takashi Kitajima
 * Author URI: http://2inc.org
 * Created : December 11, 2013
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
abstract class abstract_mwhacks_base {
	protected $base_name;
	protected $name;

	/**
	 * __construct
	 */
	public function __construct() {
		if ( !$this->base_name ) {
			exit();
		}
		$this->set_name();

		register_activation_hook( __FILE__, array( get_class( $this ), 'activation' ) );
		register_uninstall_hook( __FILE__, array( get_class( $this ), 'uninstall' ) );

		add_action( 'admin_init', array( $this, 'register_setting' ) );
		add_action( 'plugins_loaded', array( $this, 'init' ) );
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
		delete_option( $this->name );
	}

	/**
	 * get_base_name
	 * @return string
	 */
	public function get_base_name() {
		return $this->base_name;
	}

	/**
	 * set_name
	 */
	protected function set_name() {
		$this->name = MWHACKS_Config::NAME . '-' . $this->base_name;
	}

	/**
	 * register_setting
	 */
	public function register_setting() {
		register_setting(
			MWHACKS_Config::NAME . '-group',
			$this->name,
			array( $this, 'sanitize' )
		);
	}

	/**
	 * init
	 */
	abstract public function init();

	/**
	 * sanitize
	 * @param mixed $data
	 */
	abstract public function sanitize( $data );

	/**
	 * settings_page
	 */
	abstract public function settings_page();

	/**
	 * get_option
	 * @return array
	 */
	public function get_option() {
		return get_option( $this->name );
	}
}
<?php
/**
 * Premium settings page (addition to main settings)
 *
 * @package momowsw
 */
class MoMo_WSW_Settings_Premium {
	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'momo_wsw_main_menu_link', array( $this, 'momo_wsw_main_menu_add_link' ) );
		add_action( 'momo_wsw_main_menu_content', array( $this, 'momo_wsw_main_menu_add_content' ) );
	}
	/**
	 * Add main menu link
	 */
	public function momo_wsw_main_menu_add_link() {
		require_once 'page-momo-wsw-settings-premium-link.php';
	}
	/**
	 * Add main menu content
	 */
	public function momo_wsw_main_menu_add_content() {
		require_once 'page-momo-wsw-settings-premium-content.php';
	}
}
new MoMo_WSW_Settings_Premium();

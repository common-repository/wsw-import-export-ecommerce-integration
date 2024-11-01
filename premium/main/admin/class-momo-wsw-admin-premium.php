<?php
/**
 * Admin Init Premium
 *
 * @package momowsw
 */
class MoMo_WSW_Admin_Premium {
	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'momowsw_print_admin_ss' ) );
	}
	/**
	 * Enqueue script and styles
	 */
	public function momowsw_print_admin_ss() {
		global $momowsw;
		wp_register_script( 'momowsw_admin_script_premium', $momowsw->plugin_url . 'premium/main/assets/js/momo_wsw_admin_premium.js', array( 'jquery', 'jquery-effects-shake' ), $momowsw->version, true );
		wp_enqueue_script( 'momowsw_admin_script_premium' );
	}
}
new MoMo_WSW_Admin_Premium();

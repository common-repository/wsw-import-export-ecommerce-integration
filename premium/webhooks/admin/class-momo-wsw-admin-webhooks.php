<?php
/**
 * Webhooks
 *
 * @package momowsw
 */
class MoMo_WSW_Admin_Webhooks {
	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'momowsw_set_admin_menu_webhooks' ) );
		add_action( 'admin_init', array( $this, 'momowsw_register_settings_webhooks' ) );

	}
	/**
	 * Register momowsw Settings
	 */
	public function momowsw_register_settings_webhooks() {
		register_setting( 'momowsw-settings-webhooks-group', 'momo_wsw_webhooks_settings' );
		register_setting( 'momowsw-ajax-webhooks-group', 'momo_wsw_webhooks_ajax' );
	}
	/**
	 * Set Admin Menu
	 */
	public function momowsw_set_admin_menu_webhooks() {
		add_submenu_page(
			'momowsw',
			esc_html__( 'Webhooks', 'momowsw' ),
			esc_html__( 'Webhooks', 'momowsw' ),
			'manage_options',
			'momowsw-webhooks-sync',
			array( $this, 'momowsw_add_webhooks_sync_settings_page' ),
			10
		);
	}
	/**
	 * Product Feed Settings Page
	 */
	public function momowsw_add_webhooks_sync_settings_page() {
		global $momowsw;
		include_once $momowsw->plugin_path . 'premium/webhooks/admin/pages/momo-wsw-webhooks-settings.php';
	}
}
new MoMo_WSW_Admin_Webhooks();

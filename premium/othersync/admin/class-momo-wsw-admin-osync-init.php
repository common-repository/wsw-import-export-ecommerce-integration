<?php
/**
 * Other Sync Init
 *
 * @package momowsw
 */
class MoMo_WSW_Admin_OSync_Init {
	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'momowsw_set_osync_admin_menu' ) );
	}
	/**
	 * Set Admin Menu
	 */
	public function momowsw_set_osync_admin_menu() {
		add_submenu_page(
			'momowsw',
			esc_html__( 'Auto Sync - WooCommerce / Shopify', 'momowsw' ),
			esc_html__( 'Automation', 'momowsw' ),
			'manage_options',
			'momowsw-autosync',
			array( $this, 'momowsw_add_auto_sync_settings_page' )
		);
	}
	/**
	 * Auto Orders Settings Page
	 */
	public function momowsw_add_auto_sync_settings_page() {
		global $momowsw;
		include_once $momowsw->plugin_path . 'premium/othersync/admin/pages/momo-wsw-auto-sync-settings.php';
	}
}
new MoMo_WSW_Admin_OSync_Init();

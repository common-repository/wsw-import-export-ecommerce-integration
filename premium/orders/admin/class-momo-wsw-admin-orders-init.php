<?php
/**
 * Admin Init
 *
 * @package momowsw
 */
class MoMo_WSW_Admin_Orders_Init {
	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'momowsw_set_orders_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'momowsw_register_orders_settings' ) );
	}
	/**
	 * Set Admin Menu
	 */
	public function momowsw_set_orders_admin_menu() {
		add_submenu_page(
			'momowsw',
			esc_html__( 'Orders - WooCommerce / Shopify', 'momowsw' ),
			esc_html__( 'Orders', 'momowsw' ),
			'manage_options',
			'momowsw-orders',
			array( $this, 'momowsw_add_orders_settings_page' )
		);
	}
	/**
	 * Register momowsw Settings
	 */
	public function momowsw_register_orders_settings() {
		register_setting( 'momowsw-settings-orders-group', 'momo_wsw_orders_settings' );
	}
	/**
	 * Auto Orders Settings Page
	 */
	public function momowsw_add_orders_settings_page() {
		global $momowsw;
		include_once $momowsw->plugin_path . 'premium/orders/admin/pages/momo-wsw-orders-settings.php';
	}
}
new MoMo_WSW_Admin_Orders_Init();

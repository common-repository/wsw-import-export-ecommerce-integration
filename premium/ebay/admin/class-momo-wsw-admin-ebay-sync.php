<?php
/**
 * Ebay Feed
 *
 * @package momowsw
 */
class MoMo_WSW_Admin_Ebay_Sync {
	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'momowsw_set_admin_menu_ebay' ) );
		add_action( 'admin_init', array( $this, 'momowsw_register_settings_ebay' ) );

		if ( is_admin() ) {
			add_filter( 'manage_edit-product_columns', array( $this, 'momowsw_add_ebay_linked_column' ), 15 );
			add_action( 'manage_product_posts_custom_column', array( $this, 'momowsw_add_ebay_linked_column_details' ), 10, 2 );
		}

	}
	/**
	 * Add ebay Linked Column.
	 *
	 * @param array $columns Default Columns.
	 */
	public function momowsw_add_ebay_linked_column( $columns ) {

		$columns['momowsw_ebay_link'] = esc_html__( 'eBay', 'momowsw' );

		return $columns;
	}
	/**
	 * Add column content, ebay icon
	 *
	 * @param string  $column Column name.
	 * @param integer $woo_product_id Product ID.
	 */
	public function momowsw_add_ebay_linked_column_details( $column, $woo_product_id ) {
		if ( 'momowsw_ebay_link' === $column ) {
			$momowsw_ebay_product_id = get_post_meta( $woo_product_id, 'momowsw_ebay_product_id', true );
			if ( $momowsw_ebay_product_id && ! empty( $momowsw_ebay_product_id ) ) {
				echo "<span class='momowsw-ebay-link'><i class='bx bxl-ebay'></i></span>";
			}
		}
	}
	/**
	 * Register momowsw Settings
	 */
	public function momowsw_register_settings_ebay() {
		register_setting( 'momowsw-settings-ebay-sync-group', 'momo_wsw_ebay_sync' );
		register_setting( 'momowsw-settings-es-export-group', 'momo_wsw_es_export' );
		register_setting( 'momowsw-settings-es-policies-group', 'momo_wsw_ebay_policies' );
	}
	/**
	 * Set Admin Menu
	 */
	public function momowsw_set_admin_menu_ebay() {
		add_submenu_page(
			'momowsw',
			esc_html__( 'eBay', 'momowsw' ),
			esc_html__( 'eBay', 'momowsw' ),
			'manage_options',
			'momowsw-ebay-sync',
			array( $this, 'momowsw_add_ebay_sync_settings_page' ),
			10
		);
	}
	/**
	 * Product Feed Settings Page
	 */
	public function momowsw_add_ebay_sync_settings_page() {
		global $momowsw;
		include_once $momowsw->plugin_path . 'premium/ebay/admin/pages/momo-wsw-ebay-sync-settings.php';
	}
	/**
	 * After saving import settings
	 *
	 * @param mixed  $old_value The old option value.
	 * @param mixed  $value     The new option value.
	 * @param string $option    Option name.
	 */
	public function momo_wsw_after_pf_product_feeds_settings( $old_value, $value, $option ) {
		global $momowsw;
		$enable_pf_feeds = isset( $value['enable_pf_feeds'] ) ? $value['enable_pf_feeds'] : 'off';
		if ( 'on' === $enable_pf_feeds ) {
			$days   = isset( $value[0]['pf_import_days'] ) ? $value[0]['pf_import_days'] : 3;
			$hour   = isset( $value[0]['pf_import_hour'] ) ? $value[0]['pf_import_hour'] : 00;
			$minute = isset( $value[0]['pf_import_minute'] ) ? $value[0]['pf_import_minute'] : 00;
			// Enable cron job.
			add_filter( 'cron_schedules', array( $momowsw->pfcron, 'momo_wsw_custom_product_feeds_schedule' ), 5 ); // phpcs:ignore WordPress.WP.CronInterval
			$momowsw->pfcron->momo_wsw_enable_product_feed_cron();
		} else {
			// Disable cron job.
			$momowsw->pfcron->momo_wsw_disable_product_feed_cron();
		}
	}
}
new MoMo_WSW_Admin_Ebay_Sync();

<?php
/**
 * Admin Init
 *
 * @package momowsw
 */
class MoMo_WSW_Admin_Product_Feed {
	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'momowsw_set_admin_menu_pf' ) );
		add_action( 'admin_init', array( $this, 'momowsw_register_settings_pf' ) );

		if ( is_admin() ) {
			add_action( 'update_option_momo_wsw_pf_product_feeds', array( $this, 'momo_wsw_after_pf_product_feeds_settings' ), 10, 3 );
			add_action( 'admin_enqueue_scripts', array( $this, 'momowsw_enqueue_admin_style_scripts' ) );
		}
		$ajax_events = array(
			'momowsw_regenerate_product_feeds' => 'momowsw_regenerate_product_feeds', // Eleven.
		);
		foreach ( $ajax_events as $ajax_event => $class ) {
			add_action( 'wp_ajax_' . $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_' . $ajax_event, array( $this, $class ) );
		}
	}
	/**
	 * Regenerate Product Feeds
	 */
	public function momowsw_regenerate_product_feeds() {
		global $momowsw;
		$res = check_ajax_referer( 'momowsw_security_key', 'security' );
		if ( isset( $_POST['action'] ) && 'momowsw_regenerate_product_feeds' !== $_POST['action'] ) {
			return;
		}
		$date = $momowsw->premium->pfcron->momo_wsw_product_feeds_schedule();
		ob_get_clean();
		echo wp_json_encode(
			array(
				'status' => 'good',
				'date'   => $momowsw->premium->cron->momo_wsw_cron_timetostr( $date ),
			)
		);
		exit;
	}
	/**
	 * Register Scripts and Styles
	 */
	public function momowsw_enqueue_admin_style_scripts() {
		global $momowsw;
		$current = get_current_screen();
		wp_register_script( 'momowsw_pfeedjs', $momowsw->plugin_url . 'premium/product-feed/assets/momo_wsw_feeds.js', array( 'jquery' ), $momowsw->version, true );
		if ( isset( $current->id ) && 'shopify_page_momowsw-product-feed' === $current->id ) {
			wp_enqueue_script( 'momowsw_pfeedjs' );
		}
	}
	/**
	 * Register momowsw Settings
	 */
	public function momowsw_register_settings_pf() {
		register_setting( 'momowsw-settings-pf-feeds-group', 'momo_wsw_pf_product_feeds' );
	}
	/**
	 * Set Admin Menu
	 */
	public function momowsw_set_admin_menu_pf() {
		add_submenu_page(
			'momowsw',
			esc_html__( 'Google Shopping Feed', 'momowsw' ),
			esc_html__( 'Google Shopping Feed', 'momowsw' ),
			'manage_options',
			'momowsw-product-feed',
			array( $this, 'momowsw_add_product_feed_settings_page' )
		);
	}
	/**
	 * Product Feed Settings Page
	 */
	public function momowsw_add_product_feed_settings_page() {
		global $momowsw;
		include_once $momowsw->plugin_path . 'premium/product-feed/admin/pages/momo-wsw-product-feed-settings.php';
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
			add_filter( 'cron_schedules', array( $momowsw->premium->pfcron, 'momo_wsw_custom_product_feeds_schedule' ), 5 ); // phpcs:ignore WordPress.WP.CronInterval
			$momowsw->premium->pfcron->momo_wsw_enable_product_feed_cron();
		} else {
			// Disable cron job.
			$momowsw->premium->pfcron->momo_wsw_disable_product_feed_cron();
		}
	}
}
new MoMo_WSW_Admin_Product_Feed();

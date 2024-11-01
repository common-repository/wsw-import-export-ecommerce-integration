<?php
/**
 * Admin Init
 *
 * @package momowsw
 */
class MoMo_WSW_Admin_Init {
	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'momowsw_set_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'momowsw_print_admin_ss' ) );

		add_action( 'admin_init', array( $this, 'momowsw_register_settings' ) );
	}
	/**
	 * Register momowsw Settings
	 */
	public function momowsw_register_settings() {
		register_setting( 'momowsw-settings-group', 'momo_wsw_settings' );
		register_setting( 'momowsw-settings-api-cache-group', 'momo_wsw_api_cache_settings' );
		register_setting( 'momowsw-settings-export-group', 'momo_wsw_export_settings' );
	}
	/**
	 * Set Admin Menu
	 */
	public function momowsw_set_admin_menu() {
		add_menu_page(
			esc_html__( 'Shopify to WooCommerce', 'momowsw' ),
			'Shopify',
			'manage_options',
			'momowsw',
			array( $this, 'momowsw_add_admin_settings_page' ),
			'dashicons-controls-repeat',
			6
		);
	}
	/**
	 * Add Go Pro Redirection
	 */
	public function momowsw_add_go_pro_redirection() {
		/* header( 'Location:https://codecanyon.net/item/wsw-shopify-woocommerce-syncing/38074621' ); */
		?>
		<script type="text/javascript">
		window.open('https://codecanyon.net/item/wsw-shopify-woocommerce-syncing/38074621', '_blank');
		window.location.href = '<?php echo esc_url( admin_url( 'admin.php?page=momowsw' ) ); ?>';
		</script>
		<?php
		exit;
	}
	/**
	 * Settings Page
	 */
	public function momowsw_add_admin_settings_page() {
		global $momowsw;
		include_once $momowsw->plugin_path . 'includes/admin/pages/momo-wsw-settings.php';
	}
	/**
	 * Enqueue script and styles
	 */
	public function momowsw_print_admin_ss() {
		global $momowsw;
		wp_enqueue_style( 'momowsw_boxicons', $momowsw->plugin_url . 'assets/boxicons/css/boxicons.min.css', array(), '2.1.2' );
		wp_enqueue_style( 'momowsw_admin_style', $momowsw->plugin_url . 'assets/css/momo_wsw_admin.css', array(), $momowsw->version );
		wp_register_script( 'momowsw_admin_script', $momowsw->plugin_url . 'assets/js/momo_wsw_admin.js', array( 'jquery', 'jquery-effects-shake' ), $momowsw->version, true );
		wp_enqueue_script( 'momowsw_admin_script' );
		$ajaxurl = array(
			'ajaxurl'             => admin_url( 'admin-ajax.php' ),
			'momowsw_ajax_nonce'  => wp_create_nonce( 'momowsw_security_key' ),
			'empty_product_id'    => esc_html__( 'Product ID field is empty. Please enter shopify product id.', 'momowsw' ),
			'empty_page_id'       => esc_html__( 'Page ID field is empty. Please enter shopify page id.', 'momowsw' ),
			'empty_blog_id'       => esc_html__( 'Blog ID field is empty. Please enter shopify blog id.', 'momowsw' ),
			'empty_customer_term' => esc_html__( 'Customer search filed is empty. Please enter search term.', 'momowsw' ),
			'imported_message'    => esc_html__( ' item(s) imported.', 'momowsw' ),
			'imported_span'       => '<span>' . esc_html__( 'imported', 'momowsw' ) . '</span>',
			'syncing_store'       => esc_html__( 'Syncing store settings', 'momowsw' ),
			'postexport_btn_text' => esc_html__( 'Export product to Shopify', 'momowsw' ),
			'postupdate_btn_text' => esc_html__( 'Update Shopify product', 'momowsw' ),
		);
		wp_localize_script( 'momowsw_admin_script', 'momowsw_admin', $ajaxurl );

		wp_enqueue_script( 'momowsw_validation', $momowsw->plugin_url . 'assets/validation/momo-validation.js', array( 'jquery' ), $momowsw->version, true );
		wp_enqueue_style( 'momowsw_validation', $momowsw->plugin_url . 'assets/validation/momo-validation.css', array(), $momowsw->version );
	}
}
new MoMo_WSW_Admin_Init();

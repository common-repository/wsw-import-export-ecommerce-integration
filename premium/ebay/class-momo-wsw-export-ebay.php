<?php
/**
 * Ebay Export
 *
 * @package momowsw
 * @author MoMo Themes
 * @since v1.4.0
 */
class MoMo_WSW_Exports_Ebay {
	/**
	 * Ebay Settings
	 *
	 * @var array
	 */
	public $settings;
	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'momowsw_print_admin_ss' ) );
		$this->settings = get_option( 'momo_wsw_es_export' );
		if ( is_admin() ) {
			if ( isset( $this->settings['enable_product_export'] ) && 'on' === $this->settings['enable_product_export'] ) {
				add_action( 'post_submitbox_start', array( $this, 'momo_wsw_add_export_product_button' ) );
			}
		}
	}
	/**
	 * Enqueue script and styles
	 */
	public function momowsw_print_admin_ss() {
		global $momowsw;
		wp_register_script( 'momowsw_ebay_admin_script', $momowsw->plugin_url . 'premium/ebay/assets/js/momo_wsw_ebay_admin.js', array( 'jquery' ), $momowsw->version, true );
		wp_enqueue_script( 'momowsw_ebay_admin_script' );

		$ajaxurl = array(
			'ajaxurl'             => admin_url( 'admin-ajax.php' ),
			'momowsw_ajax_nonce'  => wp_create_nonce( 'momowsw_security_key' ),
			'empty_ebayitem_sku'  => esc_html__( 'SKU field is empty. Please enter eBay item SKU.', 'momowsw' ),
			'postexport_btn_text' => esc_html__( 'Export product to eBay', 'momowsw' ),
			'postupdate_btn_text' => esc_html__( 'Update eBay product', 'momowsw' ),
		);
		wp_localize_script( 'momowsw_ebay_admin_script', 'momowsw_admin_ebay', $ajaxurl );
	}
	/**
	 * Add export to ebay button
	 */
	public function momo_wsw_add_export_product_button() {
		global $post, $momowsw;
		if ( ! is_object( $post ) ) {
			return;
		}

		if ( 'product' !== $post->post_type ) {
			return;
		}
		if ( ! $momowsw->premium->eapi->momowsw_check_api_credentials() ) {
			return;
		}
		$export_settings         = get_option( 'momo_wsw_es_export' );
		$enable_product_export   = $momowsw->fn->momo_wsw_return_option_yesno( $export_settings, 'enable_product_export' );
		$export_product_variants = $momowsw->fn->momo_wsw_return_option_yesno( $export_settings, 'export_product_variants' );
		$export_product_tags     = $momowsw->fn->momo_wsw_return_option_yesno( $export_settings, 'export_product_tags' );
		$woo_product_id          = $post->ID;
		$momowsw_ebay_id         = get_post_meta( $woo_product_id, 'momowsw_ebay_product_id', true );

		if ( 'on' === $enable_product_export ) :
			$button_type = 'insert';
			$button_text = esc_html__( 'Export product to eBay', 'momowsw' );
			if ( ! empty( $momowsw_ebay_id ) ) {
				$button_type = 'update';
				$button_text = esc_html__( 'Update eBay product', 'momowsw' );
			}
			?>
			<div class="momo-be-post-submitbox">
				<div class="momo-be-post-sb-message"></div>
				<div class="momo-post-button full ebay"
					id="momo-wsw-export-to-ebay"
					data-product_id="<?php echo esc_attr( $woo_product_id ); ?>"
					data-type="<?php echo esc_attr( $button_type ); ?>"
					data-ebay_id="<?php echo esc_attr( $momowsw_ebay_id ); ?>"
					>
					<span class="momo-be-spinner"></span>
					<span class="momo-be-spinner-text"><?php echo esc_html( $button_text ); ?></span>
				</div>
				<?php if ( ! empty( $momowsw_ebay_id ) ) { ?>
					<div
						class="momo-post-btn-clear-ebay"
						data-product_id="<?php echo esc_attr( $woo_product_id ); ?>"
						data-ebay_id="<?php echo esc_attr( $momowsw_ebay_id ); ?>"
						>
						<?php esc_html_e( 'Unlink eBay ID', 'momowsw' ); ?>
					</div>
				<?php } ?>
			</div>
			<?php
		endif;
	}
}
new MoMo_WSW_Exports_Ebay();

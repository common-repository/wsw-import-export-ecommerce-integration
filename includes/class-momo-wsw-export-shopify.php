<?php
/**
 * Shopify Export
 *
 * @package momowsw
 * @author MoMo Themes
 * @since v1.1.0
 */
class MoMo_WSW_Exports_Shopify {
	/**
	 * Plugin Settings
	 *
	 * @var array
	 */
	public $settings;
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->settings = get_option( 'momo_wsw_export_settings' );
		if ( is_admin() ) {
			if ( isset( $this->settings['enable_product_export'] ) && 'on' === $this->settings['enable_product_export'] ) {
				add_action( 'post_submitbox_start', array( $this, 'momo_wsw_add_export_product_button' ) );
			}
		}
	}
	/**
	 * Add export to shopify button
	 */
	public function momo_wsw_add_export_product_button() {
		global $post, $momowsw;
		if ( ! is_object( $post ) ) {
			return;
		}

		if ( 'product' !== $post->post_type ) {
			return;
		}
		if ( ! $momowsw->fn->momowsw_check_api_credentials() ) {
			return;
		}
		$export_settings         = get_option( 'momo_wsw_export_settings' );
		$enable_product_export   = $momowsw->fn->momo_wsw_return_option_yesno( $export_settings, 'enable_product_export' );
		$export_product_variants = $momowsw->fn->momo_wsw_return_option_yesno( $export_settings, 'export_product_variants' );
		$export_product_tags     = $momowsw->fn->momo_wsw_return_option_yesno( $export_settings, 'export_product_tags' );
		$woo_product_id          = $post->ID;
		$momowsw_shopify_id      = get_post_meta( $woo_product_id, 'momowsw_product_id', true );

		if ( 'on' === $enable_product_export ) :
			$button_type = 'insert';
			$button_text = esc_html__( 'Export product to Shopify', 'momowsw' );
			if ( ! empty( $momowsw_shopify_id ) ) {
				$button_type = 'update';
				$button_text = esc_html__( 'Update Shopify product', 'momowsw' );
			}
			?>
			<div class="momo-be-post-submitbox">
				<div class="momo-be-post-sb-message"></div>
				<div class="momo-post-button full"
					id="momo-wsw-export-to-shopify"
					data-product_id="<?php echo esc_attr( $woo_product_id ); ?>"
					data-type="<?php echo esc_attr( $button_type ); ?>"
					data-shopify_id="<?php echo esc_attr( $momowsw_shopify_id ); ?>"
					>
					<span class="momo-be-spinner"></span>
					<span class="momo-be-spinner-text"><?php echo esc_html( $button_text ); ?></span>
				</div>
				<?php if ( ! empty( $momowsw_shopify_id ) ) { ?>
					<div
						class="momo-post-btn-clear-shopify"
						data-product_id="<?php echo esc_attr( $woo_product_id ); ?>"
						data-shopify_id="<?php echo esc_attr( $momowsw_shopify_id ); ?>"
						>
						<?php esc_html_e( 'Unlink Shopify ID', 'momowsw' ); ?>
					</div>
				<?php } ?>
			</div>
			<?php
		endif;
	}
}
new MoMo_WSW_Exports_Shopify();

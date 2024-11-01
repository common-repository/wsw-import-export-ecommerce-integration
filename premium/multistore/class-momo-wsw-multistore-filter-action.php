<?php
/**
 * Multistore Filter Action
 *
 * @package momowsw
 * @author MoMo Themes
 * @since v1.0.0
 */
class MoMo_WSW_Multistore_Filter_Action {
	/**
	 * Constructor
	 */
	public function __construct() {
		global $momowsw;
		$multistore_import              = get_option( 'momo_wsw_multistore_import' );
		$enable_specific_product_import = $momowsw->fn->momo_wsw_return_option_yesno( $multistore_import, 'enable_specific_product_import' );
		if ( 'on' === $enable_specific_product_import ) {
			add_filter( 'momowsw_check_specified_product_only', array( $this, 'momowsw_check_product_is_in_order' ), 10, 1 );
			add_filter( 'momowsw_check_specified_product_single', array( $this, 'momowsw_check_shopify_id_is_selected' ), 10, 1 );
		}
		$auto_add_to_list_imported_products = $momowsw->fn->momo_wsw_return_option_yesno( $multistore_import, 'auto_add_to_list_imported_products' );
		if ( 'on' === $auto_add_to_list_imported_products ) {
			add_action( 'momowsw_after_product_create', array( $this, 'momowsw_add_to_specific_product_list' ), 10, 2 );
		}
		add_filter( 'momowsw_change_order_status', array( $this, 'momowsw_change_order_status_multistore' ), 10, 2 );
	}
	/**
	 * Add to specific product list
	 *
	 * @param integer $woo_product_id Woo Product ID.
	 * @param integer $shopify_id Shopify ID.
	 */
	public function momowsw_add_to_specific_product_list( $woo_product_id, $shopify_id ) {
		$multistore_import                      = get_option( 'momo_wsw_multistore_import' );
		$selected_products                      = isset( $multistore_import['selected_products'] ) && is_array( $multistore_import['selected_products'] ) ? $multistore_import['selected_products'] : array();
		$selected_products[]                    = $woo_product_id;
		$multistore_import['selected_products'] = $selected_products;
		update_option( 'momo_wsw_multistore_import', $multistore_import );
	}
	/**
	 * multistore change order status
	 *
	 * @param string   $old_status Old Status.
	 * @param WC_Order $wc_order Woocommerce order.
	 */
	public function momowsw_change_order_status_multistore( $old_status, $wc_order ) {
		global $momowsw;
		$multistore_import    = get_option( 'momo_wsw_multistore_import' );
		$mark_as_processing = $momowsw->fn->momo_wsw_return_option_yesno( $multistore_import, 'mark_as_processing' );
		if ( 'on' === $mark_as_processing ) {
			return 'processing';
		}
		return $old_status;
	}
	/**
	 * Check Shopify ID is selected
	 *
	 * @param integer $shopify_id Shopify Product ID.
	 */
	public function momowsw_check_shopify_id_is_selected( $shopify_id ) {
		$shopify_ids = $this->momowsw_get_shopify_id_of_selected_products();
		if ( in_array( (string) $shopify_id, $shopify_ids, true ) ) {
			return true;
		}
		return false;
	}
	/**
	 * Check shopify order have given product order
	 *
	 * @param Shopify_Order $shopify_order Shopify Order.
	 */
	public function momowsw_check_product_is_in_order( $shopify_order ) {
		$shopify_ids = $this->momowsw_get_shopify_id_of_selected_products();
		if ( isset( $shopify_order->line_items ) ) {
			$line_items = $shopify_order->line_items;
			foreach ( $line_items as $item ) {
				if ( in_array( (string) $item->product_id, $shopify_ids, true ) ) {
					return true;
				}
			}
		}
		return false;
	}
	/**
	 * Get Shopify IDs of selected products
	 */
	public function momowsw_get_shopify_id_of_selected_products() {
		$multistore_import   = get_option( 'momo_wsw_multistore_import' );
		$selected_products = isset( $multistore_import['selected_products'] ) && is_array( $multistore_import['selected_products'] ) ? $multistore_import['selected_products'] : array();
		$shopify_ids       = array();
		if ( ! empty( $selected_products ) ) {
			foreach ( $selected_products as $sp ) {
				$sid = get_post_meta( $sp, 'momowsw_product_id', true );
				if ( ! empty( $sid ) ) {
					$shopify_ids[] = $sid;
				}
			}
		}
		return $shopify_ids;
	}
}
new MoMo_WSW_Multistore_Filter_Action();

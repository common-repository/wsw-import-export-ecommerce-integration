<?php
/**
 * Our own custom Webhooks
 *
 * @package momowsw
 * @author MoMo Themes
 * @since v1.7.0
 */
class MoMo_WSW_Custom_Webhooks {
	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'woocommerce_product_set_stock', array( $this, 'momowsw_trigger_on_stock_change' ), 10, 1 );
		add_action( 'woocommerce_variation_set_stock', array( $this, 'momowsw_trigger_on_stock_change' ), 10, 1 );
	}
	/**
	 * Trigger webhook on stock chamge
	 *
	 * @param WC_Product $wc_product WC_Product.
	 */
	public function momowsw_trigger_on_stock_change( $wc_product ) {
		$webhooks_settings = get_option( 'momo_wsw_webhooks_settings' );
		$selected_location = isset( $webhooks_settings['momowsw_default_inventory_location'] ) ? $webhooks_settings['momowsw_default_inventory_location'] : '';
		$webhooks_ajax     = get_option( 'momo_wsw_webhooks_ajax' );
		$index             = 'woocommerce/stock';
		$wsw_current       = isset( $webhooks_ajax['wsw_webhooks'] ) ? $webhooks_ajax['wsw_webhooks'] : array();
		if ( empty( $selected_location ) ) {
			return;
		}
		if ( isset( $wsw_current[ $index ] ) ) {
			global $wsw_updating_quantity, $momowsw;
			$woo_product_id = $wc_product->get_id();
			if ( isset( $wsw_updating_quantity[ $woo_product_id ] ) && true === $wsw_updating_quantity[ $woo_product_id ] ) {
				// Updating by webhooks.
				return;
			}
			$wsw_updating_quantity[ $woo_product_id ] = true;
			if ( $wc_product->is_type( 'variation' ) ) {
				$parent_product_id    = $wc_product->get_parent_id();
				$momowsw_shopify_id   = get_post_meta( $parent_product_id, 'momowsw_product_id', true );
				$shopify_variation_id = get_post_meta( $woo_product_id, '_momo_shopify_variation_id', true );
				$inventory_item_id    = get_post_meta( $woo_product_id, '_momo_shopify_variation_inventory_item_id', true );
				if ( empty( $inventory_item_id ) ) {
					$details = $momowsw->fn->momo_wsw_run_rest_api( 'GET', "variants/$shopify_variation_id.json" );
					if ( ! isset( $details->variant ) ) {
						return;
					}
					$inventory_item_id = $details->variant->inventory_item_id;
					update_post_meta( $woo_product_id, '_momo_shopify_variation_inventory_item_id', $inventory_item_id );
				}
				$current_stock = $wc_product->get_stock_quantity();

				$args    = array(
					'location_id'       => $selected_location,
					'inventory_item_id' => $inventory_item_id,
					'available'         => $current_stock,
				);
				$details = $momowsw->fn->momo_wsw_run_rest_api( 'POST', 'inventory_levels/set.json', '', wp_json_encode( $args ) );
			} else {
				$momowsw_shopify_id = get_post_meta( $woo_product_id, 'momowsw_product_id', true );
				$inventory_item_id  = get_post_meta( $woo_product_id, '_momo_shopify_variation_inventory_item_id', true );
				if ( empty( $inventory_item_id ) ) {
					$details = $momowsw->fn->momo_wsw_run_rest_api( 'GET', "products/$momowsw_shopify_id.json" );
					if ( ! isset( $details->product->variants[0] ) ) {
						return;
					}
					$inventory_item_id = $details->product->variants[0]->inventory_item_id;
					update_post_meta( $woo_product_id, '_momo_shopify_inventory_item_id', $inventory_item_id );
				}
				$current_stock = $wc_product->get_stock_quantity();

				$args    = array(
					'location_id'       => $selected_location,
					'inventory_item_id' => $inventory_item_id,
					'available'         => $current_stock,
				);
				$details = $momowsw->fn->momo_wsw_run_rest_api( 'POST', 'inventory_levels/set.json', '', wp_json_encode( $args ) );
			}

			unset( $wsw_updating_quantity[ $woo_product_id ] );
		}
	}
}

<?php
/**
 * Webhooks
 *
 * @package momowsw
 * @author MoMo Themes
 * @since v1.5.0
 */
class MoMo_WSW_Webhooks {
	/**
	 * Slug
	 *
	 * @var string
	 */
	public $slug;
	/**
	 * WebHooks
	 *
	 * @var array
	 */
	public $webhooks;
	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'momowsw_print_admin_ss' ) );
		$this->slug     = 'wsw-webhooks';
		$this->webhooks = get_option( 'momo_wsw_webhooks_settings' );
		add_action( 'rest_api_init', array( $this, 'momowsw_custom_rest_api_webhooks' ) );
	}
	/**
	 * Enqueue script and styles
	 */
	public function momowsw_print_admin_ss() {
		global $momowsw;
		wp_register_script( 'momowsw_webhooks_admin_script', $momowsw->plugin_url . 'premium/webhooks/assets/js/momo_wsw_webhooks_admin.js', array( 'jquery' ), $momowsw->version, true );
		wp_enqueue_script( 'momowsw_webhooks_admin_script' );
	}
	/**
	 * Setup Custom REST API Endpoints
	 */
	public function momowsw_custom_rest_api_webhooks() {
		$api_namespace = $this->slug;
		$settings      = $this->webhooks;
		register_rest_route(
			$api_namespace,
			'/product-update-quantity',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'momowsw_update_product_quantity_on_trigger' ),
				'permission_callback' => function ( WP_REST_Request $request ) {
					return true;
				},
			)
		);
	}
	/**
	 * Response from the Request API /events
	 *
	 * @param array $request Request paramenters.
	 */
	public function momowsw_update_product_quantity_on_trigger( $request ) {
		global $tecextd;
		$product  = $request->get_json_params();
		$quantity = 0;
		if ( ! empty( $product ) ) {
			$quantity = $this->momowsw_update_product_quantity( $product );
		}

		$response = array(
			'quantity' => $quantity,
		);
		return $response;
	}
	/**
	 * Update Product Quantity
	 *
	 * @param array $product Shopify Product.
	 */
	public function momowsw_update_product_quantity( $product ) {
		$momowsw_product_id = isset( $product['id'] ) ? $product['id'] : '';
		if ( empty( $momowsw_product_id ) ) {
			return;
		}
		$woo_product = $this->momowsw_get_woo_product( $momowsw_product_id );
		if ( ! $woo_product || ! isset( $woo_product['wc_product'] ) ) {
			return;
		}
		global $wsw_updating_quantity;
		$wc_product     = $woo_product['wc_product'];
		$woo_product_id = $woo_product['woo_product_id'];
		// Flag used to identify cross updates.
		$wsw_updating_quantity[ $woo_product_id ] = true;
		/** Product Array Details */
		$product_arr['momowsw_product_id'] = $product['id'];
		$product_arr['variants']           = $product['variants'];
		$product_arr['options']            = $product['options'];
		$product_arr['product_type']       = $product['product_type'];
		$product_arr['tags']               = $product['tags'];
		$product_arr['handle']             = $product['handle'];
		$product_arr['variations']         = 'on';
		if ( is_array( $product_arr['variants'] ) ) {
			$variants = $product_arr['variants'];
			$variant  = $variants[0];
			if ( count( $variants ) === 1 ) {
				$wc_product->set_manage_stock( true );
				$wc_product->set_stock_quantity( $variant['inventory_quantity'] );
				$quantity = $variant['inventory_quantity'];
				if ( $quantity <= 0 ) {
					$wc_product->set_stock_status( 'outofstock' );
				} else {
					$wc_product->set_stock_status( 'instock' );
				}
				$wc_product->save();
			} else {
				$options          = $product_arr['options'];
				$wc_variation_ids = $wc_product->get_children();
				foreach ( $wc_variation_ids as $wc_variation_id ) {
					$wc_variation = new WC_Product_Variation( $wc_variation_id );
					$shopify_vid  = $wc_variation->get_meta( '_momo_shopify_variation_id' );
					$wc_variation->set_manage_stock( true );
					foreach ( $variants as $variant ) {
						if ( (int) $shopify_vid === (int) $variant['id'] ) {
							$wc_variation->set_stock_quantity( $variant['inventory_quantity'] );
							$quantity = $variant['inventory_quantity'];
							if ( $quantity <= 0 ) {
								$wc_variation->set_stock_status( 'outofstock' );
							} else {
								$wc_variation->set_stock_status( 'instock' );
							}
						}
					}
					$wc_variation->save();
				}
				$wc_product->save();
			}
		}
		unset( $wsw_updating_quantity[ $woo_product_id ] );
	}
	/**
	 * Sanitize Taxonomy Name
	 *
	 * @param string $name Name to sanitize.
	 */
	public static function momo_sanitize_taxonomy_name( $name ) {
		return strtolower( rawurlencode( wc_sanitize_taxonomy_name( $name ) ) );
	}
	/**
	 * Get Woo Product by Shopify ID.
	 *
	 * @param integer $momowsw_product_id Shopify ID.
	 */
	public function momowsw_get_woo_product( $momowsw_product_id ) {
		$args  = array(
			'numberposts' => 1,
			'post_type'   => 'product',
			'post_status' => array(
				'publish',
				'pending',
				'draft',
			),
			'meta_query'  => array(
				array(
					'key'     => 'momowsw_product_id',
					'value'   => $momowsw_product_id,
					'compare' => '=',
				),
			),
		);
		$query = new WP_Query( $args );
		if ( 0 === $query->post_count && ! isset( $query->posts[0] ) ) {
			return false;
		} else {
			$product        = $query->posts[0];
			$woo_product_id = $product->ID;
			$wc_product     = wc_get_product( $woo_product_id );
			return array(
				'woo_product_id' => $woo_product_id,
				'wc_product'     => $wc_product,
			);
		}
		return false;
	}

}

<?php
/**
 * Orders
 *
 * @package momowsw
 * @author MoMo Themes
 * @since v1.4.0
 */
class MoMo_WSW_Orders {
	/**
	 * Constructor
	 */
	public function __construct() {
		if ( is_admin() ) {
			add_filter( 'manage_edit-shop_order_columns', array( $this, 'momowsw_add_shopify_linked_column' ), 15 );
			add_action( 'manage_shop_order_posts_custom_column', array( $this, 'momowsw_add_shopify_linked_column_details' ), 10, 2 );

			$order_settings = get_option( 'momo_wsw_orders_settings' );
			$option         = isset( $order_settings['enable_tracking_information'] ) ? $order_settings['enable_tracking_information'] : 'off';
			if ( 'on' === $option ) {
				add_action( 'add_meta_boxes', array( $this, 'momowsw_add_tracking_meta_boxes' ), 10 );
				add_action( 'save_post_shop_order', array( $this, 'momowsw_update_order_meta' ) );
			}
		}
		add_action( 'woocommerce_order_status_completed', array( $this, 'momo_wsw_check_and_make_fullfillment' ), 10 );
	}

	/**
	 * Add Tracking Metabox
	 */
	public function momowsw_add_tracking_meta_boxes() {
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';
		foreach ( wc_get_order_types( 'order-meta-boxes' ) as $type ) {
			$order_type_object = get_post_type_object( $type );
			add_meta_box(
				'momowsw-order-shopify-tracking',
				esc_html__( 'Tracking', 'momowsw' ),
				array( $this, 'momowsws_tracking_metabox_content' ),
				$type,
				'side',
				'high'
			);
		}
	}
	/**
	 * Tracking Metabox content
	 */
	public function momowsws_tracking_metabox_content() {
		global $post;
		$tracking_number = get_post_meta( $post->ID, 'momowsw_fulfillment_tracking_number', true ) ? get_post_meta( $post->ID, 'momowsw_fulfillment_tracking_number', true ) : '';
		$tracking_url    = get_post_meta( $post->ID, 'momowsw_fulfillment_tracking_url', true ) ? get_post_meta( $post->ID, 'momowsw_fulfillment_tracking_url', true ) : '';
		$message         = get_post_meta( $post->ID, 'momowsw_fulfillment_message', true ) ? get_post_meta( $post->ID, 'momowsw_fulfillment_message', true ) : '';
		$company         = get_post_meta( $post->ID, 'momowsw_fulfillment_company', true ) ? get_post_meta( $post->ID, 'momowsw_fulfillment_company', true ) : '';
		wp_nonce_field( plugin_basename( __FILE__ ), 'momowsw_noncename' );
		?>
		<div class="momowsw-side-mb">
			<div class="momowsw-side-mb-header">
				<?php esc_html_e( 'Tracking Info', 'momowsw' ); ?>
			</div>
			<div class="momowsw-side-mb-info">
				<label for="momowsw_fulfillment_tracking_number">
					<?php esc_html_e( 'Tracking Number', 'momowsw' ); ?>
				</label>
				<span class="momowsw-mb-ffield">
					<input type="text" name="momowsw_fulfillment_tracking_number" value="<?php echo esc_attr( $tracking_number ); ?>"/>
				</span>
				<label for="momowsw_fulfillment_tracking_number">
					<?php esc_html_e( 'Tracking Url', 'momowsw' ); ?>
				</label>
				<span class="momowsw-mb-ffield">
					<input type="text" name="momowsw_fulfillment_tracking_url" value="<?php echo esc_attr( $tracking_url ); ?>"/>
				</span>
				<label for="momowsw_fulfillment_tracking_number">
					<?php esc_html_e( 'Company', 'momowsw' ); ?>
				</label>
				<span class="momowsw-mb-ffield">
					<input type="text" name="momowsw_fulfillment_company" value="<?php echo esc_attr( $company ); ?>"/>
				</span>
				<label for="momowsw_fulfillment_message">
					<?php esc_html_e( 'Message', 'momowsw' ); ?>
				</label>
				<span class="momowsw-mb-ffield">
					<textarea name="momowsw_fulfillment_message" row="3"><?php echo esc_html( $message ); ?></textarea>
				</span>
			</div>
		</div>
		<?php
	}
	/**
	 * Save tracking metadata
	 *
	 * @param integer $order_id Order ID.
	 */
	public function momowsw_update_order_meta( $order_id ) {
		if ( isset( $_POST['momowsw_noncename'] ) ) {
			if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['momowsw_noncename'] ) ), plugin_basename( __FILE__ ) ) ) {
				return;
			}
		}
		$tracking_number = isset( $_POST['momowsw_fulfillment_tracking_number'] ) ? sanitize_text_field( wp_unslash( $_POST['momowsw_fulfillment_tracking_number'] ) ) : '';
		$tracking_url    = isset( $_POST['momowsw_fulfillment_tracking_url'] ) ? sanitize_text_field( wp_unslash( $_POST['momowsw_fulfillment_tracking_url'] ) ) : '';
		$message         = isset( $_POST['momowsw_fulfillment_message'] ) ? sanitize_text_field( wp_unslash( $_POST['momowsw_fulfillment_message'] ) ) : '';
		$company         = isset( $_POST['momowsw_fulfillment_company'] ) ? sanitize_text_field( wp_unslash( $_POST['momowsw_fulfillment_company'] ) ) : '';
		update_post_meta( $order_id, 'momowsw_fulfillment_tracking_number', $tracking_number );
		update_post_meta( $order_id, 'momowsw_fulfillment_tracking_url', $tracking_url );
		update_post_meta( $order_id, 'momowsw_fulfillment_message', $message );
		update_post_meta( $order_id, 'momowsw_fulfillment_company', $company );
	}
	/**
	 * Check and make shopify Fullfillment.
	 *
	 * @param integer $woo_order_id Order ID.
	 */
	public function momo_wsw_check_and_make_fullfillment( $woo_order_id ) {
		global $momowsw;
		$wc_order         = wc_get_order( $woo_order_id );
		$momowsw_order_id = get_post_meta( $woo_order_id, 'momowsw_order_id', true );
		if ( $momowsw_order_id && ! empty( $momowsw_order_id ) ) {
			$key            = 'enable_auto_sync_order_status';
			$order_settings = get_option( 'momo_wsw_orders_settings' );
			$option         = isset( $order_settings[ $key ] ) ? $order_settings[ $key ] : 'off';
			if ( 'on' === $option ) {
				$method      = 'GET';
				$orders      = $this->momowsw_get_order_details( $wc_order );
				$url         = 'orders/' . $momowsw_order_id . '/fulfillment_orders.json';
				$details     = $momowsw->fn->momo_wsw_run_rest_api( $method, $url, '', '' );
				$location_id = '';
				$count       = count( $details->fulfillment_orders );
				if ( $count > 0 ) {
					$fulfillment_order_id = $details->fulfillment_orders[ $count - 1 ]->id;
				}
				$line_items  = array(
					'fulfillment_order_id' => (int) $fulfillment_order_id,
				);
				$tracking_number = get_post_meta( $woo_order_id, 'momowsw_fulfillment_tracking_number', true ) ? get_post_meta( $woo_order_id, 'momowsw_fulfillment_tracking_number', true ) : '';
				$tracking_url    = get_post_meta( $woo_order_id, 'momowsw_fulfillment_tracking_url', true ) ? get_post_meta( $woo_order_id, 'momowsw_fulfillment_tracking_url', true ) : '';
				$message         = get_post_meta( $woo_order_id, 'momowsw_fulfillment_message', true ) ? get_post_meta( $woo_order_id, 'momowsw_fulfillment_message', true ) : '';
				$company         = get_post_meta( $woo_order_id, 'momowsw_fulfillment_company', true ) ? get_post_meta( $woo_order_id, 'momowsw_fulfillment_company', true ) : '';
				$fulfillment = array(
					'fulfillment' => array(
						'message'                         => $message,
						'notify_customer'                 => false,
						'tracking_info'                   => array(
							'number'  => $tracking_number,
							'url'     => $tracking_url,
							'company' => $company,
						),
						'line_items_by_fulfillment_order' => array(
							$line_items,
						),
					),
				);

				$method  = 'POST';
				$details = $momowsw->fn->momo_wsw_run_rest_api( $method, 'fulfillments.json', '', wp_json_encode( $fulfillment ) );

				if ( isset( $details->errors ) ) {
					$error = $details->errors;
					$error = isset( $error[0] ) ? $error[0] : $error;
					$logs  = array(
						'fulfillment' => array(
							'time' => current_time( 'Y-m-d H:i:s' ),
							'type' => 'error',
							'msg'  => is_object( $error ) ? esc_html__( 'Something went wrong while changing order status', 'momowsw' ) : $error,
						),
					);
					$momowsw->logs->momo_wsw_save_logs( 'order', $logs );
				}
				if ( isset( $details->fulfillment->status ) && 'success' === $details->fulfillment->status ) {
					$note = esc_html__( 'Shopify order status changed to fulfilled', 'momowsw' );
					$logs = array(
						'fulfillment' => array(
							'time' => current_time( 'Y-m-d H:i:s' ),
							'type' => 'success',
							'msg'  => $note,
						),
					);
					$momowsw->logs->momo_wsw_save_logs( 'order', $logs );
					$wc_order->add_order_note( $note );
					$wc_order->update_meta_data( 'momowsw_fulfillment_id', $details->fulfillment->id );
					$wc_order->save();
				}
			}
		}
	}
	/**
	 * Get Shopify Order Details
	 *
	 * @param WC_Order $wc_order WooCommerce order object.
	 */
	public function momowsw_get_order_details( $wc_order ) {
		$line_items    = $wc_order->get_items();
		$order_details = array();
		foreach ( $line_items as $item_id => $item ) {
			$product_id   = $item->get_product_id();
			$variation_id = $item->get_variation_id();
			$quantity     = $item->get_quantity();
			$type         = $item->get_type();
			if ( ! empty( $variation_id ) ) {
				$id = get_post_meta( $variation_id, '_momo_shopify_variation_id' );
			} else {
				$id = get_post_meta( $product_id, '_momo_shopify_variation_id' );
			}
			$order_details[] = array(
				'id'       => $id,
				'quantity' => $quantity,
			);
		}
		return $order_details;
	}
	/**
	 * Add Shopify Linked Column.
	 *
	 * @param array $columns Default Columns.
	 */
	public function momowsw_add_shopify_linked_column( $columns ) {

		$columns['momowsw_shopify_link'] = esc_html__( 'Shopify', 'momowsw' );

		return $columns;
	}
	/**
	 * Add column content, shopify icon
	 *
	 * @param string  $column Column name.
	 * @param integer $woo_order_id Order ID.
	 */
	public function momowsw_add_shopify_linked_column_details( $column, $woo_order_id ) {
		$wc_order = wc_get_order( $woo_order_id );
		if ( 'momowsw_shopify_link' === $column ) {
			$momowsw_order_id = get_post_meta( $woo_order_id, 'momowsw_order_id', true );
			if ( $momowsw_order_id && ! empty( $momowsw_order_id ) ) {
				echo "<span class='momowsw-shopify-link'><i class='bx bxl-shopify'></i></span>";
				$momowsw_fulfillment_id = get_post_meta( $woo_order_id, 'momowsw_fulfillment_id', true );
				if ( $momowsw_fulfillment_id && ! empty( $momowsw_fulfillment_id ) ) {
					echo "<span class='momowsw-shopify-fulfillment'><i class='bx bxs-circle momowsw-dot-icon'></i>" . esc_html__( 'Fulfilled via Woocommerce', 'momowsw' ) . '</span>';
				}
			}
		}
	}
	/**
	 * Get total Orders Count
	 */
	public static function momowsw_get_total_number_of_orders( $status = 'any' ) {
		global $momowsw;
		$method  = 'GET';
		$url     = 'orders/count.json?status=' . $status;
		$details = $momowsw->fn->momo_wsw_run_rest_api( $method, $url, 'momowsw_shopify_order_count', '' );
		return $details;
	}
}
new MoMo_WSW_Orders();

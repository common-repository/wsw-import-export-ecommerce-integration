<?php
/**
 * Admin Init
 *
 * @package momowsw
 * @author MoMo Themes
 * @since v2.0.0
 */
class MoMo_WSW_Order_Auto_Sync {
	/**
	 * Import Scheduled orders from shopify
	 */
	public function momo_wsw_import_orders_from_shopify() {
		global $momowsw;
		$crons = wp_get_scheduled_event( $momowsw->premium->ocron->cron_hooks['iorder'] );
		if ( ! empty( $crons ) ) {
			$timestamp = $crons->timestamp;
			$interval  = $crons->interval;
			$from      = $timestamp - $interval;
			$details   = $this->momo_wsw_import_orders_from_date( $from );
		}
	}
	/**
	 * Export Scheduled orders to shopify
	 */
	public function momo_wsw_export_orders_to_shopify() {
		global $momowsw;
		$crons = wp_get_scheduled_event( $momowsw->premium->ocron->cron_hooks['eorder'] );
		if ( ! empty( $crons ) ) {
			$timestamp = $crons->timestamp;
			$interval  = $crons->interval;
			$from      = $timestamp - $interval;
			$orders    = $this->momo_wsw_export_orders_from_date( $from );
		}
	}

	/**
	 * Generate orders by Publish at Min
	 *
	 * @param integer $from_timestamp Publish at min timestamp.
	 */
	public function momo_wsw_import_orders_from_date( $from_timestamp ) {
		global $momowsw;
		$catandtags = 'on';
		$pstatus    = 'publish';
		$variations = 'on';

		$pam     = wp_date( 'c', $from_timestamp );
		$args    = array(
			'published_at_min' => $pam,
		);
		$details = $momowsw->fn->momo_wsw_run_rest_api( 'GET', 'orders.json', '', $args );
		if ( isset( $details->orders ) && ! empty( $details->orders ) ) {
			$orders = $details->orders;
			$logs     = array(
				'import' => array(
					'time' => current_time( 'Y-m-d H:i:s' ),
					'type' => 'success',
					/* translators: %s: prooduct count */
					'msg'  => sprintf( esc_html__( '%s order(s) found from date: ', 'momowsw' ), count( $orders ) ) . $pam,
				),
			);
			$momowsw->logs->momo_wsw_save_logs( 'order', $logs );
			$count = 0;
			foreach ( $orders as $order ) {
				$response = $momowsw->premium->fn->momo_momowsw_create_order( $order, $catandtags, $pstatus, $variations );
				if ( $response ) {
					$count++;
				}
			}
			$logs = array(
				'import' => array(
					'time' => current_time( 'Y-m-d H:i:s' ),
					'type' => 'success',
					/* translators: %s: prooduct count */
					'msg'  => sprintf( esc_html__( '%s order(s) imported successfully', 'momowsw' ), $count ),
				),
			);
			$momowsw->logs->momo_wsw_save_logs( 'order', $logs );
		} else {
			$logs = array(
				'import' => array(
					'time' => current_time( 'Y-m-d H:i:s' ),
					'type' => 'error',
					'msg'  => esc_html__( 'order not found from date: ', 'momowsw' ) . $pam,
				),
			);
			$momowsw->logs->momo_wsw_save_logs( 'order', $logs );
		}
	}
	/**
	 * Export orders by Publish at Min
	 *
	 * @param integer $from_timestamp Publish at min timestamp.
	 */
	public function momo_wsw_export_orders_from_date( $from_timestamp ) {
		global $momowsw;
		$pam    = wp_date( 'c', $from_timestamp );
		$args   = array(
			'numberposts' => -1,
			'post_type'   => 'shop_order',
			'post_status' => array(
				'publish',
				'pending',
				'draft',
			),
			'date_query'  => array(
				'after' => $pam,
			),
			'meta_query' => array(
				array(
					'key'     => 'momowsw_order_id',
					'compare' => 'NOT EXISTS',
				),
			),
		);
		$orders = new WP_Query( $args );

		if ( $orders->have_posts() ) {
			$orders_arr = $orders->posts;
			$count      = 0;
			foreach ( $orders_arr as $order ) {
				$response = $this->momo_wsw_import_single_order_to_shopify( $order->ID );
				if ( $response ) {
					$count++;
				}
			}
			$logs = array(
				'export' => array(
					'time' => current_time( 'Y-m-d H:i:s' ),
					'type' => 'success',
					/* translators: %s: prooduct count */
					'msg'  => sprintf( esc_html__( '%s order(s) exported successfully', 'momowsw' ), $count ),
				),
			);
			$momowsw->logs->momo_wsw_save_logs( 'order', $logs );
		} else {
			$logs = array(
				'export' => array(
					'time' => current_time( 'Y-m-d H:i:s' ),
					'type' => 'error',
					'msg'  => esc_html__( 'order not found from date: ', 'momowsw' ) . $pam,
				),
			);
			$momowsw->logs->momo_wsw_save_logs( 'order', $logs );
		}
	}
	/**
	 * Sync woo order to shopify
	 *
	 * @param integer $order_id Wooorder ID.
	 */
	public function momo_wsw_import_single_order_to_shopify( $order_id ) {
		global $momowsw;
		$shopify_settings = get_option( 'momo_wsw_settings' );
		if ( ! $momowsw->fn->momowsw_check_api_credentials() ) {
			return;
		}
		if ( empty( $order_id ) ) {
			return;
		}
		$export_settings = get_option( 'momo_wsw_export_settings' );
		$order_status    = isset( $export_settings['order_status'] ) ? $export_settings['order_status'] : 'active';

		$args = array(
			'post_id'     => $order_id,
			'post_status' => $order_status,
			'type'        => 'insert',
			'ptype'       => 'order',
		);

		$export_data = $momowsw->eofn->momo_wsw_prepare_others_to_export( $args );

		$access_token = isset( $shopify_settings['access_token'] ) ? $shopify_settings['access_token'] : '';
		$api_version  = isset( $shopify_settings['api_version'] ) && ! empty( $shopify_settings['api_version'] ) ? $shopify_settings['api_version'] : '2022-04';

		$shopify_url = 'https://' . $shopify_settings['shop_url'] . '/admin/api/' . $api_version . '/orders.json';

		$args     = array(
			'headers'     => array(
				'Accept'                 => 'application/json',
				'Content-Type'           => 'application/json',
				'X-Shopify-Access-Token' => $access_token,
			),
			'httpversion' => '1.1',
			'method'      => 'POST',
			'timeout'     => 90,
			'body'        => wp_json_encode( $export_data ),
		);
		$response = wp_remote_post( $shopify_url, $args );
		$json     = wp_remote_retrieve_body( $response );
		$details  = json_decode( $json );

		if ( isset( $details->order->id ) && ! empty( $details->order->id ) ) {
			update_post_meta( $order_id, 'momowsw_order_id', $details->order->id );
			$msg  = esc_html__( 'Order exported successfully', 'momowsw' );
			$logs = array(
				'export' => array(
					'time' => current_time( 'Y-m-d H:i:s' ),
					'type' => 'success',
					'msg'  => $msg,
				),
			);
			$momowsw->logs->momo_wsw_save_logs( 'order', $logs );
			return $details->order->id;
		} else {
			if ( isset( $details->errors ) ) {
				$msg = '';
				if ( isset( $details->errors->id ) ) {
					$msg = $details->errors->id;
				} elseif ( isset( $details->errors->base ) ) {
					$msg = $details->errors->base;
				} else {
					$msg = $details->errors;
				}
				$msg = is_array( $msg ) ? implode( '|', $msg ) : $msg;
			} else {
				$msg = esc_html__( 'Something went wrong while exporting order. Please check shopify if order have been exported.', 'momowsw' );
			}
			$logs = array(
				'export' => array(
					'time' => current_time( 'Y-m-d H:i:s' ),
					'type' => 'error',
					'msg'  => $msg,
				),
			);
			$momowsw->logs->momo_wsw_save_logs( 'order', $logs );
		}
	}
}

<?php
/**
 * Admin Init
 *
 * @package momowsw
 * @author MoMo Themes
 * @since v2.0.0
 */
class MoMo_WSW_Auto_Sync {
	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'momowsw_register_auto_sync_settings' ) );

		if ( is_admin() ) {
			add_action( 'update_option_momo_wsw_as_import_settings', array( $this, 'momo_wsw_after_as_import_settings' ), 10, 3 );
			add_action( 'update_option_momo_wsw_as_export_settings', array( $this, 'momo_wsw_after_as_export_settings' ), 10, 3 );
		}
	}

	/**
	 * Register Auto Sync Settings
	 */
	public function momowsw_register_auto_sync_settings() {
		register_setting( 'momowsw-settings-as-import-group', 'momo_wsw_as_import_settings' );
		register_setting( 'momowsw-settings-as-export-group', 'momo_wsw_as_export_settings' );
	}
	/**
	 * After saving import settings
	 *
	 * @param mixed  $old_value The old option value.
	 * @param mixed  $value     The new option value.
	 * @param string $option    Option name.
	 */
	public function momo_wsw_after_as_import_settings( $old_value, $value, $option ) {
		global $momowsw;
		$enable_as_import = isset( $value['enable_as_import'] ) ? $value['enable_as_import'] : 'off';
		if ( 'on' === $enable_as_import ) {
			$days   = isset( $value['as_import_days'] ) ? $value['as_import_days'] : 1;
			$hour   = isset( $value['as_import_hour'] ) ? $value['as_import_hour'] : 00;
			$minute = isset( $value['as_import_minute'] ) ? $value['as_import_minute'] : 00;
			// Enable cron job.
			add_filter( 'cron_schedules', array( $momowsw->premium->cron, 'momo_wsw_custom_import_schedule' ), 5 ); // phpcs:ignore WordPress.WP.CronInterval
			$momowsw->premium->cron->momo_wsw_enable_product_import_cron();
		} else {
			// Disable cron job.
			$momowsw->premium->cron->momo_wsw_disable_product_import_cron();
		}
	}
	/**
	 * After saving Export settings
	 *
	 * @param mixed  $old_value The old option value.
	 * @param mixed  $value     The new option value.
	 * @param string $option    Option name.
	 */
	public function momo_wsw_after_as_export_settings( $old_value, $value, $option ) {
		global $momowsw;
		$enable_as_export = isset( $value['enable_as_export'] ) ? $value['enable_as_export'] : 'off';
		if ( 'on' === $enable_as_export ) {
			$days   = isset( $value['as_export_days'] ) ? $value['as_export_days'] : 1;
			$hour   = isset( $value['as_export_hour'] ) ? $value['as_export_hour'] : 00;
			$minute = isset( $value['as_export_minute'] ) ? $value['as_export_minute'] : 00;
			// Enable cron job.
			add_filter( 'cron_schedules', array( $momowsw->premium->cron, 'momo_wsw_custom_export_schedule' ), 5 ); // phpcs:ignore WordPress.WP.CronInterval
			$momowsw->premium->cron->momo_wsw_enable_product_export_cron();
		} else {
			// Disable cron job.
			$momowsw->premium->cron->momo_wsw_disable_product_export_cron();
		}
	}
	/**
	 * Import Scheduled Products from shopify
	 */
	public function momo_wsw_import_shceduled_products_from_shopify() {
		global $momowsw;
		$crons = wp_get_scheduled_event( $momowsw->premium->cron->cron_hooks['iproduct'] );
		if ( ! empty( $crons ) ) {
			$timestamp = $crons->timestamp;
			$interval  = $crons->interval;
			$from      = $timestamp - $interval;
			$details   = $this->momo_wsw_import_products_from_date( $from );
		}
	}
	/**
	 * Export Scheduled Products to shopify
	 */
	public function momo_wsw_export_shceduled_products_to_shopify() {
		global $momowsw;
		$crons = wp_get_scheduled_event( $momowsw->premium->cron->cron_hooks['eproduct'] );
		if ( ! empty( $crons ) ) {
			$timestamp = $crons->timestamp;
			$interval  = $crons->interval;
			$from      = $timestamp - $interval;
			$products  = $this->momo_wsw_export_products_from_date( $from );
		}
	}

	/**
	 * Generate Products by Publish at Min
	 *
	 * @param integer $from_timestamp Publish at min timestamp.
	 */
	public function momo_wsw_import_products_from_date( $from_timestamp ) {
		global $momowsw;
		$catandtags = 'on';
		$pstatus    = 'publish';
		$variations = 'on';

		$pam     = wp_date( 'c', $from_timestamp );
		$args    = array(
			'published_at_min' => $pam,
		);
		$details = $momowsw->fn->momo_wsw_run_rest_api( 'GET', 'products.json', '', $args );
		if ( isset( $details->products ) && ! empty( $details->products ) ) {
			$products = $details->products;
			$logs     = array(
				'import' => array(
					'time' => current_time( 'Y-m-d H:i:s' ),
					'type' => 'success',
					/* translators: %s: prooduct count */
					'msg'  => sprintf( esc_html__( '%s product(s) found from date: ', 'momowsw' ), count( $products ) ) . $pam,
				),
			);
			$momowsw->logs->momo_wsw_save_logs( 'cron', $logs );
			$count = 0;
			foreach ( $products as $product ) {
				$response = $momowsw->fn->momo_momowsw_create_product( $product, $catandtags, $pstatus, $variations );
				if ( $response ) {
					$count++;
				}
			}
			$logs = array(
				'import' => array(
					'time' => current_time( 'Y-m-d H:i:s' ),
					'type' => 'success',
					/* translators: %s: prooduct count */
					'msg'  => sprintf( esc_html__( '%s product(s) imported successfully', 'momowsw' ), $count ),
				),
			);
			$momowsw->logs->momo_wsw_save_logs( 'cron', $logs );
		} else {
			$logs = array(
				'import' => array(
					'time' => current_time( 'Y-m-d H:i:s' ),
					'type' => 'error',
					'msg'  => esc_html__( 'Product not found from date: ', 'momowsw' ) . $pam,
				),
			);
			$momowsw->logs->momo_wsw_save_logs( 'cron', $logs );
		}
	}
	/**
	 * Export Products by Publish at Min
	 *
	 * @param integer $from_timestamp Publish at min timestamp.
	 */
	public function momo_wsw_export_products_from_date( $from_timestamp ) {
		global $momowsw;
		$pam      = wp_date( 'c', $from_timestamp );
		$args     = array(
			'numberposts' => -1,
			'post_type'   => 'product',
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
					'key'     => 'momowsw_product_id',
					'compare' => 'NOT EXISTS',
				),
			),
		);
		$products = new WP_Query( $args );

		if ( $products->have_posts() ) {
			$products_arr = $products->posts;
			$count        = 0;
			foreach ( $products_arr as $product ) {
				$response = $this->momo_wsw_import_single_product_to_shopify( $product->ID );
				if ( $response ) {
					$count++;
				}
			}
			$logs = array(
				'export' => array(
					'time' => current_time( 'Y-m-d H:i:s' ),
					'type' => 'success',
					/* translators: %s: prooduct count */
					'msg'  => sprintf( esc_html__( '%s product(s) exported successfully', 'momowsw' ), $count ),
				),
			);
			$momowsw->logs->momo_wsw_save_logs( 'cron', $logs );
		} else {
			$logs = array(
				'export' => array(
					'time' => current_time( 'Y-m-d H:i:s' ),
					'type' => 'error',
					'msg'  => esc_html__( 'Product not found from date: ', 'momowsw' ) . $pam,
				),
			);
			$momowsw->logs->momo_wsw_save_logs( 'cron', $logs );
		}
	}
	/**
	 * Sync woo product to shopify
	 *
	 * @param integer $product_id WooProduct ID.
	 */
	public function momo_wsw_import_single_product_to_shopify( $product_id ) {
		global $momowsw;
		$shopify_settings = get_option( 'momo_wsw_settings' );
		if ( ! $momowsw->fn->momowsw_check_api_credentials() ) {
			return;
		}
		if ( empty( $product_id ) ) {
			return;
		}
		$export_settings         = get_option( 'momo_wsw_export_settings' );
		$export_product_variants = $momowsw->fn->momo_wsw_return_option_yesno( $export_settings, 'export_product_variants' );
		$export_product_tags     = $momowsw->fn->momo_wsw_return_option_yesno( $export_settings, 'export_product_tags' );
		$product_status          = isset( $export_settings['product_status'] ) ? $export_settings['product_status'] : 'active';
		$args                    = array(
			'product_id'              => $product_id,
			'export_product_variants' => $export_product_variants,
			'export_product_tags'     => $export_product_tags,
			'product_status'          => $product_status,
			'type'                    => 'insert',
		);

		$export_data          = $momowsw->efn->momo_wsw_prepare_product_to_export( $args );
		$export_data_variants = $export_data['product']['variants'];

		$access_token = isset( $shopify_settings['access_token'] ) ? $shopify_settings['access_token'] : '';
		$api_version  = isset( $shopify_settings['api_version'] ) && ! empty( $shopify_settings['api_version'] ) ? $shopify_settings['api_version'] : '2022-04';

		$shopify_url = 'https://' . $shopify_settings['shop_url'] . '/admin/api/' . $api_version . '/products.json';

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

		if ( isset( $details->product->id ) && ! empty( $details->product->id ) ) {
			$momowsw->efn->momo_wsw_check_and_insert_variants_image( $export_data_variants, $details->product, 'insert' );
			$momowsw->efn->momo_wsw_check_and_assign_variants_id( $export_data_variants, $details->product, 'insert' );
			$momowsw->efn->momo_wsw_check_and_apply_single_variant( $product_id, $details->product, 'insert' );
			update_post_meta( $product_id, 'momowsw_product_id', $details->product->id );
			return $details->product->id;
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
				$msg = esc_html__( 'Something went wrong while exporting product. Please check shopify if product have been exported.', 'momowsw' );
			}
			$logs = array(
				'export' => array(
					'time' => current_time( 'Y-m-d H:i:s' ),
					'type' => 'error',
					'msg'  => $msg,
				),
			);
			$momowsw->logs->momo_wsw_save_logs( 'cron', $logs );
		}
	}
}

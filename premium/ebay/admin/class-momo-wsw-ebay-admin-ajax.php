<?php
/**
 * MoMo WSW - Amin AJAX functions
 *
 * @package momowsw
 * @author MoMo Themes
 * @since v1.4.0
 */
class MoMo_WSW_Ebay_Admin_Ajax {
	/**
	 * Constructor
	 */
	public function __construct() {
		$ajax_events = array(
			'momowsw_sync_single_product_to_ebay'       => 'momowsw_sync_single_product_to_ebay', // One.
			'momowsw_sync_generate_ebay_authorization_code' => 'momowsw_sync_generate_ebay_authorization_code', // Two.
			'momowsw_reset_ebay_keys_transient'         => 'momowsw_reset_ebay_keys_transient', // Three.
			'momowsw_create_and_save_merchant_location' => 'momowsw_create_and_save_merchant_location', // Four.
			'momowsw_check_and_save_old_location'       => 'momowsw_check_and_save_old_location', // Five.
			'momowsw_ebay_fetch_by_sku'                 => 'momowsw_ebay_fetch_by_sku', //Six.
		);
		foreach ( $ajax_events as $ajax_event => $class ) {
			add_action( 'wp_ajax_' . $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_' . $ajax_event, array( $this, $class ) );
		}
	}
	/**
	 * Sync woo product to eBay ( One )
	 */
	public function momowsw_sync_single_product_to_ebay() {
		global $momowsw;
		$shopify_settings = get_option( 'momo_wsw_settings' );
		$res              = check_ajax_referer( 'momowsw_security_key', 'security' );
		if ( isset( $_POST['action'] ) && 'momowsw_sync_single_product_to_ebay' !== $_POST['action'] ) {
			return;
		}
		if ( ! $momowsw->premium->eapi->momowsw_check_api_credentials() ) {
			return;
		}
		$product_id = isset( $_POST['product_id'] ) ? sanitize_text_field( wp_unslash( $_POST['product_id'] ) ) : '';
		$type       = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
		if ( empty( $product_id ) ) {
			return;
		}
		$export_settings         = get_option( 'momo_wsw_es_export' );
		$enable_product_export   = $momowsw->fn->momo_wsw_return_option_yesno( $export_settings, 'enable_product_export' );
		$export_product_variants = $momowsw->fn->momo_wsw_return_option_yesno( $export_settings, 'export_product_variants' );
		$export_product_tags     = $momowsw->fn->momo_wsw_return_option_yesno( $export_settings, 'export_product_tags' );
		$product_status          = isset( $export_settings['product_status'] ) ? $export_settings['product_status'] : 'active';
		$args                    = array(
			'product_id'              => $product_id,
			'export_product_variants' => $export_product_variants,
			'export_product_tags'     => $export_product_tags,
			'product_status'          => $product_status,
			'type'                    => $type,
		);

		$export_data = $momowsw->premium->ebayfn->momo_wsw_prepare_product_to_export( $args );
		$response    = $momowsw->premium->eapi->momowsw_export_product_to_ebay( $export_data );
		if ( 204 === $response['status'] ) {
			echo wp_json_encode(
				array(
					'status' => 'good',
					'msg'    => esc_html__( 'Product exported successfully.', 'momowsw' ),
				)
			);
			exit;
		} else {
			echo wp_json_encode(
				array(
					'status' => 'errror',
					'msg'    => $response['message'],
				)
			);
			exit;
		}
	}
	/**
	 * Generate eBay authorization code
	 */
	public function momowsw_sync_generate_ebay_authorization_code() {
		global $momowsw;
		$shopify_settings = get_option( 'momo_wsw_settings' );
		$res              = check_ajax_referer( 'momowsw_security_key', 'security' );
		if ( isset( $_POST['action'] ) && 'momowsw_sync_generate_ebay_authorization_code' !== $_POST['action'] ) {
			return;
		}
		if ( ! $momowsw->premium->eapi->momowsw_check_api_credentials() ) {
			return;
		}
		$momowsw->premium->eapi->momowsw_generate_authorization_code();
	}
	/**
	 * Reset transient if new Authorization Code is requested
	 */
	public function momowsw_reset_ebay_keys_transient() {
		$res = check_ajax_referer( 'momowsw_security_key', 'security' );
		if ( isset( $_POST['action'] ) && 'momowsw_reset_ebay_keys_transient' !== $_POST['action'] ) {
			return;
		}
		delete_transient( 'mommowsw_ebay_access_token' );
		delete_transient( 'mommowsw_ebay_refresh_token' );
		echo wp_json_encode(
			array(
				'status'  => 'good',
				'message' => esc_html__( 'Transient deleted successfully', 'momowsw' ),
			)
		);
		exit;
	}
	/**
	 * Create abd save merchant location
	 */
	public function momowsw_create_and_save_merchant_location() {
		global $momowsw;
		$res = check_ajax_referer( 'momowsw_security_key', 'security' );
		if ( isset( $_POST['action'] ) && 'momowsw_create_and_save_merchant_location' !== $_POST['action'] ) {
			return;
		}
		$data      = isset( $_POST['data'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['data'] ) ) : array();
		$ebay_args = array(
			'location'                      => array(
				'address' => array(
					'addressLine1' => $data['address_1'],
					'addressLine2' => $data['address_2'],
					'city'         => $data['city'],
					'country'      => $data['country'],
					'county'       => $data['county'],
					'postalCode'   => $data['postal_code'],
				),
			),
			'locationAdditionalInformation' => $data['information'],
			'locationTypes'                 => array(
				$data['store_type'],
			),
			'locationWebUrl'                => $data['website'],
			'name'                          => $data['name'],
			'phone'                         => $data['phone'],
		);

		$merchant_key = $data['unique_id'];
		$endpoint     = 'sell/inventory/v1/location/' . $merchant_key;
		$response     = $momowsw->premium->eapi->momo_wsw_run_rest_api( 'POST', $endpoint, '', $ebay_args );
		if ( isset( $response['status'] ) && 409 === (int) $response['status'] ) {
			echo wp_json_encode(
				array(
					'status'  => 'error',
					'message' => sprintf( esc_html__( 'Location ID ( %s ) already exist. Please use Save created Location ID button to use this ID', 'momowsw' ), $merchant_key ),
				)
			);
			exit;
		}
		if ( isset( $response['status'] ) && 204 === (int) $response['status'] ) {
			$ebay_location                = get_option( 'momo_wsw_ebay_location' );
			$ebay_location['location_id'] = $merchant_key;
			update_option( 'momo_wsw_ebay_location', $ebay_location );
			echo wp_json_encode(
				array(
					'status'  => 'good',
					'message' => esc_html__( 'Location created and saved success fully', 'momowsw' ),
				)
			);
			exit;
		}
		echo wp_json_encode(
			array(
				'status'  => 'error',
				'message' => esc_html__( 'Something went wrong while creating and updating merchant location', 'momowsw' ),
			)
		);
		exit;
	}
	/**
	 * Check and save old location key
	 */
	public function momowsw_check_and_save_old_location() {
		global $momowsw;
		$res = check_ajax_referer( 'momowsw_security_key', 'security' );
		if ( isset( $_POST['action'] ) && 'momowsw_check_and_save_old_location' !== $_POST['action'] ) {
			return;
		}
		$location_key = isset( $_POST['location_key'] ) ? sanitize_text_field( wp_unslash( $_POST['location_key'] ) ) : '';
		if ( empty( $location_key ) ) {
			return;
		}
		$endpoint = 'sell/inventory/v1/location/' . $location_key;
		$response = $momowsw->premium->eapi->momo_wsw_run_rest_api( 'GET', $endpoint, '', '' );
		if ( isset( $response['status'] ) && 404 === (int) $response['status'] ) {
			echo wp_json_encode(
				array(
					'status'  => 'error',
					'message' => esc_html__( 'Given Location key doesnot exists.', 'momowsw' ),
				)
			);
			exit;
		}
		if ( isset( $response['status'] ) && 200 === (int) $response['status'] ) {
			$ebay_location                = get_option( 'momo_wsw_ebay_location' );
			$ebay_location['location_id'] = $location_key;
			update_option( 'momo_wsw_ebay_location', $ebay_location );
			echo wp_json_encode(
				array(
					'status'  => 'good',
					'message' => esc_html__( 'Location key saved successfully.', 'momowsw' ),
				)
			);
			exit;
		}

	}
	/**
	 * Fetch eBay item by SKU
	 */
	public function momowsw_ebay_fetch_by_sku() {
		global $momowsw;
		$res = check_ajax_referer( 'momowsw_security_key', 'security' );
		if ( isset( $_POST['action'] ) && 'momowsw_ebay_fetch_by_sku' !== $_POST['action'] ) {
			return;
		}
		if ( ! isset( $_POST['sku_id'] ) && empty( $_POST['sku_id'] ) ) {
			return;
		}
		if ( ! $momowsw->premium->ebayfn->momowsw_check_ebay_api_credentials() ) {
			return;
		}
		$ignore_cache = true;
		$item_id      = isset( $_POST['sku_id'] ) ? sanitize_text_field( wp_unslash( $_POST['sku_id'] ) ) : '';
		$current_list = isset( $_POST['current_list'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_POST['current_list'] ) ) ) : array();
		$dnfip        = isset( $_POST['dnfip'] ) ? sanitize_text_field( wp_unslash( $_POST['dnfip'] ) ) : 'off';
		$caller       = isset( $_POST['caller'] ) ? sanitize_text_field( wp_unslash( $_POST['caller'] ) ) : 'product';

		switch ( $caller ) {
			case 'ebayitem':
				$endpoint = 'sell/inventory/v1/inventory_item/' . $item_id;
				break;
		}
		if ( empty( $endpoint ) ) {
			return;
		}
		$response = get_transient( 'momo_momowsw_single_ebay_' . $caller . '_' . $item_id );
		if ( true === $ignore_cache || false === ( $response ) || empty( $response ) ) {
			$response = $momowsw->premium->eapi->momo_wsw_run_rest_api( 'GET', $endpoint, '', '' );
			set_transient( 'momo_momowsw_single_ebay_' . $caller . '_' . $item_id, $response, 12 * HOUR_IN_SECONDS );
		}
		if ( isset( $response['code'] ) && 404 === $response['code'] ) {
			$msg = '';
			if ( isset( $response['body']->errors[0] ) ) {
				$msg = $response['body']->errors[0]->message;
			} else {
				$msg = $response['body']->errors->message;
			}
			echo wp_json_encode(
				array(
					'status' => 'bad',
					'msg'    => $msg,
				)
			);
			exit;
		}
		if ( isset( $details->$caller ) ) {
			$item        = $details->$caller;
			$date_format = get_option( 'date_format' );
			if ( (int) $item_id === (int) $item->id ) {
				if ( 'on' === $dnfip && $momowsw->fn->momowsw_check_shopify_id_exist( $item->id, $caller, true ) ) {
					echo wp_json_encode(
						array(
							'status' => 'bad',
							/* translators: %s: caller (product, page, blog) */
							'msg'    => sprintf( esc_html__( '%s already imported.', 'momowsw' ), ucfirst( $caller ) ),
						)
					);
					exit;
				}
				$current_list[] = $item_id;
				$html           = $momowsw->premium->fn->momo_generate_item_row( $item, $caller );
				echo wp_json_encode(
					array(
						'status'     => 'good',
						/* translators: %s: caller (product, page, blog) */
						'msg'        => sprintf( esc_html__( '%s fetched successfully.', 'momowsw' ), ucfirst( $caller ) ),
						/* translators: %s: caller (product, page, blog) */
						'info'       => sprintf( esc_html__( 'Fetched 1 %s successfully.', 'momowsw' ), $caller ),
						'product_id' => $item->id,
						'plist'      => implode( ',', $current_list ),
						'html'       => $html,
					)
				);
				exit;
			}
		}
		echo wp_json_encode(
			array(
				'status' => 'bad',
				/* translators: %s: caller (product, page, blog) */
				'msg'    => sprintf( esc_html__( '%s not found', 'momowsw' ), ucfirst( $caller ) ),
			)
		);
		exit;
	}
}
new MoMo_WSW_Ebay_Admin_Ajax();

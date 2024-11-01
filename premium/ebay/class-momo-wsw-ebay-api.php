<?php
/**
 * Ebay API.
 *
 * @package momowsw
 * @author MoMo Themes
 * @since v1.4.0
 */
class MoMo_WSW_Ebay_API {
	/**
	 * Check if API Settings saved.
	 */
	public function momowsw_check_api_settings_saved() {
		$ebay_settings = get_option( 'momo_wsw_ebay_sync' );

		$client_id     = isset( $ebay_settings['client_id'] ) ? $ebay_settings['client_id'] : '';
		$client_secret = isset( $ebay_settings['client_secret'] ) ? $ebay_settings['client_secret'] : '';
		if ( ! empty( $client_id ) && ! empty( $client_secret ) ) {
			return true;
		}
		return false;
	}
	/**
	 * Check API Credentials
	 */
	public function momowsw_check_api_credentials() {
		$ebay_settings = get_option( 'momo_wsw_ebay_sync' );

		$error = false;
		$msg   = '';
		if ( ! isset( $ebay_settings['client_id'] ) || empty( $ebay_settings['client_id'] ) ) {
			$error = true;
			$msg  .= '<p>' . esc_html__( 'eBay client id is empty.', 'momowsw' ) . '</p>';
		}
		if ( ! isset( $ebay_settings['client_secret'] ) || empty( $ebay_settings['client_secret'] ) ) {
			$error = true;
			$msg  .= '<p>' . esc_html__( 'eBay client secret is empty.', 'momowsw' ) . '</p>';
		}
		if ( $error ) {
			echo wp_json_encode(
				array(
					'status' => 'bad',
					'msg'    => $msg,
				)
			);
			exit;
		}
		return true;
	}
	/**
	 * Check API Credentials
	 */
	public function momowsw_check_api_credentials_bool() {
		$ebay_settings = get_option( 'momo_wsw_ebay_sync' );

		$error = false;
		if ( ! isset( $ebay_settings['client_id'] ) || empty( $ebay_settings['client_id'] ) ) {
			$error = true;
		}
		if ( ! isset( $ebay_settings['client_secret'] ) || empty( $ebay_settings['client_secret'] ) ) {
			$error = true;
		}
		if ( $error ) {
			return false;
		}
		return true;
	}
	/**
	 * Return eBay API Url.
	 */
	public function momo_wsw_get_ebay_api_url() {
		$ebay_settings  = get_option( 'momo_wsw_ebay_sync' );
		$enable_sandbox = isset( $ebay_settings['enable_sandbox'] ) ? $ebay_settings['enable_sandbox'] : 'off';
		if ( 'on' === $enable_sandbox ) {
			return 'https://api.sandbox.ebay.com/';
		} else {
			return 'https://api.ebay.com/';
		}
	}
	/**
	 * Return Authorization Code.
	 */
	public function momo_wsw_get_ebay_auth_url() {
		$ebay_settings  = get_option( 'momo_wsw_ebay_sync' );
		$enable_sandbox = isset( $ebay_settings['enable_sandbox'] ) ? $ebay_settings['enable_sandbox'] : 'off';
		if ( 'on' === $enable_sandbox ) {
			return 'https://auth.sandbox.ebay.com/';
		} else {
			return 'https://auth.ebay.com/';
		}
	}
	/**
	 * Grant Client Credentials
	 *
	 * @param string $scope Scope list.
	 */
	public function momo_wsw_grant_client_credentials( $scope = '' ) {
		global $momowsw;
		/**
		 * HTTP method:  POST
		 * URL (Sandbox): https://api.sandbox.ebay.com/identity/v1/oauth2/token
		 *
		 * HTTP headers:
		 * Content-Type = application/x-www-form-urlencoded
		 * Authorization = Basic <B64-encoded_oauth_credentials>
		 *
		 * Request body:
		 * grant_type=grant_credentials
		 * code=<authorization_code>
		 * redirect_uri=<ru_name>
		 */
		/**
		 * HTTP method:  POST
		 * URL (Sandbox): https://api.sandbox.ebay.com/identity/v1/oauth2/token
		 *
		 * HTTP headers:
		 * Content-Type = application/x-www-form-urlencoded
		 * Authorization = Basic <B64-encoded-oauth-credentials>
		 *
		 * Request body:
		 * grant_type=refresh_token
		 * refresh_token=<your-refresh-token-value>
		 * scope=<scopeList>   // a URL-encoded string of space-separated scopes
		 */
		$ignore_cache      = $momowsw->fn->momowsw_disable_cache_is_enabled();
		$transient         = 'momo_wsw_ebay_oauth_credentials';
		$ebay_settings     = get_option( 'momo_wsw_ebay_sync' );
		$oauth_credentials = $momowsw->premium->ebayfn->generate_encoded_oauth_cresentials();
		$ebay_url          = $this->momo_wsw_get_ebay_api_url();
		$endpoint          = 'identity/v1/oauth2/token';
		$ebay_url          = $ebay_url . $endpoint;

		$trans_access_token  = get_transient( 'mommowsw_ebay_access_token' );
		$trans_refresh_token = get_transient( 'mommowsw_ebay_refresh_token' );
		if ( ! empty( $trans_access_token ) ) {
			$response = array(
				'status'       => 'good',
				'access_token' => $trans_access_token,
			);
		} elseif ( ! empty( $trans_refresh_token ) ) {
			$body     = array(
				'grant_type'    => 'refresh_token',
				'refresh_token' => $trans_refresh_token,
				'scope'         => $scope,
			);
			$args     = array(
				'headers'     => array(
					'Accept'        => 'application/json',
					'Content-Type'  => 'application/x-www-form-urlencoded',
					'Authorization' => 'Basic ' . $oauth_credentials,
				),
				'httpversion' => '1.1',
				'method'      => 'POST',
				'body'        => $body,
			);
			$response = wp_remote_post( $ebay_url, $args );
			$json     = wp_remote_retrieve_body( $response );
			$details  = json_decode( $json );
			if ( ! empty( $details ) && isset( $details->access_token ) ) {
				$response = array(
					'status'       => 'good',
					'access_token' => $details->access_token,
					'expires_in'   => $details->expires_in,
				);
				set_transient( 'mommowsw_ebay_access_token', $details->access_token, $details->expires_in );
			} else {
				$response = array(
					'status' => 'bad',
				);
			}
		} else {
			$body     = array(
				'grant_type'   => 'authorization_code',
				'code'         => $ebay_settings['authorization_code'],
				'redirect_uri' => $ebay_settings['ru_name'],
			);
			$args     = array(
				'headers'     => array(
					'Accept'        => 'application/json',
					'Content-Type'  => 'application/x-www-form-urlencoded',
					'Authorization' => 'Basic ' . $oauth_credentials,
				),
				'httpversion' => '1.1',
				'method'      => 'POST',
				'body'        => $body,
			);
			$response = wp_remote_post( $ebay_url, $args );
			$json     = wp_remote_retrieve_body( $response );
			$details  = json_decode( $json );
			if ( ! empty( $details ) && isset( $details->access_token ) ) {
				$response = array(
					'status'        => 'good',
					'access_token'  => $details->access_token,
					'expires_in'    => $details->expires_in,
					'refresh_token' => $details->refresh_token,
					'rexpires_in'   => $details->refresh_token_expires_in,
				);
				set_transient( 'mommowsw_ebay_access_token', $details->access_token, $details->expires_in );
				set_transient( 'mommowsw_ebay_refresh_token', $details->refresh_token, $details->refresh_token_expires_in );
			} else {
				$response = array(
					'status' => 'bad',
				);
			}
		}
		return $response;
	}
	/**
	 * Get location details by ID.
	 *
	 * @param string $location_id Location ID.
	 */
	public function momowsw_get_ebay_location_details( $location_id ) {
		$endpoint = 'sell/inventory/v1/location/' . $location_id;
		$scope    = 'https://api.ebay.com/oauth/api_scope/sell.inventory';
		$response = $this->momo_wsw_run_rest_api( 'GET', $endpoint, '', '', $scope );
		return $response;
	}
	/**
	 * Get location details by ID.
	 */
	public function momowsw_get_all_locations() {
		$endpoint = 'sell/inventory/v1/location/';
		$scope    = 'https://api.ebay.com/oauth/api_scope/sell.inventory';
		$response = $this->momo_wsw_run_rest_api( 'GET', $endpoint, '', '', $scope );
		return $response;
	}
	/**
	 * Export Product to eBay
	 *
	 * @param array $export_data Export Data.
	 */
	public function momowsw_export_product_to_ebay( $export_data ) {
		global $momowsw;
		$response       = array();
		$endpoint       = 'sell/inventory/v1/inventory_item/';
		$sku            = $export_data['sku'];
		$woo_product_id = $export_data['product_id'];
		$endpoint       = $endpoint . $sku;
		$marketplace    = $momowsw->premium->ebayfn->momowsw_ebay_selected_marketplace();

		$image_path  = '';
		$wc_product  = new WC_Product( $woo_product_id );
		$image_id    = $wc_product->get_image_id();
		$gallery_ids = $wc_product->get_gallery_image_ids();
		if ( ! empty( $image_id ) ) {
			$image_path = wp_get_attachment_image_src( $image_id, 'full' );
		}
		$ebay_args = array(
			'availability'         => array(
				'shipToLocationAvailability' => array(
					'quantity' => $export_data['quantity'],
				),
			),
			'condition'            => $export_data['condition'],
			'packageWeightAndSize' => array(
				'weight' => array(
					'unit'  => $export_data['weight_unit'],
					'value' => $export_data['weight'],
				),
			),
			'product'              => array(
				'title'       => $export_data['title'],
				'description' => $export_data['description'],
				'brand'       => $export_data['brand'],
				'mpn'         => $export_data['mpn'],
			),
		);
		if ( isset( $image_path[0] ) ) {
			$ebay_args['product']['imageUrls'] = array(
				$image_path[0],
			);
		}
		$scope    = 'https://api.ebay.com/oauth/api_scope/sell.inventory';
		$response = $this->momo_wsw_run_rest_api( 'PUT', $endpoint, '', $ebay_args, $scope );
		if ( isset( $response['code'] ) && 204 === (int) $response['code'] ) {
			$ebay_policies  = get_option( 'momo_wsw_ebay_policies' );
			$return_id      = isset( $ebay_policies['return_policy_id'] ) ? $ebay_policies['return_policy_id'] : '';
			$fulfillment_id = isset( $ebay_policies['fulfillment_policy_id'] ) ? $ebay_policies['fulfillment_policy_id'] : '';
			$payment_id     = isset( $ebay_policies['payment_policy_id'] ) ? $ebay_policies['payment_policy_id'] : '';

			// Prepare for CreateOffer.
			$ebay_args = array(
				'sku'                 => $sku,
				'marketplaceId'       => $marketplace['ebcode'],
				'format'              => 'FIXED_PRICE',
				'availableQuantity'   => $export_data['quantity'],
				'categoryId'          => '30120',
				'listingDescription'  => $export_data['description'],
				'pricingSummary'      => array(
					'price' => array(
						'currency' => $export_data['currency'],
						'value'    => $export_data['price'],
					),
				),
				'merchantLocationKey' => $momowsw->premium->ebayfn->momowsw_ebay_merchant_location_key(),
				'listingPolicies'     => array(
					'returnPolicyId'      => $return_id,
					'fulfillmentPolicyId' => $fulfillment_id,
					'paymentPolicyId'     => $payment_id,
					'shippingPolicyId'    => $fulfillment_id,
				),
			);

			$offer_id = get_post_meta( $woo_product_id, '_momo_ebay_offer_id', true );
			/* $endpoint = 'sell/inventory/v1/offer/250260899016';
			$response = $this->momo_wsw_run_rest_api( 'DELETE', $endpoint, '', '', $scope ); */
			if ( ! empty( $offer_id ) ) {
				$endpoint = 'sell/inventory/v1/offer/' . $offer_id;
				$response = $this->momo_wsw_run_rest_api( 'PUT', $endpoint, '', $ebay_args, $scope );
				if ( isset( $response['code'] ) && 204 === (int) $response['code'] ) {
					$endpoint = 'sell/inventory/v1/offer/' . $offer_id . '/publish/';
					$response = $this->momo_wsw_run_rest_api( 'POST', $endpoint, '', '', $scope );
				}
			} else {
				$endpoint = 'sell/inventory/v1/offer';
				$response = $this->momo_wsw_run_rest_api( 'POST', $endpoint, '', $ebay_args, $scope );

				if ( isset( $response['body']->offerId ) ) {
					$offer_id = $response['body']->offerId;
					update_post_meta( $woo_product_id, '_momo_ebay_offer_id', $offer_id );
					$endpoint = 'sell/inventory/v1/offer/' . $offer_id . '/publish/';
					$response = $this->momo_wsw_run_rest_api( 'POST', $endpoint, '', '', $scope );
				}
			}
			if ( isset( $response['status'] ) && 400 === (int) $response['status'] ) {
				$errors  = $response['body']->errors;
				$message = '';
				foreach ( $errors as $error ) {
					$message .= $error->message;
				}
				$response = array(
					'status'  => 'bad',
					'message' => $message,
				);
				return $response;
			}
		}

		return $response;
	}
	/**
	 * Run plugin rest API function
	 *
	 * @param string $method Method.
	 * @param string $url Remaining url.
	 * @param string $transient Transient name.
	 * @param array  $body Body arguments.
	 */
	public function momo_wsw_run_rest_api( $method, $url, $transient, $body, $scope = '' ) {
		global $momowsw;
		$ebay_settings = get_option( 'momo_wsw_ebay_sync' );
		$ignore_cache  = $momowsw->fn->momowsw_disable_cache_is_enabled();
		$atoken        = $this->momo_wsw_grant_client_credentials( $scope );
		if ( ! isset( $atoken['access_token'] ) || empty( $atoken['access_token'] ) ) {
			$response = array(
				'status'  => 'bad',
				'message' => esc_html__( 'Unable to generate access token', 'momowsw' ),
			);
			return $response;
		}
		$access_token = $atoken['access_token'];
		$args         = array(
			'headers' => array(
				'Accept'           => 'application/json',
				'Content-Type'     => 'application/json',
				'Authorization'    => 'Bearer ' . $access_token,
				'Content-Language' => 'en-US',
			),
			'method'  => $method,
		);
		if ( ! empty( $body ) ) {
			$args['body'] = wp_json_encode( $body );
		}
		$ebay_url = $this->momo_wsw_get_ebay_api_url();
		$ebay_url = $ebay_url . $url;
		$response = 'POST' === $method ? wp_remote_post( $ebay_url, $args ) : wp_remote_request( $ebay_url, $args );
		$json     = wp_remote_retrieve_body( $response );
		$details  = json_decode( $json );

		if ( ! is_wp_error( $response ) && isset( $response['response'] ) ) {
			$response = array(
				'status'  => $response['response']['code'],
				'message' => $response['response']['message'],
				'code'    => $response['response']['code'],
				'body'    => json_decode( $response['body'] ),
			);
			return $response;
		}
		return $details;
	}
}

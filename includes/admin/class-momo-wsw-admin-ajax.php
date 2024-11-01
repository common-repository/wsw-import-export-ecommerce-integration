<?php
/**
 * MoMo WSW - Amin AJAX functions
 *
 * @package momowsw
 * @author MoMo Themes
 * @since v1.0.0
 */
class MoMo_WSW_Admin_Ajax {
	/**
	 * Constructor
	 */
	public function __construct() {
		$ajax_events = array(
			'momowsw_fetch_by_product_id'             => 'momowsw_fetch_by_product_id', // One.
			'momowsw_import_single_product'           => 'momowsw_import_single_product', // Two.
			'momowsw_fetch_all_products'              => 'momowsw_fetch_all_products', // Three.
			'momowsw_sync_single_product_to_shopify'  => 'momowsw_sync_single_product_to_shopify', // Eight.
			'momowsw_unlink_shopify_id_from_product'  => 'momowsw_unlink_shopify_id_from_product', // Nine.
			'momowsw_schedule_import_data_background' => 'momowsw_schedule_import_data_background',
			'momowsw_clear_transient'                 => 'momowsw_clear_transient',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {
			add_action( 'wp_ajax_' . $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_' . $ajax_event, array( $this, $class ) );
		}
	}
	/**
	 * Fetch Product by Product ID ( One )
	 */
	public function momowsw_fetch_by_product_id() {
		global $momowsw;
		$shopify_settings = get_option( 'momo_wsw_settings' );
		$res              = check_ajax_referer( 'momowsw_security_key', 'security' );
		if ( isset( $_POST['action'] ) && 'momowsw_fetch_by_product_id' !== $_POST['action'] ) {
			return;
		}
		if ( ! isset( $_POST['product_id'] ) && empty( $_POST['product_id'] ) ) {
			return;
		}
		$error = false;
		$msg   = '';
		if ( ! isset( $shopify_settings['shop_url'] ) || empty( $shopify_settings['shop_url'] ) ) {
			$error = true;
			$msg  .= '<p>' . esc_html__( 'Shopify shop url is empty.', 'momowsw' ) . '</p>';
		}
		if ( ! isset( $shopify_settings['access_token'] ) || empty( $shopify_settings['access_token'] ) ) {
			$error = true;
			$msg  .= '<p>' . esc_html__( 'Storefront access token is empty.', 'momowsw' ) . '</p>';
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
		$product_id   = isset( $_POST['product_id'] ) ? sanitize_text_field( wp_unslash( $_POST['product_id'] ) ) : '';
		$current_list = isset( $_POST['current_list'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_POST['current_list'] ) ) ) : array();
		$dnfip        = isset( $_POST['dnfip'] ) ? sanitize_text_field( wp_unslash( $_POST['dnfip'] ) ) : 'off';

		$access_token = $shopify_settings['access_token'];
		$api_version  = isset( $shopify_settings['api_version'] ) && ! empty( $shopify_settings['api_version'] ) ? $shopify_settings['api_version'] : '2022-04';
		$args         = array(
			'headers'     => array(
				'Accept'                 => 'application/json',
				'Content-Type'           => 'application/json',
				'X-Shopify-Access-Token' => $access_token,
			),
			'httpversion' => '1.1',
			'method'      => 'GET',
		);
		$shopify_url  = 'https://' . $shopify_settings['shop_url'] . '/admin/api/' . $api_version . '/products/' . $product_id . '.json';
		$details      = get_transient( 'momo_momowsw_single_product_' . $product_id );
		if ( false === ( $details ) || empty( $details ) ) {
			$response = wp_remote_get( $shopify_url, $args );
			$json     = wp_remote_retrieve_body( $response );
			$details  = json_decode( $json );
			set_transient( 'momo_momowsw_single_product_' . $product_id, $details, HOUR_IN_SECONDS );
		}
		if ( isset( $details->errors ) ) {
			$msg = '';
			if ( isset( $details->errors->id ) ) {
				$msg = $details->errors->id;
			} else {
				$msg = $details->errors;
			}
			echo wp_json_encode(
				array(
					'status' => 'bad',
					'msg'    => $msg,
				)
			);
			exit;
		}
		if ( isset( $details->product ) ) {
			$product     = $details->product;
			$date_format = get_option( 'date_format' );
			if ( (int) $product_id === (int) $product->id ) {
				if ( 'on' === $dnfip && $momowsw->fn->momowsw_check_shopify_id_exist( $product->id ) ) {
					echo wp_json_encode(
						array(
							'status' => 'bad',
							'msg'    => esc_html__( 'Product already imported.', 'momowsw' ),
						)
					);
					exit;
				}
				$current_list[] = $product_id;
				$html           = $momowsw->fn->momo_generate_product_row( $product );
				echo wp_json_encode(
					array(
						'status'     => 'good',
						'msg'        => esc_html__( 'Product fetched successfully.', 'momowsw' ),
						'info'       => esc_html__( 'Fetched 1 product successfully.', 'momowsw' ),
						'product_id' => $product->id,
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
				'msg'    => esc_html__( 'Product not found', 'momowsw' ),
			)
		);
		exit;
	}
	/**
	 * Import Product by Product ID ( One )
	 */
	public function momowsw_import_single_product() {
		global $momowsw;
		$shopify_settings = get_option( 'momo_wsw_settings' );
		$res              = check_ajax_referer( 'momowsw_security_key', 'security' );
		if ( isset( $_POST['action'] ) && 'momowsw_import_single_product' !== $_POST['action'] ) {
			return;
		}
		if ( ! isset( $_POST['product_id'] ) && empty( $_POST['product_id'] ) ) {
			return;
		}
		$error = false;
		$msg   = '';
		if ( ! isset( $shopify_settings['shop_url'] ) || empty( $shopify_settings['shop_url'] ) ) {
			$error = true;
			$msg  .= '<p>' . esc_html__( 'Shopify shop url is empty.', 'momowsw' ) . '</p>';
		}
		if ( ! isset( $shopify_settings['access_token'] ) || empty( $shopify_settings['access_token'] ) ) {
			$error = true;
			$msg  .= '<p>' . esc_html__( 'Storefront access token is empty.', 'momowsw' ) . '</p>';
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

		$product_id = isset( $_POST['product_id'] ) ? sanitize_text_field( wp_unslash( $_POST['product_id'] ) ) : 0;
		$catandtags = isset( $_POST['catandtags'] ) ? sanitize_text_field( wp_unslash( $_POST['catandtags'] ) ) : 'off';
		$variations = isset( $_POST['variations'] ) ? sanitize_text_field( wp_unslash( $_POST['variations'] ) ) : 'off';
		$pstatus    = isset( $_POST['pstatus'] ) ? sanitize_text_field( wp_unslash( $_POST['pstatus'] ) ) : 'published';
		$response   = $momowsw->fn->momowsw_import_shopify_product( $product_id, $catandtags, $pstatus, $variations );
		if ( $response ) {
			echo wp_json_encode(
				array(
					'status' => 'good',
					'msg'    => __( 'Product(s) imported successfully.', 'momowsw' ),
				)
			);
			exit;
		} else {
			echo wp_json_encode(
				array(
					'status' => 'bad',
					'msg'    => __( 'Import Error.', 'momowsw' ),
				)
			);
			exit;
		}
	}
	/**
	 * Fetch all Products ( Three )
	 */
	public function momowsw_fetch_all_products() {
		global $momowsw;
		$shopify_settings = get_option( 'momo_wsw_settings' );
		$res              = check_ajax_referer( 'momowsw_security_key', 'security' );
		if ( isset( $_POST['action'] ) && 'momowsw_fetch_all_products' !== $_POST['action'] ) {
			return;
		}
		if ( ! isset( $_POST['plimit'] ) && empty( $_POST['plimit'] ) ) {
			return;
		}
		$error = false;
		$msg   = '';
		if ( ! isset( $shopify_settings['shop_url'] ) || empty( $shopify_settings['shop_url'] ) ) {
			$error = true;
			$msg  .= '<p>' . esc_html__( 'Shopify shop url is empty.', 'momowsw' ) . '</p>';
		}
		if ( ! isset( $shopify_settings['access_token'] ) || empty( $shopify_settings['access_token'] ) ) {
			$error = true;
			$msg  .= '<p>' . esc_html__( 'Storefront access token is empty.', 'momowsw' ) . '</p>';
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
		$limit = isset( $_POST['plimit'] ) ? sanitize_text_field( wp_unslash( $_POST['plimit'] ) ) : '';
		$dnfip = isset( $_POST['dnfip'] ) ? sanitize_text_field( wp_unslash( $_POST['dnfip'] ) ) : 'off';

		$pagination = isset( $_POST['pagination'] ) ? sanitize_text_field( wp_unslash( $_POST['pagination'] ) ) : 'no';
		$pageinfo   = isset( $_POST['pageinfo'] ) ? sanitize_text_field( wp_unslash( $_POST['pageinfo'] ) ) : '';
		$rel        = isset( $_POST['rel'] ) ? sanitize_text_field( wp_unslash( $_POST['rel'] ) ) : 'next';

		$access_token = $shopify_settings['access_token'];
		$api_version  = isset( $shopify_settings['api_version'] ) && ! empty( $shopify_settings['api_version'] ) ? $shopify_settings['api_version'] : '2022-04';
		$args         = array(
			'headers'     => array(
				'Accept'                 => 'application/json',
				'Content-Type'           => 'application/json',
				'X-Shopify-Access-Token' => $access_token,
			),
			'httpversion' => '1.1',
			'method'      => 'GET',
		);
		$shopify_url  = 'https://' . $shopify_settings['shop_url'] . '/admin/api/' . $api_version . '/products.json?limit=' . $limit;
		if ( 'yes' === $pagination && ! empty( $pageinfo ) ) {
			$shopify_url  = 'https://' . $shopify_settings['shop_url'] . '/admin/api/' . $api_version . '/products.json?limit=' . $limit;
			$shopify_url .= '&page_info=' . $pageinfo . '; rel={' . $rel . '}';
		}
		$details = get_transient( 'momo_momowsw_all_products_' . $limit ); // Removing transient because of pagination.
		$details = '';
		if ( false === ( $details ) || empty( $details ) ) {
			$response = wp_remote_get( $shopify_url, $args );
			$json     = wp_remote_retrieve_body( $response );
			$details  = json_decode( $json );
			set_transient( 'momo_momowsw_all_products_' . $limit, $details, HOUR_IN_SECONDS );
		}
		$header     = wp_remote_retrieve_headers( $response );
		$pagination = $momowsw->fn->momo_generate_pagination_row( $header->getAll(), $limit, 'product' );
		if ( isset( $details->errors ) ) {
			$msg = '';
			if ( isset( $details->errors->id ) ) {
				$msg = $details->errors->id;
			} else {
				$msg = $details->errors;
			}
			echo wp_json_encode(
				array(
					'status' => 'bad',
					'msg'    => $msg,
				)
			);
			exit;
		}
		if ( isset( $details->products ) ) {
			$products = $details->products;
			$html     = '';
			if ( is_array( $products ) && ! empty( $products ) ) {
				$count = count( $products );
				foreach ( $products as $product ) {
					if ( 'on' === $dnfip && $momowsw->fn->momowsw_check_shopify_id_exist( $product->id ) ) {
						--$count;
						continue;
					}
					$html .= $momowsw->fn->momo_generate_product_row( $product );
				}
				echo wp_json_encode(
					array(
						'status'     => 'good',
						'msg'        => esc_html__( 'Product(s) fetched successfully.', 'momowsw' ),
						/* translators: %s: product count */
						'info'       => sprintf( esc_html__( 'Fetched %s product successfully.', 'momowsw' ), $count ),
						'product_id' => '',
						'plist'      => '',
						'html'       => $html,
						'pagination' => $pagination,
					)
				);
				exit;
			}
		}
		echo wp_json_encode(
			array(
				'status' => 'bad',
				'msg'    => esc_html__( 'Product not found', 'momowsw' ),
			)
		);
		exit;
	}
	/**
	 * Sync woo product to shopify ( Eight )
	 */
	public function momowsw_sync_single_product_to_shopify() {
		global $momowsw;
		$shopify_settings = get_option( 'momo_wsw_settings' );
		$res              = check_ajax_referer( 'momowsw_security_key', 'security' );
		if ( isset( $_POST['action'] ) && 'momowsw_sync_single_product_to_shopify' !== $_POST['action'] ) {
			return;
		}
		if ( ! $momowsw->fn->momowsw_check_api_credentials() ) {
			return;
		}
		$product_id = isset( $_POST['product_id'] ) ? sanitize_text_field( wp_unslash( $_POST['product_id'] ) ) : '';
		$type       = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
		if ( empty( $product_id ) ) {
			return;
		}
		$export_settings         = get_option( 'momo_wsw_export_settings' );
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

		$export_data          = $momowsw->efn->momo_wsw_prepare_product_to_export( $args );
		$export_data_variants = $export_data['product']['variants'];

		$access_token = isset( $shopify_settings['access_token'] ) ? $shopify_settings['access_token'] : '';
		$api_version  = isset( $shopify_settings['api_version'] ) && ! empty( $shopify_settings['api_version'] ) ? $shopify_settings['api_version'] : '2022-04';
		if ( 'insert' === $type ) {
			$shopify_url = 'https://' . $shopify_settings['shop_url'] . '/admin/api/' . $api_version . '/products.json';
		} elseif ( 'update' === $type ) {
			$shopify_id = isset( $_POST['shopify_id'] ) ? sanitize_text_field( wp_unslash( $_POST['shopify_id'] ) ) : '';
			if ( empty( $shopify_id ) ) {
				echo wp_json_encode(
					array(
						'status' => 'warning',
						'msg'    => esc_html__( 'Unable to find shopify product ID.', 'momowsw' ),
					)
				);
				exit;
			}
			$shopify_url                  = 'https://' . $shopify_settings['shop_url'] . '/admin/api/' . $api_version . '/products/' . $shopify_id . '.json';
			$export_data['product']['id'] = $shopify_id;
		} else {
			echo wp_json_encode(
				array(
					'status' => 'warning',
					'msg'    => esc_html__( 'Unable to process requested action.', 'momowsw' ),
				)
			);
			exit;
		}

		$args     = array(
			'headers'     => array(
				'Accept'                 => 'application/json',
				'Content-Type'           => 'application/json',
				'X-Shopify-Access-Token' => $access_token,
			),
			'httpversion' => '1.1',
			'method'      => 'insert' === $type ? 'POST' : 'PUT',
			'timeout'     => 90,
			'body'        => wp_json_encode( $export_data ),
		);
		$response = 'insert' === $type ? wp_remote_post( $shopify_url, $args ) : wp_remote_request( $shopify_url, $args );
		$json     = wp_remote_retrieve_body( $response );
		$details  = json_decode( $json );

		if ( isset( $details->product->id ) && ! empty( $details->product->id ) ) {
			$momowsw->efn->momo_wsw_check_and_insert_variants_image( $export_data_variants, $details->product, $type );
			if ( 'insert' === $type ) {
				update_post_meta( $product_id, 'momowsw_product_id', $details->product->id );
				$msg = esc_html__( 'Product exported to Shopify.', 'momowsw' );
			} else {
				$msg = esc_html__( 'Product updated to Shopify.', 'momowsw' );
			}
			echo wp_json_encode(
				array(
					'status'     => 'success',
					'msg'        => $msg,
					'shopify_id' => $details->product->id,
				)
			);
			exit;
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
				echo wp_json_encode(
					array(
						'status' => 'errror',
						'msg'    => is_array( $msg ) ? implode( '|', $msg ) : $msg,
					)
				);
				exit;
			} else {
				echo wp_json_encode(
					array(
						'status' => 'errror',
						'msg'    => esc_html__( 'Something went wrong while exporting product. Please check shopify if product have been exported.', 'momowsw' ),
					)
				);
				exit;
			}
		}
	}
	/**
	 * Remove Shopify ID meta from Product
	 */
	public function momowsw_unlink_shopify_id_from_product() {
		global $momowsw;
		$shopify_settings = get_option( 'momo_wsw_settings' );
		$res              = check_ajax_referer( 'momowsw_security_key', 'security' );
		if ( isset( $_POST['action'] ) && 'momowsw_unlink_shopify_id_from_product' !== $_POST['action'] ) {
			return;
		}
		$product_id = isset( $_POST['product_id'] ) ? sanitize_text_field( wp_unslash( $_POST['product_id'] ) ) : '';
		$shopify_id = isset( $_POST['shopify_id'] ) ? sanitize_text_field( wp_unslash( $_POST['shopify_id'] ) ) : '';
		delete_post_meta( $product_id, 'momowsw_product_id' );
		echo wp_json_encode(
			array(
				'status' => 'success',
				'msg'    => esc_html__( 'Shopify ID unlinked successfully.', 'momowsw' ),
			)
		);
		exit;
	}
	/**
	 * Runs import data in background
	 */
	public function momowsw_schedule_import_data_background() {
		$res = check_ajax_referer( 'momowsw_security_key', 'security' );
		if ( isset( $_POST['action'] ) && 'momowsw_schedule_import_data_background' !== $_POST['action'] ) {
			return;
		}
		$items_array = isset( $_POST['items'] ) && ! empty( $_POST['items'] ) ? wp_unslash( $_POST['items'] ) : array();
		$items       = array();
		foreach ( $items_array as $item ) {
			$items[] = isset( $item ) ? array_map( 'sanitize_text_field', wp_unslash( $item ) ) : array();
		}
		$catandtags = isset( $_POST['catandtags'] ) ? sanitize_text_field( wp_unslash( $_POST['catandtags'] ) ) : 'off';
		$variations = isset( $_POST['variations'] ) ? sanitize_text_field( wp_unslash( $_POST['variations'] ) ) : 'off';
		$pstatus    = isset( $_POST['pstatus'] ) ? sanitize_text_field( wp_unslash( $_POST['pstatus'] ) ) : 'publish';
		$caller     = isset( $_POST['caller'] ) ? sanitize_text_field( wp_unslash( $_POST['caller'] ) ) : 'product';
		$args       = array(
			'catandtags' => $catandtags,
			'variations' => $variations,
			'pstatus'    => $pstatus,
			'caller'     => $caller,
		);
		$return     = as_enqueue_async_action( 'momowsw_run_background_import_process', array( $items, $args ), 'momowsw_background_import_' . $caller );
		echo wp_json_encode(
			array(
				'status' => 'good',
				/* translators: %s: caller (product, page, blog) */
				'msg'    => sprintf( esc_html__( 'Action ID %s has been created and import is in process.', 'momowsw' ), $return ),
			)
		);
		exit;
	}

	/**
	 * Clear all transients
	 */
	public function momowsw_clear_transient() {
		$res = check_ajax_referer( 'momowsw_security_key', 'security' );
		if ( isset( $_POST['action'] ) && 'momowsw_clear_transient' !== $_POST['action'] ) {
			return;
		}
		global $wpdb;

		// Delete all transients with the 'momo_momowsw_' prefix.
		$prefix     = 'momo_momowsw_';
		$transients = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT option_name FROM $wpdb->options WHERE option_name LIKE %s",
				'_transient_' . $prefix . '%'
			)
		);

		foreach ( $transients as $transient ) {
			$key = str_replace( '_transient_', '', $transient );
			delete_transient( $key );
		}

		// Delete the 'mowsw_shopify_order_count' transient.
		delete_transient( 'mowsw_shopify_order_count' );
		echo wp_json_encode(
			array(
				'status' => 'success',
				'msg'    => esc_html__( 'Cache deleted successfully.', 'momowsw' ),
			)
		);
		exit;
	}
}
new MoMo_WSW_Admin_Ajax();

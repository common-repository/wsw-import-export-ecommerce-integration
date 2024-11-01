<?php
/**
 * MoMo WSW - Amin AJAX functions
 *
 * @package momowsw
 * @author MoMo Themes
 * @since v1.0.0
 */
class MoMo_WSW_Admin_Ajax_Premium {
	/**
	 * Constructor
	 */
	public function __construct() {
		$ajax_events = array(
			'momowsw_fetch_by_item_id'   => 'momowsw_fetch_by_item_id', // One.
			'momowsw_import_single_item' => 'momowsw_import_single_item', // Two.
			'momowsw_fetch_all_items'    => 'momowsw_fetch_all_items', // Three.
		);
		foreach ( $ajax_events as $ajax_event => $class ) {
			add_action( 'wp_ajax_' . $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_' . $ajax_event, array( $this, $class ) );
		}
	}
	/**
	 * Fetch Item by Item ID ( One )
	 */
	public function momowsw_fetch_by_item_id() {
		global $momowsw;
		$shopify_settings = get_option( 'momo_wsw_settings' );
		$res              = check_ajax_referer( 'momowsw_security_key', 'security' );
		$ignore_cache     = $momowsw->fn->momowsw_disable_cache_is_enabled();
		$ignore_cache     = true;
		if ( isset( $_POST['action'] ) && 'momowsw_fetch_by_item_id' !== $_POST['action'] ) {
			return;
		}
		if ( ! isset( $_POST['item_id'] ) && empty( $_POST['item_id'] ) ) {
			return;
		}
		if ( ! $momowsw->fn->momowsw_check_api_credentials() ) {
			return;
		}
		$item_id      = isset( $_POST['item_id'] ) ? sanitize_text_field( wp_unslash( $_POST['item_id'] ) ) : '';
		$current_list = isset( $_POST['current_list'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_POST['current_list'] ) ) ) : array();
		$dnfip        = isset( $_POST['dnfip'] ) ? sanitize_text_field( wp_unslash( $_POST['dnfip'] ) ) : 'off';
		$caller       = isset( $_POST['caller'] ) ? sanitize_text_field( wp_unslash( $_POST['caller'] ) ) : 'product';

		$access_token = $shopify_settings['access_token'];

		/** For Multi Server */
		$multistore_multiple      = get_option( 'momo_wsw_multistore_multiple_stores' );
		$enable_multiple_stores = $momowsw->fn->momo_wsw_return_option_yesno( $multistore_multiple, 'enable_multiple_stores' );
		if ( 'on' === $enable_multiple_stores ) {
			$server   = isset( $_POST['selected_server'] ) ? sanitize_text_field( wp_unslash( $_POST['selected_server'] ) ) : $shopify_settings['shop_url'];
			$selected = $momowsw->premium->multi->multistore_get_server_details( $server );

			$ignore_cache = true;

			$shopify_settings['shop_url'] = $selected['shop_url'];
			$access_token                 = $selected['access_token'];
		}
		/** For Multi Server ends */

		$api_version = isset( $shopify_settings['api_version'] ) && ! empty( $shopify_settings['api_version'] ) ? $shopify_settings['api_version'] : '2022-04';
		$args        = array(
			'headers'     => array(
				'Accept'                 => 'application/json',
				'Content-Type'           => 'application/json',
				'X-Shopify-Access-Token' => $access_token,
			),
			'httpversion' => '1.1',
			'method'      => 'GET',
		);
		$shopify_url = '';

		switch ( $caller ) {
			case 'product':
				$shopify_url = 'https://' . $shopify_settings['shop_url'] . '/admin/api/' . $api_version . '/products/' . $item_id . '.json';
				break;
			case 'page':
				$shopify_url = 'https://' . $shopify_settings['shop_url'] . '/admin/api/' . $api_version . '/pages/' . $item_id . '.json';
				break;
			case 'article':
				$shopify_url = 'https://' . $shopify_settings['shop_url'] . '/admin/api/' . $api_version . '/articles/' . $item_id . '.json';
				break;
			case 'customer':
				$shopify_url = 'https://' . $shopify_settings['shop_url'] . '/admin/api/' . $api_version . '/customers/' . $item_id . '.json';
				break;
			case 'order':
				$shopify_url = 'https://' . $shopify_settings['shop_url'] . '/admin/api/' . $api_version . '/orders/' . $item_id . '.json';
				break;
		}
		if ( empty( $shopify_url ) ) {
			return;
		}
		$details = get_transient( 'momo_momowsw_single_' . $caller . '_' . $item_id );
		if ( true === $ignore_cache || false === ( $details ) || empty( $details ) ) {
			$response = wp_remote_get( $shopify_url, $args );
			$json     = wp_remote_retrieve_body( $response );
			$details  = json_decode( $json );
			set_transient( 'momo_momowsw_single_' . $caller . '_' . $item_id, $details, 12 * HOUR_IN_SECONDS );
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
	/**
	 * Import Item by Item ID ( One )
	 */
	public function momowsw_import_single_item() {
		global $momowsw;
		$shopify_settings = get_option( 'momo_wsw_settings' );
		$res              = check_ajax_referer( 'momowsw_security_key', 'security' );
		if ( isset( $_POST['action'] ) && 'momowsw_import_single_item' !== $_POST['action'] ) {
			return;
		}
		if ( ! isset( $_POST['item_id'] ) && empty( $_POST['item_id'] ) ) {
			return;
		}
		if ( ! $momowsw->fn->momowsw_check_api_credentials() ) {
			return;
		}

		$item_id    = isset( $_POST['item_id'] ) ? sanitize_text_field( wp_unslash( $_POST['item_id'] ) ) : 0;
		$catandtags = isset( $_POST['catandtags'] ) ? sanitize_text_field( wp_unslash( $_POST['catandtags'] ) ) : 'off';
		$variations = isset( $_POST['variations'] ) ? sanitize_text_field( wp_unslash( $_POST['variations'] ) ) : 'off';
		$pstatus    = isset( $_POST['pstatus'] ) ? sanitize_text_field( wp_unslash( $_POST['pstatus'] ) ) : 'publish';
		$caller     = isset( $_POST['caller'] ) ? sanitize_text_field( wp_unslash( $_POST['caller'] ) ) : 'product';

		/** For Multi Server */
		$multistore_multiple      = get_option( 'momo_wsw_multistore_multiple_stores' );
		$enable_multiple_stores = $momowsw->fn->momo_wsw_return_option_yesno( $multistore_multiple, 'enable_multiple_stores' );
		if ( 'on' === $enable_multiple_stores ) {
			$selected_server = isset( $_POST['selected_server'] ) ? sanitize_text_field( wp_unslash( $_POST['selected_server'] ) ) : $shopify_settings['shop_url'];
			$response        = $momowsw->premium->fn->momowsw_import_shopify_item( $item_id, $catandtags, $pstatus, $variations, $caller, $selected_server );
		} else {
			$response = $momowsw->premium->fn->momowsw_import_shopify_item( $item_id, $catandtags, $pstatus, $variations, $caller );
		}
		/** For Multi Server ends */
		if ( $response ) {
			echo wp_json_encode(
				array(
					'status' => 'good',
					/* translators: %s: caller (product, page, blog) */
					'msg'    => sprintf( esc_html__( '%s(s) imported successfully.', 'momowsw' ), ucfirst( $caller ) ),
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
	public function momowsw_fetch_all_items() {
		global $momowsw;
		$shopify_settings = get_option( 'momo_wsw_settings' );
		$res              = check_ajax_referer( 'momowsw_security_key', 'security' );
		$ignore_cache     = $momowsw->fn->momowsw_disable_cache_is_enabled();
		$ignore_cache     = true;
		if ( isset( $_POST['action'] ) && 'momowsw_fetch_all_items' !== $_POST['action'] ) {
			return;
		}
		if ( ! $momowsw->fn->momowsw_check_api_credentials() ) {
			return;
		}
		$limit  = isset( $_POST['limit'] ) ? sanitize_text_field( wp_unslash( $_POST['limit'] ) ) : '';
		$dnfip  = isset( $_POST['dnfip'] ) ? sanitize_text_field( wp_unslash( $_POST['dnfip'] ) ) : 'off';
		$caller = isset( $_POST['caller'] ) ? sanitize_text_field( wp_unslash( $_POST['caller'] ) ) : 'product';

		$search      = isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : false;
		$search_term = isset( $_POST['search_term'] ) ? sanitize_text_field( wp_unslash( $_POST['search_term'] ) ) : '';

		$item_status  = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : '';
		$olimit       = isset( $_POST['olimit'] ) ? sanitize_text_field( wp_unslash( $_POST['olimit'] ) ) : 50;
		$total_count  = 0;
		$current_page = isset( $_POST['current_page'] ) ? sanitize_text_field( wp_unslash( $_POST['current_page'] ) ) : 1;

		$pagination = isset( $_POST['pagination'] ) ? sanitize_text_field( wp_unslash( $_POST['pagination'] ) ) : 'no';
		$pageinfo   = isset( $_POST['pageinfo'] ) ? sanitize_text_field( wp_unslash( $_POST['pageinfo'] ) ) : '';
		$rel        = isset( $_POST['rel'] ) ? sanitize_text_field( wp_unslash( $_POST['rel'] ) ) : 'next';

		$access_token = $shopify_settings['access_token'];
		$api_version  = isset( $shopify_settings['api_version'] ) && ! empty( $shopify_settings['api_version'] ) ? $shopify_settings['api_version'] : '2022-04';

		/** For Multi Server */
		$multistore_multiple      = get_option( 'momo_wsw_multistore_multiple_stores' );
		$enable_multiple_stores = $momowsw->fn->momo_wsw_return_option_yesno( $multistore_multiple, 'enable_multiple_stores' );
		if ( 'on' === $enable_multiple_stores ) {
			$server   = isset( $_POST['selected_server'] ) ? sanitize_text_field( wp_unslash( $_POST['selected_server'] ) ) : $shopify_settings['shop_url'];
			$selected = $momowsw->premium->multi->multistore_get_server_details( $server );

			$ignore_cache = true;

			$shopify_settings['shop_url'] = $selected['shop_url'];
			$access_token                 = $selected['access_token'];
		}
		/** For Multi Server ends */
		$args        = array(
			'headers'     => array(
				'Accept'                 => 'application/json',
				'Content-Type'           => 'application/json',
				'X-Shopify-Access-Token' => $access_token,
			),
			'httpversion' => '1.1',
			'method'      => 'GET',
		);
		$shopify_url = '';
		switch ( $caller ) {
			case 'product':
				$shopify_url = 'https://' . $shopify_settings['shop_url'] . '/admin/api/' . $api_version . '/products.json?limit=' . $limit;
				if ( 'yes' === $pagination && ! empty( $pageinfo ) ) {
					$shopify_url  = 'https://' . $shopify_settings['shop_url'] . '/admin/api/' . $api_version . '/products.json?limit=' . $olimit;
					$shopify_url .= '&page_info=' . $pageinfo . '; rel={' . $rel . '}';
				}
				break;
			case 'page':
				$shopify_url = 'https://' . $shopify_settings['shop_url'] . '/admin/api/' . $api_version . '/pages.json';
				break;
			case 'article':
				$shopify_url = 'https://' . $shopify_settings['shop_url'] . '/admin/api/' . $api_version . '/articles.json';
				break;
			case 'customer':
				if ( $search ) {
					$shopify_url = 'https://' . $shopify_settings['shop_url'] . '/admin/api/' . $api_version . '/customers/search.json?query=' . $search_term;
				} else {
					$shopify_url = 'https://' . $shopify_settings['shop_url'] . '/admin/api/' . $api_version . '/customers.json';
				}
				break;
			case 'order':
				if ( 'all' === $item_status ) {
					$status = 'any';
				} elseif ( 'published' === $item_status ) {
					$status = 'closed';
				} elseif ( 'unpublished' === $item_status ) {
					$status = 'open';
				}
				$shopify_url = 'https://' . $shopify_settings['shop_url'] . '/admin/api/' . $api_version . '/orders.json?status=' . $status . '&limit=' . $olimit;
				if ( 'yes' === $pagination && ! empty( $pageinfo ) ) {
					$shopify_url  = 'https://' . $shopify_settings['shop_url'] . '/admin/api/' . $api_version . '/orders.json?limit=' . $olimit;
					$shopify_url .= '&page_info=' . $pageinfo . '; rel={' . $rel . '}';
				}
				break;
		}
		if ( empty( $shopify_url ) ) {
			return;
		}
		if ( ! empty( $limit ) ) {
			$details = get_transient( 'momo_momowsw_all_' . $caller . 's_' . $limit );
			if ( true === $ignore_cache || false === ( $details ) || empty( $details ) ) {
				$response = wp_remote_get( $shopify_url, $args );
				$json     = wp_remote_retrieve_body( $response );
				$details  = json_decode( $json );
				set_transient( 'momo_momowsw_all_' . $caller . 's_' . $limit, $details, 12 * HOUR_IN_SECONDS );
			}
		} elseif ( ! empty( $search_term ) ) {
			$details = get_transient( 'momo_momowsw_all_' . $caller . 's_' . $search_term );
			if ( true === $ignore_cache || false === ( $details ) || empty( $details ) ) {
				$response = wp_remote_get( $shopify_url, $args );
				$json     = wp_remote_retrieve_body( $response );
				$details  = json_decode( $json );
				set_transient( 'momo_momowsw_all_' . $caller . 's_' . $search_term, $details, 12 * HOUR_IN_SECONDS );
			}
		} else {
			$details = get_transient( 'momo_momowsw_all_' . $caller . 's_' );
			if ( true === $ignore_cache || false === ( $details ) || empty( $details ) ) {
				$response = wp_remote_get( $shopify_url, $args );
				$json     = wp_remote_retrieve_body( $response );
				$details  = json_decode( $json );
				set_transient( 'momo_momowsw_all_' . $caller . 's_', $details, 12 * HOUR_IN_SECONDS );
			}
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
		$callers = $caller . 's';
		if ( isset( $details->$callers ) ) {
			$items = $details->$callers;
			$html  = '';
			if ( is_array( $items ) && ! empty( $items ) ) {
				$count = count( $items );
				foreach ( $items as $item ) {
					if ( 'on' === $dnfip && $momowsw->fn->momowsw_check_shopify_id_exist( $item->id ) ) {
						--$count;
						continue;
					}
					$html .= $momowsw->premium->fn->momo_generate_item_row( $item, $caller );
				}
				// Currently for orders only.
				if ( 'order' === $caller ) {
					$header     = wp_remote_retrieve_headers( $response );
					$pagination = $momowsw->fn->momo_generate_pagination_row( $header->getAll(), $olimit, $caller );
				}
				echo wp_json_encode(
					array(
						'status'     => 'good',
						/* translators: %s: item */
						'msg'        => sprintf( esc_html__( '%s(s) fetched successfully.', 'momowsw' ), ucfirst( $caller ) ),
						/* translators: %1$s: item count, %2$s: caller */
						'info'       => sprintf( esc_html__( 'Fetched %1$s %2$s successfully.', 'momowsw' ), $count, $caller ),
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
				/* translators: %s: caller */
				'msg'    => sprintf( esc_html__( '%s not found', 'momowsw' ), ucfirst( $caller ) ),
			)
		);
		exit;
	}
}
new MoMo_WSW_Admin_Ajax_Premium();

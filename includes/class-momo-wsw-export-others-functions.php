<?php
/**
 * Shopify Export functions for Other Post types
 *
 * @package momowsw
 * @author MoMo Themes
 * @since v1.1.0
 */
class MoMo_WSW_Export_Others_Functions {
	/**
	 * Return base64 encode image.
	 *
	 * @param string $path path to image.
	 */
	public function momo_wsw_encode_image_from_url( $path ) {
		$image = file_get_contents( $path ); // phpcs:ignore Reading local file.
		$finfo = new finfo( FILEINFO_MIME_TYPE );
		$type  = $finfo->buffer( $image );
		return base64_encode( $image ); // phpcs:ignore For image encoding for shopify.
	}
	/**
	 * Retrieves the content of a Gutenberg page by parsing the content into an array of blocks.
	 *
	 * @param int $post_id The ID of the post.
	 * @return array The array of blocks representing the parsed content.
	 */
	public function momowsw_get_gutenberg_page_content( $post_id ) {
		$content = get_post_field( 'post_content', $post_id );
		// Parse the content into an array of blocks.
		$blocks = parse_blocks( $content );
		return $blocks;
	}
	/**
	 * A function to extract content from Gutenberg blocks.
	 *
	 * @param array $blocks The array of Gutenberg blocks.
	 * @return string The extracted content.
	 */
	public function momowsw_extract_gutenberg_content( $blocks ) {
		$html = '';
		foreach ( $blocks as $block ) {
			if ( isset( $block['innerHTML'] ) && ! empty( $block['innerHTML'] ) ) {
				$html .= $block['innerHTML'];
			}
			if ( isset( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
				$html .= $this->momowsw_extract_gutenberg_content( $block['innerBlocks'] );
			}
		}
		return $html;
	}
	/**
	 * Prepare product to export to shopify
	 *
	 * @param array $args Arguments.
	 */
	public function momo_wsw_prepare_others_to_export( $args ) {
		global $momowsw;
		$post_id     = $args['post_id'];
		$post_status = $args['post_status'];
		$ptype       = $args['ptype'];

		$wp_post    = get_post( $post_id );
		$title      = get_the_title( $post_id );
		$blocks     = $this->momowsw_get_gutenberg_page_content( $post_id );
		$body_html  = $this->momowsw_extract_gutenberg_content( $blocks );
		$categories = wp_strip_all_tags( get_the_category_list( ', ', '', $post_id ) );
		$tags       = wp_strip_all_tags( get_the_tag_list( ', ', '', $post_id ) );
		$image_id   = get_post_thumbnail_id( $post_id );
		if ( ! empty( $image_id ) ) {
			$image_path = wp_get_original_image_path( $image_id );
			$image_data = $this->momo_wsw_encode_image_from_url( $image_path );
		}
		if ( 'order' === $ptype ) {
			$wc_order     = wc_get_order( $post_id );
			$user_id      = $wc_order->get_user_id();
			$customer     = get_user_by( 'ID', $user_id );
			$shopify_user = $this->momowsw_check_user_have_shopify_id( $user_id );
			if ( $shopify_user ) {
				$shopify_customer_id = $shopify_user;
			} else {
				$shopify_settings = get_option( 'momo_wsw_settings' );

				$export_data = $this->momo_wsw_prepare_customer_to_export( $user_id );
				$shopify_url = 'https://' . $shopify_settings['shop_url'] . '/admin/api/' . $api_version . '/customers.json';

				$access_token = isset( $shopify_settings['access_token'] ) ? $shopify_settings['access_token'] : '';
				$api_version  = isset( $shopify_settings['api_version'] ) && ! empty( $shopify_settings['api_version'] ) ? $shopify_settings['api_version'] : '2022-04';

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

				$shopify_user = $details->customer->id;
				update_user_meta( $post_id, 'momowsw_customer_id', $details->customer->id );
			}
			$billing_address = array(
				'first_name' => $wc_order->get_billing_first_name(),
				'last_name'  => $wc_order->get_billing_last_name(),
				'company'    => $wc_order->get_billing_company(),
				'email'      => $customer->user_email,
				'phone'      => $wc_order->get_billing_phone(),
				'address_1'  => $wc_order->get_billing_address_1(),
				'address_2'  => $wc_order->get_billing_address_2(),
				'city'       => $wc_order->get_billing_city(),
				'state'      => $wc_order->get_billing_state(),
				'postcode'   => $wc_order->get_billing_postcode(),
				'country'    => $wc_order->get_billing_country(),
			);
			$shipping_address = array(
				'first_name' => $wc_order->get_shipping_first_name(),
				'last_name'  => $wc_order->get_shipping_last_name(),
				'company'    => $wc_order->get_shipping_company(),
				'email'      => $customer->user_email,
				'phone'      => $wc_order->get_shipping_phone(),
				'address_1'  => $wc_order->get_shipping_address_1(),
				'address_2'  => $wc_order->get_shipping_address_2(),
				'city'       => $wc_order->get_shipping_city(),
				'state'      => $wc_order->get_shipping_state(),
				'postcode'   => $wc_order->get_shipping_postcode(),
				'country'    => $wc_order->get_shipping_country(),
			);
			$post_data['order']['billing_address']  = $billing_address;
			$post_data['order']['default_address']  = $billing_address;
			$post_data['order']['shipping_address'] = $shipping_address;

			$items = $wc_order->get_items();

			$order_shop = array();
			foreach ( $items as $item_id => $item ) {
				$product_id         = $item->get_product_id();
				$momowsw_product_id = get_post_meta( $product_id, 'momowsw_product_id', true );
				if ( empty( $momowsw_product_id ) ) {
					$momowsw_product_id = $momowsw->premium->sync->momo_wsw_import_single_product_to_shopify( $product_id );
				}
				$variation_id = $item->get_variation_id();
				if ( ! empty( $variation_id ) ) {
					$_momo_shopify_variation_id = get_post_meta( $variation_id, '_momo_shopify_variation_id', true );
					$order_shop[] = array(
						'variant_id' => $_momo_shopify_variation_id,
						'quantity'   => $item->get_quantity(),
					);
				} else {
					$product                           = $item->get_product();
					$_momo_shopify_single_variation_id = get_post_meta( $product_id, '_momo_shopify_single_variation_id', true );

					$order_shop[] = array(
						'variant_id' => $_momo_shopify_single_variation_id,
						'quantity'   => $item->get_quantity(),
						/* 'price'      => $product->get_price(),
						'title'      => $product->get_name(), */
					);
				}
			}
			if ( ! empty( $order_shop ) ) {
				$post_data['order']['line_items']     = $order_shop;
				$post_data['order']['customer']['id'] = $shopify_customer_id;
			}
		} elseif ( 'customer' === $ptype ) {
			$post_data = $this->momo_wsw_prepare_customer_to_export( $post_id );
		} else {
			$post_data = array(
				"$ptype" => array(
					'title'      => $title,
					'body_html'  => $body_html,
					'categories' => $categories,
					'tags'       => 'on' === $export_product_tags ? array( $tags ) : '',
					'status'     => $post_status,
				),
			);
			if ( ! empty( $image_id ) ) {
				$images = array(
					'attachment' => $image_data,
				);
				$post_data[ $ptype ]['image'] = $images;
			}
		}
		return $post_data;
	}
	/**
	 * Prepare user to export to shopify
	 *
	 * @param integer $user_id User ID.
	 */
	public function momo_wsw_prepare_customer_to_export( $user_id ) {
		$user_data     = get_userdata( $user_id );
		$phone         = get_user_meta( $user_id, 'phone', true );
		$billing_phone = get_user_meta( $user_id, 'billing_phone', true );

		$customer_data = array(
			'customer' => array(
				'first_name'         => $user_data->first_name,
				'last_name'          => $user_data->last_name,
				'email'              => $user_data->user_email,
				'phone'              => preg_replace( '/[^+\d]/', '', $phone ),
				'verified_email'     => true,
				'addresses'          => array(
					array(
						'address1'   => get_user_meta( $user_id, 'billing_address_1', true ),
						'city'       => get_user_meta( $user_id, 'billing_city', true ),
						'province'   => get_user_meta( $user_id, 'billing_province', true ),
						'phone'      => preg_replace( '/[^+\d]/', '', $billing_phone ),
						'zip'        => get_user_meta( $user_id, 'billing_postcode', true ),
						'first_name' => get_user_meta( $user_id, 'billing_first_name', true ),
						'last_name'  => get_user_meta( $user_id, 'billing_last_name', true ),
						'country'    => get_user_meta( $user_id, 'billing_country', true ),
					),
				),
				'send_email_welcome' => false,
			),
		);
		return $customer_data;
	}
	/**
	 * Check user have shopify ID.
	 *
	 * @param integer $user_id User ID.
	 */
	public function momowsw_check_user_have_shopify_id( $user_id ) {
		$args  = array(
			'role'       => 'customer',
			'order'      => 'asc',
			'orderby'    => 'display_name',
			'meta_query' => array(
				array(
					'key'     => 'momowsw_customer_id',
					'compare' => 'EXISTS',
				),
			),
		);
		$query = new WP_User_Query( $args );
		$users = $query->get_results();

		if ( ! empty( $users ) ) {
			$momowsw_customer_id = get_user_meta( $users[0]->ID, 'momowsw_customer_id', true );

			return $momowsw_customer_id;
		} else {
			return false;
		}
	}
}

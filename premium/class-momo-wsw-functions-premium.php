<?php
/**
 * Shopify Imports functions
 *
 * @package momowsw
 * @author MoMo Themes
 * @since v1.0.0
 */
class MoMo_WSW_Functions_Premium {
	/**
	 * Premium Functions
	 */
	/**
	 * Generate Product table row
	 *
	 * @param Std_Object $item Item from Shopify.
	 * @param string     $caller Caller ( product, page, blog ).
	 */
	public function momo_generate_item_row( $item, $caller = 'product' ) {
		global $momowsw;
		ob_start();
		if ( 'product' === $caller ) :
			?>
		<tr class="data-item" data-item_id="<?php echo esc_attr( $item->id ); ?>" data-status="<?php echo $this->momowsw_check_shopify_id_exist( $item->id, $caller ) ? 'imported' : ''; ?>">
			<td>
				<?php
				if ( isset( $item->image->src ) && ! empty( $item->image->src ) ) {
					echo '<img src="' . esc_url( $item->image->src ) . '" height=100 width=100/>';
				}
				?>
			</td>
			<td>
				<?php echo esc_html( $item->title ); ?>
			</td>
			<td>
				<?php echo esc_html( $item->vendor ); ?>
			</td>
			<td>
				<?php echo wp_kses_post( $momowsw->fn->momowsw_custom_excerpt( $item->body_html, 65 ) ); ?>
			</td>
			<td>
				<?php echo esc_html( $item->created_at ); ?>
			</td>
			<td class="status">
				<span><?php echo $this->momowsw_check_shopify_id_exist( $item->id, $caller ) ? esc_html__( 'imported', 'momowsw' ) : '-'; ?></span>
			</td>
		</tr>
			<?php
		elseif ( 'page' === $caller ) :
			?>
			<tr class="data-item" data-item_id="<?php echo esc_attr( $item->id ); ?>" data-status="<?php echo $this->momowsw_check_shopify_id_exist( $item->id, $caller ) ? 'imported' : ''; ?>">
				<td>
					<?php echo esc_html( $item->title ); ?>
				</td>
				<td>
					<?php echo esc_html( $item->author ); ?>
				</td>
				<td>
					<?php echo wp_kses_post( $momowsw->fn->momowsw_custom_excerpt( $item->body_html, 65 ) ); ?>
				</td>
				<td>
					<?php echo esc_html( $item->created_at ); ?>
				</td>
				<td class="status">
					<span><?php echo $this->momowsw_check_shopify_id_exist( $item->id, $caller ) ? esc_html__( 'imported', 'momowsw' ) : '-'; ?></span>
				</td>
			</tr>
			<?php
		elseif ( 'article' === $caller ) :
			?>
			<tr class="data-item" data-item_id="<?php echo esc_attr( $item->id ); ?>" data-status="<?php echo $this->momowsw_check_shopify_id_exist( $item->id, $caller ) ? 'imported' : ''; ?>">
				<td>
					<?php
					if ( isset( $item->image->src ) && ! empty( $item->image->src ) ) {
						echo '<img src="' . esc_url( $item->image->src ) . '" height=100 width=100/>';
					}
					?>
				</td>
				<td>
					<?php echo esc_html( $item->title ); ?>
				</td>
				<td>
					<?php echo esc_html( $item->author ); ?>
				</td>
				<td>
					<?php echo wp_kses_post( $momowsw->fn->momowsw_custom_excerpt( $item->body_html, 65 ) ); ?>
				</td>
				<td>
					<?php echo esc_html( $item->created_at ); ?>
				</td>
				<td class="status">
					<span><?php echo $this->momowsw_check_shopify_id_exist( $item->id, $caller ) ? esc_html__( 'imported', 'momowsw' ) : '-'; ?></span>
				</td>
			</tr>
			<?php
		elseif ( 'customer' === $caller ) :
			$exist = $this->momowsw_check_shopify_email_exist( $item->email );
			?>
			<tr class="data-item" data-item_id="<?php echo esc_attr( $item->id ); ?>" data-status="<?php echo $this->momowsw_check_shopify_id_exist( $item->id, $caller ) ? 'imported' : ''; ?>">
				<td>
					<?php echo esc_html( $item->first_name . ' ' . $item->last_name ); ?>
				</td>
				<td>
					<?php echo esc_html( $item->email ); ?>
				</td>
				<td>
					<?php echo esc_html( $item->phone ); ?>
				</td>
				<td>
					<?php echo esc_html( $item->total_spent ); ?>
				</td>
				<td class="status">
					<span><?php echo $this->momowsw_check_shopify_id_exist( $item->id, $caller ) ? esc_html__( 'imported', 'momowsw' ) : '-'; ?></span>
					<span><?php echo $this->momowsw_check_shopify_email_exist( $item->email ) ? esc_html__( 'email exist', 'momowsw' ) : ''; ?></span>
				</td>
			</tr>
			<?php
		elseif ( 'order' === $caller ) :
			$shopify_shipping = $item->shipping_lines;
			$shipping         = 0.00;
			if ( ! empty( $shopify_shipping ) ) {
				foreach ( $shopify_shipping as $shipping_line ) {
					$shipping = $shipping + (float) $shipping_line->price;
				}
			}
			?>
			<tr class="data-item" data-item_id="<?php echo esc_attr( $item->id ); ?>" data-status="<?php echo $this->momowsw_check_shopify_id_exist( $item->id, $caller ) ? 'imported' : ''; ?>">
				<td class="customer-name">
					<?php if ( isset( $item->customer ) ) : ?>
						<?php echo esc_html( $item->customer->first_name . ' ' . $item->customer->last_name ); ?>
					<?php endif; ?>
				</td>
				<td>
					<table class="order-items">
				<?php
				$line_items = $item->line_items;
				foreach ( $line_items as $line_item ) {
					?>
					<tr>
						<td class="item-name">
							<?php echo esc_html( $line_item->name ); ?>
						</td>
						<td class="item-qty">
							<?php echo esc_html( $line_item->quantity ); ?>
						</td>
						<td class="item-price">
							<?php echo esc_html( $line_item->price ); ?>
						</td>
					</tr>
					<?php
				}
				?>
				<tr>
					<td colspan="2" class="item-name">
						<?php esc_html_e( 'Total Tax', 'momowsw' ); ?>
					</td>
					<td class="item-price">
						<?php echo esc_html( $item->current_total_tax ); ?>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="item-name">
						<?php esc_html_e( 'Total Shipping', 'momowsw' ); ?>
					</td>
					<td class="item-price">
						<?php echo esc_html( number_format( (float) $shipping, 2 ) ); ?>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="item-name">
						<?php esc_html_e( 'Total Discounts', 'momowsw' ); ?>
					</td>
					<td class="item-price">
						<?php echo esc_html( $item->current_total_discounts ); ?>
					</td>
				</tr>
				</table>
				</td>
				<td class="total-price">
					<?php echo esc_html( $item->total_price ); ?>
				</td>
				<td class="status">
					<span><?php echo $this->momowsw_check_shopify_id_exist( $item->id, $caller ) ? esc_html__( 'imported', 'momowsw' ) : '-'; ?></span>
				</td>
			</tr>
			<?php
		endif;
		return ob_get_clean();
	}
	/**
	 * Fetch and Import from Shopify
	 *
	 * @param int    $item_id Shopify Product ID.
	 * @param string $catandtags Enable Category and tags.
	 * @param string $pstatus Product status.
	 * @param string $variations Import Variation.
	 * @param string $caller Caller (Product, Blog, Page ).
	 * @param string $shop_url Shop URL.
	 */
	public function momowsw_import_shopify_item( $item_id, $catandtags, $pstatus, $variations, $caller = 'product', $shop_url = '' ) {
		global $momowsw;
		$shopify_settings = get_option( 'momo_wsw_settings' );
		$ignore_cache     = $momowsw->fn->momowsw_disable_cache_is_enabled();
		$ignore_cache     = true;

		$access_token = $shopify_settings['access_token'];
		/** For Multi Server */
		$multistore_multiple    = get_option( 'momo_wsw_multistore_multiple_stores' );
		$enable_multiple_stores = $momowsw->fn->momo_wsw_return_option_yesno( $multistore_multiple, 'enable_multiple_stores' );
		if ( 'on' === $enable_multiple_stores ) {
			$server   = ! empty( $shop_url ) ? $shop_url : $shopify_settings['shop_url'];
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
			return false;
		}
		if ( 'product' === $caller ) {
			if ( isset( $details->product ) ) {
				$product_arr = array();
				$product     = $details->product;

				$woo_product_id = $this->momo_momowsw_create_product( $product, $catandtags, $pstatus, $variations, $shop_url );
				return $woo_product_id;
			}
		} elseif ( 'article' === $caller ) {
			if ( isset( $details->article ) ) {
				$blog_arr = array();
				$blog     = $details->article;
				/** Blog Array Details */
				$blog_arr['momowsw_article_id'] = $blog->id;
				$blog_arr['title']              = $blog->title;
				$blog_arr['description']        = $blog->body_html;
				$blog_arr['image']              = isset( $blog->image ) ? $blog->image : '';
				$blog_arr['tags']               = $blog->tags;
				$blog_arr['handle']             = $blog->handle;
				$blog_arr['catandtags']         = $catandtags;
				$blog_arr['pstatus']            = $pstatus;

				$post_id = $this->momo_momowsw_create_blog( $blog_arr );
				return $post_id;
			}
		} elseif ( 'page' === $caller ) {
			if ( isset( $details->page ) ) {
				$page_arr = array();
				$page     = $details->page;
				/** Page Array Details */
				$page_arr['momowsw_page_id'] = $page->id;
				$page_arr['title']           = $page->title;
				$page_arr['description']     = $page->body_html;
				$page_arr['handle']          = $page->handle;
				$page_arr['pstatus']         = $pstatus;

				$page_id = $this->momo_momowsw_create_page( $page_arr );
				return $page_id;
			}
		} elseif ( 'customer' === $caller ) {
			if ( isset( $details->customer ) ) {
				$customer = $details->customer;
				$user_id  = $this->momo_momowsw_create_customer( $customer );
				return $user_id;
			}
		} elseif ( 'order' === $caller ) {
			if ( isset( $details->order ) ) {
				$order    = $details->order;
				$order_id = $this->momo_momowsw_create_order( $order, $shop_url );
				return $order_id;
			}
		}
		return false;
	}
	/**
	 * Create Product form Product Array
	 *
	 * @param object $product Shopify Product Object.
	 * @param string $catandtags Category and Tags.
	 * @param string $pstatus Publish status.
	 * @param array  $variations Variations List.
	 * @param string $shop_url Shop URL.
	 */
	public function momo_momowsw_create_product( $product, $catandtags, $pstatus, $variations, $shop_url = '' ) {
		global $momowsw;
		/** Product Array Details */
		$product_arr['momowsw_product_id'] = $product->id;
		$product_arr['title']              = $product->title;
		$product_arr['description']        = $product->body_html;
		$product_arr['image']              = isset( $product->image ) ? $product->image : '';
		$product_arr['variants']           = $product->variants;
		$product_arr['options']            = $product->options;
		$product_arr['product_type']       = $product->product_type;
		$product_arr['tags']               = $product->tags;
		$product_arr['handle']             = $product->handle;
		$product_arr['catandtags']         = $catandtags;
		$product_arr['pstatus']            = $pstatus;
		$product_arr['variations']         = $variations;

		$opt_draft = ! empty( $product_arr['pstatus'] ) ? $product_arr['pstatus'] : 'publish';
		$type      = 'product';
		$new_post  = array(
			'post_title'   => stripslashes( $product_arr['title'] ),
			'post_content' => ( ! empty( $product_arr['description'] ) ? wpautop( convert_chars( stripslashes( $product_arr['description'] ) ) ) : '' ),
			'post_status'  => $opt_draft,
			'post_type'    => $type,
			'post_name'    => $product_arr['title'],
			'post_author'  => $this->momo_get_author_id(),
		);
		if ( $this->momowsw_check_shopify_id_exist( $product_arr['momowsw_product_id'], 'product' ) ) {
			return false;
		}
		$woo_product_id = wp_insert_post( $new_post );
		update_post_meta( $woo_product_id, 'momowsw_product_id', $product_arr['momowsw_product_id'] );

		/** For Multi Server */
		$multistore_multiple      = get_option( 'momo_wsw_multistore_multiple_stores' );
		$enable_multiple_stores = $momowsw->fn->momo_wsw_return_option_yesno( $multistore_multiple, 'enable_multiple_stores' );
		if ( 'on' === $enable_multiple_stores ) {
			update_post_meta( $woo_product_id, 'momowsw_shopify_shop_url', $shop_url );
		}
		/** For Multi Server ends */
		if ( isset( $product_arr['image']->src ) ) {
			$image = $momowsw->fn->momo_momowsw_upload_product_image( $product_arr['image']->src, $product_arr['title'], $woo_product_id );
			if ( $image && is_array( $image ) ) {
				$thumbnail = set_post_thumbnail( $woo_product_id, $image[0] );
			}
		}
		update_post_meta( $woo_product_id, '_visibility', 'visible' );
		/** Categories */
		if ( isset( $product_arr['catandtags'] ) && 'on' === $product_arr['catandtags'] ) {
			if ( ! empty( $product_arr['catandtags'] ) ) {
				$momowsw->fn->momo_momowsw_product_category( $product_arr['product_type'], $woo_product_id );
			}
			$tags = isset( $product_arr['tags'] ) ? $product_arr['tags'] : '';
			if ( ! empty( $tags ) ) {
				wp_set_object_terms( $woo_product_id, explode( ',', $tags ), 'product_tag' );
			}
		}
		/** Variation and Prices */
		if ( is_array( $product_arr['variants'] ) ) {
			$variants = $product_arr['variants'];
			$variant  = $variants[0];
			if ( count( $variants ) === 1 || 'off' === $product_arr['variations'] ) {
				$product_type = 'simple';
				$wc_product   = wc_get_product( $woo_product_id );
				wp_set_object_terms( $woo_product_id, $product_type, 'product_type' );
				update_post_meta( $woo_product_id, '_price', $variant->price );
				update_post_meta( $woo_product_id, '_regular_price', $variant->price );
				update_post_meta( $woo_product_id, '_momo_shopify_variation_id', $variant->id );
				if ( isset( $variant->sku ) && ! empty( $variant->sku ) ) {
					update_post_meta( $woo_product_id, '_sku', $variant->sku );
				}
				if ( isset( $variant->weight ) && ! empty( $variant->weight ) ) {
					$weight = $variant->weight . ' ' . $variant->weight_unit;
					update_post_meta( $woo_product_id, '_weight', $weight );
				}
				if ( isset( $variant->inventory_quantity ) ) {
					$manage_stock = ( 'yes' === get_option( 'woocommerce_manage_stock' ) ) ? true : false;
					if ( $manage_stock ) {
						$wc_product->set_manage_stock( 'yes' );
						$wc_product->set_stock_quantity( $variant->inventory_quantity );
						if ( $variant->inventory_quantity ) {
							$wc_product->set_stock_status( 'instock' );
						} else {
							$wc_product->set_stock_status( 'outofstock' );
						}
					} else {
						$wc_product->set_manage_stock( 'no' );
						$wc_product->set_stock_status( 'instock' );
					}
				}
				$wc_product->save();
			} else {
				$product_type = 'variable';
				wp_remove_object_terms( $woo_product_id, 'simple', 'product_type' );
				wp_set_object_terms( $woo_product_id, $product_type, 'product_type', true );
				update_post_meta( $woo_product_id, '_sku', $product_arr['momowsw_product_id'] . '-' . $product_arr['handle'] );
				update_post_meta( $woo_product_id, '_manage_stock', 'no' );
				$wc_product       = wc_get_product( $woo_product_id );
				$variable_options = $product_arr['options'];
				$atts             = array();
				$opname           = array();
				$atts             = $momowsw->fn->momo_create_attributes( $variable_options );
				$wc_product->set_attributes( $atts );
				wp_set_object_terms( $woo_product_id, $product_type, 'product_type', true );
				$wc_product->save();
				$variants = $product_arr['variants'];
				foreach ( $variants as $variant ) {
					$variant = $momowsw->fn->momo_create_variation( $woo_product_id, $variant, $variable_options );
				}
				$wc_product->save();
			}
		}
		do_action( 'momowsw_after_product_create', $woo_product_id, $product->id );
		return $woo_product_id;
	}
	/**
	 * Create Order form Shopify Order Object
	 *
	 * @param object $order Shopify Order object.
	 * @param string $shop_url Shop URL.
	 */
	public function momo_momowsw_create_order( $order, $shop_url = '' ) {
		if ( $this->momowsw_check_shopify_id_exist( $order->id, 'order' ) ) {
			return false;
		}
		$check = true;
		$check = apply_filters( 'momowsw_check_specified_product_only', $order );

		if ( false === $check ) {
			return false;
		}
		$wc_order = wc_create_order();
		if ( isset( $order->customer ) ) :
			$shopify_customer    = $order->customer;
			$shopify_customer_id = $shopify_customer->id;
			$default_address     = $shopify_customer->default_address;
			$shipping_address    = isset( $order->shipping_address ) ? $order->shipping_address : false;
			$billing_address     = isset( $order->billing_address ) ? $order->billing_address : false;
			if ( ! $this->momowsw_check_shopify_id_exist( $shopify_customer_id, 'customer' ) ) {
				$customer_id = $this->momo_momowsw_create_customer( $order->customer );
			} else {
				$customer_id = $this->momowsw_check_shopify_id_exist( $shopify_customer_id, 'customer', true );
			}
			$address = array(
				'first_name' => $default_address->first_name,
				'last_name'  => $default_address->last_name,
				'company'    => $default_address->company,
				'email'      => $shopify_customer->email,
				'phone'      => $default_address->phone,
				'address_1'  => $default_address->address1,
				'address_2'  => $default_address->address2,
				'city'       => $default_address->city,
				'state'      => $default_address->province,
				'postcode'   => $default_address->zip,
				'country'    => $default_address->country,
			);
			$wc_order->set_customer_id( $customer_id );
			$wc_order->set_address( $address, 'billing' );
			if ( $shipping_address ) {
				$saddress = array(
					'first_name' => $shipping_address->first_name,
					'last_name'  => $shipping_address->last_name,
					'company'    => $shipping_address->company,
					'email'      => $shopify_customer->email,
					'phone'      => $shipping_address->phone,
					'address_1'  => $shipping_address->address1,
					'address_2'  => $shipping_address->address2,
					'city'       => $shipping_address->city,
					'state'      => $shipping_address->province,
					'postcode'   => $shipping_address->zip,
					'country'    => $shipping_address->country,
				);
				$wc_order->set_address( $saddress, 'shipping' );
			}
			$calculate_taxes_for = array(
				'country'  => isset( $shipping_address->country ) ? $shipping_address->country : '',
				'state'    => isset( $shipping_address->province ) ? $shipping_address->province : '',
				'postcode' => isset( $shipping_address->zip ) ? $shipping_address->zip : '',
				'city'     => isset( $shipping_address->city ) ? $shipping_address->city : '',
			);
			if ( $billing_address ) {
				$baddress = array(
					'first_name' => $billing_address->first_name,
					'last_name'  => $billing_address->last_name,
					'company'    => $billing_address->company,
					'email'      => $shopify_customer->email,
					'phone'      => $billing_address->phone,
					'address_1'  => $billing_address->address1,
					'address_2'  => $billing_address->address2,
					'city'       => $billing_address->city,
					'state'      => $billing_address->province,
					'postcode'   => $billing_address->zip,
					'country'    => $billing_address->country,
				);
				$wc_order->set_address( $baddress, 'billing' );
			} else {
				$wc_order->set_address( $address, 'billing' );
			}
		endif;
		$shopify_items = $order->line_items;
		foreach ( $shopify_items as $line_item ) {
			$args = array();

			$shopify_product_id = $line_item->product_id;

			$check = true;
			$check = apply_filters( 'momowsw_check_specified_product_single', $shopify_product_id );

			if ( false === $check ) {
				continue;
			}
			$shopify_variation_id = isset( $line_item->variant_id ) ? $line_item->variant_id : '';

			if ( ! $this->momowsw_check_shopify_id_exist( $shopify_product_id, 'product' ) ) {
				$wc_product_id = $this->momowsw_import_shopify_item( $shopify_product_id, 'on', 'publish', 'on', 'product', $shop_url );
			} else {
				$wc_product_id = $this->momowsw_check_shopify_id_exist( $shopify_product_id, 'product', true );
			}
			$wc_variation_id = '';
			if ( ! empty( $shopify_variation_id ) ) {
				if ( $this->momowsw_check_variation_id_exist( $shopify_variation_id ) ) {
					$wc_variation_id = $this->momowsw_check_variation_id_exist( $shopify_variation_id, true );
					if ( $wc_variation_id ) {
						continue;
					}
				}
			}
			$args['product_id']   = $wc_product_id;
			$args['variation_id'] = $wc_variation_id;

			$product = wc_get_product( isset( $args['variation_id'] ) && $args['variation_id'] > 0 ? $args['variation_id'] : $args['product_id'] );

			$item_id = $wc_order->add_product( $product, $line_item->quantity, $args );
			$item    = $wc_order->get_item( $item_id, false );
			$taxable = $line_item->taxable;
			if ( $taxable ) {
				$taxlines = $line_item->tax_lines;
				foreach ( $taxlines as $taxline ) {

					$args = array(
						'total'    => array(
							(float) $taxline->price,
						),
						'subtotal' => array(
							(float) $taxline->price,
						),
					);
					$item->set_total_tax( (float) $taxline->price );
					$item->set_subtotal_tax( (float) $taxline->price );
					$item->set_taxes( $args );
				}
			}
			$item->save();
		}
		$shopify_shipping = isset( $order->shipping_lines ) ? $order->shipping_lines : '';
		$shipping         = 0.00;
		if ( ! empty( $shopify_shipping ) ) {
			foreach ( $shopify_shipping as $shipping_line ) {
				$item_ship = new WC_Order_Item_Shipping();
				$item_ship->set_method_title( $shipping_line->title );
				$item_ship->set_total( $shipping_line->price );
				$wc_order->add_item( $item_ship );
			}
		}
		$shopify_discounts = $order->discount_applications;
		$discounts         = 0.00;
		if ( ! empty( $shopify_discounts ) ) {
			foreach ( $shopify_discounts as $discount_line ) {
				$dtype    = $discount_line->value_type;
				$dvalue   = $discount_line->value;
				$item_fee = new WC_Order_Item_Fee();
				$item_fee->set_name( $discount_line->title );
				if ( 'percentage' === $dtype ) {
					$discount = - ( $dvalue * $wc_order->get_subtotal() / 100 );
				} else {
					$discount = $dvalue > $wc_order->get_subtotal() ? -$wc_order->get_subtotal() : -$dvalue;
				}
				$item_fee->set_amount( $discount );
				$item_fee->set_total( $discount );
				$wc_order->add_item( $item_fee );
			}
		}
		$order_statuses = array(
			'pending'  => 'pending',
			'paid'     => 'completed',
			'voided'   => 'cancelled',
			'refunded' => 'refunded',
		);
		$shopify_status = $order->financial_status;
		$status         = isset( $order_statuses[ $shopify_status ] ) ? $order_statuses[ $shopify_status ] : $shopify_status;
		$status         = apply_filters( 'momowsw_change_order_status', $status, $wc_order );
		$wc_order->update_status( $status, esc_html__( 'Imported from Shopify', 'momowsw' ) );
		$wc_order->update_meta_data( 'momowsw_order_id', $order->id );
		/** For Multi Server */
		global $momowsw;
		$multistore_multiple    = get_option( 'momo_wsw_multistore_multiple_stores' );
		$enable_multiple_stores = $momowsw->fn->momo_wsw_return_option_yesno( $multistore_multiple, 'enable_multiple_stores' );
		if ( 'on' === $enable_multiple_stores ) {
			$wc_order->update_meta_data( 'momowsw_order_shopify_shop_url', $shop_url );
		}
		/** For Multi Server ends */
		$wc_order->save();
		$wc_order->update_taxes();
		$wc_order->calculate_totals( false );
		return $wc_order->get_id();
	}
	/**
	 * Check if Variation id exist and return wc_variation_id
	 *
	 * @param integer $shopify_variation_id Shopify Variation ID.
	 * @param boolean $return_id Return ID.
	 */
	public function momowsw_check_variation_id_exist( $shopify_variation_id, $return_id = false ) {
		// _momo_shopify_variation_id (Variation Meta Key).
		$args  = array(
			'numberposts' => -1,
			'post_type'   => 'product_variation',
			'meta_query'  => array(
				array(
					'key'     => '_momo_shopify_variation_id',
					'value'   => $shopify_variation_id,
					'compare' => '=',
				),
			),
		);
		$query = new WP_Query( $args );
		if ( $query->post_count > 0 ) {
			if ( $return_id ) {
				return $query->posts[0]->ID;
			}
			return true;
		}
		return false;
	}
	/**
	 * Check if Shopify Product already listed
	 *
	 * @param integer $shopify_id Shopify Product ID.
	 * @param string  $caller Caller type (product, page, blog).
	 * @param boolean $return_id Return ID.
	 */
	public function momowsw_check_shopify_id_exist( $shopify_id, $caller = 'product', $return_id = false ) {
		$post_type = 'product';
		$key       = 'momowsw_product_id';
		switch ( $caller ) {
			case 'product':
				$post_type = 'product';
				$key       = 'momowsw_product_id';
				break;
			case 'article':
				$post_type = 'post';
				$key       = 'momowsw_article_id';
				break;
			case 'page':
				$post_type = 'page';
				$key       = 'momowsw_page_id';
				break;
			case 'customer':
				$post_type = 'user';
				$key       = 'momowsw_customer_id';
				break;
			case 'order':
				$post_type = 'shop_order';
				$key       = 'momowsw_order_id';
		}
		if ( 'user' === $post_type ) {
			$args  = array(
				'role'       => 'customer',
				'order'      => 'asc',
				'orderby'    => 'display_name',
				'meta_query' => array(
					array(
						'key'     => $key,
						'value'   => $shopify_id,
						'compare' => '=',
					),
				),
			);
			$query = new WP_User_Query( $args );
		} else {
			$args  = array(
				'numberposts' => -1,
				'post_type'   => $post_type,
				'post_status' => array(
					'publish',
					'pending',
					'draft',
					'processing',
					'completed',
				),
				'meta_query'  => array(
					array(
						'key'     => $key,
						'value'   => $shopify_id,
						'compare' => '=',
					),
				),
			);
			$query = new WP_Query( $args );
		}
		if ( 'user' === $post_type ) {
			if ( ! empty( $query->get_results() ) ) {
				if ( $return_id ) {
					$user = $query->get_results();
					return $user[0]->ID;
				}
				return true;
			}
		} else {
			if ( $query->post_count > 0 ) {
				if ( $return_id ) {
					return $query->posts[0]->ID;
				}
				return true;
			}
		}
		return false;
	}
	/**
	 * Create Page form Page Array
	 *
	 * @param array $page_arr Page Array data.
	 */
	public function momo_momowsw_create_page( $page_arr ) {
		$opt_draft = ! empty( $page_arr['pstatus'] ) ? $page_arr['pstatus'] : 'publish';
		$type      = 'page';
		$new_post  = array(
			'post_title'   => stripslashes( $page_arr['title'] ),
			'post_content' => ( ! empty( $page_arr['description'] ) ? wpautop( convert_chars( stripslashes( $page_arr['description'] ) ) ) : '' ),
			'post_status'  => $opt_draft,
			'post_type'    => $type,
			'post_name'    => $page_arr['title'],
			'post_author'  => $this->momo_get_author_id(),
		);
		if ( $this->momowsw_check_shopify_id_exist( $page_arr['momowsw_page_id'], 'page' ) ) {
			return false;
		}
		$page_id = wp_insert_post( $new_post );
		update_post_meta( $page_id, 'momowsw_page_id', $page_arr['momowsw_page_id'] );
		return $page_id;
	}
	/**
	 * Get Current Author ID
	 */
	public function momo_get_author_id() {
		$current_user = wp_get_current_user();
		return ( ( $current_user instanceof WP_User ) ) ? $current_user->ID : 0;
	}
	/**
	 * Create Blog form Blog Array
	 *
	 * @param array $blog_arr Blog Array data.
	 */
	public function momo_momowsw_create_blog( $blog_arr ) {
		global $momowsw;
		$opt_draft = ! empty( $blog_arr['pstatus'] ) ? $blog_arr['pstatus'] : 'publish';
		$type      = 'post';
		$new_post  = array(
			'post_title'   => stripslashes( $blog_arr['title'] ),
			'post_content' => ( ! empty( $blog_arr['description'] ) ? wpautop( convert_chars( stripslashes( $blog_arr['description'] ) ) ) : '' ),
			'post_status'  => $opt_draft,
			'post_type'    => $type,
			'post_name'    => $blog_arr['title'],
			'post_author'  => $this->momo_get_author_id(),
		);
		if ( $this->momowsw_check_shopify_id_exist( $blog_arr['momowsw_article_id'], 'article' ) ) {
			return false;
		}
		$post_id = wp_insert_post( $new_post );
		update_post_meta( $post_id, 'momowsw_article_id', $blog_arr['momowsw_article_id'] );
		if ( isset( $blog_arr['image']->src ) ) {
			$image = $momowsw->fn->momo_momowsw_upload_product_image( $blog_arr['image']->src, $blog_arr['title'], $post_id );
			if ( $image && is_array( $image ) ) {
				$thumbnail = set_post_thumbnail( $post_id, $image[0] );
			}
		}
		/** Categories */
		if ( isset( $blog_arr['catandtags'] ) && 'on' === $blog_arr['catandtags'] ) {
			$tags = isset( $blog_arr['tags'] ) ? $blog_arr['tags'] : '';
			if ( ! empty( $tags ) ) {
				wp_set_object_terms( $post_id, explode( ',', $tags ), 'post_tag' );
			}
		}
		return $post_id;
	}
	/**
	 * Check if Email already exists
	 *
	 * @param string $email email address.
	 */
	public function momowsw_check_shopify_email_exist( $email ) {
		return email_exists( $email );
	}
	/**
	 * Create Page form Page Array
	 *
	 * @param object $customer Shopify customer object.
	 */
	public function momo_momowsw_create_customer( $customer ) {
		if ( $this->momowsw_check_shopify_id_exist( $customer->id, 'customer' ) ) {
			return false;
		}
		if ( $this->momowsw_check_shopify_email_exist( $customer->email ) ) {
			return false;
		}
		$default_address = $customer->default_address;

		$address = array(
			'first_name' => $default_address->first_name,
			'last_name'  => $default_address->last_name,
			'company'    => $default_address->company,
			'email'      => $customer->email,
			'phone'      => $default_address->phone,
			'address_1'  => $default_address->address1,
			'address_2'  => $default_address->address2,
			'city'       => $default_address->city,
			'state'      => $default_address->province,
			'postcode'   => $default_address->province_code,
			'country'    => $default_address->country_code,
		);

		$default_password = wp_generate_password();
		$user_id          = wc_create_new_customer( $customer->email, $customer->email, $default_password );
		update_user_meta( $user_id, 'billing_first_name', $address['first_name'] );
		update_user_meta( $user_id, 'billing_last_name', $address['last_name'] );
		update_user_meta( $user_id, 'billing_company', $address['company'] );
		update_user_meta( $user_id, 'billing_email', $address['email'] );
		update_user_meta( $user_id, 'billing_address_1', $address['address_1'] );
		update_user_meta( $user_id, 'billing_address_2', $address['address_2'] );
		update_user_meta( $user_id, 'billing_city', $address['city'] );
		update_user_meta( $user_id, 'billing_postcode', $address['postcode'] );
		update_user_meta( $user_id, 'billing_country', $address['country'] );
		update_user_meta( $user_id, 'billing_state', $address['state'] );
		update_user_meta( $user_id, 'billing_phone', $address['phone'] );
		update_user_meta( $user_id, 'shipping_first_name', $address['first_name'] );
		update_user_meta( $user_id, 'shipping_last_name', $address['last_name'] );
		update_user_meta( $user_id, 'shipping_company', $address['company'] );
		update_user_meta( $user_id, 'shipping_address_1', $address['address_1'] );
		update_user_meta( $user_id, 'shipping_address_2', $address['address_2'] );
		update_user_meta( $user_id, 'shipping_city', $address['city'] );
		update_user_meta( $user_id, 'shipping_postcode', $address['postcode'] );
		update_user_meta( $user_id, 'shipping_country', $address['country'] );
		update_user_meta( $user_id, 'shipping_state', $address['state'] );

		update_user_meta( $user_id, 'momowsw_customer_id', $customer->id );

		return $user_id;
	}
}


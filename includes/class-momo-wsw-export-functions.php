<?php
/**
 * Shopify Export functions
 *
 * @package momowsw
 * @author MoMo Themes
 * @since v1.1.0
 */
class MoMo_WSW_Export_Functions {
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
	 * Prepare product to export to shopify
	 *
	 * @param array $args Arguments.
	 */
	public function momo_wsw_prepare_product_to_export( $args ) {
		$product_id              = $args['product_id'];
		$export_product_variants = $args['export_product_variants'];
		$export_product_tags     = $args['export_product_tags'];
		$product_status          = $args['product_status'];

		$wc_product   = new WC_Product( $product_id );
		$wc_pvariable = new WC_Product_Variable( $product_id );
		$variations   = $wc_pvariable->get_available_variations();
		$title        = $wc_product->get_title();
		$body_html    = $wc_product->get_description();
		$categories   = wp_strip_all_tags( wc_get_product_category_list( $product_id ) );
		$tags         = wp_strip_all_tags( wc_get_product_tag_list( $product_id ) );
		$image_id     = $wc_product->get_image_id();
		$gallery_ids  = $wc_product->get_gallery_image_ids();
		if ( ! empty( $image_id ) ) {
			$image_path = wp_get_original_image_path( $image_id );
			$image_data = $this->momo_wsw_encode_image_from_url( $image_path );
		}
		$post_data    = array(
			'product' => array(
				'title'        => $title,
				'body_html'    => $body_html,
				'product_type' => $categories,
				'tags'         => 'on' === $export_product_tags ? array( $tags ) : '',
				'status'       => $product_status,
			),
		);
		$gallery_data = array();
		if ( ! empty( $gallery_ids ) ) {
			foreach ( $gallery_ids as $gallery_id ) {
				$gimage_path    = wp_get_original_image_path( $gallery_id );
				$gimage_data    = $this->momo_wsw_encode_image_from_url( $gimage_path );
				$gallery_data[] = array(
					'attachment' => $gimage_data,
				);
			}
		}
		if ( ! empty( $image_id ) ) {
			$images = array(
				'attachment' => $image_data,
			);
			$all_images    = array();
			$all_images[0] = $images;
			if ( ! empty( $gallery_data ) ) {
				$all_images = array_merge( $all_images, $gallery_data );
			}
			$post_data['product']['images'] = $all_images;
		} elseif ( ! empty( $gallery_data ) ) {
			$post_data['product']['images'] = $gallery_data;
		}
		if ( 'on' === $export_product_variants && ! empty( $variations ) ) {
			$attributes = $wc_product->get_attributes();
			$options    = array();
			if ( ! empty( $attributes ) ) {
				foreach ( $attributes as $attribute => $att_obj ) {
					$options[] = array(
						'name'   => $att_obj->get_name(),
						'values' => $att_obj->get_options(),
					);
				}
			}
			$variant = array();
			$counter = 1;
			foreach ( $variations as $variation ) {
				$variant[] = $this->momowsw_generate_post_variation( $product_id, $variation, $options );
			}
			if ( ! empty( $options ) ) {
				$post_data['product']['options'] = $options;
			}
			$post_data['product']['variants'] = $variant;
		} else {
			$variant = array(
				'compare_at_price' => $wc_product->get_sale_price(),
				'price'            => $wc_product->get_price(),
				'sku'              => $wc_product->get_sku(),
				'taxable'          => $wc_product->is_taxable(),
			);
			$weight  = $wc_product->get_weight();
			if ( ! empty( $weight ) ) {
				$variant['weight']      = $wc_product->get_weight();
				$variant['weight_unit'] = $weight_unit;
			}
			if ( $wc_product->managing_stock() ) {
				$variant['inventory_quantity']   = $wc_product->get_stock_quantity();
				$variant['inventory_management'] = 'shopify';
			}
			$post_data['product']['variants'] = array( $variant );
		}
		return $post_data;
	}
	/**
	 * Generate Post Variation
	 *
	 * @param integer $woo_product_id Woo Product ID.
	 * @param array   $variation Variation.
	 * @param array   $options Options.
	 */
	public function momowsw_generate_post_variation( $woo_product_id, $variation, $options ) {
		$weight_unit = get_option( 'woocommerce_weight_unit' );
		$wc_product  = new WC_Product( $woo_product_id );
		$attributes  = $variation['attributes'];
		$image_id    = isset( $variation['image_id'] ) ? $variation['image_id'] : '';
		$wc_image_id = $wc_product->get_image_id();
		$title       = '';
		$variant     = array(
			'price'   => $variation['display_price'],
			'sku'     => $variation['sku'],
			'taxable' => $wc_product->is_taxable(),
		);
		if ( true === $variation['is_in_stock'] ) {
			$variant['inventory_management'] = 'shopify';
			$variant['inventory_quantity']   = $variation['max_qty'];
		}
		$counter = 1;
		foreach ( $options as $option ) {
			$name = $option['name'];
			$name = sanitize_title( $name );
			if ( isset( $attributes[ 'attribute_' . $name ] ) ) {
				$title                          = $attributes[ 'attribute_' . $name ];
				$variant['title']               = $title;
				$variant[ 'option' . $counter ] = $attributes[ 'attribute_' . $name ];
			}
			$counter++;
		}
		$wc_variation = new WC_Product_Variation( $variation['variation_id'] );

		$variant['woo_variation_id'] = $variation['variation_id'];
		$variant['metafields']       = array(
			array(
				'namespace' => 'global',
				'key'       => 'woo_variation_id',
				'value'     => $variation['variation_id'],
				'type'      => 'number_integer',
			),
		);
		if ( isset( $variation['weight'] ) && ! empty( $variation['weight'] ) ) {
			$variant['weight']      = $variation['weight'];
			$variant['weight_unit'] = $weight_unit;
		}

		if ( $wc_variation->get_image_id( 'edit' ) ) {
			$image_path = wp_get_original_image_path( $image_id );
			$image_data = $this->momo_wsw_encode_image_from_url( $image_path );
			$images     = array(
				'attachment' => array( $image_data ),
			);

			$variant['images'] = array( $images );
		}
		return $variant;
	}
	/**
	 * Check and export variant images
	 *
	 * @param array  $export_data_variants Export ready data.
	 * @param object $shopify_product Shopify Product.
	 * @param string $type Insert ort Update.
	 */
	public function momo_wsw_check_and_insert_variants_image( $export_data_variants, $shopify_product, $type ) {
		$shopify_product_variants = $shopify_product->variants;
		$shopify_product_id       = $shopify_product->id;
		if ( is_array( $export_data_variants ) ) {
			foreach ( $export_data_variants as $variant ) {
				if ( isset( $variant['images'][0]['attachment'][0] ) && ! empty( $variant['images'][0]['attachment'][0] ) ) {
					$return = $this->insert_image_to_shopify_product( $variant['images'][0]['attachment'][0], $shopify_product_id, $type );
					if ( 'good' === $return['status'] ) {
						$return = $this->assign_image_to_variant( $return, $variant, $shopify_product_variants );
					}
				}
			}
		}
	}
	/**
	 * Check and assign variant id
	 *
	 * @param array  $export_data_variants Export ready data.
	 * @param object $shopify_product Shopify Product.
	 * @param string $type Insert ort Update.
	 */
	public function momo_wsw_check_and_assign_variants_id( $export_data_variants, $shopify_product, $type ) {
		$shopify_product_variants = $shopify_product->variants;
		$shopify_product_id       = $shopify_product->id;
		if ( is_array( $shopify_product_variants ) ) {
			foreach ( $shopify_product_variants as $variant ) {
				$woo_variantion_id = $this->momo_wsw_get_variant_metafield( $variant );
				if ( ! empty( $woo_variation_id ) ) {
					update_post_meta( $woo_variation_id, '_momo_shopify_variation_id', $variant->id );
				}
			}
		}
	}
	/**
	 * A description of the entire PHP function.
	 *
	 * @param integer $product_id Product ID.
	 * @param array   $shopify_product Shopify Product.
	 * @param string  $type Insert or Update.
	 */
	public function momo_wsw_check_and_apply_single_variant( $product_id, $shopify_product, $type ) {
		$product = wc_get_product( $product_id );
		if ( $product && ! $product->is_type( 'variable' ) ) {
			if ( isset( $shopify_product->variants[0]->id ) ) {
				update_post_meta( $product_id, '_momo_shopify_single_variation_id', $shopify_product->variants[0]->id );
			}
		}
	}
	/**
	 * Get Variant Metafield
	 *
	 * @param array $variant Shopify Variant Detail.
	 */
	public function momo_wsw_get_variant_metafield( $variant ) {
		$shopify_settings = get_option( 'momo_wsw_settings' );
		$variant_id       = $variant->id;
		$woo_variation_id = null;
		$access_token     = isset( $shopify_settings['access_token'] ) ? $shopify_settings['access_token'] : '';
		$api_version      = isset( $shopify_settings['api_version'] ) && ! empty( $shopify_settings['api_version'] ) ? $shopify_settings['api_version'] : '2022-04';
		$shopify_url      = 'https://' . $shopify_settings['shop_url'] . '/admin/api/' . $api_version . '/metafields.json?metafield[owner_id]=' . $variant_id . '&metafield[owner_resource]=variants';

		$args     = array(
			'headers'     => array(
				'Accept'                 => 'application/json',
				'Content-Type'           => 'application/json',
				'X-Shopify-Access-Token' => $access_token,
			),
			'httpversion' => '1.1',
			'method'      => 'GET',
		);
		$response = wp_remote_request( $shopify_url, $args );
		$json     = wp_remote_retrieve_body( $response );
		$details  = json_decode( $json );
		if ( isset( $details->metafields ) && is_array( $details->metafields ) ) {
			foreach ( $details->metafields as $metafields ) {
				if ( 'woo_variation_id' === $metafields->key ) {
					$woo_variation_id = $metafields->value;
					break;
				}
			}
		}
		return $woo_variation_id;
	}
	/**
	 * Assign image to variant
	 *
	 * @param array  $return Return array.
	 * @param array  $variant Variant array.
	 * @param object $shopify_product_variants Shopify Variants.
	 */
	public function assign_image_to_variant( $return, $variant, $shopify_product_variants ) {
		$shopify_settings = get_option( 'momo_wsw_settings' );
		$variant_id       = null;
		foreach ( $shopify_product_variants as $spvariant ) {
			if ( isset( $variant['option1'] ) && ! empty( $variant['option1'] ) ) {
				if ( $variant['option1'] === $spvariant->option1 ) {
					$variant_id = $spvariant->id;
					break;
				}
			}
			if ( isset( $variant['option2'] ) && ! empty( $variant['option2'] ) ) {
				if ( $variant['option2'] === $spvariant->option2 ) {
					$variant_id = $spvariant->id;
					break;
				}
			}
			if ( isset( $variant['option3'] ) && ! empty( $variant['option3'] ) ) {
				if ( $variant['option3'] === $spvariant->option3 ) {
					$variant_id = $spvariant->id;
					break;
				}
			}
		}

		if ( ! $variant_id ) {
			return;
		}
		$access_token = isset( $shopify_settings['access_token'] ) ? $shopify_settings['access_token'] : '';
		$api_version  = isset( $shopify_settings['api_version'] ) && ! empty( $shopify_settings['api_version'] ) ? $shopify_settings['api_version'] : '2022-04';
		$shopify_url  = 'https://' . $shopify_settings['shop_url'] . '/admin/api/' . $api_version . '/variants/' . $variant_id . '.json';

		$export_data = array(
			'variant' => array(
				'id'       => $variant_id,
				'image_id' => $return['id'],
			),
		);
		$args        = array(
			'headers'     => array(
				'Accept'                 => 'application/json',
				'Content-Type'           => 'application/json',
				'X-Shopify-Access-Token' => $access_token,
			),
			'httpversion' => '1.1',
			'method'      => 'PUT',
			'body'        => wp_json_encode( $export_data ),
		);
		$response    = wp_remote_request( $shopify_url, $args );
		$json        = wp_remote_retrieve_body( $response );
		$details     = json_decode( $json );
	}
	/**
	 * Inset image to shopify product ID
	 *
	 * @param string  $attachment encoded image.
	 * @param integer $shopify_product_id Shopify ID.
	 * @param string  $type Insert or Update.
	 */
	public function insert_image_to_shopify_product( $attachment, $shopify_product_id, $type ) {
		$shopify_settings = get_option( 'momo_wsw_settings' );
		$access_token     = isset( $shopify_settings['access_token'] ) ? $shopify_settings['access_token'] : '';
		$api_version      = isset( $shopify_settings['api_version'] ) && ! empty( $shopify_settings['api_version'] ) ? $shopify_settings['api_version'] : '2022-04';
		$shopify_url      = 'https://' . $shopify_settings['shop_url'] . '/admin/api/' . $api_version . '/products/' . $shopify_product_id . '/images.json';

		$export_data = array(
			'image' => array(
				'attachment' => $attachment,
			),
		);
		$args        = array(
			'headers'     => array(
				'Accept'                 => 'application/json',
				'Content-Type'           => 'application/json',
				'X-Shopify-Access-Token' => $access_token,
			),
			'httpversion' => '1.1',
			'method'      => 'POST',
			'body'        => wp_json_encode( $export_data ),
		);
		$response    = wp_remote_post( $shopify_url, $args );
		$json        = wp_remote_retrieve_body( $response );
		$details     = json_decode( $json );
		if ( $details->image->id ) {
			$return['status']   = 'good';
			$return['id']       = $details->image->id;
			$return['position'] = $details->image->position;
		} else {
			$return['status'] = 'bad';
		}
		return $return;
	}
}

<?php
/**
 * Shopify Imports functions
 *
 * @package momowsw
 * @author MoMo Themes
 * @since v1.0.0
 */
class MoMo_WSW_Functions {
	/**
	 * Returns check option check or unchecked
	 *
	 * @param array  $settings Settings array.
	 * @param string $key Option key.
	 */
	public function momo_wsw_return_check_option( $settings, $key ) {
		$option = isset( $settings[ $key ] ) ? $settings[ $key ] : 'off';
		if ( 'on' === $option ) {
			$check = 'checked="checked"';
		} else {
			$check = '';
		}
		return $check;
	}
	/**
	 * Returns check option check or unchecked
	 *
	 * @param array  $settings Settings array.
	 * @param string $key Option key.
	 */
	public function momo_wsw_return_option_yesno( $settings, $key ) {
		$option = isset( $settings[ $key ] ) ? $settings[ $key ] : 'off';
		return $option;
	}
	/**
	 * Check API Cache enabled or disabled.
	 *
	 * @return boolean
	 */
	public function momowsw_disable_cache_is_enabled() {
		$cache_settings    = get_option( 'momo_wsw_api_cache_settings' );
		$disable_api_cache = isset( $cache_settings['disable_api_cache'] ) ? $cache_settings['disable_api_cache'] : 'off';
		if ( 'on' === $disable_api_cache ) {
			return true;
		}
		return false;
	}
	/**
	 * Check if API Settings saved.
	 */
	public function momowsw_check_api_settings_saved() {
		$shopify_settings = get_option( 'momo_wsw_settings' );

		$access_token = isset( $shopify_settings['access_token'] ) ? $shopify_settings['access_token'] : '';
		if ( ! empty( $access_token ) ) {
			return true;
		}
		return false;
	}
	/**
	 * Check API Credentials
	 */
	public function momowsw_check_api_credentials() {
		$shopify_settings = get_option( 'momo_wsw_settings' );

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
		return true;
	}
	/**
	 * MoMo WSW Excerpt function
	 *
	 * @param string $string Excerpt text.
	 * @param int    $length Length.
	 */
	public function momowsw_custom_excerpt( $string, $length ) {
		if ( strlen( $string ) < $length ) {
			return $string;
		} else {
			$new = wordwrap( $string, ( $length - 2 ) );
			$new = explode( "\n", $new );

			$new = $new[0] . '...';

			return $new;
		}
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
	 * Fetch and Import from Shopify
	 *
	 * @param int    $product_id Shopify Product ID.
	 * @param string $catandtags Enable Category and tags.
	 * @param string $pstatus Product status.
	 * @param string $variations Import Variation.
	 */
	public function momowsw_import_shopify_product( $product_id, $catandtags, $pstatus, $variations ) {
		global $momowsw;
		$shopify_settings = get_option( 'momo_wsw_settings' );

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
			return false;
		}
		if ( isset( $details->product ) ) {
			$product_arr = array();
			$product     = $details->product;
			/** Product Array Details */
			$product_arr['momowsw_product_id'] = $product->id;
			$product_arr['title']              = $product->title;
			$product_arr['description']        = $product->body_html;
			$product_arr['image']              = $product->image;
			$product_arr['variants']           = $product->variants;
			$product_arr['options']            = $product->options;
			$product_arr['product_type']       = $product->product_type;
			$product_arr['tags']               = $product->tags;
			$product_arr['handle']             = $product->handle;
			$product_arr['catandtags']         = $catandtags;
			$product_arr['pstatus']            = $pstatus;
			$product_arr['variations']         = $variations;
			$product_arr['images']             = $product->images;

			$woo_product_id = $this->momo_momowsw_create_product( $product_arr );
			return $woo_product_id;
		}
		return false;
	}
	/**
	 * Create Product form Product Array
	 *
	 * @param array $product_arr Product Array data.
	 */
	public function momo_momowsw_create_product( $product_arr ) {
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
		if ( $this->momowsw_check_shopify_id_exist( $product_arr['momowsw_product_id'] ) ) {
			return false;
		}
		$woo_product_id = wp_insert_post( $new_post );
		update_post_meta( $woo_product_id, 'momowsw_product_id', $product_arr['momowsw_product_id'] );
		if ( isset( $product_arr['image']->src ) ) {
			$image = $this->momo_momowsw_upload_product_image( $product_arr['image']->src, $product_arr['title'], $woo_product_id );
			if ( $image && is_array( $image ) ) {
				$thumbnail = set_post_thumbnail( $woo_product_id, $image[0] );
			}
		}
		update_post_meta( $woo_product_id, '_visibility', 'visible' );
		/** Categories */
		if ( isset( $product_arr['catandtags'] ) && 'on' === $product_arr['catandtags'] ) {
			if ( ! empty( $product_arr['catandtags'] ) ) {
				$this->momo_momowsw_product_category( $product_arr['product_type'], $woo_product_id );
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
				$variable_options = $product_arr['options'];
				$wc_product       = wc_get_product( $woo_product_id );
				$atts             = array();
				$opname           = array();
				$atts             = $this->momo_create_attributes( $variable_options );
				$wc_product->set_attributes( $atts );
				wp_set_object_terms( $woo_product_id, $product_type, 'product_type', true );
				$wc_product->save();
				$variants = $product_arr['variants'];
				foreach ( $variants as $variant ) {
					$variant = $this->momo_create_variation( $woo_product_id, $variant, $variable_options );
				}
				$wc_product->save();
			}
		}
		/** Image gallery and variation images */
		$shopify_images = $product_arr['images'];
		$featured_id    = isset( $product_arr->image->id ) ? $product_arr->image->id : null;
		$gallery_ids    = array();
		$variant_images = array();
		if ( count( $shopify_images ) > 1 ) {
			foreach ( $shopify_images as $simage ) {
				if ( $featured_id === $simage->id ) {
					continue;
				}
				$image = $this->momo_momowsw_upload_product_image( $simage->src, $product_arr['title'], $woo_product_id );
				if ( $image && is_array( $image ) ) {
					$gallery_ids[] = $image[0];
				}
				if ( isset( $simage->variant_ids ) && is_array( $simage->variant_ids ) && count( $simage->variant_ids ) > 0 ) {
					$variant_images[ $simage->id ] = array(
						'variant_ids' => $simage->variant_ids,
						'wp_image'    => $image,
					);
				}
			}
			if ( count( $gallery_ids ) > 0 ) {
				$wc_product->set_gallery_image_ids( $gallery_ids );
				$wc_product->save();
			}
			if ( count( $variant_images ) > 0 ) {
				$wc_product       = wc_get_product( $woo_product_id );
				$wc_variation_ids = $wc_product->get_children();
				foreach ( $wc_variation_ids as $wc_variation_id ) {
					$wc_variation = new WC_Product_Variation( $wc_variation_id );
					$shopify_vid  = $wc_variation->get_meta( '_momo_shopify_variation_id' );
					foreach ( $variant_images as $vimages ) {
						$variant_ids = $vimages['variant_ids'];
						if ( in_array( $shopify_vid, $variant_ids, false ) ) {
							$image = $vimages['wp_image'];
							if ( is_array( $image ) ) {
								$wc_variation->set_image_id( $image[0] );
								$wc_variation->save();
							}
						}
					}
				}
				$wc_product->save();
			}
		}
		return $woo_product_id;
	}
	/**
	 * Create Product Attributes
	 *
	 * @param  array $options Variable Options.
	 */
	public function momo_create_attributes( $options ) {
		$attr = array();
		foreach ( $options as $opi => $opj ) {
			$wc_product_attr = new WC_Product_Attribute();
			$wc_product_attr->set_name( $opj->name );
			$wc_product_attr->set_options( $opj->values );
			$wc_product_attr->set_position( $opj->position );
			$wc_product_attr->set_visible( true );
			$wc_product_attr->set_variation( 1 );
			$attr[] = $wc_product_attr;
		}
		return $attr;
	}
	/**
	 * Create Product Variation
	 *
	 * @param integer    $woo_product_id Product ID.
	 * @param std_object $variant Variant Object.
	 * @param array      $options Variable Options.
	 */
	public function momo_create_variation( $woo_product_id, $variant, $options ) {
		$manage_stock  = ( 'yes' === get_option( 'woocommerce_manage_stock' ) ) ? true : false;
		$sale_price    = $variant->compare_at_price;
		$regular_price = $variant->price;
		$sale_price    = '';
		$wc_variation  = new WC_Product_Variation();
		$wc_variation->set_parent_id( $woo_product_id );
		$attributes = array();
		foreach ( $options as $opi => $opj ) {
			$j    = $opi + 1;
			$name = 'option' . $j;
			if ( isset( $variant->$name ) && $variant->$name ) {
				$attrname                = $this->momo_sanitize_taxonomy_name( $opj->name );
				$attributes[ $attrname ] = $variant->$name;
			}
		}
		$wc_variation->set_attributes( $attributes );
		$sku       = $variant->sku;
		$check_sku = wc_get_product_id_by_sku( $sku );
		$sku       = ( empty( $check_sku ) || 0 === (int) $check_sku ) ? $sku : '';
		$fields    = array(
			'sku'           => $sku,
			'regular_price' => $regular_price,
		);
		$wc_variation->set_manage_stock( 'no' );
		$wc_variation->set_stock_status( 'instock' );
		$wc_variation->set_status( 'publish' );
		if ( $manage_stock ) {
			$wc_variation->set_manage_stock( 'yes' );
			$wc_variation->set_stock_quantity( $variant->inventory_quantity );
			if ( $variant->inventory_quantity ) {
				$wc_variation->set_stock_status( 'instock' );
			} else {
				$wc_variation->set_stock_status( 'outofstock' );
			}
		} else {
			$wc_variation->set_manage_stock( 'no' );
			$wc_variation->set_stock_status( 'instock' );
		}
		if ( $variant->weight ) {
			$fields['weight'] = $variant->weight;
		}
		if ( $sale_price ) {
			$fields['sale_price'] = $sale_price;
		}
		foreach ( $fields as $field => $field_v ) {
			$wc_variation->{"set_$field"}( wc_clean( $field_v ) );
		}
		$wc_variation->save();
		$wc_variation_id = $wc_variation->get_id();
		update_post_meta( $wc_variation_id, '_momo_shopify_variation_id', $variant->id );
		return true;
	}
	/**
	 * Get Current Author ID
	 */
	public function momo_get_author_id() {
		$current_user = wp_get_current_user();
		return ( ( $current_user instanceof WP_User ) ) ? $current_user->ID : 0;
	}
	/**
	 * Sanitize Taxonomy Name
	 *
	 * @param string $name Name to sanitize.
	 */
	public static function momo_sanitize_taxonomy_name( $name ) {
		return strtolower( rawurlencode( wc_sanitize_taxonomy_name( $name ) ) );
	}
	/**
	 * Upload Image for Event
	 *
	 * @param string $url Image URL.
	 * @param string $product_name Product Name.
	 * @param int    $woo_product_id Woo Product ID.
	 */
	public function momo_momowsw_upload_product_image( $url, $product_name, $woo_product_id ) {
		if ( empty( $url ) ) {
			return false;
		}
		$no_extension = false;
		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		$image_url = esc_url( urldecode( $url ) );
		$tmp       = download_url( $image_url );
		preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $image_url, $matches );
		$file_array['name']     = $woo_product_id . '_shopify_image';
		$file_array['tmp_name'] = $tmp;
		if ( true === $no_extension ) {
			$file_array['name'] .= '.jpg';
		} else {
			$file_array['name'] .= '_' . basename( $matches[0] );
		}
		if ( is_wp_error( $tmp ) ) {
			unlink( $file_array['tmp_name'] );
			$file_array['tmp_name'] = '';
			return;
		}

		$desc = "Featured image for '$product_name'";
		$id   = media_handle_sideload( $file_array, $woo_product_id, $desc );
		if ( is_wp_error( $id ) ) {
			unlink( $file_array['tmp_name'] );
			return false;
		}

		$src = wp_get_attachment_url( $id );
		return array(
			0 => $id,
			1 => $src,
		);

	}
	/**
	 * Insert and save Product Category / Tags
	 *
	 * @param string $category Category name..
	 * @param int    $woo_product_id Product ID.
	 * @param string $tt Taxonomy Name.
	 */
	public function momo_momowsw_product_category( $category, $woo_product_id, $tt = 'product_cat' ) {
		$base_name = esc_attr( stripslashes( $category ) );
		$term      = term_exists( $base_name, $tt );
		if ( 0 !== $term && null !== $term ) {
			wp_set_object_terms( $woo_product_id, $base_name, $tt );
		} else {
			$slug         = str_replace( ' ', '-', $base_name );
			$new_taxonomy = wp_insert_term(
				$base_name,
				$tt,
				array(
					'slug' => $slug,
				)
			);
			if ( ! is_wp_error( $new_taxonomy ) ) {
				$term_id   = (int) $new_taxonomy['term_id'];
				$term_meta = array();
				wp_set_object_terms( $woo_product_id, $term_id, $tt, true );
			} else {
				$new_taxonomy->get_error_message();
			}
		}
	}

	/**
	 * Get Tax Terms
	 *
	 * @param Object $tax Tax Object.
	 */
	public function get_tax_terms( $tax ) {
		$terms = get_terms(
			$tax,
			array(
				'orderby'    => 'name',
				'hide_empty' => false,
			)
		);

		return ! empty( $terms ) ? $terms : false;
	}
	/**
	 * Generate Product table row
	 *
	 * @param momowsw_Object $product Product from momowsw.
	 */
	public function momo_generate_product_row( $product ) {
		ob_start();
		?>
		<tr data-product_id="<?php echo esc_attr( $product->id ); ?>" data-status="<?php echo $this->momowsw_check_shopify_id_exist( $product->id ) ? 'imported' : ''; ?>">
			<td>
				<?php
				if ( isset( $product->image->src ) && ! empty( $product->image->src ) ) {
					echo '<img src="' . esc_url( $product->image->src ) . '" height=100 width=100/>';
				}
				?>
			</td>
			<td>
				<?php echo esc_html( $product->title ); ?>
			</td>
			<td>
				<?php echo esc_html( $product->vendor ); ?>
			</td>
			<td>
				<?php echo wp_kses_post( $this->momowsw_custom_excerpt( $product->body_html, 65 ) ); ?>
			</td>
			<td>
				<?php echo esc_html( $product->created_at ); ?>
			</td>
			<td class="status">
				<span><?php echo $this->momowsw_check_shopify_id_exist( $product->id ) ? esc_html__( 'imported', 'momowsw' ) : '-'; ?></span>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}
	/**
	 * Run plugin rest API function
	 *
	 * @param string $method Method.
	 * @param string $url Remaining url.
	 * @param string $transient Transient name.
	 * @param array  $body Body arguments.
	 * @param string $shop_url Shop URL.
	 */
	public function momo_wsw_run_rest_api( $method, $url, $transient = '', $body = array(), $shop_url = '' ) {
		global $momowsw;
		$shopify_settings = get_option( 'momo_wsw_settings' );
		$ignore_cache     = $momowsw->fn->momowsw_disable_cache_is_enabled();
		$ignore_cache     = true;

		$access_token = isset( $shopify_settings['access_token'] ) ? $shopify_settings['access_token'] : '';
		$shop_url     = isset( $shopify_settings['shop_url'] ) ? $shopify_settings['shop_url'] : '';
		$api_version  = isset( $shopify_settings['api_version'] ) && ! empty( $shopify_settings['api_version'] ) ? $shopify_settings['api_version'] : '2022-07';
		if ( empty( $access_token ) || empty( $shop_url ) ) {
			$response = array(
				'status'  => 'bad',
				'message' => esc_html__( 'Empty Access Token or Shop URL, please store Access Token or Shop URL in WSW settings first.', 'momowsw' ),
			);
			return $response;
		}
		/** For Multi Server */
		if ( momowsw_fs()->is_premium() && isset( $momowsw->premium ) ) {
			$multistore_multiple    = get_option( 'momo_wsw_multistore_multiple_stores' );
			$enable_multiple_stores = $momowsw->fn->momo_wsw_return_option_yesno( $multistore_multiple, 'enable_multiple_stores' );
			if ( 'on' === $enable_multiple_stores ) {
				$server   = ! empty( $shop_url ) ? $shop_url : $shopify_settings['shop_url'];
				$selected = $momowsw->premium->multi->multistore_get_server_details( $server );

				$ignore_cache = true;

				$shopify_settings['shop_url'] = $selected['shop_url'];
				$access_token                 = $selected['access_token'];
			}
		}
		/** For Multi Server ends */
		$args         = array(
			'headers'     => array(
				'Accept'                 => 'application/json',
				'Content-Type'           => 'application/json',
				'X-Shopify-Access-Token' => $access_token,
			),
			'httpversion' => '1.1',
			'method'      => $method,
		);
		if ( ! empty( $body ) ) {
			$args['body'] = $body;
		}
		$shopify_url = 'https://' . $shopify_settings['shop_url'] . '/admin/api/' . $api_version . '/' . $url;
		if ( ! empty( $transient ) ) {
			$details = get_transient( $transient );
		}
		if ( true === $ignore_cache || false === ( $details ) || empty( $details ) ) {
			$response = 'GET' === $method ? wp_remote_get( $shopify_url, $args ) : wp_remote_request( $shopify_url, $args );
			$json     = wp_remote_retrieve_body( $response );
			$details  = json_decode( $json );
			set_transient( $transient, $details, 12 * HOUR_IN_SECONDS );
		}
		return $details;
	}
	/**
	 * Get total Products Count
	 */
	public static function momowsw_get_total_number_of_products() {
		global $momowsw;
		$method  = 'GET';
		$url     = 'products/count.json';
		$details = $momowsw->fn->momo_wsw_run_rest_api( $method, $url, 'momowsw_shopify_product_count', '' );
		return $details;
	}
	/**
	 * Generate Pagination
	 *
	 * @param array  $header Response header.
	 * @param array  $limit Page limit.
	 * @param string $caller Shopify Caller.
	 */
	public function momo_generate_pagination_row( $header, $limit, $caller = 'order' ) {
		$header_link = isset( $header['link'] ) ? $header['link'] : '';
		$link_array  = explode( ',', $header_link );
		// Create variables for the new page infos.
		$prev_link = '';
		$next_link = '';

		// Check if the $link_array variable's size is more than one.
		if ( count( $link_array ) > 1 ) {
			$prev_link = $link_array[0];
			$prev_link = $this->momowsw_str_btwn( $prev_link, '<', '>' );

			$param = wp_parse_url( $prev_link );
			parse_str( $param['query'], $prev_link );
			$prev_link = $prev_link['page_info'];

			$next_link = $link_array[1];
			$next_link = $this->momowsw_str_btwn( $next_link, '<', '>' );

			$param = wp_parse_url( $next_link );
			parse_str( $param['query'], $next_link );

			$next_link = $next_link['page_info'];
		} else {
			$rel = explode( ';', $header_link );
			$rel = $this->momowsw_str_btwn( $rel[1], '"', '"' );

			if ( 'previous' === $rel ) {
				$prev_link = $header_link;
				$prev_link = $this->momowsw_str_btwn( $prev_link, '<', '>' );

				$param = wp_parse_url( $prev_link );
				parse_str( $param['query'], $prev_link );

				$prev_link = $prev_link['page_info'];

				$next_link = '';
			} else {
				$next_link = $header_link;
				$next_link = $this->momowsw_str_btwn( $next_link, '<', '>' );

				$param = wp_parse_url( $next_link );
				parse_str( $param['query'], $next_link );

				$next_link = $next_link['page_info'];

				$prev_link = '';
			}
		}
		ob_start();
		if ( ! empty( $prev_link ) ) :
			$class = 'momowsw-pagination-link';
			if ( 'product' === $caller ) {
				$class .= '-product';
			}
			?>
			<span class="<?php echo esc_attr( $class ); ?> prev-link" data-page_info="<?php echo esc_attr( $prev_link ); ?>" data-limit="<?php echo esc_attr( $limit ); ?>" data-caller="<?php echo esc_attr( $caller ); ?>" data-rel="previous">
			<i class='bx bx-chevron-left'></i><?php esc_html_e( 'Previous', 'momowsw' ); ?>
			</span>
			<?php
		endif;
		if ( ! empty( $next_link ) ) :
			$class = 'momowsw-pagination-link';
			if ( 'product' === $caller ) {
				$class .= '-product';
			}
			?>
			<span class="<?php echo esc_attr( $class ); ?> next-link" data-page_info="<?php echo esc_attr( $next_link ); ?>" data-limit="<?php echo esc_attr( $limit ); ?>" data-caller="<?php echo esc_attr( $caller ); ?>" data-rel="next">
			<?php esc_html_e( 'Next', 'momowsw' ); ?><i class='bx bx-chevron-right'></i>
			</span>
			<?php
		endif;
		return ob_get_clean();
	}
	/**
	 * Get inbetween string
	 *
	 * @param string $string Main string.
	 * @param string $start Start strig.
	 * @param string $end End strig.
	 */
	public function momowsw_str_btwn( $string, $start, $end ) {
		$string = ' ' . $string;
		$ini    = strpos( $string, $start );
		if ( 0 === $ini ) {
			return '';
		}
		$ini += strlen( $start );
		$len  = strpos( $string, $end, $ini ) - $ini;
		return substr( $string, $ini, $len );
	}
	/**
	 * Generate upgrade button
	 */
	public static function upgrade_button() {
		ob_start();
		$url        = admin_url( 'admin.php?page=momowsw-pricing' );
		$is_premium = momowsw_fs()->is_premium();
		if ( $is_premium ) {
			return;
		}
		?>
		<a class="momowsw-upgrade-button" href="<?php echo esc_url( $url ); ?>" target="_blank"><?php esc_html_e( 'Upgrade Now', 'momowsw' ); ?></a>
		<?php
		return ob_get_contents();
	}
}

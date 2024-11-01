<?php
/**
 * Generate Feeds
 *
 * @package momowsw
 * @author MoMo Themes
 * @since v1.3.0
 */
class MoMo_WSW_Feed_Generation {
	/**
	 * Generate Google Product Feed
	 *
	 * @param string $file_path File path.
	 */
	public function momo_wsw_generate_google_product_feeds( $file_path ) {
		$products      = wc_get_products(
			array(
				'limit'  => 20,
				'status' => 'publish',
			)
		);
		$shop_name     = get_bloginfo( 'name' );
		$shop_link     = get_bloginfo( 'url' );
		$shop_desc     = get_bloginfo( 'description' );
		$feed_products = array();
		if ( ! empty( $products ) ) :
			foreach ( $products as $wc_product ) {
				$gf_product = array();
				$image      = wp_get_attachment_image_src( get_post_thumbnail_id( $wc_product->get_id() ) );
				$is_on_sale = $wc_product->is_on_sale();
				// Feed attributes.
				$gf_product['g:id']                      = $wc_product->get_slug();
				$gf_product['g:sku']                     = $wc_product->get_sku();
				$gf_product['g:title']                   = $wc_product->get_name();
				$gf_product['g:description']             = $wc_product->get_description();
				$gf_product['g:link']                    = $wc_product->get_permalink();
				$gf_product['title']                     = $wc_product->get_name();
				$gf_product['description']               = $wc_product->get_description();
				$gf_product['link']                      = $wc_product->get_permalink();
				$gf_product['g:image_link']              = isset( $image[0] ) ? $image[0] : '';
				$gf_product['g:availability']            = $wc_product->is_in_stock() ? 'in stock' : 'out of stock';
				$gf_product['g:price']                   = $wc_product->get_price() . ' ' . get_woocommerce_currency();
				$gf_product['g:google_product_category'] = wc_get_product_category_list( $wc_product->get_id(), ',' );
				$gf_product['g:brand']                   = '';
				$gf_product['g:gtin']                    = '';
				$gf_product['g:mpn']                     = '';
				$gf_product['g:identifier_exists']       = 'no';
				if ( ( '' === $gf_product['g:gtin'] ) && ( '' === $gf_product['g:mpn'] ) ) {
					$gf_product['g:identifier_exists'] = 'no';
				};
				$gf_product['g:condition'] = 'NEW';

				if ( $is_on_sale ) {
					$gf_product['g:sale_price']                = $wc_product->get_sale_price() . ' ' . get_woocommerce_currency();
					$gf_product['g:sale_price_effective_date'] = $wc_product->get_date_on_sale_from() . ' ' . $wc_product->get_date_on_sale_to();
				}

				$feed_products[] = $gf_product;
			}
		endif;
		$doc      = new DOMDocument( '1.0', 'UTF-8' );
		$xml_root = $doc->createElement( 'rss' );
		$xml_root = $doc->appendChild( $xml_root );
		$xml_root->setAttribute( 'version', '2.0' );
		$xml_root->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:g', 'http://base.google.com/ns/1.0' );

		$channel_node = $xml_root->appendChild( $doc->createElement( 'channel' ) );
		$channel_node->appendChild( $doc->createElement( 'title', $shop_name ) );
		$channel_node->appendChild( $doc->createElement( 'link', $shop_link ) );
		$channel_node->appendChild( $doc->createElement( 'description', $shop_desc ) );

		foreach ( $feed_products as $product ) {
			$item_node = $channel_node->appendChild( $doc->createElement( 'item' ) );
			foreach ( $product as $key => $value ) {
				if ( '' !== $value ) {
					if ( is_array( $product[ $key ] ) ) {
						$sub_item_node = $item_node->appendChild( $doc->createElement( $key ) );
						foreach ( $product[ $key ] as $key2 => $value2 ) {
							$sub_item_node->appendChild( $doc->createElement( $key2 ) )->appendChild( $doc->createTextNode( $value2 ) );
						}
					} else {
						$item_node->appendChild( $doc->createElement( $key ) )->appendChild( $doc->createTextNode( $value ) );
					}
				} else {
					$item_node->appendChild( $doc->createElement( $key ) );
				}
			}
		}

		$doc->formatOutput = true; // phpcs:ignore
		$doc->save( $file_path );
	}
}

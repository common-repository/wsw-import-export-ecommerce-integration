<?php
/**
 * Admin Init
 *
 * @package momowsw
 * @author MoMo Themes
 * @since v2.0.0
 */
class MoMo_WSW_Other_Auto_Sync {
	/**
	 * Import Scheduled orders from shopify
	 */
	public function momo_wsw_import_others_from_shopify( $type = 'order' ) {
		global $momowsw;
		$hook  = 'i' . $type;
		$crons = wp_get_scheduled_event( $momowsw->premium->othercron->cron_hooks[ $hook ] );
		if ( ! empty( $crons ) ) {
			$timestamp = $crons->timestamp;
			$interval  = $crons->interval;
			$from      = $timestamp - $interval;
			$details   = $this->momo_wsw_import_others_from_date( $from, $type );
		}
	}
	/**
	 * Export Scheduled orders to shopify
	 */
	public function momo_wsw_export_others_to_shopify( $type = 'order' ) {
		global $momowsw;
		$hook  = 'e' . $type;
		$crons = wp_get_scheduled_event( $momowsw->premium->othercron->cron_hooks[ $hook ] );
		if ( ! empty( $crons ) ) {
			$timestamp = $crons->timestamp;
			$interval  = $crons->interval;
			$from      = $timestamp - $interval;
			$orders    = $this->momo_wsw_export_others_from_date( $from, $type );
		}
	}

	/**
	 * Generate orders by Publish at Min
	 *
	 * @param integer $from_timestamp Publish at min timestamp.
	 */
	public function momo_wsw_import_others_from_date( $from_timestamp, $type ) {
		global $momowsw;
		$catandtags = 'on';
		$pstatus    = 'publish';
		$variations = 'on';

		$pam     = wp_date( 'c', $from_timestamp );
		$args    = array(
			'published_at_min' => $pam,
		);
		$details = $momowsw->fn->momo_wsw_run_rest_api( 'GET', "{$type}.json", '', $args );
		if ( isset( $details->{$type} ) && ! empty( $details->{$type} ) ) {
			$posts = $details->{$type};
			$logs  = array(
				'import' => array(
					'time' => current_time( 'Y-m-d H:i:s' ),
					'type' => 'success',
					/* translators: %1$s: prooduct count, %2$s: type */
					'msg'  => sprintf( esc_html__( '%1$s %2$s(s) found from date: ', 'momowsw' ), count( $posts ), $type ) . $pam,
				),
			);
			$momowsw->logs->momo_wsw_save_logs( $type, $logs );
			$count = 0;
			foreach ( $posts as $post ) {
				switch ( $type ) {
					case 'order':
						$response = $momowsw->premium->fn->momo_momowsw_create_order( $post, $catandtags, $pstatus, $variations );
						break;
					case 'customer':
						$response = $momowsw->premium->fn->momo_momowsw_create_customer( $post );
						break;
					case 'page':
						/** Page Array Details */
						$page_arr['momowsw_page_id'] = $post->id;
						$page_arr['title']           = $post->title;
						$page_arr['description']     = $post->body_html;
						$page_arr['handle']          = $post->handle;
						$page_arr['pstatus']         = 'draft';

						$response = $momowsw->premium->fn->momo_momowsw_create_page( $page_arr );
						break;
					case 'article':
						$blog_arr['momowsw_article_id'] = $post->id;
						$blog_arr['title']              = $post->title;
						$blog_arr['description']        = $post->body_html;
						$blog_arr['image']              = isset( $post->image ) ? $post->image : '';
						$blog_arr['tags']               = $post->tags;
						$blog_arr['handle']             = $post->handle;
						$blog_arr['catandtags']         = 'on';
						$blog_arr['pstatus']            = 'draft';

						$resposne = $momowsw->premium->fn->momo_momowsw_create_blog( $blog_arr );
						break;
				}

				if ( $response ) {
					$count++;
				}
			}
			$logs = array(
				'import' => array(
					'time' => current_time( 'Y-m-d H:i:s' ),
					'type' => 'success',
					/* translators: %1$s: prooduct count, %2$s: type */
					'msg'  => sprintf( esc_html__( '%1$s %2$s(s) imported successfully', 'momowsw' ), $count, $type ),
				),
			);
			$momowsw->logs->momo_wsw_save_logs( $type, $logs );
		} else {
			$logs = array(
				'import' => array(
					'time' => current_time( 'Y-m-d H:i:s' ),
					'type' => 'error',
					/* translators: %s: type */
					'msg'  => sprintf( esc_html__( '%s not found from date: ', 'momowsw' ), $type ) . $pam,
				),
			);
			$momowsw->logs->momo_wsw_save_logs( $type, $logs );
		}
	}
	/**
	 * Export orders by Publish at Min
	 *
	 * @param integer $from_timestamp Publish at min timestamp.
	 */
	public function momo_wsw_export_others_from_date( $from_timestamp, $type ) {
		global $momowsw;
		switch ( $type ) {
			case 'order':
				$post_type = 'shop_order';
				break;
			case 'customer':
				$post_type = 'customer';
				break;
			case 'page':
				$post_type = 'page';
				break;
			case 'article':
				$post_type = 'post';
				break;
		}
		$pam = wp_date( 'c', $from_timestamp );
		if ( 'customer' === $type ) {
			$args   = array(
				'numberposts' => -1,
				'date_query'  => array(
					'after' => $pam,
				),
				'meta_query' => array(
					array(
						'key'     => "momowsw_{$type}_id",
						'compare' => 'NOT EXISTS',
					),
				),
			);
			$posts = new WP_User_Query( $args );
		} else {
			$args   = array(
				'numberposts' => -1,
				'post_type'   => $post_type,
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
						'key'     => "momowsw_{$type}_id",
						'compare' => 'NOT EXISTS',
					),
				),
			);
			$posts = new WP_Query( $args );
		}
		if (
			( 'user' !== $type && $posts->have_posts() )
			||
			( 'user' === $type && $posts->get_results() )
			) {
			if ( 'customer' === $type ) {
				$posts_arr = $posts->get_results();
			} else {
				$posts_arr = $posts->posts;
			}
			$count           = 0;
			$export_settings = get_option( 'momo_wsw_export_settings' );
			$access_token    = isset( $shopify_settings['access_token'] ) ? $shopify_settings['access_token'] : '';
			$api_version     = isset( $shopify_settings['api_version'] ) && ! empty( $shopify_settings['api_version'] ) ? $shopify_settings['api_version'] : '2022-04';
			foreach ( $posts_arr as $post ) {
				$post_id             = $post->ID;
				$enable_order_export = $momowsw->fn->momo_wsw_return_option_yesno( $export_settings, 'enable_order_export' );
				$post_status         = isset( $export_settings['order_status'] ) ? $export_settings['order_status'] : 'active';
				$ptype               = 'insert';
				$args                = array(
					'post_id'     => $post_id,
					'post_status' => $post_status,
					'type'        => $ptype,
					'ptype'       => $type,
				);

				$export_data = $momowsw->eofn->momo_wsw_prepare_others_to_export( $args );
				switch ( $type ) {
					case 'page':
						$shopify_url = 'https://' . $shopify_settings['shop_url'] . '/admin/api/' . $api_version . '/pages.json';
						break;
					case 'article':
						$shopify_url = 'https://' . $shopify_settings['shop_url'] . '/admin/api/' . $api_version . '/articles.json';
						break;
					case 'order':
						$shopify_url = 'https://' . $shopify_settings['shop_url'] . '/admin/api/' . $api_version . '/orders.json';
						break;
					case 'customer':
						$shopify_url = 'https://' . $shopify_settings['shop_url'] . '/admin/api/' . $api_version . '/customers.json';
						break;
				}
				$args     = array(
					'headers'     => array(
						'Accept'                 => 'application/json',
						'Content-Type'           => 'application/json',
						'X-Shopify-Access-Token' => $access_token,
					),
					'httpversion' => '1.1',
					'method'      => 'insert' === $ptype ? 'POST' : 'PUT',
					'timeout'     => 90,
					'body'        => wp_json_encode( $export_data ),
				);
				$response = 'insert' === $ptype ? wp_remote_post( $shopify_url, $args ) : wp_remote_request( $shopify_url, $args );
				$json     = wp_remote_retrieve_body( $response );
				$details  = json_decode( $json );

				if ( isset( $details->{$type}->id ) && ! empty( $details->{$type}->id ) ) {
					if ( 'customer' === $type ) {
						update_user_meta( $post_id, "momowsw_{$type}_id", $details->{$type}->id );
					} else {
						update_post_meta( $post_id, "momowsw_{$type}_id", $details->{$type}->id );
					}
					$count++;
				}
			}
			$logs = array(
				'export' => array(
					'time' => current_time( 'Y-m-d H:i:s' ),
					'type' => 'success',
					/* translators: %2$s: prooduct count, %2$s: type */
					'msg'  => sprintf( esc_html__( '%1$s %2$s(s) exported successfully', 'momowsw' ), $count, $type ),
				),
			);
			$momowsw->logs->momo_wsw_save_logs( $type, $logs );
		} else {
			$logs = array(
				'export' => array(
					'time' => current_time( 'Y-m-d H:i:s' ),
					'type' => 'error',
					/* translators: %s: type */
					'msg'  => sprintf( esc_html__( '%s not found from date: ', 'momowsw' ), $type ) . $pam,
				),
			);
			$momowsw->logs->momo_wsw_save_logs( $type, $logs );
		}
	}
}

<?php
/**
 * Multistore Admin
 *
 * @package momowsw
 */
class MoMo_WSW_Admin_Multistore {
	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'momowsw_set_admin_menu_multistore' ) );
		add_action( 'admin_init', array( $this, 'momowsw_register_settings_multistore' ) );

		if ( is_admin() ) {
			/** For Multi Server */
			global $momowsw;
			$multistore_multiple    = get_option( 'momo_wsw_multistore_multiple_stores' );
			$enable_multiple_stores = $momowsw->fn->momo_wsw_return_option_yesno( $multistore_multiple, 'enable_multiple_stores' );
			if ( 'on' === $enable_multiple_stores ) {
				add_filter( 'manage_edit-product_columns', array( $this, 'momowsw_add_shopify_store_linked_column' ), 15 );
				add_action( 'manage_product_posts_custom_column', array( $this, 'momowsw_add_shopify_store_linked_column_details' ), 10, 2 );

				add_filter( 'manage_edit-shop_order_columns', array( $this, 'momowsw_add_shopify_store_linked_column' ), 15 );
				add_action( 'manage_shop_order_posts_custom_column', array( $this, 'momowsw_add_shopify_store_linked_column_details_order' ), 10, 2 );
			}
			/** For Multi Server ends */
			add_action( 'admin_enqueue_scripts', array( $this, 'momowsw_enqueue_admin_style_scripts' ) );
			$as_order_settings = get_option( 'momo_wsw_multistore_order_sync' );
			$option            = isset( $as_order_settings['enable_tracking_information'] ) ? $as_order_settings['enable_tracking_information'] : 'off';
			if ( 'on' === $option ) {
				add_action( 'add_meta_boxes', array( $this, 'momowsw_multistore_add_tracking_meta_boxes' ), 10 );
				add_action( 'save_post_shop_order', array( $this, 'momowsw_multistore_update_order_meta' ) );
			}
			add_action( 'woocommerce_order_status_completed', array( $this, 'momowsw_multistore_check_and_make_fullfillment' ), 10 );

		}

	}
	/**
	 * Register Scripts and Styles
	 */
	public function momowsw_enqueue_admin_style_scripts() {
		global $momowsw;
		$current = get_current_screen();

		wp_register_style( 'momowsw_select2', $momowsw->plugin_url . 'premium/multistore/assets/select2/select2.min.css', array(), '4.1.0' );
		wp_register_style( 'momowsw_multiselectcss', $momowsw->plugin_url . 'premium/multistore/assets/css/momo_wsw_multistore.css', array(), $momowsw->version );
		wp_register_script( 'momowsw_select2', $momowsw->plugin_url . 'premium/multistore/assets/select2/select2.min.js', array( 'jquery' ), '4.1.0', true );
		wp_register_script( 'momowsw_multistorejs', $momowsw->plugin_url . 'premium/multistore/assets/js/momo_wsw_multistore_admin.js', array( 'jquery' ), $momowsw->version, true );

		if ( isset( $current->id ) && 'shopify_page_momowsw-multistore' === $current->id ) {
			wp_enqueue_style( 'momowsw_select2' );
			wp_enqueue_style( 'momowsw_multiselectcss' );
			wp_enqueue_script( 'momowsw_select2' );
			wp_enqueue_script( 'momowsw_multistorejs' );

			$ajaxurl = array(
				'ajaxurl'            => admin_url( 'admin-ajax.php' ),
				'momowsw_ajax_nonce' => wp_create_nonce( 'momowsw_security_key' ),
				'placeholder'        => esc_html__( 'Select Product(s)', 'momowsw' ),
			);
			wp_localize_script( 'momowsw_multistorejs', 'momowsw_admin_multistore', $ajaxurl );
		}
	}
	/**
	 * Add shopify_store Linked Column.
	 *
	 * @param array $columns Default Columns.
	 */
	public function momowsw_add_shopify_store_linked_column( $columns ) {

		$columns['momowsw_shopify_store_link'] = esc_html__( 'Shopify store', 'momowsw' );
		return $columns;
	}
	/**
	 * Add column content, shopify_store icon
	 *
	 * @param string  $column Column name.
	 * @param integer $woo_product_id Product ID.
	 */
	public function momowsw_add_shopify_store_linked_column_details( $column, $woo_product_id ) {
		if ( 'momowsw_shopify_store_link' === $column ) {
			$momowsw_shopify_shop_url = get_post_meta( $woo_product_id, 'momowsw_shopify_shop_url', true );
			if ( $momowsw_shopify_shop_url && ! empty( $momowsw_shopify_shop_url ) ) {
				echo '<a href="' . esc_url( $momowsw_shopify_shop_url ) . '" target="_blank">' . esc_html( $momowsw_shopify_shop_url ) . '</a>';
			}
		}
	}
	/**
	 * Add column content, shopify_store icon
	 *
	 * @param string  $column Column name.
	 * @param integer $woo_order_id Order ID.
	 */
	public function momowsw_add_shopify_store_linked_column_details_order( $column, $woo_order_id ) {
		$wc_order = wc_get_order( $woo_order_id );
		if ( 'momowsw_shopify_store_link' === $column ) {
			$momowsw_shopify_shop_url = get_post_meta( $woo_order_id, 'momowsw_order_shopify_shop_url', true );
			if ( $momowsw_shopify_shop_url && ! empty( $momowsw_shopify_shop_url ) ) {
				echo '<a href="' . esc_url( $momowsw_shopify_shop_url ) . '" target="_blank">' . esc_html( $momowsw_shopify_shop_url ) . '</a>';
			}
		}
	}
	/**
	 * Register momowsw Settings
	 */
	public function momowsw_register_settings_multistore() {
		register_setting( 'momowsw-settings-multistore-import-group', 'momo_wsw_multistore_import' );
		register_setting( 'momowsw-settings-multistore-multiple-stores-group', 'momo_wsw_multistore_multiple_stores' );
	}
	/**
	 * Set Admin Menu
	 */
	public function momowsw_set_admin_menu_multistore() {
		add_submenu_page(
			'momowsw',
			esc_html__( 'Multistore', 'momowsw' ),
			esc_html__( 'Multistore', 'momowsw' ),
			'manage_options',
			'momowsw-multistore',
			array( $this, 'momowsw_add_multistore_settings_page' ),
			10
		);
	}
	/**
	 * Product Feed Settings Page
	 */
	public function momowsw_add_multistore_settings_page() {
		global $momowsw;
		include_once $momowsw->plugin_path . 'premium/multistore/admin/pages/momo-wsw-multistore-settings.php';
	}
	/**
	 * After saving import settings
	 *
	 * @param mixed  $old_value The old option value.
	 * @param mixed  $value     The new option value.
	 * @param string $option    Option name.
	 */
	public function momo_wsw_after_pf_product_feeds_settings( $old_value, $value, $option ) {
		global $momowsw;
		$enable_pf_feeds = isset( $value['enable_pf_feeds'] ) ? $value['enable_pf_feeds'] : 'off';
		if ( 'on' === $enable_pf_feeds ) {
			$days   = isset( $value[0]['pf_import_days'] ) ? $value[0]['pf_import_days'] : 3;
			$hour   = isset( $value[0]['pf_import_hour'] ) ? $value[0]['pf_import_hour'] : 00;
			$minute = isset( $value[0]['pf_import_minute'] ) ? $value[0]['pf_import_minute'] : 00;
			// Enable cron job.
			add_filter( 'cron_schedules', array( $momowsw->pfcron, 'momo_wsw_custom_product_feeds_schedule' ), 5 ); // phpcs:ignore WordPress.WP.CronInterval
			$momowsw->pfcron->momo_wsw_enable_product_feed_cron();
		} else {
			// Disable cron job.
			$momowsw->pfcron->momo_wsw_disable_product_feed_cron();
		}
	}


	/**
	 * For Fulfillment
	 */
	/**
	 * Add Tracking Metabox
	 */
	public function momowsw_multistore_add_tracking_meta_boxes() {
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';
		foreach ( wc_get_order_types( 'order-meta-boxes' ) as $type ) {
			$order_type_object = get_post_type_object( $type );
			add_meta_box(
				'momowsw-order-shopify-tracking',
				esc_html__( 'Multistore Tracking', 'momowsw' ),
				array( $this, 'momowsws_tracking_metabox_content' ),
				$type,
				'side',
				'high'
			);
		}
	}
	/**
	 * Tracking Metabox content
	 */
	public function momowsws_tracking_metabox_content() {
		global $post;
		$tracking_number = get_post_meta( $post->ID, 'momowsw_fulfillment_tracking_number', true ) ? get_post_meta( $post->ID, 'momowsw_fulfillment_tracking_number', true ) : '';
		$tracking_url    = get_post_meta( $post->ID, 'momowsw_fulfillment_tracking_url', true ) ? get_post_meta( $post->ID, 'momowsw_fulfillment_tracking_url', true ) : '';
		$message         = get_post_meta( $post->ID, 'momowsw_fulfillment_message', true ) ? get_post_meta( $post->ID, 'momowsw_fulfillment_message', true ) : '';
		$company         = get_post_meta( $post->ID, 'momowsw_fulfillment_company', true ) ? get_post_meta( $post->ID, 'momowsw_fulfillment_company', true ) : '';
		wp_nonce_field( plugin_basename( __FILE__ ), 'momowsw_noncename' );
		?>
		<div class="momowsw-side-mb">
			<div class="momowsw-side-mb-header">
				<?php esc_html_e( 'multistore Tracking Info', 'momowsw' ); ?>
			</div>
			<div class="momowsw-side-mb-info">
				<label for="momowsw_fulfillment_tracking_number">
					<?php esc_html_e( 'Tracking Number', 'momowsw' ); ?>
				</label>
				<span class="momowsw-mb-ffield">
					<input type="text" name="momowsw_fulfillment_tracking_number" value="<?php echo esc_attr( $tracking_number ); ?>"/>
				</span>
				<label for="momowsw_fulfillment_tracking_number">
					<?php esc_html_e( 'Tracking Url', 'momowsw' ); ?>
				</label>
				<span class="momowsw-mb-ffield">
					<input type="text" name="momowsw_fulfillment_tracking_url" value="<?php echo esc_attr( $tracking_url ); ?>"/>
				</span>
				<label for="momowsw_fulfillment_tracking_number">
					<?php esc_html_e( 'Company', 'momowsw' ); ?>
				</label>
				<span class="momowsw-mb-ffield">
					<input type="text" name="momowsw_fulfillment_company" value="<?php echo esc_attr( $company ); ?>"/>
				</span>
				<label for="momowsw_fulfillment_message">
					<?php esc_html_e( 'Message', 'momowsw' ); ?>
				</label>
				<span class="momowsw-mb-ffield">
					<textarea name="momowsw_fulfillment_message" row="3"><?php echo esc_html( $message ); ?></textarea>
				</span>
			</div>
		</div>
		<?php
	}
	/**
	 * Save tracking metadata
	 *
	 * @param integer $order_id Order ID.
	 */
	public function momowsw_multistore_update_order_meta( $order_id ) {
		if ( isset( $_POST['momowsw_noncename'] ) ) {
			if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['momowsw_noncename'] ) ), plugin_basename( __FILE__ ) ) ) {
				return;
			}
		}
		$tracking_number = isset( $_POST['momowsw_fulfillment_tracking_number'] ) ? sanitize_text_field( wp_unslash( $_POST['momowsw_fulfillment_tracking_number'] ) ) : '';
		$tracking_url    = isset( $_POST['momowsw_fulfillment_tracking_url'] ) ? sanitize_text_field( wp_unslash( $_POST['momowsw_fulfillment_tracking_url'] ) ) : '';
		$message         = isset( $_POST['momowsw_fulfillment_message'] ) ? sanitize_text_field( wp_unslash( $_POST['momowsw_fulfillment_message'] ) ) : '';
		$company         = isset( $_POST['momowsw_fulfillment_company'] ) ? sanitize_text_field( wp_unslash( $_POST['momowsw_fulfillment_company'] ) ) : '';
		update_post_meta( $order_id, 'momowsw_fulfillment_tracking_number', $tracking_number );
		update_post_meta( $order_id, 'momowsw_fulfillment_tracking_url', $tracking_url );
		update_post_meta( $order_id, 'momowsw_fulfillment_message', $message );
		update_post_meta( $order_id, 'momowsw_fulfillment_company', $company );
	}
	/**
	 * Check and make shopify Fullfillment.
	 *
	 * @param integer $woo_order_id Order ID.
	 */
	public function momowsw_multistore_check_and_make_fullfillment( $woo_order_id ) {
		global $momowsw;
		$wc_order         = wc_get_order( $woo_order_id );
		$momowsw_order_id = get_post_meta( $woo_order_id, 'momowsw_order_id', true );
		if ( $momowsw_order_id && ! empty( $momowsw_order_id ) ) {
			$key            = 'update_shopify_on_completed';
			$order_settings = get_option( 'momo_wsw_multistore_order_sync' );
			$option         = isset( $order_settings[ $key ] ) ? $order_settings[ $key ] : 'off';
			if ( 'on' === $option ) {
				$method      = 'GET';
				$orders      = $this->momowsw_get_order_details( $wc_order );
				$url         = 'orders/' . $momowsw_order_id . '/fulfillment_orders.json';
				$details     = $momowsw->fn->momo_wsw_run_rest_api( $method, $url, '', '' );
				$location_id = '';
				$count       = count( $details->fulfillment_orders );
				if ( $count > 0 ) {
					$fulfillment_order_id = $details->fulfillment_orders[ $count - 1 ]->id;
				}
				$line_items  = array(
					'fulfillment_order_id' => (int) $fulfillment_order_id,
				);

				$for_multistore       = $details->fulfillment_orders[ $count - 1 ]->line_items;
				$multistore_lineitems = array();

				$url           = 'orders/' . (int) $momowsw_order_id . '.json';
				$details       = $momowsw->fn->momo_wsw_run_rest_api( $method, $url, '', '' );
				$count         = count( $details->order->fulfillments );
				$ff_line_items = $details->order->fulfillments[ $count - 1 ]->line_items;
				foreach ( $for_multistore as $fp_item ) {
					$fp_item_id  = $fp_item->line_item_id;
					$sproduct_id = null;
					foreach ( $ff_line_items as $ff_item ) {
						if ( (int) $ff_item->id === (int) $fp_item_id ) {
							$sproduct_id = $ff_item->product_id;
						}
					}
					$check = $this->momowsw_check_shopify_id_is_selected( $sproduct_id );
					if ( $check ) {
						$multistore_lineitems[] = $fp_item;
						/* $multistore_lineitems[] = array(
							'id'       => (int) $fp_item->line_item_id,
							'quantity' => (int) $fp_item->quantity,
						); */
					}
				}
				$tracking_number = get_post_meta( $woo_order_id, 'momowsw_fulfillment_tracking_number', true ) ? get_post_meta( $woo_order_id, 'momowsw_fulfillment_tracking_number', true ) : '';
				$tracking_url    = get_post_meta( $woo_order_id, 'momowsw_fulfillment_tracking_url', true ) ? get_post_meta( $woo_order_id, 'momowsw_fulfillment_tracking_url', true ) : '';
				$message         = get_post_meta( $woo_order_id, 'momowsw_fulfillment_message', true ) ? get_post_meta( $woo_order_id, 'momowsw_fulfillment_message', true ) : '';
				$company         = get_post_meta( $woo_order_id, 'momowsw_fulfillment_company', true ) ? get_post_meta( $woo_order_id, 'momowsw_fulfillment_company', true ) : '';

				$fulfillment = array(
					'fulfillment' => array(
						'message'                         => $message,
						'notify_customer'                 => false,
						'tracking_info'                   => array(
							'number'  => $tracking_number,
							'url'     => $tracking_url,
							'company' => $company,
						),
						'line_items_by_fulfillment_order' => array(
							array(
								'fulfillment_order_id' => (int) $fulfillment_order_id,
								'fulfillment_order_line_items' => $multistore_lineitems,
							),
						),
					),
				);

				$method  = 'POST';
				$details = $momowsw->fn->momo_wsw_run_rest_api( $method, 'fulfillments.json', '', wp_json_encode( $fulfillment ) );
				if ( isset( $details->errors ) ) {
					$error = $details->errors;
					$error = ! is_object( $error ) && isset( $error[0] ) ? $error[0] : $error;
					$logs  = array(
						'fulfillment' => array(
							'time' => current_time( 'Y-m-d H:i:s' ),
							'type' => 'error',
							'msg'  => is_object( $error ) ? esc_html__( 'Something went wrong while changing order status', 'momowsw' ) : $error,
						),
					);
					$momowsw->logs->momo_wsw_save_logs( 'order', $logs );
				}
				if ( isset( $details->fulfillment->status ) && 'success' === $details->fulfillment->status ) {
					$note = esc_html__( 'Shopify order status changed to fulfilled', 'momowsw' );
					$logs = array(
						'fulfillment' => array(
							'time' => current_time( 'Y-m-d H:i:s' ),
							'type' => 'success',
							'msg'  => $note,
						),
					);
					$momowsw->logs->momo_wsw_save_logs( 'order', $logs );
					$wc_order->add_order_note( $note );
					$wc_order->update_meta_data( 'momowsw_fulfillment_id', $details->fulfillment->id );
					$wc_order->save();
				}
			}
		}
	}
	/**
	 * Check Shopify ID is selected
	 *
	 * @param integer $shopify_id Shopify Product ID.
	 */
	public function momowsw_check_shopify_id_is_selected( $shopify_id ) {
		$shopify_ids = $this->momowsw_get_shopify_id_of_selected_products();
		if ( in_array( (string) $shopify_id, $shopify_ids, true ) ) {
			return true;
		}
		return false;
	}
	/**
	 * Get Shopify IDs of selected products
	 */
	public function momowsw_get_shopify_id_of_selected_products() {
		$multistore_import   = get_option( 'momo_wsw_multistore_import' );
		$selected_products = isset( $multistore_import['selected_products'] ) && is_array( $multistore_import['selected_products'] ) ? $multistore_import['selected_products'] : array();
		$shopify_ids       = array();
		if ( ! empty( $selected_products ) ) {
			foreach ( $selected_products as $sp ) {
				$sid = get_post_meta( $sp, 'momowsw_product_id', true );
				if ( ! empty( $sid ) ) {
					$shopify_ids[] = $sid;
				}
			}
		}
		return $shopify_ids;
	}
	/**
	 * Get Shopify Order Details
	 *
	 * @param WC_Order $wc_order WooCommerce order object.
	 */
	public function momowsw_get_order_details( $wc_order ) {
		$line_items    = $wc_order->get_items();
		$order_details = array();
		foreach ( $line_items as $item_id => $item ) {
			$product_id   = $item->get_product_id();
			$variation_id = $item->get_variation_id();
			$quantity     = $item->get_quantity();
			$type         = $item->get_type();
			if ( ! empty( $variation_id ) ) {
				$id = get_post_meta( $variation_id, '_momo_shopify_variation_id' );
			} else {
				$id = get_post_meta( $product_id, '_momo_shopify_variation_id' );
			}
			$order_details[] = array(
				'id'       => $id,
				'quantity' => $quantity,
			);
		}
		return $order_details;
	}
}
new MoMo_WSW_Admin_multistore();

<?php
/**
 * Export Related Hooks and Functions
 *
 * @package momowsw
 */
class MoMo_WSW_Export_Init {
	/**
	 * A variable to store other sync orders
	 *
	 * @var array
	 */
	public $others;
	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'momowsw_register_sync_orders_settings' ) );

		$this->others = array(
			'customer',
			'article',
			'page',
		);
		if ( is_admin() ) {
			add_action( 'update_option_momo_wsw_as_order_import_settings', array( $this, 'momo_wsw_after_as_order_import_settings' ), 10, 3 );
			add_action( 'update_option_momo_wsw_as_order_export_settings', array( $this, 'momo_wsw_after_as_order_export_settings' ), 10, 3 );
			foreach ( $this->others as $other ) {
				add_action( "update_option_momo_wsw_as_{$other}_import_settings", array( $this, 'momo_wsw_after_as_other_import_settings' ), 10, 3 );
				add_action( "update_option_momo_wsw_as_{$other}_export_settings", array( $this, 'momo_wsw_after_as_other_export_settings' ), 10, 3 );
			}
		}
		add_action( 'post_submitbox_start', array( $this, 'momo_wsw_add_export_others_button' ) );

		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_momowsw_gutenberg_button' ) );

		add_filter( 'woocommerce_order_actions_end', array( $this, 'momo_wsw_add_export_others_button' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'momo_wsw_add_export_scripts' ) );

		add_action( 'edit_user_profile', array( $this, 'momo_wsw_add_export_button_to_user_edit_page' ), 20 );

		$ajax_events = array(
			'momowsw_sync_single_others_to_shopify' => 'momowsw_sync_single_others_to_shopify',
			'momowsw_sync_single_user_to_shopify'   => 'momowsw_sync_single_user_to_shopify',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {
			add_action( 'wp_ajax_' . $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_' . $ajax_event, array( $this, $class ) );
		}
	}
	/**
	 * Register Auto Sync Settings
	 */
	public function momowsw_register_sync_orders_settings() {
		register_setting( 'momowsw-settings-as-order-import-group', 'momo_wsw_as_order_import_settings' );
		register_setting( 'momowsw-settings-as-order-export-group', 'momo_wsw_as_order_export_settings' );
		foreach ( $this->others as $other ) {
			register_setting( "momowsw-settings-as-{$other}-import-group", "momo_wsw_as_{$other}_import_settings" );
			register_setting( "momowsw-settings-as-{$other}-export-group", "momo_wsw_as_{$other}_export_settings" );
		}
	}
	/**
	 * After saving import settings
	 *
	 * @param mixed  $old_value The old option value.
	 * @param mixed  $value     The new option value.
	 * @param string $option    Option name.
	 */
	public function momo_wsw_after_as_order_import_settings( $old_value, $value, $option ) {
		global $momowsw;
		$enable_as_order_import = isset( $value['enable_as_order_import'] ) ? $value['enable_as_order_import'] : 'off';
		if ( 'on' === $enable_as_order_import ) {
			$days   = isset( $value['as_order_import_days'] ) ? $value['as_order_import_days'] : 1;
			$hour   = isset( $value['as_order_import_hour'] ) ? $value['as_order_import_hour'] : 00;
			$minute = isset( $value['as_order_import_minute'] ) ? $value['as_order_import_minute'] : 00;
			// Enable cron job.
			add_filter( 'cron_schedules', array( $momowsw->premium->ocron, 'momo_wsw_custom_import_schedule' ), 5 ); // phpcs:ignore WordPress.WP.CronInterval
			$momowsw->premium->ocron->momo_wsw_enable_order_import_cron();
		} else {
			// Disable cron job.
			$momowsw->premium->ocron->momo_wsw_disable_order_import_cron();
		}
	}
	/**
	 * After saving Export settings
	 *
	 * @param mixed  $old_value The old option value.
	 * @param mixed  $value     The new option value.
	 * @param string $option    Option name.
	 */
	public function momo_wsw_after_as_order_export_settings( $old_value, $value, $option ) {
		global $momowsw;
		$enable_as_order_export = isset( $value['enable_as_order_export'] ) ? $value['enable_as_order_export'] : 'off';
		if ( 'on' === $enable_as_order_export ) {
			$days   = isset( $value['as_order_export_days'] ) ? $value['as_order_export_days'] : 1;
			$hour   = isset( $value['as_order_export_hour'] ) ? $value['as_order_export_hour'] : 00;
			$minute = isset( $value['as_order_export_minute'] ) ? $value['as_order_export_minute'] : 00;
			// Enable cron job.
			add_filter( 'cron_schedules', array( $momowsw->premium->ocron, 'momo_wsw_custom_export_schedule' ), 5 ); // phpcs:ignore WordPress.WP.CronInterval
			$momowsw->premium->ocron->momo_wsw_enable_order_export_cron();
		} else {
			// Disable cron job.
			$momowsw->premium->ocron->momo_wsw_disable_order_export_cron();
		}
	}
	/**
	 * After saving import settings
	 *
	 * @param mixed  $old_value The old option value.
	 * @param mixed  $value     The new option value.
	 * @param string $option    Option name.
	 */
	public function momo_wsw_after_as_other_import_settings( $old_value, $value, $option ) {
		global $momowsw;

		$opt_arr = array(
			'momo_wsw_as_page_import_settings'     => 'page',
			'momo_wsw_as_article_import_settings'  => 'article',
			'momo_wsw_as_customer_import_settings' => 'customer',
		);

		$type             = $opt_arr[ $option ];
		$enable_as_import = isset( $value[ "enable_as_{$type}_import" ] ) ? $value[ "enable_as_{$type}_import" ] : 'off';
		if ( 'on' === $enable_as_import ) {
			$days   = isset( $value['as_import_days'] ) ? $value['as_import_days'] : 1;
			$hour   = isset( $value['as_import_hour'] ) ? $value['as_import_hour'] : 00;
			$minute = isset( $value['as_import_minute'] ) ? $value['as_import_minute'] : 00;
			// Enable cron job.
			add_filter( 'cron_schedules', array( $momowsw->premium->othercron, "momo_wsw_custom_import_schedule_{$type}" ), 5 ); // phpcs:ignore WordPress.WP.CronInterval
			$function_name = 'momo_wsw_enable_' . $type . '_import_cron';
			$momowsw->premium->othercron->{$function_name}();
		} else {
			$function_name = 'momo_wsw_disable_' . $type . '_import_cron';
			// Disable cron job.
			$momowsw->premium->othercron->{$function_name}();
		}
	}
	/**
	 * After saving Export settings
	 *
	 * @param mixed  $old_value The old option value.
	 * @param mixed  $value     The new option value.
	 * @param string $option    Option name.
	 */
	public function momo_wsw_after_as_other_export_settings( $old_value, $value, $option ) {
		global $momowsw;
		$opt_arr = array(
			'momo_wsw_as_page_export_settings'     => 'page',
			'momo_wsw_as_article_export_settings'  => 'article',
			'momo_wsw_as_customer_export_settings' => 'customer',
		);

		$type = $opt_arr[ $option ];

		$enable_as_order_export = isset( $value[ "enable_as_{$type}_export" ] ) ? $value[ "enable_as_{$type}_export" ] : 'off';
		if ( 'on' === $enable_as_order_export ) {
			$days   = isset( $value['as_export_days'] ) ? $value['as_export_days'] : 1;
			$hour   = isset( $value['as_export_hour'] ) ? $value['as_export_hour'] : 00;
			$minute = isset( $value['as_export_minute'] ) ? $value['as_export_minute'] : 00;
			// Enable cron job.
			add_filter( 'cron_schedules', array( $momowsw->premium->othercron, "momo_wsw_custom_export_schedule_{$type}" ), 5 ); // phpcs:ignore WordPress.WP.CronInterval
			$function_name = 'momo_wsw_enable_' . $type . '_export_cron';
			$momowsw->premium->othercron->{$function_name}();
		} else {
			// Disable cron job.
			$function_name = 'momo_wsw_disable_' . $type . '_export_cron';
			$momowsw->premium->othercron->{$function_name}();
		}
	}
	/**
	 * Add export button to user edit
	 *
	 * @param WP_User $user User.
	 * @return void
	 */
	public function momo_wsw_add_export_button_to_user_edit_page( $user ) {
		$user_id             = $user->ID;
		$momowsw_customer_id = get_user_meta( $user_id, 'momowsw_customer_id', true );
		if ( empty( $momowsw_customer_id ) ) {
			$button_type = 'insert';
			$button_text = esc_html__( 'Export to Shopify', 'momowsw' );
		} else {
			$button_type = 'update';
			$button_text = esc_html__( 'Update Shopify User', 'momowsw' );
		}
		?>
		<h3><?php esc_html_e( 'Export', 'momowsw' ); ?></h3>
		<table class="form-table">
			<tr>
				<th><label for="custom-button"></label></th>
				<td>
				<div class="momo-be-post-submitbox" style="padding: 8px;">
					<div class="momo-be-post-sb-message"></div>
					<div class="momo-post-button"
						id="momo-wsw-export-to-shopify-others"
						data-post_id="<?php echo esc_attr( $user_id ); ?>"
						data-type="<?php echo esc_attr( $button_type ); ?>"
						data-shopify_id="<?php echo esc_attr( $momowsw_customer_id ); ?>"
						data-ptype="customer"
						>
						<span class="momo-be-spinner"></span>
						<span class="momo-be-spinner-text"><?php echo esc_html( $button_text ); ?></span>
					</div>
					<?php if ( ! empty( $momowsw_customer_id ) ) { ?>
						<div
							class="momo-post-btn-clear-shopify"
							data-post_id="<?php echo esc_attr( $user_id ); ?>"
							data-shopify_id="<?php echo esc_attr( $momowsw_customer_id ); ?>"
							data-type="<?php echo esc_attr( $button_type ); ?>"
							data-ptype="customer"
							>
							<?php esc_html_e( 'Unlink Shopify ID', 'momowsw' ); ?>
						</div>
					<?php } ?>
				</div>
				</td>
			</tr>
		</table>
		<?php
	}
	/**
	 * A function to add export scripts for Momo WSW.
	 */
	public function momo_wsw_add_export_scripts() {
		global $momowsw;
		wp_enqueue_script(
			'momowsw_export',
			$momowsw->plugin_url . 'premium/assets/js/momo_wsw_export.js',
			array( 'jquery' ),
			$momowsw->version,
			true
		);
	}
	/**
	 * Insert button if Gutenberg enabled
	 */
	public function enqueue_momowsw_gutenberg_button() {
		global $momowsw, $post;
		if ( get_post_type() === 'post' || get_post_type() === 'page' ) {
			$ptype = get_post_type();
			$ptype = 'post' === $ptype ? 'article' : $ptype;
			wp_enqueue_script(
				'momowsw_gutenberg_button',
				$momowsw->plugin_url . 'premium/assets/js/momo_wsw_gutenberg_button.js',
				array( 'wp-edit-post', 'wp-element', 'wp-components', 'wp-hooks' ),
				$momowsw->version,
				true
			);
			$wp_post_id         = $post->ID;
			$momowsw_shopify_id = get_post_meta( $wp_post_id, "momowsw_{$ptype}_id", true );
			/* translators: %s is a placeholder for the post type */
			$button_text = sprintf( esc_html__( 'Export %s to Shopify', 'momowsw' ), $ptype );
			$button_type = 'insert';
			if ( ! empty( $momowsw_shopify_id ) ) {
				$button_type = 'update';
				/* translators: %s is a placeholder for the post type */
				$button_text = sprintf( esc_html__( 'Update Shopify %s', 'momowsw' ), $ptype );
			}
			wp_localize_script(
				'momowsw_gutenberg_button',
				'momowsw_gutenberg_vars',
				array(
					'btnText'    => $button_text,
					'post_id'    => $wp_post_id,
					'btn_type'   => $button_type,
					'ptype'      => $ptype,
					'shopify_id' => $momowsw_shopify_id,
				)
			);
		}
	}
	/**
	 * Add export to other post type
	 *
	 * @param int $post_id Post ID.
	 */
	public function momo_wsw_add_export_others_button( $post_id ) {
		global $momowsw;
		$post = get_post( $post_id );
		if ( ! is_object( $post ) ) {
			return;
		}
		$allowed = array(
			'post',
			'page',
			'shop_order',
			'shop_order_placehold',
		);
		if (
			! in_array(
				$post->post_type,
				$allowed,
				true
			)
			) {
			return;
		}
		if ( ! $momowsw->fn->momowsw_check_api_credentials() ) {
			return;
		}
		$ptype = 'article';
		switch ( $post->post_type ) {
			case 'page':
				$ptype = 'page';
				break;
			case 'post':
				$ptype = 'article';
				break;
			case 'shop_order' || 'shop_order_placehold':
				$ptype = 'order';
				break;
			default:
				return;
		}
		$export_settings    = get_option( 'momo_wsw_export_settings' );
		$enable_type_export = $momowsw->fn->momo_wsw_return_option_yesno( $export_settings, "enable_{$ptype}_export" );
		$wp_post_id         = $post->ID;
		$momowsw_shopify_id = get_post_meta( $wp_post_id, "momowsw_{$ptype}_id", true );
		if ( 'on' === $enable_type_export || 'off' === $enable_type_export || empty( $enable_type_export ) ) :
			$button_type = 'insert';
			/* translators: %s is a placeholder for the post type */
			$button_text = sprintf( esc_html__( 'Export %s to Shopify', 'momowsw' ), $ptype );
			if ( ! empty( $momowsw_shopify_id ) ) {
				$button_type = 'update';
				/* translators: %s is a placeholder for the post type */
				$button_text = sprintf( esc_html__( 'Update Shopify %s', 'momowsw' ), $ptype );
			}
			?>
			<div class="momo-be-post-submitbox" style="padding: 8px;">
				<div class="momo-be-post-sb-message"></div>
				<div class="momo-post-button full"
					id="momo-wsw-export-to-shopify-others"
					data-post_id="<?php echo esc_attr( $wp_post_id ); ?>"
					data-type="<?php echo esc_attr( $button_type ); ?>"
					data-shopify_id="<?php echo esc_attr( $momowsw_shopify_id ); ?>"
					data-ptype="<?php echo esc_attr( $ptype ); ?>"
					>
					<span class="momo-be-spinner"></span>
					<span class="momo-be-spinner-text"><?php echo esc_html( $button_text ); ?></span>
				</div>
				<?php if ( ! empty( $momowsw_shopify_id ) ) { ?>
					<div
						class="momo-post-btn-clear-shopify"
						data-post_id="<?php echo esc_attr( $wp_post_id ); ?>"
						data-shopify_id="<?php echo esc_attr( $momowsw_shopify_id ); ?>"
						data-type="<?php echo esc_attr( $button_type ); ?>"
						data-ptype="<?php echo esc_attr( $ptype ); ?>"
						>
						<?php esc_html_e( 'Unlink Shopify ID', 'momowsw' ); ?>
					</div>
				<?php } ?>
			</div>
			<?php
		endif;
	}
	/**
	 * Sync woo order to shopify ( Eight )
	 */
	public function momowsw_sync_single_others_to_shopify() {
		global $momowsw;
		$shopify_settings = get_option( 'momo_wsw_settings' );
		$res              = check_ajax_referer( 'momowsw_security_key', 'security' );
		if ( isset( $_POST['action'] ) && 'momowsw_sync_single_others_to_shopify' !== $_POST['action'] ) {
			return;
		}
		if ( ! $momowsw->fn->momowsw_check_api_credentials() ) {
			return;
		}
		$post_id = isset( $_POST['post_id'] ) ? sanitize_text_field( wp_unslash( $_POST['post_id'] ) ) : '';
		$type    = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
		$ptype   = isset( $_POST['ptype'] ) ? sanitize_text_field( wp_unslash( $_POST['ptype'] ) ) : '';
		if ( empty( $post_id ) ) {
			return;
		}
		$export_settings       = get_option( 'momo_wsw_export_settings' );
		$enable_order_export = $momowsw->fn->momo_wsw_return_option_yesno( $export_settings, 'enable_order_export' );
		$post_status           = isset( $export_settings['order_status'] ) ? $export_settings['order_status'] : 'active';
		$args                  = array(
			'post_id'     => $post_id,
			'post_status' => $post_status,
			'type'        => $type,
			'ptype'       => $ptype,
		);

		$export_data = $momowsw->eofn->momo_wsw_prepare_others_to_export( $args );

		$access_token = isset( $shopify_settings['access_token'] ) ? $shopify_settings['access_token'] : '';
		$api_version  = isset( $shopify_settings['api_version'] ) && ! empty( $shopify_settings['api_version'] ) ? $shopify_settings['api_version'] : '2022-04';
		if ( 'insert' === $type ) {
			switch ( $ptype ) {
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
		} elseif ( 'update' === $type ) {
			$shopify_id = isset( $_POST['shopify_id'] ) ? sanitize_text_field( wp_unslash( $_POST['shopify_id'] ) ) : '';
			if ( empty( $shopify_id ) ) {
				echo wp_json_encode(
					array(
						'status' => 'warning',
						'msg'    => esc_html__( 'Unable to find shopify order ID.', 'momowsw' ),
					)
				);
				exit;
			}
			switch ( $ptype ) {
				case 'page':
					$shopify_url = 'https://' . $shopify_settings['shop_url'] . '/admin/api/' . $api_version . '/pages/' . $shopify_id . '.json';
					break;
				case 'article':
					$shopify_url = 'https://' . $shopify_settings['shop_url'] . '/admin/api/' . $api_version . '/articles/' . $shopify_id . '.json';
					break;
				case 'order':
					$shopify_url = 'https://' . $shopify_settings['shop_url'] . '/admin/api/' . $api_version . '/orders/' . $shopify_id . '.json';
					break;
				case 'customer':
					$shopify_url = 'https://' . $shopify_settings['shop_url'] . '/admin/api/' . $api_version . '/customers/' . $shopify_id . '.json';
					break;
			}
			$export_data[ $ptype ]['id'] = $shopify_id;
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

		if ( isset( $details->{$ptype}->id ) && ! empty( $details->{$ptype}->id ) ) {
			if ( 'insert' === $type ) {
				if ( 'customer' === $ptype ) {
					update_user_meta( $post_id, "momowsw_{$ptype}_id", $details->{$ptype}->id );
				} else {
					update_post_meta( $post_id, "momowsw_{$ptype}_id", $details->{$ptype}->id );
				}
				/* translators: %s is a placeholder for the post type */
				$msg = sprintf( esc_html__( '%s exported to Shopify.', 'momowsw' ), ucfirst( $ptype ) );
			} else {
				/* translators: %s is a placeholder for the post type */
				$msg = sprintf( esc_html__( '%s updated to Shopify.', 'momowsw' ), ucfirst( $ptype ) );
			}
			echo wp_json_encode(
				array(
					'status'     => 'success',
					'msg'        => $msg,
					'shopify_id' => $details->{$ptype}->id,
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
						/* translators: %s is a placeholder for the post type */
						'msg'    => sprintf( esc_html__( 'Something went wrong while exporting %1$s. Please check shopify if %2$s have been exported.', 'momowsw' ), $ptype, $ptype ),
					)
				);
				exit;
			}
		}
	}
	/**
	 * Sync user to shopify
	 */
	public function momowsw_sync_single_user_to_shopify() {
		global $momowsw;
		$shopify_settings = get_option( 'momo_wsw_settings' );
		$res              = check_ajax_referer( 'momowsw_security_key', 'security' );
		if ( isset( $_POST['action'] ) && 'momowsw_sync_single_user_to_shopify' !== $_POST['action'] ) {
			return;
		}
		if ( ! $momowsw->fn->momowsw_check_api_credentials() ) {
			return;
		}
		$user_id    = isset( $_POST['user_id'] ) ? sanitize_text_field( wp_unslash( $_POST['user_id'] ) ) : '';
		$type       = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
		$ptype      = isset( $_POST['ptype'] ) ? sanitize_text_field( wp_unslash( $_POST['ptype'] ) ) : '';
		$shopify_id = isset( $_POST['shopify_id'] ) ? sanitize_text_field( wp_unslash( $_POST['shopify_id'] ) ) : '';
		if ( empty( $user_id ) ) {
			return;
		}
	}

}
new MoMo_WSW_Export_Init();

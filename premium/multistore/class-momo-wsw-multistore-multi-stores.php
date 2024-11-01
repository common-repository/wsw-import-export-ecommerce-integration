<?php
/**
 * Admin Init
 *
 * @package momowsw
 * @author MoMo Themes
 * @since v1.0.0
 */
class MoMo_WSW_Multistore_Multi_Stores {
	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'momowsw_register_multiple_stores_settings' ) );

		$ajax_events = array(
			'momowsw_multistore_update_store_data'           => 'momowsw_multistore_update_store_data', // One.
			'momowsw_multistore_enable_disable_multi_stores' => 'momowsw_multistore_enable_disable_multi_stores', // Two.
		);
		foreach ( $ajax_events as $ajax_event => $class ) {
			add_action( 'wp_ajax_' . $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_' . $ajax_event, array( $this, $class ) );
		}
	}
	/**
	 * Add edit store data
	 */
	public function momowsw_multistore_update_store_data() {
		$res = check_ajax_referer( 'momowsw_security_key', 'security' );
		if ( isset( $_POST['action'] ) && 'momowsw_multistore_update_store_data' !== $_POST['action'] ) {
			return;
		}

		$type = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : 'add';

		$multistore_multiple = get_option( 'momo_wsw_multistore_multiple_stores' );
		$multi_stores_data = isset( $multistore_multiple['multi_stores_data'] ) ? $multistore_multiple['multi_stores_data'] : array();
		$multi_stores_data = array_values( $multi_stores_data );
		if ( 'add' === $type ) {
			$input = isset( $_POST['input'] ) ? wp_unslash( $_POST['input'] ) : array();

			$multi_stores_data[]                    = $input;
			$multistore_multiple['multi_stores_data'] = $multi_stores_data;
			update_option( 'momo_wsw_multistore_multiple_stores', $multistore_multiple );
		} elseif ( 'delete' === $type ) {
			$index = 0;
			$shop  = isset( $_POST['shop'] ) ? sanitize_text_field( wp_unslash( $_POST['shop'] ) ) : '';
			foreach ( $multi_stores_data as $store ) {
				if ( trim( $store['shop_url'] ) === trim( $shop ) ) {
					unset( $multi_stores_data[ $index ] );
				}
				++$index;
			}
			$multistore_multiple['multi_stores_data'] = array_values( $multi_stores_data );
			update_option( 'momo_wsw_multistore_multiple_stores', $multistore_multiple );
		}
		$slist_html = $this->multistore_draw_list_and_exit( $multi_stores_data );
		echo wp_json_encode(
			array(
				'status'  => 'good',
				'content' => $slist_html,
				'msdata'  => wp_json_encode( $multi_stores_data ),
			)
		);
		exit;
	}
	/**
	 * Handle Enable Disable ajax.
	 */
	public function momowsw_multistore_enable_disable_multi_stores() {
		$res = check_ajax_referer( 'momowsw_security_key', 'security' );
		if ( isset( $_POST['action'] ) && 'momowsw_multistore_enable_disable_multi_stores' !== $_POST['action'] ) {
			return;
		}
		$val = isset( $_POST['val'] ) ? sanitize_text_field( wp_unslash( $_POST['val'] ) ) : 'off';

		$multistore_multiple                           = get_option( 'momo_wsw_multistore_multiple_stores' );
		$multistore_multiple['enable_multiple_stores'] = $val;
		update_option( 'momo_wsw_multistore_multiple_stores', $multistore_multiple );
		echo wp_json_encode(
			array(
				'status'  => 'good',
				'content' => esc_html__( 'Option updated successfully.', 'momowsw' ),
			)
		);
		exit;

	}
	/**
	 * Draw server list
	 *
	 * @param string $multi_store_data Multi store data.
	 */
	public function multistore_draw_list_and_exit( $multi_store_data ) {
		ob_start();
		if ( ! empty( $multi_store_data ) ) {
			$data = $multi_store_data;
			foreach ( $data as $server ) {
				?>
				<li data-shop="<?php echo esc_html( $server['shop_url'] ); ?>">
					<span class="shop_url">
					<?php echo esc_html( $server['shop_url'] ); ?>
					</span>
					<span class="access_token">
					<?php echo esc_html( $server['access_token'] ); ?>
					</span>
					<i>x</i>
				</li>
				<?php
			}
		} else {
			?>
			<li><?php esc_html_e( 'Empty server list', 'momowsw' ); ?></li>
			<?php
		}
		return ob_get_clean();
	}
	/**
	 * Register Auto Sync Settings
	 */
	public function momowsw_register_multiple_stores_settings() {
		register_setting( 'momowsw-settings-multistore-multiple-stores-group', 'momo_wsw_multistore_multiple_stores' );
	}
	/**
	 * Get all server list.
	 */
	public function multistore_get_all_server_lists() {
		$shopify_settings = get_option( 'momo_wsw_settings' );
		$shop_url         = $shopify_settings['shop_url'];
		$access_token     = $shopify_settings['access_token'];
		$api_version      = isset( $shopify_settings['api_version'] ) && ! empty( $shopify_settings['api_version'] ) ? $shopify_settings['api_version'] : '2022-04';

		$multistore_multiple = get_option( 'momo_wsw_multistore_multiple_stores' );
		$multi_stores_data = isset( $multistore_multiple['multi_stores_data'] ) ? $multistore_multiple['multi_stores_data'] : array();
		$multi_stores_data = array_values( $multi_stores_data );

		$server_list   = array();
		$server_list[] = array(
			'shop_url'     => $shop_url,
			'access_token' => $access_token,
		);
		foreach ( $multi_stores_data as $store ) {
			$server_list[] = $store;
		}
		return $server_list;
	}
	/**
	 * Get details from shop URL
	 *
	 * @param string $shop_url Sho URL.
	 */
	public function multistore_get_server_details( $shop_url ) {
		$server_list = $this->multistore_get_all_server_lists();
		foreach ( $server_list as $server ) {
			if ( $shop_url === $server['shop_url'] ) {
				return $server;
			}
		}
		return $server_list[0];
	}
}

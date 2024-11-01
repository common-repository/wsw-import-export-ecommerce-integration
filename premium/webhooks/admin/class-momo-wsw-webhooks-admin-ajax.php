<?php
/**
 * MoMo WSW - Amin AJAX functions
 *
 * @package momowsw
 * @author MoMo Themes
 * @since v1.5.0
 */
class MoMo_WSW_Webhooks_Admin_Ajax {
	/**
	 * Constructor
	 */
	public function __construct() {
		$ajax_events = array(
			'momowsw_enable_webhooks'     => 'momowsw_enable_webhooks', // One.
			'momowsw_delete_webhooks'     => 'momowsw_delete_webhooks', // Two.
			'momowsw_wsw_enable_webhooks' => 'momowsw_wsw_enable_webhooks', // Three.
			'momowsw_wsw_delete_webhooks' => 'momowsw_wsw_delete_webhooks', // Four.
		);
		foreach ( $ajax_events as $ajax_event => $class ) {
			add_action( 'wp_ajax_' . $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_' . $ajax_event, array( $this, $class ) );
		}
	}
	/**
	 * Enable webhooks( One )
	 */
	public function momowsw_enable_webhooks() {
		global $momowsw;
		$res = check_ajax_referer( 'momowsw_security_key', 'security' );
		if ( isset( $_POST['action'] ) && 'momowsw_enable_webhooks' !== $_POST['action'] ) {
			return;
		}

		$source = isset( $_POST['source'] ) ? sanitize_text_field( wp_unslash( $_POST['source'] ) ) : '';
		$type   = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
		if ( 'shopify' === $source ) {
			$this->momowsw_create_shopify_webhooks( $type );
		}
		echo wp_json_encode(
			array(
				'status' => 'good',
				'msg'    => esc_html__( 'Webhook created successfully.', 'momowsw' ),
			)
		);
		exit;
	}
	/**
	 * Delete webhooks( Two )
	 */
	public function momowsw_delete_webhooks() {
		global $momowsw;
		$res = check_ajax_referer( 'momowsw_security_key', 'security' );
		if ( isset( $_POST['action'] ) && 'momowsw_delete_webhooks' !== $_POST['action'] ) {
			return;
		}

		$source = isset( $_POST['source'] ) ? sanitize_text_field( wp_unslash( $_POST['source'] ) ) : '';
		$type   = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
		$id     = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';
		$args   = array(
			'source' => $source,
			'type'   => $type,
			'id'     => $id,
		);
		if ( 'shopify' === $source ) {
			$this->momowsw_delete_shopify_webhooks( $args );
		}
		echo wp_json_encode(
			array(
				'status' => 'good',
				'msg'    => esc_html__( 'Webhook deleted successfully.', 'momowsw' ),
			)
		);
		exit;
	}
	/**
	 * Enable webhooks( Three )
	 */
	public function momowsw_wsw_enable_webhooks() {
		global $momowsw;
		$res = check_ajax_referer( 'momowsw_security_key', 'security' );
		if ( isset( $_POST['action'] ) && 'momowsw_wsw_enable_webhooks' !== $_POST['action'] ) {
			return;
		}

		$source = isset( $_POST['source'] ) ? sanitize_text_field( wp_unslash( $_POST['source'] ) ) : '';
		$type   = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
		if ( 'shopify' === $source ) {
			$this->momowsw_create_wsw_webhooks( $type );
		}
		echo wp_json_encode(
			array(
				'status' => 'good',
				'msg'    => esc_html__( 'Webhook created successfully.', 'momowsw' ),
			)
		);
		exit;
	}
	/**
	 * Delete webhooks( Four )
	 */
	public function momowsw_wsw_delete_webhooks() {
		global $momowsw;
		$res = check_ajax_referer( 'momowsw_security_key', 'security' );
		if ( isset( $_POST['action'] ) && 'momowsw_wsw_delete_webhooks' !== $_POST['action'] ) {
			return;
		}

		$source = isset( $_POST['source'] ) ? sanitize_text_field( wp_unslash( $_POST['source'] ) ) : '';
		$type   = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
		$id     = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';
		$args   = array(
			'source' => $source,
			'type'   => $type,
			'id'     => $id,
		);
		if ( 'shopify' === $source ) {
			$this->momowsw_delete_wsw_webhooks( $args );
		}
		echo wp_json_encode(
			array(
				'status' => 'good',
				'msg'    => esc_html__( 'Webhook deleted successfully.', 'momowsw' ),
			)
		);
		exit;
	}
	/**
	 * Create Shopify Webhooks
	 *
	 * @param string $type Type.
	 */
	public function momowsw_create_shopify_webhooks( $type ) {
		global $momowsw;
		$topic   = '';
		$address = get_rest_url( '', '/' . $momowsw->premium->webhooks->slug . '/' );
		switch ( $type ) {
			case 'product_update_quantity':
				$address .= 'product-update-quantity/';
				$topic    = 'products/update';
				break;
		}
		$args    = array(
			'webhook' => array(
				'topic'   => $topic,
				'address' => $address,
				'format'  => 'json',
			),
		);
		$details = $momowsw->fn->momo_wsw_run_rest_api( 'POST', 'webhooks.json', '', wp_json_encode( $args ) );
		return $details;
	}
	/**
	 * Create Shopify Webhooks
	 *
	 * @param array $args Arguments.
	 */
	public function momowsw_delete_shopify_webhooks( $args ) {
		global $momowsw;
		$topic   = '';
		$type    = $args['type'];
		$id      = $args['id'];
		$details = $momowsw->fn->momo_wsw_run_rest_api( 'DELETE', 'webhooks/' . $id . '.json' );
		return $details;
	}
	/**
	 * Create WSW Webhooks
	 *
	 * @param string $type Type.
	 */
	public function momowsw_create_wsw_webhooks( $type ) {
		global $momowsw;
		switch ( $type ) {
			case 'product_update_quantity':
				$index = 'woocommerce/stock';
				$id    = 114564;
				break;
		}
		$webhooks_settings     = get_option( 'momo_wsw_webhooks_ajax' );
		$wsw_current           = isset( $webhooks_settings['wsw_webhooks'] ) ? $webhooks_settings['wsw_webhooks'] : array();
		$wsw_current[ $index ] = array(
			'id' => $id,
		);

		$webhooks_settings['wsw_webhooks'] = $wsw_current;
		update_option( 'momo_wsw_webhooks_ajax', $webhooks_settings );
		return true;
	}
	/**
	 * Create Shopify Webhooks
	 *
	 * @param array $args Arguments.
	 */
	public function momowsw_delete_wsw_webhooks( $args ) {
		global $momowsw;
		$type = $args['type'];
		$id   = $args['id'];

		$webhooks_settings = get_option( 'momo_wsw_webhooks_ajax' );
		switch ( $type ) {
			case 'product_update_quantity':
				$index       = 'woocommerce/stock';
				$wsw_current = isset( $webhooks_settings['wsw_webhooks'] ) ? $webhooks_settings['wsw_webhooks'] : array();
				if ( isset( $wsw_current[ $index ] ) ) {
					unset( $wsw_current[ $index ] );
					$webhooks_settings['wsw_webhooks'] = $wsw_current;
					update_option( 'momo_wsw_webhooks_ajax', $webhooks_settings );
				}
				break;
		}
		return true;
	}
}
new MoMo_WSW_Webhooks_Admin_Ajax();

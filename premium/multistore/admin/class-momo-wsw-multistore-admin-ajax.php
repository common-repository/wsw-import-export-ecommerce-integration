<?php
/**
 * MoMo WSW - Amin AJAX functions
 *
 * @package momowsw
 * @author MoMo Themes
 * @since v1.4.0
 */
class MoMo_WSW_multistore_Admin_Ajax {
	/**
	 * Constructor
	 */
	public function __construct() {
		$ajax_events = array(
			'momowsw_woo_product_list_search'      => 'momowsw_woo_product_list_search', // One.
			'momowsw_clear_multi_store_order_logs' => 'momowsw_clear_multi_store_order_logs', // Two.
		);
		foreach ( $ajax_events as $ajax_event => $class ) {
			add_action( 'wp_ajax_' . $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_' . $ajax_event, array( $this, $class ) );
		}
	}
	/**
	 * Product List Search
	 */
	public function momowsw_woo_product_list_search() {
		global $momowsw;
		$res = check_ajax_referer( 'momowsw_security_key', 'security' );
		if ( isset( $_POST['action'] ) && 'momowsw_woo_product_list_search' !== $_POST['action'] ) {
			return;
		}
		$query          = isset( $_POST['q'] ) ? sanitize_text_field( wp_unslash( $_POST['q'] ) ) : '';
		$search_results = new WP_Query(
			array(
				's'                   => $query,
				'post_status'         => 'publish',
				'ignore_sticky_posts' => 1,
				'post_type'           => 'product',
				'posts_per_page'      => 50,
				'meta_query' => array(
					array(
						'key'     => 'momowsw_product_id',
						'compare' => 'EXISTS',
					),
				),
			)
		);

		$return = array();
		if ( $search_results->have_posts() ) :
			while ( $search_results->have_posts() ) :
				$search_results->the_post();
				$title    = ( mb_strlen( $search_results->post->post_title ) > 50 ) ? mb_substr( $search_results->post->post_title, 0, 49 ) . '...' : $search_results->post->post_title;
				$return[] = array( $search_results->post->ID, $title );
			endwhile;
		endif;
		echo wp_json_encode( $return );
		die;
	}
	/**
	 * Clear Multi store Order Cron Logs
	 */
	public function momowsw_clear_multi_store_order_logs() {
		global $momowsw;
		$res = check_ajax_referer( 'momowsw_security_key', 'security' );
		if ( isset( $_POST['action'] ) && 'momowsw_clear_multi_store_order_logs' !== $_POST['action'] ) {
			return;
		}
		$momowsw->logs->momo_wsw_flush_logs( 'msorder' );
		echo wp_json_encode(
			array(
				'status' => 'good',
				'msg'    => esc_html__( 'Multi Store Order logs cleared.', 'momowsw' ),
			)
		);
		exit;
	}
}
new MoMo_WSW_multistore_Admin_Ajax();

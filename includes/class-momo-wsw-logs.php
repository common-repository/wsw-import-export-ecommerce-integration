<?php
/**
 * Shopify Export functions
 *
 * @package momowsw
 * @author MoMo Themes
 * @since v1.2.0
 */

class MoMo_WSW_Logs {
	/**
	 * Logs Data
	 *
	 * @var array
	 */
	public $logs;
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->logs = array(
			'cron'     => '_momo_wsw_cron_job_logs',
			'api'      => '_momo_wsw_cron_api_logs',
			'order'    => '_momo_wsw_cron_order_logs',
			'page'     => '_momo_wsw_cron_page_logs',
			'article'  => '_momo_wsw_cron_article_logs',
			'customer' => '_momo_wsw_cron_customer_logs',
			'msorder'  => '_momo_wsw_multi_store_cron_job_logs',
		);
	}

	/**
	 * Add remove logs
	 *
	 * @param string $type Log type.
	 * @param array  $logs_data Data.
	 */
	public function momo_wsw_save_logs( $type, $logs_data ) {
		if ( ! $logs_data ) {
			return;
		}
		$logs   = get_option( $this->logs[ $type ] );
		$logs   = ( false === $logs || empty( $logs ) ) ? array() : $logs;
		$logs[] = $logs_data;

		update_option( $this->logs[ $type ], $logs );
	}
	/**
	 * Get Logs
	 *
	 * @param string $type Log type.
	 */
	public function momo_wsw_get_logs( $type ) {
		$logs = get_option( $this->logs[ $type ] );

		if ( empty( $logs ) ) {
			return array();
		}
		return $logs;
	}
	/**
	 * Clear Logs
	 *
	 * @param string $type Log type.
	 */
	public function momo_wsw_flush_logs( $type ) {
		$logs = get_option( $this->logs[ $type ] );

		if ( empty( $logs ) ) {
			return true;
		}

		$logs = array();

		delete_option( $this->logs[ $type ] );
	}
}

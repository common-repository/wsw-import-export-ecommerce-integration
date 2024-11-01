<?php
/**
 * Shopify Export functions
 *
 * @package momowsw
 * @author MoMo Themes
 * @since v2.0.0
 */
class MoMo_WSW_Multistore_Cron {
	/**
	 * Cron Hooks
	 *
	 * @var array
	 */
	public $cron_hooks;
	/**
	 * Constructor
	 */
	public function __construct() {
		$as_multistore_settings = get_option( 'momo_wsw_multistore_order_sync' );
		$enable_as_import       = isset( $as_multistore_settings['enable_as_import'] ) ? $as_multistore_settings['enable_as_import'] : 'off';
		$this->cron_hooks       = array(
			'iorder' => '_momo_wsw_import_order_schedule',
		);
		if ( 'on' === $enable_as_import ) {
			add_filter( 'cron_schedules', array( $this, 'momo_wsw_custom_import_schedule' ), 5 ); // phpcs:ignore WordPress.WP.CronInterval
			add_action( $this->cron_hooks['iorder'], array( $this, 'momo_wsw_import_order_schedule' ), 15 );
		}
	}
	/**
	 * Perform Scheduled Import Cron
	 */
	public function momo_wsw_import_order_schedule() {
		global $momowsw;
		$momowsw->premium->osync->momo_wsw_import_shceduled_orders_from_shopify();
	}
	/**
	 * Enable Order Import Cron
	 */
	public function momo_wsw_enable_order_import_cron() {
		$as_multistore_settings = get_option( 'momo_wsw_multistore_order_sync' );
		wp_clear_scheduled_hook( $this->cron_hooks['iorder'] );
		$days   = isset( $as_multistore_settings['as_import_days'] ) && ! empty( $as_multistore_settings['as_import_days'] ) ? $as_multistore_settings['as_import_days'] : 1;
		$hour   = isset( $as_multistore_settings['as_import_hour'] ) && ! empty( $as_multistore_settings['as_import_hour'] ) ? $as_multistore_settings['as_import_hour'] : '00';
		$minute = isset( $as_multistore_settings['as_import_minute'] ) && ! empty( $as_multistore_settings['as_import_minute'] ) ? $as_multistore_settings['as_import_minute'] : '00';
		$scname = 'momo_wsw_order_import_interval';
		$nrun   = $this->momo_wsw_cron_strtotime( '+' . $days . ' days ' . $hour . ':' . $minute );
		$result = wp_schedule_event( $nrun, $scname, $this->cron_hooks['iorder'], array(), true );
	}
	/**
	 * Disable Order Import Cron
	 */
	public function momo_wsw_disable_order_import_cron() {
		wp_clear_scheduled_hook( $this->cron_hooks['iorder'] );
	}
	/**
	 * Add Import Cron Schedule
	 *
	 * @param array $schedules Schedules.
	 */
	public function momo_wsw_custom_import_schedule( $schedules ) {
		$as_multistore_settings = get_option( 'momo_wsw_multistore_order_sync' );
		$days                   = isset( $as_multistore_settings['as_import_days'] ) && ! empty( $as_multistore_settings['as_import_days'] ) ? $as_multistore_settings['as_import_days'] : 1;
		$scname                 = 'momo_wsw_order_import_interval';
		$interval               = 60 * 60 * 24 * (int) $days;
		$schedules[ $scname ]   = array(
			'interval' => $interval,
			/* translators: %s: number of days */
			'display'  => sprintf( esc_html__( 'Every %s day(s)', 'momowsw' ), $days ),
		);
		return $schedules;
	}
	/**
	 * Perform Scheduled Export Cron
	 */
	public function momo_wsw_export_order_schedule() {
		global $momowsw;
		$momowsw->premium->osync->momo_wsw_export_shceduled_orders_to_shopify();
	}
	/**
	 * Enable Order Export Cron
	 */
	public function momo_wsw_enable_order_export_cron() {
		$as_multistore_settings = get_option( 'momo_wsw_multistore_order_sync' );
		wp_clear_scheduled_hook( $this->cron_hooks['eorder'] );
		$days   = isset( $as_multistore_settings['as_export_days'] ) && ! empty( $as_multistore_settings['as_export_days'] ) ? $as_multistore_settings['as_export_days'] : 1;
		$hour   = isset( $as_multistore_settings['as_export_hour'] ) && ! empty( $as_multistore_settings['as_export_hour'] ) ? $as_multistore_settings['as_export_hour'] : '00';
		$minute = isset( $as_multistore_settings['as_export_minute'] ) && ! empty( $as_multistore_settings['as_export_minute'] ) ? $as_multistore_settings['as_export_minute'] : '00';
		$scname = 'momo_wsw_order_export_interval';
		$nrun   = $this->momo_wsw_cron_strtotime( '+' . $days . ' days ' . $hour . ':' . $minute );
		$result = wp_schedule_event( $nrun, $scname, $this->cron_hooks['eorder'], array(), true );
	}
	/**
	 * Disable Order Export Cron
	 */
	public function momo_wsw_disable_order_export_cron() {
		wp_clear_scheduled_hook( $this->cron_hooks['eorder'] );
	}
	/**
	 * Add Export Cron Schedule
	 *
	 * @param array $schedules Schedules.
	 */
	public function momo_wsw_custom_export_schedule( $schedules ) {
		$as_multistore_settings = get_option( 'momo_wsw_as_export_settings' );
		$days                   = isset( $as_multistore_settings['as_export_days'] ) && ! empty( $as_multistore_settings['as_export_days'] ) ? $as_multistore_settings['as_export_days'] : 1;
		$scname                 = 'momo_wsw_order_export_interval';
		$interval               = 60 * 60 * 24 * (int) $days;
		if ( ! isset( $schedules[ $scname ] ) ) {
			$schedules[ $scname ] = array(
				'interval' => $interval,
				/* translators: %s: number of days */
				'display'  => sprintf( esc_html__( 'Every %s day(s)', 'momowsw' ), $days ),
			);
		}
		return $schedules;
	}
	/**
	 * PHP strtotime to local timezone
	 *
	 * @param string $str Time string.
	 */
	public function momo_wsw_cron_strtotime( $str ) {
		$tz_string = get_option( 'timezone_string' );
		$tz_offset = get_option( 'gmt_offset', 0 );

		if ( ! empty( $tz_string ) ) {
			// If site timezone option string exists, use it.
			$timezone = $tz_string;
		} elseif ( 0 === $tz_offset ) {
			// get UTC offset, if it isn’t set then return UTC.
			$timezone = 'UTC';
		} else {
			$timezone = $tz_offset;
			if ( substr( $tz_offset, 0, 1 ) !== '-' && substr( $tz_offset, 0, 1 ) !== '+' && substr( $tz_offset, 0, 1 ) !== 'U' ) {
				$timezone = '+' . $tz_offset;
			}
		}

		$datetime = new DateTime( $str, new DateTimeZone( $timezone ) );
		return $datetime->format( 'U' );
	}
	/**
	 * PHP strtotime to local timezone
	 *
	 * @param string $timestamp Timestamp.
	 */
	public function momo_wsw_cron_timetostr( $timestamp ) {
		return wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $timestamp );
	}
	/**
	 * Generate earlier date from interval
	 *
	 * @param int $timestamp Timestamp.
	 * @param int $interval Interval.
	 */
	public function momo_wsw_cron_generate_from_timestamp( $timestamp, $interval ) {
		$new_timestamp = $timestamp - $interval;
		return $this->momo_wsw_cron_timetostr( $new_timestamp );
	}
}

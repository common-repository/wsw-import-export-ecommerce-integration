<?php
/**
 * Shopify Export functions
 *
 * @package momowsw
 * @author MoMo Themes
 * @since v1.2.0
 */
class MoMo_WSW_Cron {
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
		$as_import_settings = get_option( 'momo_wsw_as_import_settings' );
		$as_export_settings = get_option( 'momo_wsw_as_export_settings' );
		$enable_as_import   = isset( $as_import_settings['enable_as_import'] ) ? $as_import_settings['enable_as_import'] : 'off';
		$enable_as_export   = isset( $as_export_settings['enable_as_export'] ) ? $as_export_settings['enable_as_export'] : 'off';
		$this->cron_hooks   = array(
			'iproduct' => '_momo_wsw_import_product_schedule',
			'eproduct' => '_momo_wsw_export_product_schedule',
		);
		if ( 'on' === $enable_as_import ) {
			add_filter( 'cron_schedules', array( $this, 'momo_wsw_custom_import_schedule' ), 5 ); // phpcs:ignore WordPress.WP.CronInterval
			add_action( $this->cron_hooks['iproduct'], array( $this, 'momo_wsw_import_product_schedule' ), 15 );
		}
		if ( 'on' === $enable_as_export ) {
			add_filter( 'cron_schedules', array( $this, 'momo_wsw_custom_export_schedule' ), 5 ); // phpcs:ignore WordPress.WP.CronInterval
			add_action( $this->cron_hooks['eproduct'], array( $this, 'momo_wsw_export_product_schedule' ), 15 );
		}
	}
	/**
	 * Perform Scheduled Import Cron
	 */
	public function momo_wsw_import_product_schedule() {
		global $momowsw;
		$momowsw->premium->sync->momo_wsw_import_shceduled_products_from_shopify();
	}
	/**
	 * Enable Product Import Cron
	 */
	public function momo_wsw_enable_product_import_cron() {
		$as_import_settings = get_option( 'momo_wsw_as_import_settings' );
		wp_clear_scheduled_hook( $this->cron_hooks['iproduct'] );
		$days   = isset( $as_import_settings['as_import_days'] ) && ! empty( $as_import_settings['as_import_days'] ) ? $as_import_settings['as_import_days'] : 1;
		$hour   = isset( $as_import_settings['as_import_hour'] ) && ! empty( $as_import_settings['as_import_hour'] ) ? $as_import_settings['as_import_hour'] : '00';
		$minute = isset( $as_import_settings['as_import_minute'] ) && ! empty( $as_import_settings['as_import_minute'] ) ? $as_import_settings['as_import_minute'] : '00';
		$scname = 'momo_wsw_product_import_interval';
		$nrun   = $this->momo_wsw_cron_strtotime( '+' . $days . ' days ' . $hour . ':' . $minute );
		$result = wp_schedule_event( $nrun, $scname, $this->cron_hooks['iproduct'], array(), true );
	}
	/**
	 * Disable Product Import Cron
	 */
	public function momo_wsw_disable_product_import_cron() {
		wp_clear_scheduled_hook( $this->cron_hooks['iproduct'] );
	}
	/**
	 * Add Import Cron Schedule
	 *
	 * @param array $schedules Schedules.
	 */
	public function momo_wsw_custom_import_schedule( $schedules ) {
		$as_import_settings   = get_option( 'momo_wsw_as_import_settings' );
		$days                 = isset( $as_import_settings['as_import_days'] ) && ! empty( $as_import_settings['as_import_days'] ) ? $as_import_settings['as_import_days'] : 1;
		$scname               = 'momo_wsw_product_import_interval';
		$interval             = 60 * 60 * 24 * (int) $days;
		$schedules[ $scname ] = array(
			'interval' => $interval,
			/* translators: %s: number of days */
			'display'  => sprintf( esc_html__( 'Every %s day(s)', 'momowsw' ), $days ),
		);
		return $schedules;
	}
	/**
	 * Perform Scheduled Export Cron
	 */
	public function momo_wsw_export_product_schedule() {
		global $momowsw;
		$momowsw->premium->sync->momo_wsw_export_shceduled_products_to_shopify();
	}
	/**
	 * Enable Product Export Cron
	 */
	public function momo_wsw_enable_product_export_cron() {
		$as_export_settings = get_option( 'momo_wsw_as_export_settings' );
		wp_clear_scheduled_hook( $this->cron_hooks['eproduct'] );
		$days   = isset( $as_export_settings['as_export_days'] ) && ! empty( $as_export_settings['as_export_days'] ) ? $as_export_settings['as_export_days'] : 1;
		$hour   = isset( $as_export_settings['as_export_hour'] ) && ! empty( $as_export_settings['as_export_hour'] ) ? $as_export_settings['as_export_hour'] : '00';
		$minute = isset( $as_export_settings['as_export_minute'] ) && ! empty( $as_export_settings['as_export_minute'] ) ? $as_export_settings['as_export_minute'] : '00';
		$scname = 'momo_wsw_product_export_interval';
		$nrun   = $this->momo_wsw_cron_strtotime( '+' . $days . ' days ' . $hour . ':' . $minute );
		$result = wp_schedule_event( $nrun, $scname, $this->cron_hooks['eproduct'], array(), true );
	}
	/**
	 * Disable Product Export Cron
	 */
	public function momo_wsw_disable_product_export_cron() {
		wp_clear_scheduled_hook( $this->cron_hooks['eproduct'] );
	}
	/**
	 * Add Export Cron Schedule
	 *
	 * @param array $schedules Schedules.
	 */
	public function momo_wsw_custom_export_schedule( $schedules ) {
		$as_export_settings = get_option( 'momo_wsw_as_export_settings' );
		$days               = isset( $as_export_settings['as_export_days'] ) && ! empty( $as_export_settings['as_export_days'] ) ? $as_export_settings['as_export_days'] : 1;
		$scname             = 'momo_wsw_product_export_interval';
		$interval           = 60 * 60 * 24 * (int) $days;
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

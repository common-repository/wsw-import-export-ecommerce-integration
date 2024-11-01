<?php
/**
 * Shopify Product Feed Cron
 *
 * @package momowsw
 * @author MoMo Themes
 * @since v1.3.0
 */
class MoMo_WSW_PF_Cron {
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
		$pf_feeds_settings = get_option( 'momo_wsw_pf_product_feeds' );
		$enable_pf_feeds   = isset( $pf_feeds_settings['enable_pf_feeds'] ) ? $pf_feeds_settings['enable_pf_feeds'] : 'off';
		$this->cron_hooks  = array(
			'pf_feeds' => '_momo_wsw_product_feeds_schedule',
		);
		if ( 'on' === $enable_pf_feeds ) {
			add_filter( 'cron_schedules', array( $this, 'momo_wsw_custom_product_feeds_schedule' ), 5 ); // phpcs:ignore WordPress.WP.CronInterval
			add_action( $this->cron_hooks['pf_feeds'], array( $this, 'momo_wsw_product_feeds_schedule' ), 15 );
		}
	}
	/**
	 * Perform Scheduled Import Cron
	 */
	public function momo_wsw_product_feeds_schedule() {
		global $momowsw;
		$pf_product_feeds = get_option( 'momo_wsw_pf_product_feeds' );
		$enable_pf_feeds  = $momowsw->fn->momo_wsw_return_check_option( $pf_product_feeds, 'enable_pf_feeds' );
		$file_name_0      = isset( $pf_product_feeds[0]['file_name'] ) ? $pf_product_feeds[0]['file_name'] : '';
		$slug             = sanitize_title( $file_name_0 );
		$upload_dir       = wp_upload_dir();
		$uploads_path     = $upload_dir['basedir'];
		$url              = $uploads_path . '/' . $slug . '.xml';
		$momowsw->premium->feeds->momo_wsw_generate_google_product_feeds( $url );
		$current_ts                          = time();
		$pf_product_feeds[0]['generated_at'] = $current_ts;
		update_option( 'momo_wsw_pf_product_feeds', $pf_product_feeds );
		return $current_ts;
	}
	/**
	 * Enable Product Import Cron
	 */
	public function momo_wsw_enable_product_feed_cron() {
		$pf_feeds_settings = get_option( 'momo_wsw_pf_product_feeds' );
		wp_clear_scheduled_hook( $this->cron_hooks['pf_feeds'] );
		$days   = isset( $pf_feeds_settings[0]['pf_import_days'] ) ? $pf_feeds_settings[0]['pf_import_days'] : 3;
		$hour   = isset( $pf_feeds_settings[0]['pf_import_hour'] ) ? $pf_feeds_settings[0]['pf_import_hour'] : 00;
		$minute = isset( $pf_feeds_settings[0]['pf_import_minute'] ) ? $pf_feeds_settings[0]['pf_import_minute'] : 00;
		$scname = 'momo_wsw_product_feeds_interval';
		$nrun   = $this->momo_wsw_cron_strtotime( '+' . $days . ' days ' . $hour . ':' . $minute );
		$result = wp_schedule_event( $nrun, $scname, $this->cron_hooks['pf_feeds'], array(), true );
		$this->momo_wsw_generate_product_feeds();
	}
	/**
	 * Disable Product Import Cron
	 */
	public function momo_wsw_disable_product_feed_cron() {
		wp_clear_scheduled_hook( $this->cron_hooks['pf_feeds'] );
	}
	/**
	 * Add Import Cron Schedule
	 *
	 * @param array $schedules Schedules.
	 */
	public function momo_wsw_custom_product_feeds_schedule( $schedules ) {
		$pf_feeds_settings    = get_option( 'momo_wsw_pf_product_feeds' );
		$days                 = isset( $pf_feeds_settings[0]['pf_import_days'] ) ? $pf_feeds_settings[0]['pf_import_days'] : 3;
		$scname               = 'momo_wsw_product_feeds_interval';
		$interval             = 60 * 60 * 24 * (int) $days;
		$schedules[ $scname ] = array(
			'interval' => $interval,
			/* translators: %s: number of days */
			'display'  => sprintf( esc_html__( 'Every %s day(s)', 'momowsw' ), $days ),
		);
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
	/**
	 * Generate product feeds
	 */
	public function momo_wsw_generate_product_feeds() {

	}
}

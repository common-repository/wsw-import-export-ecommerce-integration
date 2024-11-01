<?php
/**
 * Shopify Export functions
 *
 * @package momowsw
 * @author MoMo Themes
 * @since v2.0.0
 */
class MoMo_WSW_Other_Cron {
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
		$as_page_import_settings     = get_option( 'momo_wsw_as_page_import_settings' );
		$as_page_export_settings     = get_option( 'momo_wsw_as_page_export_settings' );
		$as_article_import_settings  = get_option( 'momo_wsw_as_article_import_settings' );
		$as_article_export_settings  = get_option( 'momo_wsw_as_article_export_settings' );
		$as_customer_import_settings = get_option( 'momo_wsw_as_customer_import_settings' );
		$as_customer_export_settings = get_option( 'momo_wsw_as_customer_export_settings' );
		$enable_as_page_import       = isset( $as_page_import_settings['enable_as_page_import'] ) ? $as_page_import_settings['enable_as_page_import'] : 'off';
		$enable_as_page_export       = isset( $as_page_export_settings['enable_as_page_export'] ) ? $as_page_export_settings['enable_as_page_export'] : 'off';
		$enable_as_article_import    = isset( $as_article_import_settings['enable_as_article_import'] ) ? $as_article_import_settings['enable_as_article_import'] : 'off';
		$enable_as_article_export    = isset( $as_article_export_settings['enable_as_article_export'] ) ? $as_article_export_settings['enable_as_article_export'] : 'off';
		$enable_as_customer_import   = isset( $as_customer_import_settings['enable_as_customer_import'] ) ? $as_customer_import_settings['enable_as_customer_import'] : 'off';
		$enable_as_customer_export   = isset( $as_customer_export_settings['enable_as_customer_export'] ) ? $as_customer_export_settings['enable_as_customer_export'] : 'off';

		$this->cron_hooks = array(
			'ipage'     => '_momo_wsw_import_page_schedule',
			'epage'     => '_momo_wsw_export_page_schedule',
			'iarticle'  => '_momo_wsw_import_article_schedule',
			'earticle'  => '_momo_wsw_export_article_schedule',
			'icustomer' => '_momo_wsw_import_customer_schedule',
			'ecustomer' => '_momo_wsw_export_customer_schedule',
		);
		if ( 'on' === $enable_as_page_import ) {
			add_filter( 'cron_schedules', array( $this, 'momo_wsw_custom_import_schedule_page' ), 5 ); // phpcs:ignore WordPress.WP.CronInterval
			add_action( $this->cron_hooks['ipage'], array( $this, 'momo_wsw_import_page_schedule' ), 15 );
		}
		if ( 'on' === $enable_as_page_export ) {
			add_filter( 'cron_schedules', array( $this, 'momo_wsw_custom_export_schedule_page' ), 5 ); // phpcs:ignore WordPress.WP.CronInterval
			add_action( $this->cron_hooks['epage'], array( $this, 'momo_wsw_export_page_schedule' ), 15 );
		}
		if ( 'on' === $enable_as_article_import ) {
			add_filter( 'cron_schedules', array( $this, 'momo_wsw_custom_import_schedule_article' ), 5 ); // phpcs:ignore WordPress.WP.CronInterval
			add_action( $this->cron_hooks['iarticle'], array( $this, 'momo_wsw_import_article_schedule' ), 15 );
		}
		if ( 'on' === $enable_as_article_export ) {
			add_filter( 'cron_schedules', array( $this, 'momo_wsw_custom_export_schedule_article' ), 5 ); // phpcs:ignore WordPress.WP.CronInterval
			add_action( $this->cron_hooks['earticle'], array( $this, 'momo_wsw_export_article_schedule' ), 15 );
		}
		if ( 'on' === $enable_as_customer_import ) {
			add_filter( 'cron_schedules', array( $this, 'momo_wsw_custom_import_schedule_customer' ), 5 ); // phpcs:ignore WordPress.WP.CronInterval
			add_action( $this->cron_hooks['icustomer'], array( $this, 'momo_wsw_import_customer_schedule' ), 15 );
		}
		if ( 'on' === $enable_as_customer_export ) {
			add_filter( 'cron_schedules', array( $this, 'momo_wsw_custom_export_schedule_customer' ), 5 ); // phpcs:ignore WordPress.WP.CronInterval
			add_action( $this->cron_hooks['ecustomer'], array( $this, 'momo_wsw_export_customer_schedule' ), 15 );
		}
	}
	/**
	 * Perform Scheduled Import Cron
	 */
	public function momo_wsw_import_page_schedule() {
		global $momowsw;
		$momowsw->premium->syncother->momo_wsw_import_others_from_shopify( 'page' );
	}
	/**
	 * Perform Scheduled Import Cron
	 */
	public function momo_wsw_import_article_schedule() {
		global $momowsw;
		$momowsw->premium->syncother->momo_wsw_import_others_from_shopify( 'article' );
	}
	/**
	 * Perform Scheduled Import Cron
	 */
	public function momo_wsw_import_customer_schedule() {
		global $momowsw;
		$momowsw->premium->syncother->momo_wsw_import_others_from_shopify( 'customer' );
	}
	/**
	 * Enable order Page Cron
	 */
	public function momo_wsw_enable_page_import_cron() {
		$as_page_import_settings = get_option( 'momo_wsw_as_page_import_settings' );
		wp_clear_scheduled_hook( $this->cron_hooks['ipage'] );
		$days   = isset( $as_page_import_settings['as_import_days'] ) && ! empty( $as_page_import_settings['as_import_days'] ) ? $as_page_import_settings['as_import_days'] : 1;
		$hour   = isset( $as_page_import_settings['as_import_hour'] ) && ! empty( $as_page_import_settings['as_import_hour'] ) ? $as_page_import_settings['as_import_hour'] : '00';
		$minute = isset( $as_page_import_settings['as_import_minute'] ) && ! empty( $as_page_import_settings['as_import_minute'] ) ? $as_page_import_settings['as_import_minute'] : '00';
		$scname = 'momo_wsw_page_import_interval';
		$nrun   = $this->momo_wsw_cron_strtotime( '+' . $days . ' days ' . $hour . ':' . $minute );
		$result = wp_schedule_event( $nrun, $scname, $this->cron_hooks['ipage'], array(), true );
	}
	/**
	 * Enable Article Import Cron
	 */
	public function momo_wsw_enable_article_import_cron() {
		$as_article_import_settings = get_option( 'momo_wsw_as_article_import_settings' );
		wp_clear_scheduled_hook( $this->cron_hooks['iarticle'] );
		$days   = isset( $as_article_import_settings['as_import_days'] ) && ! empty( $as_article_import_settings['as_import_days'] ) ? $as_article_import_settings['as_import_days'] : 1;
		$hour   = isset( $as_article_import_settings['as_import_hour'] ) && ! empty( $as_article_import_settings['as_import_hour'] ) ? $as_article_import_settings['as_import_hour'] : '00';
		$minute = isset( $as_article_import_settings['as_import_minute'] ) && ! empty( $as_article_import_settings['as_import_minute'] ) ? $as_article_import_settings['as_import_minute'] : '00';
		$scname = 'momo_wsw_article_import_interval';
		$nrun   = $this->momo_wsw_cron_strtotime( '+' . $days . ' days ' . $hour . ':' . $minute );
		$result = wp_schedule_event( $nrun, $scname, $this->cron_hooks['iarticle'], array(), true );
	}
	/**
	 * Enable Customer Import Cron
	 */
	public function momo_wsw_enable_customer_import_cron() {
		$as_customer_import_settings = get_option( 'momo_wsw_as_customer_import_settings' );
		wp_clear_scheduled_hook( $this->cron_hooks['icustomer'] );
		$days   = isset( $as_customer_import_settings['as_import_days'] ) && ! empty( $as_customer_import_settings['as_import_days'] ) ? $as_customer_import_settings['as_import_days'] : 1;
		$hour   = isset( $as_customer_import_settings['as_import_hour'] ) && ! empty( $as_customer_import_settings['as_import_hour'] ) ? $as_customer_import_settings['as_import_hour'] : '00';
		$minute = isset( $as_customer_import_settings['as_import_minute'] ) && ! empty( $as_customer_import_settings['as_import_minute'] ) ? $as_customer_import_settings['as_import_minute'] : '00';
		$scname = 'momo_wsw_customer_import_interval';
		$nrun   = $this->momo_wsw_cron_strtotime( '+' . $days . ' days ' . $hour . ':' . $minute );
		$result = wp_schedule_event( $nrun, $scname, $this->cron_hooks['icustomer'], array(), true );
	}
	/**
	 * Disable Page Import Cron
	 */
	public function momo_wsw_disable_page_import_cron() {
		wp_clear_scheduled_hook( $this->cron_hooks['ipage'] );
	}
	/**
	 * Disable Article Import Cron
	 */
	public function momo_wsw_disable_article_import_cron() {
		wp_clear_scheduled_hook( $this->cron_hooks['iarticle'] );
	}
	/**
	 * Disable Customer Import Cron
	 */
	public function momo_wsw_disable_customer_import_cron() {
		wp_clear_scheduled_hook( $this->cron_hooks['icustomer'] );
	}
	/**
	 * Add Import Cron Schedule
	 *
	 * @param array $schedules Schedules.
	 */
	public function momo_wsw_custom_import_schedule_page( $schedules ) {
		$as_page_import_settings = get_option( 'momo_wsw_as_page_import_settings' );
		$days                    = isset( $as_page_import_settings['as_import_days'] ) && ! empty( $as_page_import_settings['as_import_days'] ) ? $as_page_import_settings['as_import_days'] : 1;
		$scname                  = 'momo_wsw_page_import_interval';
		$interval                = 60 * 60 * 24 * (int) $days;
		$schedules[ $scname ]    = array(
			'interval' => $interval,
			/* translators: %s: number of days */
			'display'  => sprintf( esc_html__( 'Every %s day(s)', 'momowsw' ), $days ),
		);
		return $schedules;
	}
	/**
	 * Add Import Cron Schedule
	 *
	 * @param array $schedules Schedules.
	 */
	public function momo_wsw_custom_import_schedule_article( $schedules ) {
		$as_article_import_settings = get_option( 'momo_wsw_as_article_import_settings' );
		$days                       = isset( $as_article_import_settings['as_import_days'] ) && ! empty( $as_article_import_settings['as_import_days'] ) ? $as_article_import_settings['as_import_days'] : 1;
		$scname                     = 'momo_wsw_article_import_interval';
		$interval                   = 60 * 60 * 24 * (int) $days;
		$schedules[ $scname ]       = array(
			'interval' => $interval,
			/* translators: %s: number of days */
			'display'  => sprintf( esc_html__( 'Every %s day(s)', 'momowsw' ), $days ),
		);
		return $schedules;
	}
	/**
	 * Add Import Cron Schedule
	 *
	 * @param array $schedules Schedules.
	 */
	public function momo_wsw_custom_import_schedule_customer( $schedules ) {
		$as_customer_import_settings = get_option( 'momo_wsw_as_customer_import_settings' );
		$days                        = isset( $as_customer_import_settings['as_import_days'] ) && ! empty( $as_customer_import_settings['as_import_days'] ) ? $as_customer_import_settings['as_import_days'] : 1;
		$scname                      = 'momo_wsw_customer_import_interval';
		$interval                    = 60 * 60 * 24 * (int) $days;
		$schedules[ $scname ]        = array(
			'interval' => $interval,
			/* translators: %s: number of days */
			'display'  => sprintf( esc_html__( 'Every %s day(s)', 'momowsw' ), $days ),
		);
		return $schedules;
	}
	/**
	 * Perform Scheduled Export Cron
	 */
	public function momo_wsw_export_page_schedule() {
		global $momowsw;
		$momowsw->premium->syncother->momo_wsw_export_others_to_shopify( 'page' );
	}
	/**
	 * Perform Scheduled Export Cron
	 */
	public function momo_wsw_export_article_schedule() {
		global $momowsw;
		$momowsw->premium->syncother->momo_wsw_export_others_to_shopify( 'article' );
	}
	/**
	 * Perform Scheduled Export Cron
	 */
	public function momo_wsw_export_customer_schedule() {
		global $momowsw;
		$momowsw->premium->syncother->momo_wsw_export_others_to_shopify( 'customer' );
	}
	/**
	 * Enable Page Export Cron
	 */
	public function momo_wsw_enable_page_export_cron() {
		$as_page_export_settings = get_option( 'momo_wsw_as_page_export_settings' );
		wp_clear_scheduled_hook( $this->cron_hooks['epage'] );
		$days   = isset( $as_page_export_settings['as_export_days'] ) && ! empty( $as_page_export_settings['as_export_days'] ) ? $as_page_export_settings['as_export_days'] : 1;
		$hour   = isset( $as_page_export_settings['as_export_hour'] ) && ! empty( $as_page_export_settings['as_export_hour'] ) ? $as_page_export_settings['as_export_hour'] : '00';
		$minute = isset( $as_page_export_settings['as_export_minute'] ) && ! empty( $as_page_export_settings['as_export_minute'] ) ? $as_page_export_settings['as_export_minute'] : '00';
		$scname = 'momo_wsw_page_export_interval';
		$nrun   = $this->momo_wsw_cron_strtotime( '+' . $days . ' days ' . $hour . ':' . $minute );
		$result = wp_schedule_event( $nrun, $scname, $this->cron_hooks['epage'], array(), true );
	}
	/**
	 * Enable Article Export Cron
	 */
	public function momo_wsw_enable_article_export_cron() {
		$as_article_export_settings = get_option( 'momo_wsw_as_article_export_settings' );
		wp_clear_scheduled_hook( $this->cron_hooks['earticle'] );
		$days   = isset( $as_article_export_settings['as_export_days'] ) && ! empty( $as_article_export_settings['as_export_days'] ) ? $as_article_export_settings['as_export_days'] : 1;
		$hour   = isset( $as_article_export_settings['as_export_hour'] ) && ! empty( $as_article_export_settings['as_export_hour'] ) ? $as_article_export_settings['as_export_hour'] : '00';
		$minute = isset( $as_article_export_settings['as_export_minute'] ) && ! empty( $as_article_export_settings['as_export_minute'] ) ? $as_article_export_settings['as_export_minute'] : '00';
		$scname = 'momo_wsw_article_export_interval';
		$nrun   = $this->momo_wsw_cron_strtotime( '+' . $days . ' days ' . $hour . ':' . $minute );
		$result = wp_schedule_event( $nrun, $scname, $this->cron_hooks['earticle'], array(), true );
	}
	/**
	 * Enable Customer Export Cron
	 */
	public function momo_wsw_enable_customer_export_cron() {
		$as_customer_export_settings = get_option( 'momo_wsw_as_customer_export_settings' );
		wp_clear_scheduled_hook( $this->cron_hooks['ecustomer'] );
		$days   = isset( $as_customer_export_settings['as_export_days'] ) && ! empty( $as_customer_export_settings['as_export_days'] ) ? $as_customer_export_settings['as_export_days'] : 1;
		$hour   = isset( $as_customer_export_settings['as_export_hour'] ) && ! empty( $as_customer_export_settings['as_export_hour'] ) ? $as_customer_export_settings['as_export_hour'] : '00';
		$minute = isset( $as_customer_export_settings['as_export_minute'] ) && ! empty( $as_customer_export_settings['as_export_minute'] ) ? $as_customer_export_settings['as_export_minute'] : '00';
		$scname = 'momo_wsw_customer_export_interval';
		$nrun   = $this->momo_wsw_cron_strtotime( '+' . $days . ' days ' . $hour . ':' . $minute );
		$result = wp_schedule_event( $nrun, $scname, $this->cron_hooks['ecustomer'], array(), true );
	}
	/**
	 * Disable order Export Cron
	 */
	public function momo_wsw_disable_order_export_cron() {
		wp_clear_scheduled_hook( $this->cron_hooks['eorder'] );
	}
	/**
	 * Add Export Cron Schedule
	 *
	 * @param array $schedules Schedules.
	 */
	public function momo_wsw_custom_export_schedule_page( $schedules ) {
		$as_page_export_settings = get_option( 'momo_wsw_as_page_export_settings' );
		$days                    = isset( $as_page_export_settings['as_export_days'] ) && ! empty( $as_page_export_settings['as_export_days'] ) ? $as_page_export_settings['as_export_days'] : 1;
		$scname                  = 'momo_wsw_page_export_interval';
		$interval                = 60 * 60 * 24 * (int) $days;
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
	 * Add Export Cron Schedule
	 *
	 * @param array $schedules Schedules.
	 */
	public function momo_wsw_custom_export_schedule_article( $schedules ) {
		$as_article_export_settings = get_option( 'momo_wsw_as_article_export_settings' );
		$days                       = isset( $as_article_export_settings['as_export_days'] ) && ! empty( $as_article_export_settings['as_export_days'] ) ? $as_article_export_settings['as_export_days'] : 1;
		$scname                     = 'momo_wsw_article_export_interval';
		$interval                   = 60 * 60 * 24 * (int) $days;
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
	 * Add Export Cron Schedule
	 *
	 * @param array $schedules Schedules.
	 */
	public function momo_wsw_custom_export_schedule_customer( $schedules ) {
		$as_customer_export_settings = get_option( 'momo_wsw_as_customer_export_settings' );
		$days                        = isset( $as_customer_export_settings['as_export_days'] ) && ! empty( $as_customer_export_settings['as_export_days'] ) ? $as_customer_export_settings['as_export_days'] : 1;
		$scname                      = 'momo_wsw_customer_export_interval';
		$interval                    = 60 * 60 * 24 * (int) $days;
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

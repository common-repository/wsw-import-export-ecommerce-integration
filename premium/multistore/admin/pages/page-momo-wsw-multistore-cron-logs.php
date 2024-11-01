<?php
/**
 * MoMO WSW - Cron Logs
 *
 * @author MoMo Themes
 * @package momowsw
 * @since v2.0.0
 */

global $momowsw;
$logs = $momowsw->logs->momo_wsw_get_logs( 'msorder' );

$content = '';
if ( ! empty( $logs ) ) {
	foreach ( $logs as $log ) {
		foreach ( $log as $sn => $lg ) {
			$content .= '[' . strtoupper( $sn ) . ']' . "\n";
			$content .= implode( "\n", $lg ) . "\n\n";
		}
	}
}
?>
<div class="momo-admin-content-box">
	<div class="momo-be-table-header">
		<h3><?php esc_html_e( 'Cron Logs', 'momowsw' ); ?></h3>
	</div>
	<div class="momo-ms-admin-content-main momowsw-as-export-main" id="momowsw-momo-wsw-as-export">
		<div class="momo-be-msg-block"></div>
		<div class="momo-be-section">
			<h2><?php esc_html_e( 'Auto Sync Logs', 'momowcext' ); ?></h2>
			<div class="momo-be-section-block">
				<div class="momo-be-block">
					<textarea class="full-width" rows="12" readonly autocomplete="off" id="momo_wsw_cron_logs_textarea" style="background-color:#0c0c0c; color: #FFF;"><?php echo wp_kses_post( $content ); ?></textarea>
				</div>
			</div>
			<div class="momo-be-buttons-block">
				<span class="momo-be-btn momo-be-btn-secondary momowsw-admin-clear-multi-store-order-logs">
					<?php esc_html_e( 'Clear Logs', 'momowsw' ); ?>
				</span>
			</div>
		</div>
	</div>
</div>

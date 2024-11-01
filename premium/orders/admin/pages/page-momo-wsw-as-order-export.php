<?php

/**
 * MoMO WSW - Shopify Auto Export
 *
 * @author MoMo Themes
 * @package momowsw
 * @since v2.0.0
 */
global $momowsw;
$as_order_export_settings = get_option( 'momo_wsw_as_order_export_settings' );
$enable_as_order_export = $momowsw->fn->momo_wsw_return_check_option( $as_order_export_settings, 'enable_as_order_export' );
$as_order_export_days = ( isset( $as_order_export_settings['as_order_export_days'] ) ? $as_order_export_settings['as_order_export_days'] : '' );
$as_order_export_hour = ( isset( $as_order_export_settings['as_order_export_hour'] ) ? $as_order_export_settings['as_order_export_hour'] : '' );
$as_order_export_minute = ( isset( $as_order_export_settings['as_order_export_minute'] ) ? $as_order_export_settings['as_order_export_minute'] : '' );
$is_premium = momowsw_fs()->is_premium();
$disabled = ( !$is_premium ? 'disabled=disabled' : '' );
$msg = '';
$next = '';
if ( momowsw_fs()->is_premium() ) {
}
?>
<div class="momo-admin-content-box">
	<div class="momo-be-table-header">
		<h3><?php 
esc_html_e( 'Auto Export', 'momowsw' );
?></h3>
	</div>
	<div class="momo-ms-admin-content-main momowsw-as-export-main" id="momowsw-momo-wsw-as-export">
		<div class="momo-be-msg-block <?php 
echo esc_attr( ( !empty( $msg ) ? 'show info' : '' ) );
?>">
			<?php 
echo wp_kses_post( $msg );
?>
		</div>
		<div class="momo-be-msg-block momo-mt-10 <?php 
echo esc_attr( ( !empty( $next ) ? 'show warning' : '' ) );
?>">
			<?php 
echo wp_kses_post( $next );
?>
		</div>
		<div class="momo-be-section">
			<h2>
				<?php 
esc_html_e( 'Export Orders', 'momowcext' );
?>
				<?php 
if ( !$is_premium ) {
    ?>
					<span class="momo-pro-tip"><?php 
    esc_html_e( 'Pro', 'momowsw' );
    ?></span>
					<?php 
}
?>
			</h2>
			<div class="momo-be-section-block">
				<div class="momo-be-block">
					<span class="momo-be-toggle-container" momo-be-tc-yes-container="enable_as_order_export">
						<label class="switch">
							<input type="checkbox" class="switch-input" name="momo_wsw_as_order_export_settings[enable_as_order_export]" autocomplete="off" <?php 
echo esc_attr( $enable_as_order_export );
?> >
							<span class="switch-label" data-on="Yes" data-off="No"></span>
							<span class="switch-handle"></span>
						</label>
					</span>
					<span class="momo-be-toggle-container-label">
						<?php 
esc_html_e( 'Auto export orders', 'momowsw' );
?>
					</span>
					<div class="momo-be-tc-yes-container" id="enable_as_order_export">
						<div class="momo-be-block">
							<p>
								<label class="regular inline">
									<?php 
esc_html_e( 'Run auto export orders every ', 'momowsw' );
?>
								</label>
								<span class="momo-input-group">
									<input type="number" name="momo_wsw_as_order_export_settings[as_order_export_days]" class="momo-small inline" value="<?php 
echo esc_attr( $as_order_export_days );
?>" <?php 
echo esc_attr( $disabled );
?> />
									<span class="momo-input-group-append">
										<span class="momo-input-group-text">
											<?php 
esc_html_e( 'Day(s)', 'momowsw' );
?>
										</span>
									</span>
								</span>
							</p>
						</div>
						<div class="momo-be-block">
							<p>
								<label class="regular inline">
									<?php 
esc_html_e( 'Run auto export at ', 'momowsw' );
?>
								</label>
								<span class="momo-input-group">
									<input type="number" name="momo_wsw_as_order_export_settings[as_order_export_hour]" class="momo-small inline" value="<?php 
echo esc_attr( $as_order_export_hour );
?>" max="24" <?php 
echo esc_attr( $disabled );
?> />
									<span class="momo-input-group-append">
										<span class="momo-input-group-text">
											<?php 
esc_html_e( 'Hour', 'momowsw' );
?>
										</span>
									</span>
								</span>
								<span class="momo-input-group">
									<input type="number" name="momo_wsw_as_order_export_settings[as_order_export_minute]" class="momo-small inline" value="<?php 
echo esc_attr( $as_order_export_minute );
?>" max="60" <?php 
echo esc_attr( $disabled );
?> />
									<span class="momo-input-group-append">
										<span class="momo-input-group-text">
											<?php 
esc_html_e( 'Minute', 'momowsw' );
?>
										</span>
									</span>
								</span>
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="momo-be-note-block">
	<?php 
esc_html_e( 'For auto import and export, first enable the option and save. Then, adjust the day, hour, and minutes as needed, and save again. If a job is scheduled, "Your next scheduled..." will be displayed at the top, along with the scheduled time.', 'momowsw' );
?>
</div>
<?php 
MoMo_WSW_Functions::upgrade_button();
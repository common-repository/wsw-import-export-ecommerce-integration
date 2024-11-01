<?php

/**
 * MoMO WSW - Shopify Auto Import Customers
 *
 * @author MoMo Themes
 * @package momowsw
 * @since v2.0.0
 */
global $momowsw;
$as_import_settings = get_option( 'momo_wsw_as_customer_import_settings' );
$enable_as_customer_import = $momowsw->fn->momo_wsw_return_check_option( $as_import_settings, 'enable_as_customer_import' );
$as_import_days = ( isset( $as_import_settings['as_import_days'] ) ? $as_import_settings['as_import_days'] : '' );
$as_import_hour = ( isset( $as_import_settings['as_import_hour'] ) ? $as_import_settings['as_import_hour'] : '' );
$as_import_minute = ( isset( $as_import_settings['as_import_minute'] ) ? $as_import_settings['as_import_minute'] : '' );
$msg = '';
$next = '';
if ( momowsw_fs()->is_premium() ) {
}
$is_premium = momowsw_fs()->is_premium();
$disabled = ( !$is_premium ? 'disabled=disabled' : '' );
?>
<form method="post" action="options.php" id="momo-momowsw-admin-as-customer-import-form">
	<?php 
settings_fields( 'momowsw-settings-as-customer-import-group' );
?>
	<?php 
do_settings_sections( 'momowsw-settings-as-customer-import-group' );
?>
	<div class="momo-admin-content-box">
		<div class="momo-be-table-header">
			<h3><?php 
esc_html_e( 'Customer Auto Import', 'momowsw' );
?></h3>
		</div>
		<div class="momo-ms-admin-content-main momowsw-as-import-main" id="momowsw-momo-wsw-as-import">
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
				<div class="momo-be-section-block">
					<div class="momo-be-block">
						<span class="momo-be-toggle-container" momo-be-tc-yes-container="enable_as_customer_import">
							<label class="switch">
								<input type="checkbox" class="switch-input" name="momo_wsw_as_customer_import_settings[enable_as_customer_import]" autocomplete="off" <?php 
echo esc_attr( $enable_as_customer_import );
?>>
								<span class="switch-label" data-on="Yes" data-off="No"></span>
								<span class="switch-handle"></span>
							</label>
						</span>
						<span class="momo-be-toggle-container-label">
							<?php 
esc_html_e( 'Auto import customers', 'momowsw' );
?>
						</span>
						<?php 
if ( !$is_premium ) {
    ?>
							<span class="momo-pro-tip"><?php 
    esc_html_e( 'Pro', 'momowsw' );
    ?></span>
							<?php 
}
?>
						<div class="momo-be-tc-yes-container" id="enable_as_customer_import">
							<div class="momo-be-block">
								<p>
									<label class="regular inline">
										<?php 
esc_html_e( 'Run auto import customers every ', 'momowsw' );
?>
									</label>
									<span class="momo-input-group">
										<input type="number" name="momo_wsw_as_customer_import_settings[as_import_days]" class="momo-small inline" value="<?php 
echo esc_attr( $as_import_days );
?>" <?php 
echo esc_attr( $disabled );
?>/>
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
esc_html_e( 'Run auto import at ', 'momowsw' );
?>
									</label>
									<span class="momo-input-group">
										<input type="number" name="momo_wsw_as_customer_import_settings[as_import_hour]" class="momo-small inline" value="<?php 
echo esc_attr( $as_import_hour );
?>" max="24" <?php 
echo esc_attr( $disabled );
?>/>
										<span class="momo-input-group-append">
											<span class="momo-input-group-text">
												<?php 
esc_html_e( 'Hour', 'momowsw' );
?>
											</span>
										</span>
									</span>
									<span class="momo-input-group">
										<input type="number" name="momo_wsw_as_customer_import_settings[as_import_minute]" class="momo-small inline" value="<?php 
echo esc_attr( $as_import_minute );
?>" max="60" <?php 
echo esc_attr( $disabled );
?>/>
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
	<?php 
if ( momowsw_fs()->is_premium() ) {
    ?>
		<?php 
    ?>
	<?php 
}
?>
</form>
<?php 
$as_export_settings = get_option( 'momo_wsw_as_customer_export_settings' );
$enable_as_customer_export = $momowsw->fn->momo_wsw_return_check_option( $as_export_settings, 'enable_as_customer_export' );
$as_export_days = ( isset( $as_export_settings['as_export_days'] ) ? $as_export_settings['as_export_days'] : '' );
$as_export_hour = ( isset( $as_export_settings['as_export_hour'] ) ? $as_export_settings['as_export_hour'] : '' );
$as_export_minute = ( isset( $as_export_settings['as_export_minute'] ) ? $as_export_settings['as_export_minute'] : '' );
$msg = '';
$next = '';
if ( momowsw_fs()->is_premium() ) {
}
?>
<form method="post" action="options.php" id="momo-momowsw-admin-as-customer-export-form">
	<?php 
settings_fields( 'momowsw-settings-as-customer-export-group' );
?>
	<?php 
do_settings_sections( 'momowsw-settings-as-customer-export-group' );
?>
	<div class="momo-admin-content-box">
		<div class="momo-be-table-header">
			<h3><?php 
esc_html_e( 'Customer Auto Export', 'momowsw' );
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
				<div class="momo-be-section-block">
					<div class="momo-be-block">
						<span class="momo-be-toggle-container" momo-be-tc-yes-container="enable_as_customer_export">
							<label class="switch">
								<input type="checkbox" class="switch-input" name="momo_wsw_as_customer_export_settings[enable_as_customer_export]" autocomplete="off" <?php 
echo esc_attr( $enable_as_customer_export );
?>>
								<span class="switch-label" data-on="Yes" data-off="No"></span>
								<span class="switch-handle"></span>
							</label>
						</span>
						<span class="momo-be-toggle-container-label">
							<?php 
esc_html_e( 'Auto export customers', 'momowsw' );
?>
						</span>
						<?php 
if ( !$is_premium ) {
    ?>
							<span class="momo-pro-tip"><?php 
    esc_html_e( 'Pro', 'momowsw' );
    ?></span>
							<?php 
}
?>
						<div class="momo-be-tc-yes-container" id="enable_as_customer_export">
							<div class="momo-be-block">
								<p>
									<label class="regular inline">
										<?php 
esc_html_e( 'Run auto export customers every ', 'momowsw' );
?>
									</label>
									<span class="momo-input-group">
										<input type="number" name="momo_wsw_as_customer_export_settings[as_export_days]" class="momo-small inline" value="<?php 
echo esc_attr( $as_export_days );
?>" <?php 
echo esc_attr( $disabled );
?>/>
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
										<input type="number" name="momo_wsw_as_customer_export_settings[as_export_hour]" class="momo-small inline" value="<?php 
echo esc_attr( $as_export_hour );
?>" max="24" <?php 
echo esc_attr( $disabled );
?>/>
										<span class="momo-input-group-append">
											<span class="momo-input-group-text">
												<?php 
esc_html_e( 'Hour', 'momowsw' );
?>
											</span>
										</span>
									</span>
									<span class="momo-input-group">
										<input type="number" name="momo_wsw_as_customer_export_settings[as_export_minute]" class="momo-small inline" value="<?php 
echo esc_attr( $as_export_minute );
?>" max="60" <?php 
echo esc_attr( $disabled );
?>/>
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
	<?php 
if ( momowsw_fs()->is_premium() ) {
    ?>
		<?php 
    ?>
	<?php 
}
?>
</form>

<?php

/**
 * MoMO WSW - Shopify Auto Import Orders
 *
 * @author MoMo Themes
 * @package momowsw
 * @since v1.0.0
 */
global $momowsw;
$as_order_settings = get_option( 'momo_wsw_multistore_order_sync' );
$enable_as_import = $momowsw->fn->momo_wsw_return_check_option( $as_order_settings, 'enable_as_import' );
$update_shopify_on_completed = $momowsw->fn->momo_wsw_return_check_option( $as_order_settings, 'update_shopify_on_completed' );
$as_import_days = ( isset( $as_order_settings['as_import_days'] ) ? $as_order_settings['as_import_days'] : '' );
$as_import_hour = ( isset( $as_order_settings['as_import_hour'] ) ? $as_order_settings['as_import_hour'] : '' );
$as_import_minute = ( isset( $as_order_settings['as_import_minute'] ) ? $as_order_settings['as_import_minute'] : '' );
$enable_tracking_information = $momowsw->fn->momo_wsw_return_check_option( $as_order_settings, 'enable_tracking_information' );
$msg = '';
$next = '';
if ( momowsw_fs()->is_premium() ) {
}
$is_premium = momowsw_fs()->is_premium();
$disabled = ( !$is_premium ? 'disabled=disabled' : '' );
?>
<div class="momo-admin-content-box">
	<div class="momo-be-table-header">
		<h3><?php 
esc_html_e( 'Order Auto Sync', 'momowsw' );
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
			<h2><?php 
esc_html_e( 'Order Import', 'momowsw' );
?></h2>
			<div class="momo-be-section-block">
				<div class="momo-be-block">
					<span class="momo-be-toggle-container" momo-be-tc-yes-container="enable_as_import">
						<label class="switch">
							<input type="checkbox" class="switch-input" name="momo_wsw_multistore_order_sync[enable_as_import]" autocomplete="off" <?php 
echo esc_attr( $enable_as_import );
?> <?php 
echo esc_attr( $disabled );
?> >
							<span class="switch-label" data-on="Yes" data-off="No"></span>
							<span class="switch-handle"></span>
						</label>
					</span>
					<span class="momo-be-toggle-container-label">
						<?php 
esc_html_e( 'Auto import orders', 'momowsw' );
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
					<div class="momo-be-tc-yes-container" id="enable_as_import">
						<div class="momo-be-block">
							<p>
								<label class="regular inline">
									<?php 
esc_html_e( 'Run auto import orders every ', 'momowsw' );
?>
								</label>
								<span class="momo-input-group">
									<input type="number" name="momo_wsw_multistore_order_sync[as_import_days]" class="momo-small inline" value="<?php 
echo esc_attr( $as_import_days );
?>"/>
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
									<input type="number" name="momo_wsw_multistore_order_sync[as_import_hour]" class="momo-small inline" value="<?php 
echo esc_attr( $as_import_hour );
?>" max="24"/>
									<span class="momo-input-group-append">
										<span class="momo-input-group-text">
											<?php 
esc_html_e( 'Hour', 'momowsw' );
?>
										</span>
									</span>
								</span>
								<span class="momo-input-group">
									<input type="number" name="momo_wsw_multistore_order_sync[as_import_minute]" class="momo-small inline" value="<?php 
echo esc_attr( $as_import_minute );
?>" max="60"/>
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
		<div class="momo-be-section">
			<h2><?php 
esc_html_e( 'Order Fulfillment', 'momowsw' );
?></h2>
			<div class="momo-be-section-block">
				<div class="momo-be-block">
					<span class="momo-be-toggle-container" momo-be-tc-yes-container="update_shopify_on_completed">
						<label class="switch">
							<input type="checkbox" class="switch-input" name="momo_wsw_multistore_order_sync[update_shopify_on_completed]" autocomplete="off" <?php 
echo esc_attr( $update_shopify_on_completed );
?> <?php 
echo esc_attr( $disabled );
?> >
							<span class="switch-label" data-on="Yes" data-off="No"></span>
							<span class="switch-handle"></span>
						</label>
					</span>
					<span class="momo-be-toggle-container-label">
						<?php 
esc_html_e( 'Update shopify orders on woocommerce order completed.', 'momowsw' );
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
						<p class="momo-below-toggle"><i class="momo-highlight"><?php 
esc_html_e( 'Please disable order auto sync from Order settings page', 'momowsw' );
?></i></p>
					</span>
					<div class="momo-be-tc-yes-container" id="update_shopify_on_completed">
						<div class="momo-be-block">
							<span class="momo-be-toggle-container">
								<label class="switch">
									<input type="checkbox" class="switch-input" name="momo_wsw_multistore_order_sync[enable_tracking_information]" autocomplete="off" <?php 
echo esc_attr( $enable_tracking_information );
?>>
									<span class="switch-label" data-on="Yes" data-off="No"></span>
									<span class="switch-handle"></span>
								</label>
							</span>
							<span class="momo-be-toggle-container-label">
								<?php 
esc_html_e( 'Add option to add tracking information in order edit page', 'momowsw' );
?>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php 
MoMo_WSW_Functions::upgrade_button();
?>
</div>

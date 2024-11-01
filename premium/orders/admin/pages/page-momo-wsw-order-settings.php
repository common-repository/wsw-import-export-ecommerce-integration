<?php
/**
 * MoMO WSW - Orders Settings
 *
 * @author MoMo Themes
 * @package momowsw
 * @since v1.4.0
 */

global $momowsw;
$order_settings                = get_option( 'momo_wsw_orders_settings' );
$enable_auto_sync_order_status = $momowsw->fn->momo_wsw_return_check_option( $order_settings, 'enable_auto_sync_order_status' );
$enable_tracking_information   = $momowsw->fn->momo_wsw_return_check_option( $order_settings, 'enable_tracking_information' );

$is_premium = momowsw_fs()->is_premium();
$disabled   = ! $is_premium ? 'disabled=disabled' : '';
?>
<div class="momo-admin-content-box">
	<div class="momo-be-table-header">
		<h3><?php esc_html_e( 'Order Settings', 'momowsw' ); ?></h3>
	</div>
	<div class="momo-ms-admin-content-main momowsw-export-settings-main" id="momowsw-momo-wsw-order-settings-form">
		<div class="momo-be-msg-block"></div>
		<div class="momo-be-buttons-block">
			<div class="momo-be-table-header">
				<h4><?php esc_html_e( 'WooCommerce Orders', 'momowsw' ); ?></h4>
			</div>
			<h2>
				<?php esc_html_e( 'Settings', 'momowcext' ); ?>
				<?php
				if ( ! $is_premium ) {
					?>
					<span class="momo-pro-tip"><?php esc_html_e( 'Pro', 'momowsw' ); ?></span>
					<?php
				}
				?>
			</h2>
			<div class="momo-be-block momo-mb-10">
				<span class="momo-be-toggle-container" momo-be-tc-yes-container="enable_auto_sync_order_status">
					<label class="switch">
						<input type="checkbox" class="switch-input" name="momo_wsw_orders_settings[enable_auto_sync_order_status]" autocomplete="off" <?php echo esc_attr( $enable_auto_sync_order_status ); ?> <?php echo esc_attr( $disabled ); ?> >
						<span class="switch-label" data-on="Yes" data-off="No"></span>
						<span class="switch-handle"></span>
					</label>
				</span>
				<span class="momo-be-toggle-container-label">
					<?php esc_html_e( 'Auto sync order on order status change in WooCommerce', 'momowsw' ); ?>
				</span>
				<div class="momo-be-tc-yes-container" id="enable_auto_sync_order_status">
					<div class="momo-be-block">
						<span class="momo-be-toggle-container">
							<label class="switch">
								<input type="checkbox" class="switch-input" name="momo_wsw_orders_settings[enable_tracking_information]" autocomplete="off" <?php echo esc_attr( $enable_tracking_information ); ?> <?php echo esc_attr( $disabled ); ?> >
								<span class="switch-label" data-on="Yes" data-off="No"></span>
								<span class="switch-handle"></span>
							</label>
						</span>
						<span class="momo-be-toggle-container-label">
							<?php esc_html_e( 'Add option to add tracking information in order edit page', 'momowsw' ); ?>
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php MoMo_WSW_Functions::upgrade_button(); ?>
</div>

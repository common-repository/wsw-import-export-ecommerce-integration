<?php
/**
 * MoMO WSW - Export Settings
 *
 * @author MoMo Themes
 * @package momowsw
 * @since v1.1.0
 */

global $momowsw;
$export_settings         = get_option( 'momo_wsw_export_settings' );
$enable_product_export   = $momowsw->fn->momo_wsw_return_check_option( $export_settings, 'enable_product_export' );
$export_product_variants = $momowsw->fn->momo_wsw_return_check_option( $export_settings, 'export_product_variants' );
$export_product_tags     = $momowsw->fn->momo_wsw_return_check_option( $export_settings, 'export_product_tags' );
$enable_bulk_export      = $momowsw->fn->momo_wsw_return_check_option( $export_settings, 'enable_bulk_export' );
$pstatus                 = isset( $export_settings['product_status'] ) ? $export_settings['product_status'] : 'active';
?>
<div class="momo-admin-content-box">
	<div class="momo-be-table-header">
		<h3><?php esc_html_e( 'Export Settings', 'momowsw' ); ?></h3>
	</div>
	<div class="momo-ms-admin-content-main momowsw-export-settings-main" id="momowsw-momo-wsw-export-settings-form">
		<div class="momo-be-msg-block"></div>
		<div class="momo-be-buttons-block">
			<div class="momo-be-table-header">
				<h4><?php esc_html_e( 'WooCommerce Product', 'momowsw' ); ?></h4>
			</div>
			<div class="momo-be-block momo-mb-10">
				<span class="momo-be-toggle-container" momo-be-tc-yes-container="enable_product_export">
					<label class="switch">
						<input type="checkbox" class="switch-input" name="momo_wsw_export_settings[enable_product_export]" autocomplete="off" <?php echo esc_attr( $enable_product_export ); ?>>
						<span class="switch-label" data-on="Yes" data-off="No"></span>
						<span class="switch-handle"></span>
					</label>
				</span>
				<span class="momo-be-toggle-container-label">
					<?php esc_html_e( 'Enable Woocommerce product export', 'momowsw' ); ?>
				</span>
				<div class="momo-be-tc-yes-container" id="enable_product_export">
					<div class="momo-be-block">
						<span class="momo-be-toggle-container">
							<label class="switch">
								<input type="checkbox" class="switch-input" name="momo_wsw_export_settings[export_product_variants]" autocomplete="off" <?php echo esc_attr( $export_product_variants ); ?>>
								<span class="switch-label" data-on="Yes" data-off="No"></span>
								<span class="switch-handle"></span>
							</label>
						</span>
						<span class="momo-be-toggle-container-label">
							<?php esc_html_e( 'Export product variants', 'momowsw' ); ?>
						</span>
					</div>
					<div class="momo-be-block">
						<span class="momo-be-toggle-container">
							<label class="switch">
								<input type="checkbox" class="switch-input" name="momo_wsw_export_settings[export_product_tags]" autocomplete="off" <?php echo esc_attr( $export_product_tags ); ?>>
								<span class="switch-label" data-on="Yes" data-off="No"></span>
								<span class="switch-handle"></span>
							</label>
						</span>
						<span class="momo-be-toggle-container-label">
							<?php esc_html_e( 'Export product tags', 'momowsw' ); ?>
						</span>
					</div>
					<div class="momo-be-block">
						<p>
							<label class="regular inline">
								<?php
								esc_html_e( 'Product Status ', 'momowsw' );
								?>
							</label>
							<select class="inline" name="momo_wsw_export_settings[product_status]">
								<option value="active" <?php echo esc_attr( 'active' === $pstatus ? 'selected' : '' ); ?> ><?php esc_html_e( 'Active', 'momowsw' ); ?></option>
								<option value="draft" <?php echo esc_attr( 'draft' === $pstatus ? 'selected' : '' ); ?>><?php esc_html_e( 'Draft', 'momowsw' ); ?></option>
								<option value="archived" <?php echo esc_attr( 'archived' === $pstatus ? 'selected' : '' ); ?>><?php esc_html_e( 'Archieved', 'momowsw' ); ?></option>
							</select>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

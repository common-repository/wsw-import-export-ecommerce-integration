<?php
/**
 * MoMO WSW - multistore Import
 *
 * @author MoMo Themes
 * @package momowsw
 * @since v1.0.0
 */

global $momowsw;
$multistore_import                    = get_option( 'momo_wsw_multistore_import' );
$enable_specific_product_import     = $momowsw->fn->momo_wsw_return_check_option( $multistore_import, 'enable_specific_product_import' );
$mark_as_processing                 = $momowsw->fn->momo_wsw_return_check_option( $multistore_import, 'mark_as_processing' );
$auto_add_to_list_imported_products = $momowsw->fn->momo_wsw_return_check_option( $multistore_import, 'auto_add_to_list_imported_products' );
$selected_products                  = isset( $multistore_import['selected_products'] ) && is_array( $multistore_import['selected_products'] ) ? $multistore_import['selected_products'] : array();

$yesone = false;

$is_premium = momowsw_fs()->is_premium();
$disabled   = ! $is_premium ? 'disabled=disabled' : '';
?>
<div class="momo-admin-content-box">
	<div class="momo-be-table-header">
		<h3><?php esc_html_e( 'Multistore Import Settings', 'momowsw' ); ?></h3>
	</div>
	<div class="momo-ms-admin-content-main momowsw-es-api-main" id="momowsw-momo-wsw-fp-import">
		<div class="momo-be-section">
			<div class="momo-be-section">
				<div class="momo-be-block">
					<span class="momo-be-toggle-container" momo-be-tc-yes-container="enable_specific_product_import">
						<label class="switch">
							<input type="checkbox" class="switch-input" name="momo_wsw_multistore_import[enable_specific_product_import]" autocomplete="off" <?php echo esc_attr( $enable_specific_product_import ); ?> <?php echo esc_attr( $disabled ); ?> >
							<span class="switch-label" data-on="Yes" data-off="No"></span>
							<span class="switch-handle"></span>
						</label>
					</span>
					<span class="momo-be-toggle-container-label">
						<?php esc_html_e( 'Enable import of orders for specific product(s) only.', 'momowsw' ); ?>
					</span>
					<?php
					if ( ! $is_premium ) {
						?>
						<span class="momo-pro-tip"><?php esc_html_e( 'Pro', 'momowsw' ); ?></span>
						<?php
					}
					?>
					<div class="momo-be-tc-yes-container" id="enable_specific_product_import">
						<div class="momo-be-block">
							<select id="multistore-select-desired-product" class="form-control" name="momo_wsw_multistore_import[selected_products][]" multiple="multiple" autocomplete="off">
								<?php
								if ( count( $selected_products ) > 0 ) {
									$yesone = false;
									foreach ( $selected_products as $product_id ) {
										$ptitle = get_the_title( $product_id );
										$check  = $momowsw->fn->momowsw_check_product_exist_and_is_shopify( $product_id );
										$class  = ( $check ) ? 'alive' : 'nothere';
										if ( ! $check ) {
											$yesone = true;
										}
										?>
										<option
											value=<?php echo esc_attr( $product_id ); ?>
											selected="selected"
											data-type="<?php echo esc_attr( $class ); ?>"	
											>
											<?php echo esc_html( trim( $ptitle ) ); ?>
										</option>
										<?php
									}
								}
								?>
							</select>
						</div>
						<?php if ( $yesone ) : ?>
							<em class="momo-be-hr-line-blank"></em>
							<span class="info-dot"></span> <?php esc_html_e( 'Trashed or non Shopify item, please remove.', 'momowsw' ); ?>
						<?php endif; ?>
						<em class="momo-be-hr-line-blank"></em>
						<div class="momo-be-block">
							<span class="momo-be-toggle-container">
								<label class="switch">
									<input type="checkbox" class="switch-input" name="momo_wsw_multistore_import[auto_add_to_list_imported_products]" autocomplete="off" <?php echo esc_attr( $auto_add_to_list_imported_products ); ?>>
									<span class="switch-label" data-on="Yes" data-off="No"></span>
									<span class="switch-handle"></span>
								</label>
							</span>
							<span class="momo-be-toggle-container-label">
								<?php esc_html_e( 'Auto add Shopify imported products to this list.', 'momowsw' ); ?>
							</span>
						</div>
					</div>
				</div>
				<div class="momo-be-block">
					<span class="momo-be-toggle-container">
						<label class="switch">
							<input type="checkbox" class="switch-input" name="momo_wsw_multistore_import[mark_as_processing]" autocomplete="off" <?php echo esc_attr( $mark_as_processing ); ?> <?php echo esc_attr( $disabled ); ?> >
							<span class="switch-label" data-on="Yes" data-off="No"></span>
							<span class="switch-handle"></span>
						</label>
					</span>
					<span class="momo-be-toggle-container-label">
						<?php esc_html_e( 'Mark imported order as processing.', 'momowsw' ); ?>
					</span>
					<?php
					if ( ! $is_premium ) {
						?>
						<span class="momo-pro-tip"><?php esc_html_e( 'Pro', 'momowsw' ); ?></span>
						<?php
					}
					?>
				</div>
			</div>
		</div>
	</div>
	<?php MoMo_WSW_Functions::upgrade_button(); ?>
</div>

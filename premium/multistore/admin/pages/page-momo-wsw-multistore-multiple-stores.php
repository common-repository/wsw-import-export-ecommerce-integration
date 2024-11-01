<?php

/**
 * MoMO WSW - multistore Multiple Stores
 *
 * @author MoMo Themes
 * @package momowsw
 * @since v1.0.0
 */
global $momowsw;
$multistore_multiple = get_option( 'momo_wsw_multistore_multiple_stores' );
$enable_multiple_stores = $momowsw->fn->momo_wsw_return_check_option( $multistore_multiple, 'enable_multiple_stores' );
$multi_stores_data = ( isset( $multistore_multiple['multi_stores_data'] ) ? $multistore_multiple['multi_stores_data'] : array() );
$is_premium = momowsw_fs()->is_premium();
/* $multistore_multiple['multi_stores_data'] = array();
update_option( 'momo_wsw_multistore_multiple_stores', $multistore_multiple ); */
?>
<div class="momo-admin-content-box">
	<div class="momo-be-table-header">
		<h3><?php 
esc_html_e( 'Multiple Stores', 'momowsw' );
?></h3>
	</div>
	<div class="momo-ms-admin-content-main momowsw-es-api-main" id="momowsw-momo-wsw-fp-import">
		<div class="momo-be-section">
			<div class="momo-be-section">
				<div class="momo-be-block">
					<span class="momo-be-toggle-container" momo-be-tc-yes-container="enable_multiple_stores">
						<label class="switch">
							<input type="checkbox" class="switch-input" name="momo_wsw_multistore_multiple_stores[enable_multiple_stores]" autocomplete="off" <?php 
echo esc_attr( $enable_multiple_stores );
?>>
							<span class="switch-label" data-on="Yes" data-off="No"></span>
							<span class="switch-handle"></span>
						</label>
					</span>
					<span class="momo-be-toggle-container-label">
						<?php 
esc_html_e( 'Enable multiple Shopify stores only.', 'momowsw' );
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
					<div class="momo-be-tc-yes-container" id="enable_multiple_stores">
						<div class="momo-be-block momo-fp-server-list">
							<ul class="multistore-server-list">
								<?php 
if ( momowsw_fs()->is_premium() ) {
}
?>
							</ul>
						</div>
						<div class="momo-be-block momo-fp-bottom-block momo-be-buttons-block">
						<a href="#" class="momo-be-btn momo-be-btn-secondary add_edit_store_popbox" data-type="add">
							<?php 
esc_html_e( 'Add new Store', 'momowsw' );
?>
						</a>
						</div>
					</div>
				</div>
				<div class="momo-be-block">
					<!-- <input type="hidden" id="multi_stores_data_hidden" name="momo_wsw_multistore_multiple_stores[multi_stores_data]" value="<?php 
echo wp_json_encode( $multi_stores_data );
?>"/> -->
				</div>
			</div>
		</div>
	</div>
	<?php 
MoMo_WSW_Functions::upgrade_button();
?>
</div>

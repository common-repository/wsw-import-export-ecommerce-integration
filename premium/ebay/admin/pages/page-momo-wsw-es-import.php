<?php

/**
 * MoMO WSW - eBay Import Page
 *
 * @author MoMo Themes
 * @package momowsw
 * @since v1.0.0
 */
$shopify_settings = get_option( 'momo_wsw_settings' );
$is_premium = momowsw_fs()->is_premium();
$disabled = ( !$is_premium ? 'disabled=disabled' : '' );
?>
<div class="momo-admin-content-box">
	<h3>
		<?php 
esc_html_e( 'Import eBay Items', 'momowsw' );
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
	</h3>
	<div class="momo-ms-admin-content-main momowsw-import-main" id="momowsw-momo-wsw-import-form">
		<div class="momo-be-msg-block"></div>
		<div class="momo-be-section-block-normal">
			<p>
				<label class="regular block">
					<?php 
esc_html_e( 'Import Item(s) by ', 'momowsw' );
?>
				</label>
				<select class="block" name="momowsw_import_by" autocomplete="off" <?php 
echo esc_attr( $disabled );
?> >
					<option value="item_id"><?php 
esc_html_e( 'SKU', 'momowsw' );
?></option>
					<option value="all_items"><?php 
esc_html_e( 'All Item(s)', 'momowsw' );
?></option>
				</select>
			</p>
		</div>
		<div class="momo-be-option-block show momo-wsw-by-item-id" data-by="product_id" id="momo-by-item-id">
			<div class="momo-eo-ei-block">
				<label class="regular block"><?php 
esc_html_e( 'Item SKU', 'momowsw' );
?></label>
				<input type="text" class="block" name="ebay_sku" <?php 
echo esc_attr( $disabled );
?> />
			</div>
		</div>
		<div class="momo-be-option-block" id="momo-by-all-item" data-by="all_product">
			<div class="momo-be-block">
				<label class="regular block">
					<?php 
esc_html_e( 'Number of item(s) ', 'momowsw' );
?>
				</label>
				<select class="block" name="momowsw_product_limit" autocomplete="off" <?php 
echo esc_attr( $disabled );
?> >
					<option value="50"><?php 
esc_html_e( '50', 'momowsw' );
?></option>
					<option value="100"><?php 
esc_html_e( '100', 'momowsw' );
?></option>
					<option value="150"><?php 
esc_html_e( '150', 'momowsw' );
?></option>
					<option value="200"><?php 
esc_html_e( '200', 'momowsw' );
?></option>
					<option value="250"><?php 
esc_html_e( '250', 'momowsw' );
?></option>
				</select>
			</div>
		</div>
		<div class="momo-be-buttons-block">
			<div class="momo-be-block momo-mb-10">
				<span class="momo-be-toggle-container">
					<label class="switch">
						<input type="checkbox" class="switch-input" name="momowsw_imported_donot_fetch" autocomplete="off">
						<span class="switch-label" data-on="Yes" data-off="No"></span>
						<span class="switch-handle"></span>
					</label>
				</span>
				<span class="momo-be-toggle-container-label">
					<?php 
esc_html_e( 'Do not fetch imported product(s) ( Published, Pending or Draft )', 'momowsw' );
?>
				</span>
			</div>
			<?php 
if ( momowsw_fs()->is_premium() ) {
    ?>
				<?php 
    ?>
			<?php 
}
?>
			<div class="momo-be-back-to-list-block">
				<span class="momo-be-float-right momo-be-btn momo-be-btn-extra  momowsw-back-to-fetch-list">
					<?php 
esc_html_e( 'Back to item(s) list', 'momowsw' );
?><i class="fa fa-angle-right"></i>
				</span>
			</div>
		</div>
	</div>
	<!-- Report Block -->
	<div class="momo-be-result-block">
		<div class="momo-be-msg-block"></div>
		<input name="momowsw_generated_items" id="momowsw_generated_items" type="hidden" value="" autocomplete="off"/>
		<div class="momo-be-imports-table">
			<table>
				<thead>
					<tr>
						<th colspan="2">
							<?php 
esc_html_e( 'Product Name', 'momowsw' );
?>
						</th>
						<th>
							<?php 
esc_html_e( 'Vendor', 'momowsw' );
?>
						</th>
						<th>
							<?php 
esc_html_e( 'Description', 'momowsw' );
?>
						</th>
						<th>
							<?php 
esc_html_e( 'Created at', 'momowsw' );
?>
						</th>
						<th>
							<?php 
esc_html_e( 'Status', 'momowsw' );
?>
						</th>
					</tr>
				</thead>
				<tbody>

				</tbody>
			</table>
			<div class="momo-be-buttons-block import-section">
				<div class="momo-be-section-toggle">
					<span class="momo-be-toggle-container">
						<label class="switch">
							<input type="checkbox" class="switch-input" name="momowsw_import_categories" checked="checked" autocomplete="off">
							<span class="switch-label" data-on="Yes" data-off="No"></span>
							<span class="switch-handle"></span>
						</label>
					</span>
					<span class="momo-be-toggle-container-label">
						<?php 
esc_html_e( 'Import categories ans tags', 'momowsw' );
?>
					</span>
					<span class="momo-be-note">
					<?php 
esc_html_e( 'Product type form eBay will be saved as categories and tags will be saved as woocommerce tags.', 'momowsw' );
?>
					</span>
				</div>
				<div class="momo-be-section-toggle">
					<span class="momo-be-toggle-container">
						<label class="switch">
							<input type="checkbox" class="switch-input" name="momowsw_import_variations" checked="checked" autocomplete="off">
							<span class="switch-label" data-on="Yes" data-off="No"></span>
							<span class="switch-handle"></span>
						</label>
					</span>
					<span class="momo-be-toggle-container-label">
						<?php 
esc_html_e( 'Import Itemn Variation(s)', 'momowsw' );
?>
					</span>
				</div>
				<em class="momo-be-hr-line-blank"></em>
				<div class="momo-be-section">
					<label class="regular block">
					<?php 
esc_html_e( 'Product status after import', 'momowsw' );
?>
					</label>
					<select class="block" name="item_status">
						<option value="publish"><?php 
esc_html_e( 'Published', 'momowsw' );
?></option>
						<option value="pending"><?php 
esc_html_e( 'Pending', 'momowsw' );
?></option>
						<option value="draft"><?php 
esc_html_e( 'Draft', 'momowsw' );
?></option>
					</select>
				</div>
				<em class="momo-be-hr-line-blank"></em>
				<div class="momo-be-section">
					<span class="momo-be-btn momo-be-btn-primary momowsw-admin-import-items" data-caller="ebayitem">
						<?php 
esc_html_e( 'Import Item(s)', 'momowsw' );
?>
					</span>
				</div>
			</div>
		</div>
		<div class="momo-be-fetch-more-box momo-be-buttons-block">
			<span class="momo-be-btn momo-be-btn-secondary momowsw-admin-fetch-more">
				<?php 
esc_html_e( 'Fetch More Item(s)', 'momowsw' );
?>
			</span>
		</div>
	</div>
</div>

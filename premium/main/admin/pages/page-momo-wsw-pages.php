<?php

/**
 * MoMO WSW - Shopify Store Pages
 *
 * @author MoMo Themes
 * @package momowsw
 * @since v1.0.0
 */
$shopify_settings = get_option( 'momo_wsw_settings' );
$is_premium = momowsw_fs()->is_premium();
$disabled = ( !$is_premium ? 'disabled=disabled' : '' );
global $momowsw;
?>
<div class="momo-admin-content-box">
	<div class="momo-be-table-header">
		<h3><?php 
esc_html_e( 'Shopify Pages', 'momowsw' );
?></h3>
	</div>
	<div class="momo-ms-admin-content-main momowsw-import-main" id="momowsw-momo-wsw-shopify-pages">
		<div class="momo-be-msg-block"></div>
		<div class="momo-be-section-block-normal">
			<p>
				<label class="regular block">
					<?php 
esc_html_e( 'Fetch Page(s) by ', 'momowsw' );
?>
				</label>
				<?php 
if ( !$is_premium ) {
    ?>
					<span class="momo-pro-tip"><?php 
    esc_html_e( 'Pro', 'momowsw' );
    ?></span>
					<?php 
}
?>
				<select class="block" name="momowsw_import_by" autocomplete="off" <?php 
echo esc_attr( $disabled );
?> >
					<option value="item_id"><?php 
esc_html_e( 'Page ID', 'momowsw' );
?></option>
					<option value="all_items"><?php 
esc_html_e( 'All Page(s)', 'momowsw' );
?></option>
				</select>
			</p>
		</div>
		<div class="momo-be-option-block show momo-wsw-by-item-id" data-by="page_id" id="momo-by-item-id">
			<div class="momo-eo-ei-block">
				<label class="regular block"><?php 
esc_html_e( 'Page ID', 'momowsw' );
?></label>
				<input type="text" class="block" name="shopify_item_id" <?php 
echo esc_attr( $disabled );
?> />
			</div>
			<em class="hr_line"></em>
			<div class="momo-eo-ei-block">
				<span class="momo-be-note">
					<p><?php 
esc_html_e( 'Go to shopify admin page and select your online store and select page to edit ', 'momowsw' );
?></p>
					<?php 
esc_html_e( 'for e.g: ', 'momowsw' );
?></br>
					<i>https://momothemes.myshopify.com/admin/pages/<b>86996320396</b></i></br>
					<i>https://momothemes.myshopify.com/admin/pages/<b>86558015628</b></i>
					<p><?php 
esc_html_e( 'Page ID is the end number of the URL.', 'momowsw' );
?></p>
				</span>
			</div>
		</div>
		<div class="momo-be-option-block momo-wsw-by-all-items" id="momo-by-all-item" data-by="all_pages">
			<div class="momo-be-block">
				<label class="regular block">
					<?php 
esc_html_e( 'Page Status ', 'momowsw' );
?>
				</label>
				<select class="block" name="momowsw_page_status" autocomplete="off">
					<option value="published"><?php 
esc_html_e( 'Published', 'momowsw' );
?></option>
					<option value="unpublished"><?php 
esc_html_e( 'Unpublished', 'momowsw' );
?></option>
					<option value="all"><?php 
esc_html_e( 'All', 'momowsw' );
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
esc_html_e( 'Do not fetch imported page(s) ( Published or Draft )', 'momowsw' );
?>
				</span>
			</div>
			<?php 
if ( momowsw_fs()->is_premium() ) {
}
?>
			<div class="momo-be-back-to-list-block">
				<span class="momo-be-float-right momo-be-btn momo-be-btn-extra  momowsw-back-to-fetch-list">
					<?php 
esc_html_e( 'Back to page(s) list', 'momowsw' );
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
						<th>
							<?php 
esc_html_e( 'Page Title', 'momowsw' );
?>
						</th>
						<th>
							<?php 
esc_html_e( 'Author', 'momowsw' );
?>
						</th>
						<th>
							<?php 
esc_html_e( 'Body', 'momowsw' );
?>
						</th>
						<th>
							<?php 
esc_html_e( 'Published at', 'momowsw' );
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
				<div class="momo-be-section">
					<label class="regular block">
					<?php 
esc_html_e( 'Page status after import', 'momowsw' );
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
					<span class="momo-be-btn momo-be-btn-primary momowsw-admin-import-items" data-caller="page">
						<?php 
esc_html_e( 'Import Page(s)', 'momowsw' );
?>
					</span>
				</div>
			</div>
		</div>
		<div class="momo-be-fetch-more-box momo-be-buttons-block">
			<span class="momo-be-btn momo-be-btn-secondary momowsw-admin-fetch-more">
				<?php 
esc_html_e( 'Fetch More Page(s)', 'momowsw' );
?>
			</span>
		</div>
	</div>
	<?php 
MoMo_WSW_Functions::upgrade_button();
?>
</div>

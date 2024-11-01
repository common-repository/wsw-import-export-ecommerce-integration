<?php

/**
 * MoMO WSW - Shopify Store Orders
 *
 * @author MoMo Themes
 * @package momowsw
 * @since v1.4.0
 */
$shopify_settings = get_option( 'momo_wsw_settings' );
$total_orders = MoMo_WSW_Orders::momowsw_get_total_number_of_orders();
$count = ( isset( $total_orders->count ) ? $total_orders->count : 0 );
$limit = 4;
$no_of_pages = ceil( $count / $limit );
$is_premium = momowsw_fs()->is_premium();
$disabled = ( !$is_premium ? 'disabled=disabled' : '' );
global $momowsw;
?>
<div class="momo-admin-content-box">
	<div class="momo-be-table-header">
		<h3><?php 
esc_html_e( 'Shopify Orders', 'momowsw' );
?></h3>
	</div>
	<div class="momo-ms-admin-content-main momowsw-import-main" id="momowsw-momo-wsw-shopify-orders">
		<div class="momo-be-msg-block"></div>
		<div class="momo-be-section-block-normal">
			<p>
				<label class="regular block">
					<?php 
esc_html_e( 'Fetch Order(s) by ', 'momowsw' );
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
esc_html_e( 'Order ID', 'momowsw' );
?></option>
					<option value="all_items"><?php 
esc_html_e( 'All Order(s)', 'momowsw' );
?></option>
				</select>
			</p>
		</div>
		<div class="momo-be-option-block show momo-wsw-by-item-id" data-by="order_id" id="momo-by-item-id">
			<div class="momo-eo-ei-block">
				<label class="regular block"><?php 
esc_html_e( 'Order ID', 'momowsw' );
?></label>
				<input type="text" class="block" name="shopify_item_id" <?php 
echo esc_attr( $disabled );
?> />
			</div>
			<em class="hr_line"></em>
			<div class="momo-eo-ei-block">
				<span class="momo-be-note">
					<p><?php 
esc_html_e( 'Please import shopify product(s) before importing order(s)', 'momowsw' );
?></p>
				</span>
				<span class="momo-be-note">
					<p><?php 
esc_html_e( 'Go to shopify admin page and select your online store and select order to edit ', 'momowsw' );
?></p>
					<?php 
esc_html_e( 'for e.g: ', 'momowsw' );
?></br>
					<i>https://momothemes.myshopify.com/admin/orders/<b>4070009667724</b></i></br>
					<i>https://momothemes.myshopify.com/admin/orders/<b>4074095640716</b></i>
					<p><?php 
esc_html_e( 'Order ID is the end number of the URL.', 'momowsw' );
?></p>
				</span>
			</div>
		</div>
		<div class="momo-be-option-block momo-wsw-by-all-items" id="momo-by-all-item" data-by="all_orders">
			<div class="momo-be-block">
				<label class="regular block">
					<?php 
esc_html_e( 'Order Status ', 'momowsw' );
?>
				</label>
				<select class="block" name="momowsw_page_status" autocomplete="off">
					<option value="all"><?php 
esc_html_e( 'All', 'momowsw' );
?></option>
					<option value="published"><?php 
esc_html_e( 'Closed', 'momowsw' );
?></option>
					<option value="unpublished"><?php 
esc_html_e( 'Open', 'momowsw' );
?></option>
				</select>
			</div>
			<div class="momo-be-block">
				<span class="momo-be-msg-block momo-mt-10 show info">
					<?php 
esc_html_e( 'Total number of orders: ', 'momowsw' );
?><strong><i class="order-count"><?php 
echo esc_html( $count );
?></i></strong>
					<input type="hidden" name="momowsw_order_count" value="<?php 
echo esc_html( $count );
?>"/>
					<span class="momo-be-note">
						<?php 
esc_html__( 'Please note, Only limited orders can processed at a single time.', 'momowsw' );
?>
					</span>
					<span class="momo-be-note">
						<?php 
esc_html_e( 'Please use pagination for next or previous order(s).', 'momowsw' );
?>
					</span>
				</span>
			</div>
			<div class="momo-be-block">
				<label class="regular block">
					<?php 
esc_html_e( 'Page Limit ', 'momowsw' );
?>
				</label>
				<select class="block" name="momowsw_page_limit" autocomplete="off">
					<option value="5"><?php 
esc_html_e( '5', 'momowsw' );
?></option>
					<option value="10"><?php 
esc_html_e( '10', 'momowsw' );
?></option>
					<option value="20"><?php 
esc_html_e( '20', 'momowsw' );
?></option>
					<option value="30"><?php 
esc_html_e( '30', 'momowsw' );
?></option>
					<option value="40"><?php 
esc_html_e( '40', 'momowsw' );
?></option>
				</select>
			</div>
		</div>
		<div class="momo-be-buttons-block">
			<div class="momo-be-block momo-mb-10">
				<span class="momo-be-toggle-container">
					<label class="switch">
						<input type="checkbox" class="switch-input" name="momowsw_imported_donot_fetch" autocomplete="off" <?php 
echo esc_attr( $disabled );
?> >
						<span class="switch-label" data-on="Yes" data-off="No"></span>
						<span class="switch-handle"></span>
					</label>
				</span>
				<span class="momo-be-toggle-container-label">
					<?php 
esc_html_e( 'Do not fetch imported order(s)', 'momowsw' );
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
esc_html_e( 'Back to order(s) list', 'momowsw' );
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
			<table class="momowsw-order-table">
				<thead>
					<tr>
						<th>
							<?php 
esc_html_e( 'Customer', 'momowsw' );
?>
						</th>
						<th>
							<?php 
esc_html_e( 'Items', 'momowsw' );
?>
						</th>
						<th>
							<?php 
esc_html_e( 'Total', 'momowsw' );
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
			<div class="momowsw-pagination">
			</div>
			<div class="momo-be-buttons-block import-section">
				<div class="momo-be-block momo-mb-10 momo-mt-10">
					<span class="momo-be-toggle-container">
						<label class="switch">
							<input type="checkbox" class="switch-input" name="momowsw_import_at_background" autocomplete="off">
							<span class="switch-label" data-on="Yes" data-off="No"></span>
							<span class="switch-handle"></span>
						</label>
					</span>
					<span class="momo-be-toggle-container-label">
						<?php 
esc_html_e( 'Import in background', 'momowsw' );
?>
					</span>
				</div>
				<div class="momo-be-section">
					<span class="momo-be-btn momo-be-btn-primary momowsw-admin-import-items" data-caller="order">
						<?php 
esc_html_e( 'Import Order(s)', 'momowsw' );
?>
					</span>
				</div>
			</div>
		</div>
		<div class="momo-be-fetch-more-box momo-be-buttons-block">
			<span class="momo-be-btn momo-be-btn-secondary momowsw-admin-fetch-more">
				<?php 
esc_html_e( 'Fetch More Order(s)', 'momowsw' );
?>
			</span>
		</div>
	</div>
	<?php 
MoMo_WSW_Functions::upgrade_button();
?>
</div>

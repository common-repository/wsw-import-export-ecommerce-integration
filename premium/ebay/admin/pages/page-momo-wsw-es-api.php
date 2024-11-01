<?php

/**
 * MoMO WSW - Shopify Auto Import
 *
 * @author MoMo Themes
 * @package momowsw
 * @since v2.0.0
 */
global $momowsw;
$ebay_sync = get_option( 'momo_wsw_ebay_sync' );
$enable_sandbox = $momowsw->fn->momo_wsw_return_check_option( $ebay_sync, 'enable_sandbox' );
$client_id = ( isset( $ebay_sync['client_id'] ) ? $ebay_sync['client_id'] : '' );
$client_secret = ( isset( $ebay_sync['client_secret'] ) ? $ebay_sync['client_secret'] : '' );
$ru_name = ( isset( $ebay_sync['ru_name'] ) ? $ebay_sync['ru_name'] : '' );
$authorization_code = ( isset( $ebay_sync['authorization_code'] ) ? $ebay_sync['authorization_code'] : '' );
$marketplace_id_s = ( isset( $ebay_sync['marketplace_id'] ) ? $ebay_sync['marketplace_id'] : '' );
$uc_url = '';
$marketplace_list = array();
if ( momowsw_fs()->is_premium() ) {
}
$is_premium = momowsw_fs()->is_premium();
$disabled = ( !$is_premium ? 'disabled=disabled' : '' );
?>
<div class="momo-admin-content-box">
	<div class="momo-be-table-header">
		<h3><?php 
esc_html_e( 'API Settings', 'momowsw' );
?></h3>
	</div>
	<div class="momo-ms-admin-content-main momowsw-es-api-main" id="momowsw-momo-wsw-es-api">
		<div class="momo-be-section">
			<h2>
				<?php 
esc_html_e( 'Ebay API Credentials', 'momowcext' );
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
					<label class="regular block"><?php 
esc_html_e( 'Select Marketplace', 'momowsw' );
?></label>
					<select class="block" name="momo_wsw_ebay_sync[marketplace_id]" <?php 
echo esc_attr( $disabled );
?> >
						<?php 
if ( !empty( $marketplace_list ) ) {
    ?>
							<?php 
    foreach ( $marketplace_list as $marketplace_id => $details ) {
        ?>
								<option value="<?php 
        echo esc_attr( $marketplace_id );
        ?>" <?php 
        echo esc_attr( ( $marketplace_id_s === $marketplace_id ? 'selected="selected"' : '' ) );
        ?>><?php 
        echo esc_html( $details['country'] );
        ?></option>
							<?php 
    }
    ?>
						<?php 
}
?>
					</select>
				</div>
				<div class="momo-be-block">
					<label class="regular block"><?php 
esc_html_e( 'Client ID', 'momowsw' );
?></label>
					<input type="text" class="block wide" name="momo_wsw_ebay_sync[client_id]" value="<?php 
echo esc_attr( $client_id );
?>" <?php 
echo esc_attr( $disabled );
?>/>
				</div>
				<div class="momo-be-block">
					<label class="regular block"><?php 
esc_html_e( 'Client Secret', 'momowsw' );
?></label>
					<input type="text" class="block wide" name="momo_wsw_ebay_sync[client_secret]" value="<?php 
echo esc_attr( $client_secret );
?>" <?php 
echo esc_attr( $disabled );
?>/>
				</div>
				<div class="momo-be-block">
					<label class="regular block"><?php 
esc_html_e( 'Redirect URI (RuName)', 'momowsw' );
?></label>
					<input type="text" class="block wide" name="momo_wsw_ebay_sync[ru_name]" value="<?php 
echo esc_attr( $ru_name );
?>" <?php 
echo esc_attr( $disabled );
?>/>
				</div>
			</div>
			<div class="momo-be-section">
				<div class="momo-be-block">
					<span class="momo-be-toggle-container">
						<label class="switch">
							<input type="checkbox" class="switch-input" name="momo_wsw_ebay_sync[enable_sandbox]" autocomplete="off" <?php 
echo esc_attr( $enable_sandbox );
?> <?php 
echo esc_attr( $disabled );
?> >
							<span class="switch-label" data-on="Yes" data-off="No"></span>
							<span class="switch-handle"></span>
						</label>
					</span>
					<span class="momo-be-toggle-container-label">
						<?php 
esc_html_e( 'Enable Sandbox mode', 'momowsw' );
?>
					</span>
				</div>
			</div>
			<div class="momo-be-section-block">
				<div class="momo-be-block">
					<label class="regular block"><?php 
esc_html_e( 'Authorization Code', 'momowsw' );
?></label>
					<input type="text" class="block wide" name="momo_wsw_ebay_sync[authorization_code]" value="<?php 
echo esc_attr( $authorization_code );
?>" <?php 
echo esc_attr( $disabled );
?> />
				</div>
				<span class="momo-be-note">
					<?php 
esc_html_e( 'Please save your eBay API settings before generating authorization code', 'momowsw' );
?>
				</span>
				<?php 
if ( momowsw_fs()->is_premium() ) {
}
?>
			</div>
		</div>
	</div>
</div>

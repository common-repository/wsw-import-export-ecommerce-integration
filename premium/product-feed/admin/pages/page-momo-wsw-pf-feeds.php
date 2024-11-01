<?php

/**
 * MoMO WSW - Shopify Auto Import
 *
 * @author MoMo Themes
 * @package momowsw
 * @since v2.0.0
 */
global $momowsw;
$pf_product_feeds = get_option( 'momo_wsw_pf_product_feeds' );
$enable_pf_feeds = $momowsw->fn->momo_wsw_return_check_option( $pf_product_feeds, 'enable_pf_feeds' );
$file_name_0 = ( isset( $pf_product_feeds[0]['file_name'] ) ? $pf_product_feeds[0]['file_name'] : '' );
$target_country_0 = ( isset( $pf_product_feeds[0]['target_country'] ) ? $pf_product_feeds[0]['target_country'] : '' );
$generated_at_0 = ( isset( $pf_product_feeds[0]['generated_at'] ) ? $pf_product_feeds[0]['generated_at'] : '' );
$clist = MoMo_WSW_Country_List::momo_wsw_generate_cl_array();
$pf_feeds_enabled = ( isset( $pf_product_feeds['enable_pf_feeds'] ) ? $pf_product_feeds['enable_pf_feeds'] : 'off' );
$msg = '';
if ( momowsw_fs()->is_premium() ) {
}
$is_premium = momowsw_fs()->is_premium();
$disabled = ( !$is_premium ? 'disabled=disabled' : '' );
?>
<div class="momo-admin-content-box">
	<div class="momo-be-table-header">
		<h3><?php 
esc_html_e( 'Manage Product Feed', 'momowsw' );
?></h3>
	</div>
	<div class="momo-ms-admin-content-main momowsw-as-import-main" id="momowsw-momo-wsw-as-import">
		<div class="momo-be-section">
			<h2><?php 
esc_html_e( 'Products', 'momowcext' );
?></h2>
			<div class="momo-be-section-block">
				<div class="momo-be-block">
					<span class="momo-be-toggle-container" momo-be-tc-yes-container="enable_pf_feeds">
						<label class="switch">
							<input type="checkbox" class="switch-input" name="momo_wsw_pf_product_feeds[enable_pf_feeds]" autocomplete="off" <?php 
echo esc_attr( $enable_pf_feeds );
?>>
							<span class="switch-label" data-on="Yes" data-off="No"></span>
							<span class="switch-handle"></span>
						</label>
					</span>
					<span class="momo-be-toggle-container-label">
						<?php 
esc_html_e( 'Enable Product feeds', 'momowsw' );
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
					<div class="momo-be-tc-yes-container" id="enable_pf_feeds">
						<div class="momo-be-block">
							<label class="regular block"><?php 
esc_html_e( 'File Name', 'momowsw' );
?></label>
							<input type="text" class="block" name="momo_wsw_pf_product_feeds[0][file_name]" value="<?php 
echo esc_attr( $file_name_0 );
?>" <?php 
echo esc_attr( $disabled );
?> />
						</div>
						<div class="momo-be-block">
							<p>
								<label class="regular block">
									<?php 
esc_html_e( 'Select Channel ', 'momowsw' );
?>
								</label>
								<select class="block" name="momo_wsw_pf_product_feeds[0][channel_select]" autocomplete="off" disabled="disabled">
									<option value="google_merchant"><?php 
esc_html_e( 'Google Merchant', 'momowsw' );
?></option>
								</select>
							</p>
						</div>
						<div class="momo-be-block">
							<p>
								<label class="regular block">
									<?php 
esc_html_e( 'Target Country ', 'momowsw' );
?>
								</label>
								<select class="block" name="momo_wsw_pf_product_feeds[0][target_country]" autocomplete="off" <?php 
echo esc_attr( $disabled );
?> >
									<option value=""><?php 
esc_html_e( 'Select Target Country', 'momowsw' );
?></option>
									<?php 
foreach ( $clist as $ccode => $cname ) {
    ?>
										<option value="<?php 
    echo esc_attr( $ccode );
    ?>"
										<?php 
    echo esc_attr( ( $ccode === $target_country_0 ? 'selected="selected"' : '' ) );
    ?>
										>
										<?php 
    echo esc_html( $cname );
    ?>
										</option>
									<?php 
}
?>
								</select>
							</p>
						</div>
						<?php 
if ( !empty( $crons ) ) {
    ?>
							<div class="momo-be-msg-block momo-mt-10 <?php 
    echo esc_attr( ( 'on' === $pf_feeds_enabled ? 'show info' : '' ) );
    ?>">
								<p>
									<?php 
    esc_html_e( 'Your product feed is active', 'momowsw' );
    ?>
								</p>
								<p>
									<?php 
    echo wp_kses_post( $msg );
    ?>
								</p>
								<p>
									<?php 
    echo esc_html( $pfeedurl );
    ?><a href="<?php 
    echo esc_url( $url );
    ?>" target="_blank"><?php 
    echo esc_url( $url );
    ?></a>
								</p>
								<p>
									<strong><?php 
    esc_html_e( 'Generated at ', 'momowsw' );
    ?></strong> : <i class="momowsw-generated-at-value"><?php 
    echo esc_html( $momowsw->premium->cron->momo_wsw_cron_timetostr( $generated_at_0 ) );
    ?></i>
								</p>
								<p>
									<span class="momo-be-btn momo-be-btn-secondary regenerate" id="momowsw-regenerate-pffeeds">
										<?php 
    esc_html_e( 'Regenerate Feed', 'momowsw' );
    ?>
									</span>
								</p>
							</div>
						<?php 
}
?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php 
MoMo_WSW_Functions::upgrade_button();
?>
</div>

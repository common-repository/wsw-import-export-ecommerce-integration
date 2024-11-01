<?php

/**
 * MoMO WSW - ColourStone Webhooks
 *
 * @author MoMo Themes
 * @package momowsw
 * @since v1.0.0
 */
global $momowsw;
$webhooks_ajax = get_option( 'momo_wsw_webhooks_ajax' );
$lists = array();
if ( momowsw_fs()->is_premium() ) {
}
$current = array();
if ( count( $lists ) > 0 ) {
    foreach ( $lists as $list ) {
        $current[$list->topic] = $list;
    }
}
$is_premium = momowsw_fs()->is_premium();
$wsw_current = ( isset( $webhooks_ajax['wsw_webhooks'] ) ? $webhooks_ajax['wsw_webhooks'] : array() );
?>
<div class="momo-admin-content-box">
	<div class="momo-be-table-header">
		<h3><?php 
esc_html_e( 'Shopify Webhooks', 'momowsw' );
?></h3>
	</div>
	<div class="momo-ms-admin-content-main momowsw-cs-webhooks-main" id="momowsw-momo-wsw-cs-webhooks">
		<div class="momo-be-section">
			<h2>
				<?php 
esc_html_e( 'Webhooks List ', 'momowsw' );
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
		</div>
		<div class="momo-be-section">
			<div class="momo-be-block">
				<h3><?php 
esc_html_e( 'Change on Shopify', 'momowsw' );
?></h3>
				<table class="momo-be-webhooks-list">
					<tr>
						<td class="momo-be-wh-details">
							<?php 
esc_html_e( 'Adjust (WC Product) product quantity on Shopify product(s) quantity change', 'momowsw' );
?>
						</td>
						<td class="momo-be-wh-actions">
							<?php 
if ( momowsw_fs()->is_premium() ) {
    ?>
								<?php 
    ?>
							<?php 
}
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
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="momo-be-section">
			<div class="momo-be-block">
				<h3><?php 
esc_html_e( 'Change on Woocommerce', 'momowsw' );
?></h3>
				<span class="momo-be-note"><?php 
esc_html_e( 'This action requires merchant approval for write_inventory scope.', 'momowsw' );
?></span>
				<table class="momo-be-webhooks-list">
					<tr>
						<td class="momo-be-wh-details">
							<?php 
esc_html_e( 'Adjust (Shopify) product quantity on Woocommerce product(s) quantity change', 'momowsw' );
?>
						</td>
						<td class="momo-be-wh-actions">
							<?php 
if ( momowsw_fs()->is_premium() ) {
    ?>
								<?php 
    ?>
							<?php 
}
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
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	<?php 
MoMo_WSW_Functions::upgrade_button();
?>
</div>

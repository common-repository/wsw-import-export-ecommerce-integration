<?php

/**
 * MoMO WSW - ColourStone Webhooks
 *
 * @author MoMo Themes
 * @package momowsw
 * @since v1.0.0
 */
global $momowsw;
$webhooks_settings = get_option( 'momo_wsw_webhooks_settings' );
$locations = array();
if ( momowsw_fs()->is_premium() ) {
}
$is_premium = momowsw_fs()->is_premium();
$selected = ( isset( $webhooks_settings['momowsw_default_inventory_location'] ) ? $webhooks_settings['momowsw_default_inventory_location'] : '' );
?>
<div class="momo-admin-content-box">
	<div class="momo-be-table-header">
		<h3><?php 
esc_html_e( 'Webhooks Settings', 'momowsw' );
?></h3>
	</div>
	<h2>
		<?php 
esc_html_e( 'Settings', 'momowcext' );
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
	<div class="momo-ms-admin-content-main momowsw-cs-webhooks-settings" id="momowsw-momo-wsw-settings-webhooks">
		<?php 
if ( !$momowsw->fn->momowsw_check_api_settings_saved() ) {
    ?>
			<div class="momo-be-msg-block warning show">
				<p>
				<?php 
    esc_html_e( 'API settings seems empty. Please save your Shopify App API settings in order to view this page.', 'momowsw' );
    ?>
				</p>
				<p>
					<a href="<?php 
    echo esc_url( admin_url( 'admin.php?page=momowsw' ) );
    ?>" class="momo-inside"><?php 
    esc_html_e( 'API Settings', 'momowsw' );
    ?></a>
				</p>
			</div>
		<?php 
} elseif ( !empty( $locations ) ) {
    ?>
		<div class="momo-be-section">
			<h2><?php 
    esc_html_e( 'Shopify Invetory: ', 'momowsw' );
    ?></h2>
		</div>
		<div class="mo-be-block">
			<p>
				<label class="regular inline">
					<?php 
    esc_html_e( 'Select default inventory location when webhook triggers', 'momowsw' );
    ?>
				</label>
				<select class="inline" name="momo_wsw_webhooks_settings[momowsw_default_inventory_location]" autocomplete="off">
					<option value=""><?php 
    esc_html_e( 'Select Location', 'momowsw' );
    ?></option>
					<?php 
    foreach ( $locations as $location ) {
        ?>
						<option value="<?php 
        echo esc_attr( $location->id );
        ?>"
						<?php 
        echo esc_attr( ( (int) $selected === $location->id ? 'selected="selected"' : '' ) );
        ?>
						>
						<?php 
        echo esc_html( $location->name );
        ?>
						</option>
					<?php 
    }
    ?>
				</select>
			</p>
		</div>
		<?php 
}
?>
	</div>
	<?php 
MoMo_WSW_Functions::upgrade_button();
?>
</div>

<?php
/**
 * MoMo WSW - Shopify Settings Page
 *
 * @author MoMo Themes
 * @package momowsw
 * @since v1.0.0
 */

$shopify_settings = get_option( 'momo_wsw_settings' );
$shop_url         = isset( $shopify_settings['shop_url'] ) ? $shopify_settings['shop_url'] : '';
$access_token     = isset( $shopify_settings['access_token'] ) ? $shopify_settings['access_token'] : '';
$api_key          = isset( $shopify_settings['api_key'] ) ? $shopify_settings['api_key'] : '';
$secret_key       = isset( $shopify_settings['secret_key'] ) ? $shopify_settings['secret_key'] : '';
$api_version      = isset( $shopify_settings['api_version'] ) ? $shopify_settings['api_version'] : '';
?>
<div class="momo-admin-content-box">
	<div class="momo-be-table-header">
		<h3><?php esc_html_e( 'Shopify Settings', 'momowsw' ); ?></h3>
	</div>
	<div class="momo-ms-admin-content-main" id="momo-wsw-settings-form">
		<div class="momo-be-section">
			<div class="momo-eo-ei-block">
				<label class="regular block" for="momo_wsw_settings[shop_url]"><?php esc_html_e( 'Shopify URL', 'momowsw' ); ?></label>
				<input type="text" class="block wide momo-required" name="momo_wsw_settings[shop_url]" value="<?php echo esc_html( $shop_url ); ?>" placeholder="momothemes.myshopify.com"/>
			</div>
			<div class="momo-eo-ei-block">
				<label class="regular block" for="momo_wsw_settings[access_token]"><?php esc_html_e( 'Shopify Access Token', 'momowsw' ); ?></label>
				<input type="text" class="block wide momo-required" name="momo_wsw_settings[access_token]" value="<?php echo esc_html( $access_token ); ?>"/>
			</div>
			<div class="momo-eo-ei-block">
				<label class="regular block" for="momo_wsw_settings[api_key]"><?php esc_html_e( 'API Key', 'momowsw' ); ?></label>
				<input type="text" class="block wide momo-required" name="momo_wsw_settings[api_key]" value="<?php echo esc_html( $api_key ); ?>"/>
			</div>
			<div class="momo-eo-ei-block">
				<label class="regular block" for="momo_wsw_settings[secret_key]"><?php esc_html_e( 'API Secret Key', 'momowsw' ); ?></label>
				<input type="text" class="block wide momo-required" name="momo_wsw_settings[secret_key]" value="<?php echo esc_html( $secret_key ); ?>"/>
			</div>
			<div class="momo-eo-ei-block">
				<label class="block"></label>
				<span class="momo-be-note block tut-note">
					<?php
					$tlink = '<a href="http://momothemes.com/documentationwsw"><i class="tlink">' . esc_html__( 'link', 'momowsw' ) . '</i></a>';
					$video = '<a href="https://youtu.be/iV3u1pusnvA"><i class="vlink">' . esc_html__( 'video', 'momowsw' ) . '</i></a>';
					echo sprintf(
						esc_html__(
							/* translators: %1$1s: tutorial link, %2$2s: video link */
							'To learn about generating the keys, check this %1$1s. You can also watch this %2$2s',
							'momowsw'
						),
						$tlink,
						$video
					);
					?>
				</span>
			</div>
			<div class="momo-eo-ei-block">
				<label class="regular block"><?php esc_html_e( 'API Version:', 'momowsw' ); ?></label>
				<input type="text" class="block wide" name="momo_wsw_settings[api_version]" value="<?php echo esc_html( $api_version ); ?>" placeholder="2022-04"/>
			</div>
			<span class="momo-be-note">
				<?php
				echo sprintf(
					wp_kses_post( 'Note: Current API version is <b>2022-04</b>. In case of any changes in API version, you can change version in here.', 'momowsw' )
				);
				?>
			</span>
			<span class="momo-be-note momo-note-block">
				<span class="momo-be-post-sb-message"></span>
				<p>
					<?php esc_html_e( 'If any changes are made to the API keys or authentication details, please ensure to clear the cache before proceeding with further actions. This will help avoid potential issues caused by outdated or incorrect cached data and ensure that the latest credentials are being used.', 'momowsw' ); ?>
				</p>
				<span class="momo-be-btn momo-be-btn-extra momo_wsw_clear_transient">
					<span class="momo-be-spinner"></span>
					<span class="momo-be-spinner-text"><?php esc_html_e( 'Clear Cache', 'momowsw' ); ?></span>
				</span>
			</span>
		</div>
	</div>
</div>

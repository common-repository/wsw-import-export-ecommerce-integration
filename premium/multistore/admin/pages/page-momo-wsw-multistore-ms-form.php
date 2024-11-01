<?php
/**
 * MoMO WSW - multistore Multiple Stores
 *
 * @author MoMo Themes
 * @package momowsw
 * @since v1.0.0
 */

?>
<div class="momo-fp-popbox fp-popbox-background" data-type="new">
	<div class="momo-be-section momo-fp-popbox-container">
		<div class="momo-be-working"></div>
		<span id="momo-fp-close-pb" class="momo-fp-close-pb">X</span>
		<div class="form-holder">
			<div class="momo-be-block">
				<label class="regular inline"><?php esc_html_e( 'Shopify URL:', 'momowsw' ); ?></label>
				<input type="text" class="inline wide" name="shop_url" value="" placeholder="momothemes.myshopify.com"/>
			</div>
			<div class="momo-be-block">
				<label class="regular inline"><?php esc_html_e( 'Shopify Access Token:', 'momowsw' ); ?></label>
				<input type="text" class="inline wide" name="access_token" value=""/>
			</div>
			<div class="momo-be-block">
				<label class="regular inline"><?php esc_html_e( 'API Key:', 'momowsw' ); ?></label>
				<input type="text" class="inline wide" name="api_key" value=""/>
			</div>
			<div class="momo-be-block">
				<label class="regular inline"><?php esc_html_e( 'API Secret Key:', 'momowsw' ); ?></label>
				<input type="text" class="inline wide" name="secret_key" value=""/>
			</div>
			<div class="momo-be-block momo-mt-10">
				<p class="submit">
					<button class="button button-primary momo-be-float-right add_edit_store_submit"><?php esc_html_e( 'Submit', 'momowsw' ); ?></button>
				</p>
			</div>
		</div>
	</div>
</div>

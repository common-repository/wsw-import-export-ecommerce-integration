

<?php
/**
 * MoMo WSW - Page Getting Started
 *
 * @author MoMo Themes
 * @package momowsw
 * @since v2.0.0
 */

global $momowsw;
$assets = $momowsw->momowsw_assets . 'getting-started/';
?>
<style>
	body.shopify_page_momowsw-getting-started #wpcontent{
		padding: 0;
		background: #fff;
	}
	section.momo-be-getting-started{
		padding: 50px;
	}
	.momo-be-gs-header{
		text-align: center;
		padding: 50px 0;
	}
	.momo-be-gs-center{
		text-align: center;
	}
	h2.momo-be-gs-header-title{
		margin: 5px 0;
		font-size: 30px;
		font-weight: 700;
	}
	h3.momo-be-gs-header-subtitle{
		font-weight: 400;
	}
	.momo-be-gs-block{
		/* display: flex; */
	}
	.momo-be-gs-block-column {
		display: flex;
		padding: 30px;
		background: #eaf7ed;
		border-radius: 20px;
	}
	.momo-be-gs-block-column img{
		max-width: 100%;
		height: auto;
	}
	.momo-be-gs-block-column-70 {
		flex: 70%;
	}

	.momo-be-gs-block-column-30 {
		flex: 30%;
		padding: 40px;
	}
	h2.momo-be-gs-block-header{
		font-size: 1.5rem;
		font-weight: 600;
		margin: 1rem 0;
		display: inline-flex;
		align-items: center;
	}
	.momo-be-gs-block-content p{
		font-size: 1rem;
		color: #000;
	}
	.momo-be-gs-container {
		display: flex;
		justify-content: space-between;
		margin-top: 50px;
	}

	.momo-be-gs-column-50 {
		flex-basis: calc(50% - 25px); /* 50% width minus gap */
		padding: 30px;
		background: #eaf7ed;
		border-radius: 20px;
		box-sizing: border-box;
		position: relative;
	}

	.momo-be-gs-column-gap {
		width: 50px; /* Width of the gap */
	}
	.momo-be-gs-mw-60{
		max-width: 60%!important;
	}
	.momo-va-center{
		display: flex;
		justify-content: center; /* Horizontal centering */
		align-items: center; /* Vertical centering */
	}
	.momo-fd-column{
		flex-direction: column;
	}
	.momo-mh-130{
		max-height: 130px;
		max-width: 100%;
	}
	.momo-gs-pro-tip{
		background-color: #b3edbd;
		padding: 1px 12px;
		color: #000;
		font-size: 14px;
		border-radius: 12px;
		position: absolute;
		right: 30px;
		top: 40px;
		font-weight: 600;
	}
</style>
<section class="momo-be-getting-started">
	<div class="momo-be-gs-header">
		<h2 class="momo-be-gs-header-title">
			<?php esc_html_e( 'Getting Started', 'momowsw' ); ?>
		</h2>
	</div>
	<div class="momo-be-gs-block">
		<div class="momo-be-gs-block-column">
			<div class="momo-be-gs-block-column-70">
				<h2 class="momo-be-gs-block-header">
					<?php esc_html_e( 'WordPress Shopify Integration', 'momowsw' ); ?>
				</h2>
				<div class="momo-be-gs-block-content">
					<p>
						<?php esc_html_e( 'WSW WordPress plugin, a seamless integration tool designed to bridge the gap between Shopify and WooCommerce. This powerful plugin empowers online retailers by facilitating effortless import and export of products, orders, and essential data across both e-commerce platforms.', 'momowsw' ); ?>
					</p>
					<p>
						<?php esc_html_e( 'It simplifies the management of your online store by enabling bidirectional synchronization, ensuring that product catalogs and order information stay up-to-date on both Shopify and WooCommerce. Effortlessly import products from one platform to another, maintaining consistency and accuracy in your inventory.', 'momowsw' ); ?>
					</p>
					<p>
						<?php esc_html_e( "It has a user-friendly interface, allowing store owners to easily configure synchronization settings, schedule automated updates, and customize mappings for product attributes. Whether you're migrating from Shopify to WooCommerce or managing a multi-channel selling strategy, WSW streamlines operations, reduces manual efforts, and enhances overall efficiency.", 'momowsw' ); ?>
					</p>
				</div>
			</div>
			<div class="momo-be-gs-block-column-30 momo-be-gs-center momo-va-center momo-fd-column">
				<div class="momo-be-gs-row">
					<img src="<?php echo esc_url( $assets . 'shopify-icon.png' ); ?>" alt="shopify" class="momo-be-gs-mw-60 momo-mh-130" />
				</div>
				<div class="momo-be-gs-row">
					<img src="<?php echo esc_url( $assets . 'woocommerce.png' ); ?>" alt="woocommerce" class="momo-mh-130" />
				</div>
			</div>
		</div>
	</div>
	<div class="momo-be-gs-header">
		<h2 class="momo-be-gs-header-title">
			<?php esc_html_e( 'Unique Features', 'momowsw' ); ?>
		</h2>
		<h3 class="momo-be-gs-header-subtitle">
			<?php esc_html_e( 'These are some useful features of the plugin', 'momowsw' ); ?>
		</h3>
	</div>
	<div class="momo-be-gs-block">
		<div class="momo-be-gs-block-column">
			<div class="momo-be-gs-block-column-70">
				<h2 class="momo-be-gs-block-header">
					<?php esc_html_e( 'MultiChannel Integration', 'momowsw' ); ?>
				</h2>
				<div class="momo-be-gs-block-content">
					<p>
						<?php esc_html_e( 'This plugin is a versatile tool designed to seamlessly integrate with eBay and Google Merchant Center, enhancing the e-commerce experience for users. One of its standout features is its robust import and export functionality, streamlining the management of products across platforms.', 'momowsw' ); ?>
					</p>
					<p>
						<?php esc_html_e( 'With the import feature, users can effortlessly bring in product listings from eBay, populating their WordPress site with relevant details such as product descriptions, images, and pricing. This not only saves time but ensures consistency and accuracy in product information. The export capability, on the other hand, empowers users to showcase their WordPress products on eBay and Google Merchant Center, expanding their reach to a broader audience.', 'momowsw' ); ?>
					</p>
					<p>
						<?php esc_html_e( "The connection with Google Merchant Center is a strategic move to tap into the vast audience using Google Shopping. The plugin optimizes product data for Google's requirements, enhancing visibility in search results and increasing the likelihood of attracting potential customers.", 'momowsw' ); ?>
					</p>
				</div>
			</div>
			<div class="momo-be-gs-block-column-30 momo-be-gs-center momo-va-center">
				<div class="momo-be-gs-row">
					<img src="<?php echo esc_url( $assets . 'multichannel.png' ); ?>" alt="multichannel" />
				</div>
			</div>
		</div>
	</div>
	<div class="momo-be-gs-container momo-be-gs-center">
		<div class="momo-be-gs-column-50">
			<span class="momo-gs-pro-tip"><?php esc_html_e( 'Pro', 'momowsw' ); ?></span>
			<h2 class="momo-be-gs-block-header">
				<?php esc_html_e( 'Automation', 'momowsw' ); ?>
			</h2>
			<div class="momo-be-gs-block-content">
				<div class="momo-be-gs-row">
					<img src="<?php echo esc_url( $assets . 'automation.png' ); ?>" alt="automation" class="momo-mh-130" />
				</div>
				<p>
					<?php esc_html_e( 'It also offers a powerful automation feature. This automation streamlines data synchronization, ensuring a seamless and efficient cross-platform e-commerce experience without the need for manual intervention, saving time and minimizing the risk of errors.', 'momowsw' ); ?>
				</p>
			</div>
		</div>
		<div class="momo-be-gs-column-gap"></div>
		<div class="momo-be-gs-column-50">
			<span class="momo-gs-pro-tip"><?php esc_html_e( 'Pro', 'momowsw' ); ?></span>
			<h2 class="momo-be-gs-block-header">
				<?php esc_html_e( 'Multistore', 'momowsw' ); ?>
			</h2>
			<div class="momo-be-gs-block-content">
				<div class="momo-be-gs-row">
					<img src="<?php echo esc_url( $assets . 'multiple-store.png' ); ?>" alt="multiple-store" class="momo-mh-130" />
				</div>
				<p>
					<?php esc_html_e( 'This WordPress plugin boasts a unique feature facilitating seamless integration with multiple Shopify stores. The import/export functionality empowers users to effortlessly manage products across various Shopify storefronts. ', 'momowsw' ); ?>
				</p>
			</div>
		</div>
	</div>
	<div class="momo-be-gs-container momo-be-gs-center">
		<div class="momo-be-gs-column-50">
			<span class="momo-gs-pro-tip"><?php esc_html_e( 'Pro', 'momowsw' ); ?></span>
			<h2 class="momo-be-gs-block-header">
				<?php esc_html_e( 'Inventory Synchronization', 'momowsw' ); ?>
			</h2>
			<div class="momo-be-gs-block-content">
				<div class="momo-be-gs-row">
					<img src="<?php echo esc_url( $assets . 'sync-inventory.png' ); ?>" alt="sync-inventory" class="momo-mh-130" />
				</div>
				<p>
					<?php esc_html_e( 'This plugin excels in real-time inventory management. It allows users to adjust inventories instantaneously, ensuring synchronization between the two platforms. Whether a product is sold on Shopify or WooCommerce, the plugin automatically updates inventory levels across both.', 'momowsw' ); ?>
				</p>
			</div>
		</div>
		<div class="momo-be-gs-column-gap"></div>
		<div class="momo-be-gs-column-50">
			<span class="momo-gs-pro-tip"><?php esc_html_e( 'Pro', 'momowsw' ); ?></span>
			<h2 class="momo-be-gs-block-header">
				<?php esc_html_e( 'Order Adjustment', 'momowsw' ); ?>
			</h2>
			<div class="momo-be-gs-block-content">
				<div class="momo-be-gs-row">
					<img src="<?php echo esc_url( $assets . 'order-sync.png' ); ?>" alt="order-sync" class="momo-mh-130" />
				</div>
				<p>
					<?php esc_html_e( 'The seamless import/export feature facilitates effortless order transfer between platforms. Automate the process to enhance efficiency,  optimizing your business operations and saving valuable time for more strategic tasks.', 'momowsw' ); ?>
				</p>
			</div>
		</div>
	</div>
	<div class="momo-be-gs-container momo-be-gs-center">
		<div class="momo-be-gs-column-50">
			<h2 class="momo-be-gs-block-header">
				<?php esc_html_e( 'Documentation', 'momowsw' ); ?>
			</h2>
			<div class="momo-be-gs-block-content">
				<div class="momo-be-gs-row">
					<img src="<?php echo esc_url( $assets . 'documentation.png' ); ?>" alt="documentation" class="momo-mh-130" />
				</div>
				<p>
					<?php esc_html_e( 'We have comprehensive documentation, serving as a valuable resource for users. This documentation provides detailed information on installation, configuration, and usage, offering clear guidelines and troubleshooting tips. ', 'momowsw' ); ?>
				</p>
			</div>
		</div>
		<div class="momo-be-gs-column-gap"></div>
		<div class="momo-be-gs-column-50">
			<h2 class="momo-be-gs-block-header">
				<?php esc_html_e( 'HelpDesk', 'momowsw' ); ?>
			</h2>
			<div class="momo-be-gs-block-content">
				<div class="momo-be-gs-row">
					<img src="<?php echo esc_url( $assets . 'helpdesk.png' ); ?>" alt="helpdesk" class="momo-mh-130" />
				</div>
				<p>
					<?php esc_html_e( "The plugin provides a dedicated helpdesk for customer support, offering users a direct avenue to seek assistance. This ensures prompt and reliable support for any queries or issues, enhancing user experience and providing a reliable resource for addressing concerns related to the plugin's functionality and features.", 'momowsw' ); ?>
				</p>
			</div>
		</div>
	</div>
</section>

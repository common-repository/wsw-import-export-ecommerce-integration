<?php
/**
 * Premium settings page content (addition to main settings)
 *
 * @package momowsw
 */

global $momowsw;
?>
<div id="momo-be-wsw-pages" class="momo-be-admin-content">
	<?php
		require_once 'page-momo-wsw-pages.php';
	?>
</div>
<div id="momo-be-wsw-blogs" class="momo-be-admin-content">
	<?php
		require_once 'page-momo-wsw-blogs.php';
	?>
</div>
<div id="momo-be-wsw-customers" class="momo-be-admin-content">
	<?php
	/*
	Old Style for premium
	if ( momowsw_fs()->is_premium() ) {
		if ( momowsw_fs()->is__premium_only() ) {
			require_once 'page-momo-wsw-customers.php';
		}
	} else {
		$pagetitle = esc_html__( 'Shopify Customers', 'momowsw' );
		$line_two  = esc_html__( 'Import customer(s) from Shopify', 'momowsw' );
		require $momowsw->plugin_path . 'premium/page-momo-wsw-upgrade.php';
	}
	*/
	require_once 'page-momo-wsw-customers.php';
	?>
</div>

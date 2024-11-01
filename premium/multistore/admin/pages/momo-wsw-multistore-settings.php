<?php

/**
 * MoMo WSW - Auto Sync Settings Page
 *
 * @author MoMo Themes
 * @package momowsw
 * @since v2.0.0
 */
global $momowsw;
?>
<div id="momo-be-form" class="momo-be-new">
	<div class="momo-be-wrapper">
		<div class="momo-be-header-right">
			<a href="http://momothemes.com/documentationwsw-pro/" target="_blank" class="momo-be-upper-right-menu"><?php 
esc_html_e( 'Documentation', 'momowsw' );
?><i class='bx bxs-info-circle'></i></a>
			<a href="http://helpdesk.momothemes.com/" target="_blank" class="momo-be-upper-right-menu"><?php 
esc_html_e( 'Support', 'momowsw' );
?><i class='bx bx-support'></i></a>
		</div>

		<table class="momo-be-tab-table">
			<tbody>
				<tr>
					<td valign="top">
						<ul class="momo-be-main-tab">
							<li><a class="momo-be-tablinks active" href="#momo-be-fintprint-multiple-stores"><i class='bx bxs-business'></i><span><?php 
esc_html_e( 'Multiple Stores', 'momowsw' );
?></span></a></li>
							<li><a class="momo-be-tablinks" href="#momo-be-wsw-products"><i class='bx bx-paint'></i><span><?php 
esc_html_e( 'Import Product(s)', 'momowsw' );
?></span></a></li>
							<li><a class="momo-be-tablinks" href="#momo-be-fintprint-import-settings"><i class='bx bx-import'></i><span><?php 
esc_html_e( 'Import Settings', 'momowsw' );
?></span></a></li>
							<li><a class="momo-be-tablinks" href="#momo-be-wsw-orders"><i class='bx bx-list-ol'></i><span><?php 
esc_html_e( 'Import Order(s)', 'momowsw' );
?></span></a></li>
							<li><a class="momo-be-tablinks" href="#momo-be-multistore-order-sync-settings"><i class='bx bx-list-ol'></i><span><?php 
esc_html_e( 'Order Sync', 'momowsw' );
?></span></a></li>
							<li><a class="momo-be-tablinks" href="#momo-be-multistore-cron-logs"><i class='bx bxs-book-content'></i><span><?php 
esc_html_e( 'Logs', 'momowsw' );
?></span></a></li>
						</ul>
					</td>
					<td class="momo-be-main-tabcontent" width="100%" valign="top">
						<div class="momo-be-working"></div>
						<div id="momo-be-fintprint-multiple-stores" class="momo-be-admin-content  active">
							<form method="post" action="options.php" id="momo-momowsw-admin-multistore-multiple-stores-form">
								<?php 
settings_fields( 'momowsw-settings-multistore-multiple-stores-group' );
?>
								<?php 
do_settings_sections( 'momowsw-settings-multistore-multiple-stores-group' );
?>
								<?php 
require_once 'page-momo-wsw-multistore-multiple-stores.php';
?>
							</form>
							<?php 
require_once 'page-momo-wsw-multistore-ms-form.php';
?>
						</div>
						<div id="momo-be-wsw-products" class="momo-be-admin-content">
							<?php 
require_once 'page-momo-wsw-import-products.php';
?>
						</div>
						<div id="momo-be-fintprint-import-settings" class="momo-be-admin-content">
							<form method="post" action="options.php" id="momo-momowsw-admin-multistore-import-form">
								<?php 
settings_fields( 'momowsw-settings-multistore-import-group' );
?>
								<?php 
do_settings_sections( 'momowsw-settings-multistore-import-group' );
?>
								<?php 
require_once 'page-momo-wsw-multistore-import.php';
?>
								<?php 
if ( momowsw_fs()->is_premium() ) {
    ?>
									<?php 
    ?>
								<?php 
}
?>
							</form>
						</div>
						<div id="momo-be-wsw-orders" class="momo-be-admin-content">
							<?php 
require_once 'page-momo-wsw-import-orders.php';
?>
						</div>
						<div id="momo-be-multistore-order-sync-settings" class="momo-be-admin-content">
							<form method="post" action="options.php" id="momo-momowsw-admin-multistore-order-sync-form">
								<?php 
settings_fields( 'momowsw-settings-multistore-order-sync-group' );
?>
								<?php 
do_settings_sections( 'momowsw-settings-multistore-order-sync-group' );
?>
								<?php 
require_once 'page-momo-wsw-multistore-order-sync.php';
?>
								<?php 
if ( momowsw_fs()->is_premium() ) {
    ?>
									<?php 
    ?>
								<?php 
}
?>
							</form>
						</div>
						<div id="momo-be-multistore-cron-logs" class="momo-be-admin-content">
							<?php 
require_once 'page-momo-wsw-multistore-cron-logs.php';
?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

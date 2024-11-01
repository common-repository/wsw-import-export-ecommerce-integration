<?php

/**
 * MoMo WSW - Orders Settings Page
 *
 * @author MoMo Themes
 * @package momowsw
 * @since v1.4.0
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
							<li><a class="momo-be-tablinks active" href="#momo-be-wsw-orders"><i class="bx bxs-file-import"></i><span><?php 
esc_html_e( 'Import Orders', 'momowsw' );
?></span></a></li>
							<li><a class="momo-be-tablinks" href="#momo-be-as-order-import"><i class="bx bx-import"></i><span><?php 
esc_html_e( 'Auto Import', 'momowsw' );
?></span></a></li>
							<li><a class="momo-be-tablinks" href="#momo-be-as-order-export"><i class="bx bx-export"></i><span><?php 
esc_html_e( 'Auto Export', 'momowsw' );
?></span></a></li>
							<li><a class="momo-be-tablinks" href="#momo-be-as-orders-settings"><i class='bx bx-cog'></i><span><?php 
esc_html_e( 'Settings', 'momowsw' );
?></span></a></li>
							<li><a class="momo-be-tablinks" href="#momo-be-orders-logs"><i class='bx bxs-book-content'></i><span><?php 
esc_html_e( 'Logs', 'momowsw' );
?></span></a></li>
						</ul>
					</td>
					<td class="momo-be-main-tabcontent" width="100%" valign="top">
						<div class="momo-be-working"></div>	
						<div id="momo-be-wsw-orders" class="momo-be-admin-content active">
							<?php 
require_once 'page-momo-wsw-import-orders.php';
?>
						</div>
						<div id="momo-be-as-order-import" class="momo-be-admin-content">
							<form method="post" action="options.php" id="momo-momowsw-admin-as-order-import-form">
								<?php 
settings_fields( 'momowsw-settings-as-order-import-group' );
?>
								<?php 
do_settings_sections( 'momowsw-settings-as-order-import-group' );
?>
								<?php 
require_once 'page-momo-wsw-as-order-import.php';
?>
								<?php 
if ( momowsw_fs()->is_premium() ) {
}
?>
							</form>
						</div>
						<div id="momo-be-as-order-export" class="momo-be-admin-content">
							<form method="post" action="options.php" id="momo-momowsw-admin-as-order-export-form">
								<?php 
settings_fields( 'momowsw-settings-as-order-export-group' );
?>
								<?php 
do_settings_sections( 'momowsw-settings-as-order-export-group' );
?>
								<?php 
require_once 'page-momo-wsw-as-order-export.php';
?>
								<?php 
if ( momowsw_fs()->is_premium() ) {
}
?>
							</form>
						</div>
						<div id="momo-be-as-orders-settings" class="momo-be-admin-content">
							<form method="post" action="options.php" id="momo-momowsw-admin-order-settings-form">
								<?php 
settings_fields( 'momowsw-settings-orders-group' );
?>
								<?php 
do_settings_sections( 'momowsw-settings-orders-group' );
?>
								<?php 
require_once 'page-momo-wsw-order-settings.php';
?>
								<?php 
if ( momowsw_fs()->is_premium() ) {
}
?>
							</form>
						</div>
						<div id="momo-be-orders-logs" class="momo-be-admin-content">
							<?php 
require_once 'page-momo-wsw-orders-logs.php';
?>
						</div>

					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>


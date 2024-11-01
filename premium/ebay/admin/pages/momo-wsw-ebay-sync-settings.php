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
		<?php 
require_once $momowsw->plugin_path . 'includes/admin/pages/partial-momo-settings-header.php';
?>

		<table class="momo-be-tab-table">
			<tbody>
				<tr>
					<td valign="top">
						<ul class="momo-be-main-tab">
							<li><a class="momo-be-tablinks active" href="#momo-be-es-api"><i class='bx bx-code'></i><span><?php 
esc_html_e( 'eBay API', 'momowsw' );
?></span></a></li>
							<li><a class="momo-be-tablinks" href="#momo-be-es-merchant-location"><i class='bx bxs-edit-location'></i><span><?php 
esc_html_e( 'Location', 'momowsw' );
?></span></a></li>
							<li><a class="momo-be-tablinks" href="#momo-be-es-policies"><i class='bx bxs-notepad'></i><span><?php 
esc_html_e( 'Policies', 'momowsw' );
?></span></a></li>
							<li><a class="momo-be-tablinks" href="#momo-be-es-export-settings"><i class='bx bx-export' ></i><span><?php 
esc_html_e( 'Export Settings', 'momowsw' );
?></span></a></li>
							<li><a class="momo-be-tablinks" href="#momo-be-es-import-page"><i class='bx bx-import' ></i><span><?php 
esc_html_e( 'Import Listings', 'momowsw' );
?></span></a></li>
						</ul>
					</td>
					<td class="momo-be-main-tabcontent" width="100%" valign="top">
						<div class="momo-be-working"></div>	
						<div id="momo-be-es-api" class="momo-be-admin-content active">
							<form method="post" action="options.php" id="momo-momowsw-admin-ebay-sync-form">
								<?php 
settings_fields( 'momowsw-settings-ebay-sync-group' );
?>
								<?php 
do_settings_sections( 'momowsw-settings-ebay-sync-group' );
?>
								<?php 
require_once 'page-momo-wsw-es-api.php';
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
						<div id="momo-be-es-merchant-location" class="momo-be-admin-content">
								<?php 
require_once 'page-momo-wsw-es-location.php';
?>
						</div>
						<div id="momo-be-es-policies" class="momo-be-admin-content">
							<form method="post" action="options.php" id="momo-momowsw-admin-es-policies-form">
								<?php 
settings_fields( 'momowsw-settings-es-policies-group' );
?>
								<?php 
do_settings_sections( 'momowsw-settings-es-policies-group' );
?>
								<?php 
require_once 'page-momo-wsw-es-policies.php';
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
						<div id="momo-be-es-export-settings" class="momo-be-admin-content">
							<form method="post" action="options.php" id="momo-momowsw-admin-es-settings-export-form">
								<?php 
settings_fields( 'momowsw-settings-es-export-group' );
?>
								<?php 
do_settings_sections( 'momowsw-settings-es-export-group' );
?>
								<?php 
require_once 'page-momo-wsw-es-export-settings.php';
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
						<div id="momo-be-es-import-page" class="momo-be-admin-content">
							<?php 
require_once 'page-momo-wsw-es-import.php';
?>
						</div>
						<?php 
MoMo_WSW_Functions::upgrade_button();
?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

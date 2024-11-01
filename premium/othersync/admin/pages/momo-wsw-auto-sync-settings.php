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
							<li><a class="momo-be-tablinks active" href="#momo-be-as-import"><i class="bx bx-import"></i><span><?php 
esc_html_e( 'Auto Import', 'momowsw' );
?></span></a></li>
							<li><a class="momo-be-tablinks" href="#momo-be-as-export"><i class="bx bx-export"></i><span><?php 
esc_html_e( 'Auto Export', 'momowsw' );
?></span></a></li>
							<li><a class="momo-be-tablinks" href="#momo-be-as-page"><i class='bx bxs-copy-alt'></i><span><?php 
esc_html_e( 'Page', 'momowsw' );
?></span></a></li>
							<li><a class="momo-be-tablinks" href="#momo-be-as-article"><i class='bx bx-book-open' ></i><span><?php 
esc_html_e( 'Article', 'momowsw' );
?></span></a></li>
							<li><a class="momo-be-tablinks" href="#momo-be-as-customer"><i class='bx bxs-user-pin' ></i><span><?php 
esc_html_e( 'Customer', 'momowsw' );
?></span></a></li>
							<li><a class="momo-be-tablinks" href="#momo-be-as-cron-logs"><i class='bx bxs-book-content'></i><span><?php 
esc_html_e( 'Logs', 'momowsw' );
?></span></a></li>
						</ul>
					</td>
					<td class="momo-be-main-tabcontent" width="100%" valign="top">
						<div class="momo-be-working"></div>	
						<div id="momo-be-as-import" class="momo-be-admin-content active">
							<form method="post" action="options.php" id="momo-momowsw-admin-as-import-form">
								<?php 
settings_fields( 'momowsw-settings-as-import-group' );
?>
								<?php 
do_settings_sections( 'momowsw-settings-as-import-group' );
?>
								<?php 
require_once 'page-momo-wsw-as-import.php';
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
						<div id="momo-be-as-export" class="momo-be-admin-content">
							<form method="post" action="options.php" id="momo-momowsw-admin-as-export-form">
								<?php 
settings_fields( 'momowsw-settings-as-export-group' );
?>
								<?php 
do_settings_sections( 'momowsw-settings-as-export-group' );
?>
								<?php 
require_once 'page-momo-wsw-as-export.php';
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
						<div id="momo-be-as-page" class="momo-be-admin-content">
							<?php 
require_once 'page-momo-wsw-as-page.php';
?>
						</div>
						<div id="momo-be-as-article" class="momo-be-admin-content">
							<?php 
require_once 'page-momo-wsw-as-article.php';
?>
						</div>
						<div id="momo-be-as-customer" class="momo-be-admin-content">
							<?php 
require_once 'page-momo-wsw-as-customer.php';
?>
						</div>
						<div id="momo-be-as-cron-logs" class="momo-be-admin-content">
							<?php 
require_once 'page-momo-wsw-as-cron-logs.php';
?>
						</div>
						<div class="momo-be-note-block">
							<?php 
esc_html_e( 'For auto import and export, first enable the option and save. Then, adjust the day, hour, and minutes as needed, and save again. If a job is scheduled, "Your next scheduled..." will be displayed at the top, along with the scheduled time.', 'momowsw' );
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


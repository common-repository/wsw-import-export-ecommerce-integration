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
							<li><a class="momo-be-tablinks active" href="#momo-be-as-import"><i class='bx bx-list-ul'></i><span><?php 
esc_html_e( 'Feeds', 'momowsw' );
?></span></a></li>
						</ul>
					</td>
					<td class="momo-be-main-tabcontent" width="100%" valign="top">
						<div class="momo-be-working"></div>	
						<div id="momo-be-as-import" class="momo-be-admin-content active">
							<form method="post" action="options.php" id="momo-momowsw-admin-pf-feeds-form">
								<?php 
settings_fields( 'momowsw-settings-pf-feeds-group' );
?>
								<?php 
do_settings_sections( 'momowsw-settings-pf-feeds-group' );
?>
								<?php 
require_once 'page-momo-wsw-pf-feeds.php';
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
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

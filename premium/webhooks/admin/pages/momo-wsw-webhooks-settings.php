<?php

/**
 * MoMo WSW - Webohhoks Settings Page
 *
 * @author MoMo Themes
 * @package momowsw
 * @since v1.0.0
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
							<li><a class="momo-be-tablinks active" href="#momo-be-cs-webhooks"><i class='bx bx-anchor'></i><span><?php 
esc_html_e( 'Webhooks', 'momowsw' );
?></span></a></li>
							<li><a class="momo-be-tablinks" href="#momo-be-webhooks-settings"><i class='bx bxs-cog'></i><span><?php 
esc_html_e( 'Settings', 'momowsw' );
?></span></a></li>
						</ul>
					</td>
					<td class="momo-be-main-tabcontent" width="100%" valign="top">
						<div class="momo-be-working"></div>

						<div id="momo-be-cs-webhooks" class="momo-be-admin-content active">
							<?php 
require_once 'page-momo-wsw-webhooks.php';
?>
						</div>
						<div id="momo-be-webhooks-settings" class="momo-be-admin-content">
							<form method="post" action="options.php" id="momo-momowsw-admin-webhooks-form">
								<?php 
settings_fields( 'momowsw-settings-webhooks-group' );
?>
								<?php 
do_settings_sections( 'momowsw-settings-webhooks-group' );
?>
								<?php 
require_once 'page-momo-wsw-webhooks-settings.php';
?>
								<?php 
if ( momowsw_fs()->is_premium() ) {
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


<?php
/**
 * MoMo WSW - Admin Settings Page
 *
 * @author MoMo Themes
 * @package momowsw
 * @since v1.0
 */

global $momowsw;
$momowsw_options = get_option( 'momowsw_options' );
?>
<div id="momo-be-form" class="momo-be-new">
	<div class="momo-be-wrapper">
		<?php require_once $momowsw->plugin_path . 'includes/admin/pages/partial-momo-settings-header.php'; ?>

		<table class="momo-be-tab-table">
			<tbody>
				<tr>
					<td valign="top">
						<ul class="momo-be-main-tab">
							<li><a class="momo-be-tablinks active" href="#momo-be-settings-momo-wsw"><i class='bx bx-code-alt'></i><span><?php esc_html_e( 'Settings', 'momowsw' ); ?></span></a></li>
							<li><a class="momo-be-tablinks" href="#momo-be-import-momo-wsw"><i class='bx bx-import'></i><span><?php esc_html_e( 'Shopify Import', 'momowsw' ); ?></span></a></li>
							<li><a class="momo-be-tablinks" href="#momo-be-wsw-export-settings"><i class='bx bx-export' ></i><span><?php esc_html_e( 'Export Settings', 'momowsw' ); ?></span></a></li>
							<?php do_action( 'momo_wsw_main_menu_link' ); ?>
						</ul>
					</td>
					<td class="momo-be-main-tabcontent" width="100%" valign="top">
						<div class="momo-be-working"></div>	
						<div id="momo-be-settings-momo-wsw" class="momo-be-admin-content active">
							<form method="post" action="options.php" id="momo-momowsw-admin-settings-form">
								<?php settings_fields( 'momowsw-settings-group' ); ?>
								<?php do_settings_sections( 'momowsw-settings-group' ); ?>
								<?php require_once 'page-momo-wsw-settings.php'; ?>
								<p class="momo-be-right submit">
									<input type="submit" name="submit" id="submit" class="momo-be-btn momo-be-btn-primary" value="<?php esc_html_e( 'Save Changes', 'momowsw' ); ?>"/>
								</p>
							</form>
						</div>
						<div id="momo-be-import-momo-wsw" class="momo-be-admin-content">
							<?php require_once 'page-momo-wsw-import.php'; ?>
						</div>
						<div id="momo-be-wsw-export-settings" class="momo-be-admin-content">
							<form method="post" action="options.php" id="momo-momowsw-admin-settings-export-form">
								<?php settings_fields( 'momowsw-settings-export-group' ); ?>
								<?php do_settings_sections( 'momowsw-settings-export-group' ); ?>
								<?php require_once 'page-momo-wsw-export-settings.php'; ?>
								<p class="momo-be-right submit">
									<input type="submit" name="submit" id="submit" class="momo-be-btn momo-be-btn-primary" value="<?php esc_html_e( 'Save Changes', 'momowsw' ); ?>"/>
								</p>
							</form>
						</div>
						<?php do_action( 'momo_wsw_main_menu_content' ); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

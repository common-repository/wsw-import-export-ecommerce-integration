
<?php
/**
 * MoMO WSW - Upgrade Page
 *
 * @author MoMo Themes
 * @package momowsw
 * @since v2.0.0
 */

$pagetitle = isset( $pagetitle ) ? $pagetitle : '';
$line_one  = isset( $line_one ) ? $line_one : esc_html__( 'Available in Pro version', 'momowsw' );
$line_two  = isset( $line_two ) ? $line_two : '';

?>
<style>
	.momowsw-premium-upgrade h3{
		text-transform: uppercase;
	}
	.momo-pro-label{
		padding: 4px 8px;
		background: #d63638;
		color: #fff;
		font-weight: 600;
		font-size: 16px;
		line-height: 1.5;
		text-transform: uppercase;
		margin-left: 22px;
		border-radius: 12px;
	}
	.momo-be-upgrade-info{
		display: flex;
		justify-content: center;
		align-items: center;
		height: 50vh;
		background: #f1f1f1;
	}
	.momo-be-ui-content {
		text-align: center;
		max-width: 300px;
		padding: 0 20px;
	}
	.momo-be-ui-lone{
		color: #0c0c0c;
		font-weight: 800;
		font-size: 18px;
		line-height: 2.5;
	}
	.momo-be-ui-ltwo{
		color: #0c0c0c;
		font-weight: 400;
		font-size: 14px;
		line-height: 1.5;
	}
	.momo-be-ui-lthree{
		margin-top: 30px;
	}
	.momo-upgrade-btn{
		padding: 8px 36px;
		background: #d63638;
		color: #fff;
		font-weight: 600;
		font-size: 16px;
		line-height: 1.5;
		border-radius: 12px;
		text-decoration: none;
		display: inline-block;
	}
	.momo-upgrade-btn:hover{
		color: #FFF;
		text-decoration: none;
		opacity: 0.8;
	}
</style>
<div class="momo-admin-content-box momowsw-premium-upgrade">
	<div class="momo-be-table-header">
		<h3><?php echo esc_html( $pagetitle ); ?><span class="momo-pro-label"><?php esc_html_e( 'Pro', 'momowsw' ); ?></span></h3>
	</div>
	<div class="momo-be-upgrade-info">
		<div class="momo-be-ui-content">
			<div class="momo-be-ui-lone">
				<?php echo esc_html( $line_one ); ?>
			</div>
			<div class="momo-be-ui-ltwo">
				<?php echo esc_html( $line_two ); ?>
			</div>
			<div class="momo-be-ui-lthree">
				<a class="momo-upgrade-btn" href="<?php echo esc_url( admin_url( 'admin.php?page=momowsw-pricing' ) ); ?>"><?php esc_html_e( 'Upgrade Now', 'momowsw' ); ?></a>
			</div>
		</div>
	</div>
</div>

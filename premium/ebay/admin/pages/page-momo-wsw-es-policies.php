<?php
/**
 * MoMO WSW - eBay Location
 *
 * @author MoMo Themes
 * @package momowsw
 * @since v1.4.0
 */

global $momowsw;
if ( ! momowsw_fs()->is_premium() || ! $momowsw->premium->eapi->momowsw_check_api_credentials_bool() ) {
	?>
	<div class="momo-admin-content-box">
		<h2>
			<?php esc_html_e( 'eBay Policies', 'momowsw' ); ?>
			<?php
			if ( ! $is_premium ) {
				?>
				<span class="momo-pro-tip"><?php esc_html_e( 'Pro', 'momowsw' ); ?></span>
				<?php
			}
			?>
		</h2>
		<div class="momo-be-msg-block warning show">
			<p>
			<?php esc_html_e( 'API settings seems empty. Please save your ebay credentials first in order to view this page.', 'momowsw' ); ?>
			</p>
			<p>
				<a href="#momo-be-es-api" class="momo-inside-page-link"><?php esc_html_e( 'API Settings', 'momowsw' ); ?></a>
			</p>
		</div>
	</div>
	<?php
} else {
	$ebay_policies  = get_option( 'momo_wsw_ebay_policies' );
	$return_id      = isset( $ebay_policies['return_policy_id'] ) ? $ebay_policies['return_policy_id'] : '';
	$fulfillment_id = isset( $ebay_policies['fulfillment_policy_id'] ) ? $ebay_policies['fulfillment_policy_id'] : '';
	$payment_id     = isset( $ebay_policies['payment_policy_id'] ) ? $ebay_policies['payment_policy_id'] : '';
	$response       = $momowsw->premium->ebayfn->momowsw_get_all_policies( 'return_policy' );
	$messages       = '';
	$ppcount        = 0;
	$fpcount        = 0;
	$rpcount        = 0;
	if ( isset( $response['status'] ) && 403 === (int) $response['status'] ) {
		if ( isset( $response['body']->errors ) ) {
			foreach ( $response['body']->errors as $error ) {
				$messages .= ' ' . $error->message;
			}
		}
	}
	if ( isset( $response['status'] ) && 200 === (int) $response['status'] ) {
		$rpcount   = $response['body']->total;
		$rpolicies = $response['body']->returnPolicies;
	}

	$response = $momowsw->premium->ebayfn->momowsw_get_all_policies( 'fulfillment_policy' );
	if ( isset( $response['status'] ) && 403 === (int) $response['status'] ) {
		if ( isset( $response['body']->errors ) ) {
			foreach ( $response['body']->errors as $error ) {
				$messages .= ' ' . $error->message;
			}
		}
	}
	if ( isset( $response['status'] ) && 200 === (int) $response['status'] ) {
		$fpcount   = $response['body']->total;
		$fpolicies = $response['body']->fulfillmentPolicies;
	}
	$class = '';
	if ( ! empty( $messages ) ) {
		$class = 'show errror';
	}
	$response = $momowsw->premium->ebayfn->momowsw_get_all_policies( 'payment_policy' );
	if ( isset( $response['status'] ) && 403 === (int) $response['status'] ) {
		if ( isset( $response['body']->errors ) ) {
			foreach ( $response['body']->errors as $error ) {
				$messages .= ' ' . $error->message;
			}
		}
	}
	if ( isset( $response['status'] ) && 200 === (int) $response['status'] ) {
		$ppcount   = isset( $response['body']->total ) ? $response['body']->total : 0;
		$ppolicies = $response['body']->paymentPolicies;
	}
	$class = '';
	if ( ! empty( $messages ) ) {
		$class = 'show errror';
	}
	?>
	<div class="momo-admin-content-box">
		<div class="momo-be-table-header">
			<h3><?php esc_html_e( 'eBay Policies', 'momowsw' ); ?></h3>
		</div>
		<div class="momo-ms-admin-content-main momowsw-es-policies-main" id="momowsw-momo-wsw-es-policies">
			<div class="momo-be-msg-block <?php echo esc_attr( $class ); ?>"><?php echo esc_html( $messages ); ?></div>
			<div class="momo-be-section-block ebay-return-policy-form">
				<h2><?php esc_html_e( 'Return Policies', 'momowsw' ); ?></h2>
				<?php if ( isset( $response['status'] ) && 'bad' === (string) $response['status'] ) { ?>
					<div class="momo-be-msg-block show error">
						<p>
						<?php echo esc_html( $response['message'] ); ?>
						</p>
					</div>
				<?php } elseif ( $rpcount <= 0 ) { ?>
					<div class="momo-be-msg-block show error">
						<p>
						<?php esc_html_e( 'Empty return policies. Please create a return policy first.', 'momowsw' ); ?>
						</p>
						<a href="https://www.bizpolicy.ebay.com/businesspolicy/manage" target="_blank"><?php esc_html_e( 'Manage my Policies', 'momowsw' ); ?></a>
					</div>
				<?php } else { ?>
					<div class="momo-be-block">
						<label class="regular inline"><?php esc_html_e( 'Select Policy', 'momowsw' ); ?></label>
						<select class="inline" name="momo_wsw_ebay_policies[return_policy_id]">
						<?php foreach ( $rpolicies as $policy ) { ?>
							<option value="<?php echo esc_attr( $policy->returnPolicyId ); ?>"
							<?php echo esc_attr( $return_id === $policy->returnPolicyId ? 'selected="selected"' : '' ); ?>
							><?php echo esc_html( $policy->name ); ?></option>
						<?php } ?>
						</select>
					</div>
				<?php } ?>
			</div>
			<div class="momo-be-section-block ebay-return-fulfillment-form">
				<h2><?php esc_html_e( 'Fulfillment Policies', 'momowsw' ); ?></h2>
				<?php if ( isset( $response['status'] ) && 'bad' === (string) $response['status'] ) { ?>
					<div class="momo-be-msg-block show error">
						<p>
						<?php echo esc_html( $response['message'] ); ?>
						</p>
					</div>
				<?php } elseif ( $fpcount <= 0 ) { ?>
					<div class="momo-be-msg-block show error">
						<p>
						<?php esc_html_e( 'Empty fulfillment policies. Please create a fulfillment policy first.', 'momowsw' ); ?>
						</p>
						<a href="https://www.bizpolicy.ebay.com/businesspolicy/manage" target="_blank"><?php esc_html_e( 'Manage my Policies', 'momowsw' ); ?></a>
					</div>
				<?php } else { ?>
					<div class="momo-be-block">
						<label class="regular inline"><?php esc_html_e( 'Select Policy', 'momowsw' ); ?></label>
						<select class="inline" name="momo_wsw_ebay_policies[fulfillment_policy_id]">
						<?php foreach ( $fpolicies as $policy ) { ?>
							<option value="<?php echo esc_attr( $policy->fulfillmentPolicyId ); ?>" 
							<?php echo esc_attr( $fulfillment_id === $policy->fulfillmentPolicyId ? 'selected="selected"' : '' ); ?>
							><?php echo esc_html( $policy->name ); ?></option>
						<?php } ?>
						</select>
					</div>
				<?php } ?>
			</div>
			<div class="momo-be-section-block ebay-return-fulfillment-form">
				<h2><?php esc_html_e( 'Payment Policies', 'momowsw' ); ?></h2>
				<?php if ( isset( $response['status'] ) && 'bad' === (string) $response['status'] ) { ?>
					<div class="momo-be-msg-block show error">
						<p>
						<?php echo esc_html( $response['message'] ); ?>
						</p>
					</div>
				<?php } elseif ( $ppcount <= 0 ) { ?>
					<div class="momo-be-msg-block show error">
						<p>
						<?php esc_html_e( 'Empty payment policies. Please create a payment policy first.', 'momowsw' ); ?>
						</p>
						<a href="https://www.bizpolicy.ebay.com/businesspolicy/manage" target="_blank"><?php esc_html_e( 'Manage my Policies', 'momowsw' ); ?></a>
					</div>
				<?php } else { ?>
					<div class="momo-be-block">
						<label class="regular inline"><?php esc_html_e( 'Select Policy', 'momowsw' ); ?></label>
						<select class="inline" name="momo_wsw_ebay_policies[payment_policy_id]">
						<?php foreach ( $ppolicies as $policy ) { ?>
							<option value="<?php echo esc_attr( $policy->paymentPolicyId ); ?>" 
							<?php echo esc_attr( $payment_id === $policy->paymentPolicyId ? 'selected="selected"' : '' ); ?>
							><?php echo esc_html( $policy->name ); ?></option>
						<?php } ?>
						</select>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
<?php } // Top Level. ?>

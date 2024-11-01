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
			<?php esc_html_e( 'eBay Merchant Location', 'momowsw' ); ?>
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
	$ebay_location = get_option( 'momo_wsw_ebay_location' );
	$location_id   = isset( $ebay_location['location_id'] ) ? $ebay_location['location_id'] : '';
	$country_list  = $momowsw->premium->ebayfn->momowsw_get_country_with_code();
	if ( ! empty( $location_id ) ) {
		$response         = $momowsw->premium->eapi->momowsw_get_ebay_location_details( $location_id );
		$location_details = '';
		if ( isset( $response['status'] ) && 200 === (int) $response['status'] ) {
			$location_details = $response['body'];
		}
	}
	$response        = $momowsw->premium->eapi->momowsw_get_all_locations();
	$saved_locations = array();
	if ( isset( $response['status'] ) && 200 === (int) $response['status'] ) {
		$locations = $response['body'];
		$total     = isset( $locations->total ) ? $locations->total : 0;
		if ( $total > 0 ) {
			foreach ( $locations->locations as $location ) {
				$saved_locations[] = array(
					'location_key' => $location->merchantLocationKey,
					'name'         => $location->name,
				);
			}
		}
	}

	?>
	<div class="momo-admin-content-box">
		<div class="momo-be-table-header">
			<h3><?php esc_html_e( 'eBay Merchant Location', 'momowsw' ); ?></h3>
		</div>
		<div class="momo-ms-admin-content-main momowsw-es-location-main" id="momowsw-momo-wsw-es-location">
			<div class="momo-be-msg-block"></div>
			<?php if ( isset( $response['status'] ) && 'bad' === (string) $response['status'] ) : ?>
					<div class="momo-be-msg-block show error">
						<p>
						<?php echo esc_html( $response['message'] ); ?>
						</p>
					</div>
			<?php elseif ( empty( $location_id ) || empty( $location_details ) ) : ?>
				<div class="momo-be-block-section ebay-new-location-form">
					<h4><?php esc_html_e( 'Address Details', 'momowsw' ); ?></h4>
					<div class="momo-be-section-block">
						<div class="momo-be-block">
							<label class="regular inline"><?php esc_html_e( 'Address 1', 'momowsw' ); ?></label>
							<input type="text" class="inline wide" name="address_1"/>*
						</div>
						<div class="momo-be-block">
							<label class="regular inline"><?php esc_html_e( 'Address 2', 'momowsw' ); ?></label>
							<input type="text" class="inline wide" name="address_2"/>*
						</div>
						<div class="momo-be-block">
							<label class="regular inline"><?php esc_html_e( 'City', 'momowsw' ); ?></label>
							<input type="text" class="inline wide" name="city"/>*
						</div>
						<div class="momo-be-block">
							<label class="regular inline"><?php esc_html_e( 'County', 'momowsw' ); ?></label>
							<input type="text" class="inline wide" name="county"/>*
						</div>
						<div class="momo-be-block">
							<label class="regular inline"><?php esc_html_e( 'Country', 'momowsw' ); ?></label>
							<select class="inline" name="country">
							<?php foreach ( $country_list as $code => $name ) { ?>
								<option value="<?php echo esc_attr( $code ); ?>" ><?php echo esc_html( $name ); ?></option>
							<?php } ?>
							</select>*
						</div>
						<div class="momo-be-block">
							<label class="regular inline"><?php esc_html_e( 'Postal Code', 'momowsw' ); ?></label>
							<input type="text" class="inline wide" name="postal_code"/>*
						</div>
					</div>
					<h4><?php esc_html_e( 'Location Details', 'momowsw' ); ?></h4>
					<div class="momo-be-section-block">
						<div class="momo-be-block">
							<label class="regular inline"><?php esc_html_e( 'Location Name', 'momowsw' ); ?></label>
							<input type="text" class="inline wide" name="name"/>*
						</div>
						<div class="momo-be-block">
							<label class="regular inline"><?php esc_html_e( 'Information', 'momowsw' ); ?></label>
							<input type="text" class="inline wide" name="information"/>*
						</div>
						<div class="momo-be-block">
							<label class="regular inline"><?php esc_html_e( 'Website', 'momowsw' ); ?></label>
							<input type="text" class="inline wide" name="website"/>*
						</div>
						<div class="momo-be-block">
							<label class="regular inline"><?php esc_html_e( 'Phone', 'momowsw' ); ?></label>
							<input type="text" class="inline wide" name="phone"/>*
						</div>
						<div class="momo-be-block">
							<label class="regular inline"><?php esc_html_e( 'Store Type', 'momowsw' ); ?></label>
							<select class="inline" name="store_type">
								<option value="<?php echo esc_attr( 'STORE' ); ?>" ><?php echo esc_html( 'Store' ); ?></option>
								<option value="<?php echo esc_attr( 'WAREHOUSE' ); ?>" ><?php echo esc_html( 'Warehouse' ); ?></option>
							</select>*
						</div>
						<div class="momo-be-block">
							<label class="regular inline"><?php esc_html_e( 'Unique ID', 'momowsw' ); ?></label>
							<input type="text" class="inline wide" name="unique_id"/>*
						</div>
					</div>
					<div class="momo-be-section-block">
						<span class="button button-primary momowsw-ebay-generate-location-id">
							<?php esc_html_e( 'Save Location Info', 'momowsw' ); ?>
						</span>
						<span class="button button-primary momowsw-ebay-back-to-location momo-be-float-right">
							<?php esc_html_e( 'Back', 'momowsw' ); ?>
						</span>
					</div>
				</div>
				<div class="momo-be-block-section ebay-old-location-form">
					<div class="momo-be-block">
						<label class="regular inline"><?php esc_html_e( 'Unique ID', 'momowsw' ); ?></label>
						<input type="text" class="inline wide" name="old_unique_id"/>
					</div>
					<div class="momo-be-section-block">
						<span class="button button-primary momowsw-ebay-save-old-location">
							<?php esc_html_e( 'Save Location Info', 'momowsw' ); ?>
						</span>
						<span class="button button-primary momowsw-ebay-back-to-location momo-be-float-right">
							<?php esc_html_e( 'Back', 'momowsw' ); ?>
						</span>
					</div>
				</div>
				<div class="momo-be-block-section location-empty-form">
					<div class="momo-be-block momo-mt-10">
						<span class="momo-be-btn momo-be-btn-secondary momowsw-ebay-location-btn" data-type="new">
							<?php esc_html_e( 'Create new Location', 'momowsw' ); ?>
						</span>
					</div>
					<div class="momo-be-block momo-mt-10">
						<span class="momo-be-btn momo-be-btn-secondary resync momowsw-ebay-location-btn" data-type="old">
							<?php esc_html_e( 'Save created Merchant Location Key', 'momowsw' ); ?>
						</span>
					</div>
				</div>
			<?php else : ?>
				<?php if ( isset( $location_details['status'] ) && 'bad' === $location_details['status'] || empty( $location_details ) ) : ?>
					<div class="momo-be-msg-block warning show">
						<p>
						<?php esc_html_e( 'Something went wrong retrieving location details. Please recheck you API Settings or unlink current one.', 'momowsw' ); ?>
						</p>
						<p>
							<a href="#momo-be-es-api" class="momo-inside-page-link"><?php esc_html_e( 'API Settings', 'momowsw' ); ?></a>
						</p>
					</div>
				<?php else : ?>
				<table class="momo-be-listing-table">
					<tbody>
						<tr>
							<td><?php esc_html_e( 'Location Key', 'momowsw' ); ?></td>
							<td><?php echo esc_html( $location_id ); ?></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'Location ID', 'momowsw' ); ?></td>
							<td><?php echo esc_html( $location_details->location->locationId ); ?></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'Phone', 'momowsw' ); ?></td>
							<td><?php echo esc_html( $location_details->phone ); ?></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'URL', 'momowsw' ); ?></td>
							<td><?php echo esc_html( $location_details->locationWebUrl ); ?></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'Additional Information', 'momowsw' ); ?></td>
							<td><?php echo esc_html( $location_details->locationAdditionalInformation ); ?></td>
						</tr>
						<tr>
							<td colspan="2"><strong><?php esc_html_e( 'Address Detail', 'momowsw' ); ?></strong></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'Address Line 1', 'momowsw' ); ?></td>
							<td><?php echo esc_html( $location_details->location->address->addressLine1 ); ?></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'Address Line 2', 'momowsw' ); ?></td>
							<td><?php echo esc_html( $location_details->location->address->addressLine2 ); ?></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'City', 'momowsw' ); ?></td>
							<td><?php echo esc_html( $location_details->location->address->city ); ?></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'Postal Code', 'momowsw' ); ?></td>
							<td><?php echo esc_html( $location_details->location->address->postalCode ); ?></td>
						</tr>
					</tbody>
				</table>
				<?php endif; ?>
				<div class="momo-be-listing-footer">
					<span class="momo-be-btn momo-be-btn-secondary unlink-location"><?php esc_html_e( 'Unlink this location', 'momowsw' ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
<?php } // Top If Else ?>

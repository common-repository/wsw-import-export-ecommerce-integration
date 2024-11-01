<?php
/**
 * EBay sync related functions
 *
 * @package momowsw
 * @author MoMo Themes
 * @since v1.4.0
 */
class MoMo_WSW_Ebay_Functions {
	/**
	 * Check API Credentials
	 */
	public function momowsw_check_ebay_api_credentials() {
		$ebay_settings = get_option( 'momo_wsw_ebay_sync' );

		$error = false;
		$msg   = '';
		if ( ! isset( $ebay_settings['client_id'] ) || empty( $ebay_settings['client_id'] ) ) {
			$error = true;
			$msg  .= '<p>' . esc_html__( 'eBay client id is empty.', 'momowsw' ) . '</p>';
		}
		if ( ! isset( $ebay_settings['client_secret'] ) || empty( $ebay_settings['client_secret'] ) ) {
			$error = true;
			$msg  .= '<p>' . esc_html__( 'eBay client secret is empty.', 'momowsw' ) . '</p>';
		}
		if ( $error ) {
			echo wp_json_encode(
				array(
					'status' => 'bad',
					'msg'    => $msg,
				)
			);
			exit;
		}
		return true;
	}
	/**
	 * Prepare product to export to eBay
	 *
	 * @param array $args Arguments.
	 */
	public function momo_wsw_prepare_product_to_export( $args ) {
		$product_id              = $args['product_id'];
		$export_product_variants = $args['export_product_variants'];
		$export_product_tags     = $args['export_product_tags'];
		$product_status          = $args['product_status'];

		$wc_product   = new WC_Product( $product_id );
		$wc_pvariable = new WC_Product_Variable( $product_id );
		$export_data  = array();

		$export_data['title']       = $wc_product->get_title();
		$export_data['description'] = $wc_product->get_description();
		$export_data['sku']         = $wc_product->get_sku();
		$export_data['brand']       = '';
		$export_data['mpn']         = '';
		$export_data['condition']   = 'NEW';
		$export_data['quantity']    = empty( $wc_product->get_stock_quantity() ) ? 0 : $wc_product->get_stock_quantity();
		$export_data['currency']    = get_woocommerce_currency();
		$export_data['price']       = $wc_product->get_price();
		$export_data['weight']      = $wc_product->get_weight();
		$export_data['product_id']  = $product_id;

		$weight_unit = get_option( 'woocommerce_weight_unit' );
		switch ( $weight_unit ) {
			case 'kg':
				$export_data['weight_unit'] = 'KILOGRAM';
				break;
			case 'g':
				$export_data['weight_unit'] = 'GRAM';
				break;
			case 'lbs':
				$export_data['weight_unit'] = 'POUND';
				break;
			case 'oz':
				$export_data['weight_unit'] = 'OUNCE';
				break;
			default:
				$export_data['weight_unit'] = 'KILOGRAM';
				break;
		}
		return $export_data;
	}
	/**
	 * Generate encode oauth credentials
	 */
	public function generate_encoded_oauth_cresentials() {
		$ebay_settings = get_option( 'momo_wsw_ebay_sync' );

		$client_id     = isset( $ebay_settings['client_id'] ) ? $ebay_settings['client_id'] : '';
		$client_secret = isset( $ebay_settings['client_secret'] ) ? $ebay_settings['client_secret'] : '';
		return base64_encode( $client_id . ':' . $client_secret ); // phpcs:ignore
	}
	/**
	 * Generate eBay user consent url for authorization code
	 */
	public function generate_user_consent_url() {
		/* URL redirects a user to the application's Grant Application Access page */

		/*
		GET https://auth.sandbox.ebay.com/oauth2/authorize?
		client_id=<app-client-id-value>&
		locale=<locale-value>&          // optional
		prompt=login                    // optional
		redirect_uri=<app-RuName-value>&
		response_type=code&
		scope=<scopeList>&              // a URL-encoded string of space-separated scopes
		state=<custom-state-value>&     // optional
		*/
		global $momowsw;
		$ebay_sync     = get_option( 'momo_wsw_ebay_sync' );
		$ebay_url      = $momowsw->premium->eapi->momo_wsw_get_ebay_auth_url();
		$client_id     = isset( $ebay_sync['client_id'] ) ? $ebay_sync['client_id'] : '';
		$client_secret = isset( $ebay_sync['client_secret'] ) ? $ebay_sync['client_secret'] : '';
		$ru_name       = isset( $ebay_sync['ru_name'] ) ? $ebay_sync['ru_name'] : '';

		$scope_list = 'https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.account https://api.ebay.com/oauth/api_scope/sell.account.readonly https://api.ebay.com/oauth/api_scope/sell.inventory.readonly';

		$uc_url = $ebay_url . 'oauth2/authorize?' .
			'client_id=' . $client_id . '&' .
			'redirect_uri=' . $ru_name . '&' .
			'response_type=code&' .
			'scope=' . $scope_list;
		return $uc_url;
	}
	/**
	 * Get Mechant Location Key
	 */
	public function momowsw_ebay_merchant_location_key() {
		$ebay_location = get_option( 'momo_wsw_ebay_location' );
		$location_id   = isset( $ebay_location['location_id'] ) ? $ebay_location['location_id'] : '';
		return $location_id;
	}
	/**
	 * Get all Return Policies
	 *
	 * @param string $type Policy type return_policy / fulfillment_policy / payment_policy.
	 */
	public function momowsw_get_all_policies( $type ) {
		global $momowsw;
		$scope          = 'https://api.ebay.com/oauth/api_scope/sell.account https://api.ebay.com/oauth/api_scope/sell.account.readonly';
		$marketplace_id = $this->momowsw_ebay_selected_marketplace();
		$endpoint       = 'sell/account/v1/' . $type . '?marketplace_id=' . $marketplace_id['ebcode'];
		$response       = $momowsw->premium->eapi->momo_wsw_run_rest_api( 'GET', $endpoint, '', '', $scope );
		return $response;
	}
	/**
	 * Get selected marketplace
	 */
	public function momowsw_ebay_selected_marketplace() {
		$ebay_sync      = get_option( 'momo_wsw_ebay_sync' );
		$marketplace_id = isset( $ebay_sync['marketplace_id'] ) ? $ebay_sync['marketplace_id'] : '';
		$mplist         = $this->momowsw_ebay_marketplace_list();
		if ( isset( $mplist[ $marketplace_id ] ) ) {
			return $mplist[ $marketplace_id ];
		} else {
			return $mplist['EBAT_US'];
		}
	}
	/**
	 * Returns marketplace list
	 */
	public function momowsw_ebay_marketplace_list() {
		$marketplace = array(
			'EBAY_US'        => array(
				'country'  => 'United States',
				'url'      => 'https://www.ebay.com',
				'language' => 'en-US',
				'ebcode'   => 'EBAY_US',
			),
			'EBAY_AT'        => array(
				'country'  => 'Austria',
				'url'      => 'https://www.ebay.at',
				'language' => 'de-AT',
				'ebcode'   => 'EBAY_AT',
			),
			'EBAY_AU'        => array(
				'country'  => 'Australia',
				'url'      => 'https://www.ebay.com.au',
				'language' => 'en-AU',
				'ebcode'   => 'EBAY_AU',
			),
			'EBAY_BE_F'      => array(
				'country'  => 'Belgium (Française)',
				'url'      => 'https://www.befr.ebay.be/',
				'language' => 'fr-BE',
				'ebcode'   => 'EBAY_BE',
			),
			'EBAY_BE_N'      => array(
				'country'  => 'Belgium (Nederlandse)',
				'url'      => 'https://www.benl.ebay.be/',
				'language' => 'nl-BE',
				'ebcode'   => 'EBAY_BE',
			),
			'EBAY_CA_E'      => array(
				'country'  => 'Canada (English)',
				'url'      => 'https://www.ebay.ca',
				'language' => 'en-CA',
				'ebcode'   => 'EBAY_CA',
			),
			'EBAY_CA_F'      => array(
				'country'  => 'Canada (Française)',
				'url'      => 'https://www.cafr.ebay.ca/',
				'language' => 'fr-CA',
				'ebcode'   => 'EBAY_CA',
			),
			'EBAY_CH'        => array(
				'country'  => 'Switzerland',
				'url'      => 'https://www.ebay.ch',
				'language' => 'de-CH',
				'ebcode'   => 'EBAY_CH',
			),
			'EBAY_DE'        => array(
				'country'  => 'Germany',
				'url'      => 'https://www.ebay.de',
				'language' => 'de-DE',
				'ebcode'   => 'EBAY_DE',
			),
			'EBAY_ES'        => array(
				'country'  => 'Spain',
				'url'      => 'https://www.ebay.es',
				'language' => 'es-ES',
				'ebcode'   => 'EBAY_ES',
			),
			'EBAY_FR'        => array(
				'country'  => 'France',
				'url'      => 'https://www.ebay.fr',
				'language' => 'fr-FR',
				'ebcode'   => 'EBAY_FR',
			),
			'EBAY_GB'        => array(
				'country'  => 'Great Britain',
				'url'      => 'https://www.ebay.co.uk',
				'language' => 'en-GB',
				'ebcode'   => 'EBAY_GB',
			),
			'EBAY_HK'        => array(
				'country'  => 'Hong Kong',
				'url'      => 'https://www.ebay.com.hk',
				'language' => 'zh-HK',
				'ebcode'   => 'EBAY_HK',
			),
			'EBAY_IE'        => array(
				'country'  => 'Ireland',
				'url'      => 'https://www.ebay.ie',
				'language' => 'en-IE',
				'ebcode'   => 'EBAY_IE',
			),
			'EBAY_IN'        => array(
				'country'  => 'India',
				'url'      => 'https://www.ebay.in',
				'language' => 'en-GB',
				'ebcode'   => 'EBAY_IN',
			),
			'EBAY_IT'        => array(
				'country'  => 'Italy',
				'url'      => 'https://www.ebay.it',
				'language' => 'it-IT',
				'ebcode'   => 'EBAY_IT',
			),
			'EBAY_MY'        => array(
				'country'  => 'Malaysia',
				'url'      => 'https://www.ebay.com.my',
				'language' => 'en-US',
				'ebcode'   => 'EBAY_MY',
			),
			'EBAY_NL'        => array(
				'country'  => 'Netherlands',
				'url'      => 'https://www.ebay.nl',
				'language' => 'nl-NL',
				'ebcode'   => 'EBAY_NL',
			),
			'EBAY_PH'        => array(
				'country'  => 'Philippines',
				'url'      => 'https://www.ebay.ph',
				'language' => 'en-PH',
				'ebcode'   => 'EBAY_PH',
			),
			'EBAY_PL'        => array(
				'country'  => 'Poland',
				'url'      => 'https://www.ebay.pl',
				'language' => 'pl-PL',
				'ebcode'   => 'EBAY_PL',
			),
			'EBAY_SG'        => array(
				'country'  => 'Singapore',
				'url'      => 'https://www.ebay.com.sg',
				'language' => 'en-US',
				'ebcode'   => 'EBAY_SG',
			),
			'EBAY_TH'        => array(
				'country'  => 'Thailand',
				'url'      => 'https://info.ebay.co.th',
				'language' => 'th-TH',
				'ebcode'   => 'EBAY_TH',
			),
			'EBAY_TW'        => array(
				'country'  => 'Taiwan',
				'url'      => 'https://www.ebay.com.tw',
				'language' => 'zh-TW',
				'ebcode'   => 'EBAY_TW',
			),
			'EBAY_VN'        => array(
				'country'  => 'Vietnam',
				'url'      => 'https://www.ebay.vn',
				'language' => 'en-US',
				'ebcode'   => 'EBAY_VN',
			),
			'EBAY_MOTORS_US' => array(
				'country'  => 'United States Motors',
				'url'      => 'https://www.ebay.com/motors',
				'language' => 'en-US',
				'ebcode'   => 'EBAY_MOTORS_US',
			),
		);
		return $marketplace;
	}
	/**
	 * Get counrty with code
	 */
	public function momowsw_get_country_with_code() {
		$country_list = array(
			'AF' => 'Afghanistan',
			'AL' => 'Albania',
			'DZ' => 'Algeria',
			'AS' => 'American Samoa',
			'AD' => 'Andorra',
			'AO' => 'Angola',
			'AI' => 'Anguilla',
			'AQ' => 'Antarctica',
			'AG' => 'Antigua And Barbuda',
			'AR' => 'Argentina',
			'AM' => 'Armenia',
			'AW' => 'Aruba',
			'AU' => 'Australia',
			'AT' => 'Austria',
			'AZ' => 'Azerbaijan',
			'BS' => 'Bahamas, The',
			'BH' => 'Bahrain',
			'BD' => 'Bangladesh',
			'BB' => 'Barbados',
			'BY' => 'Belarus',
			'BE' => 'Belgium',
			'BZ' => 'Belize',
			'BJ' => 'Benin',
			'BM' => 'Bermuda',
			'BT' => 'Bhutan',
			'BO' => 'Bolivia',
			'BA' => 'Bosnia And Herzegovina',
			'BW' => 'Botswana',
			'BV' => 'Bouvet Island',
			'BR' => 'Brazil',
			'IO' => 'British Indian Ocean Territory',
			'BN' => 'Brunei',
			'BG' => 'Bulgaria',
			'BF' => 'Burkina Faso',
			'MM' => 'Burma',
			'BI' => 'Burundi',
			'KH' => 'Cambodia',
			'CM' => 'Cameroon',
			'CA' => 'Canada',
			'CV' => 'Cape Verde',
			'KY' => 'Cayman Islands',
			'CF' => 'Central African Republic',
			'TD' => 'Chad',
			'CL' => 'Chile',
			'CN' => 'China',
			'CX' => 'Christmas Island',
			'CC' => 'Cocos (keeling) Islands',
			'CO' => 'Colombia',
			'KM' => 'Comoros',
			'CG' => 'Congo (brazzaville) ',
			'CD' => 'Congo (kinshasa)',
			'CK' => 'Cook Islands',
			'CR' => 'Costa Rica',
			'CI' => 'CÔte D’ivoire',
			'HR' => 'Croatia',
			'CU' => 'Cuba',
			'CW' => 'CuraÇao',
			'CY' => 'Cyprus',
			'CZ' => 'Czech Republic',
			'DK' => 'Denmark',
			'DJ' => 'Djibouti',
			'DM' => 'Dominica',
			'DO' => 'Dominican Republic',
			'EC' => 'Ecuador',
			'EG' => 'Egypt',
			'SV' => 'El Salvador',
			'GQ' => 'Equatorial Guinea',
			'ER' => 'Eritrea',
			'EE' => 'Estonia',
			'ET' => 'Ethiopia',
			'FK' => 'Falkland Islands (islas Malvinas)',
			'FO' => 'Faroe Islands',
			'FJ' => 'Fiji',
			'FI' => 'Finland',
			'FR' => 'France',
			'GF' => 'French Guiana',
			'PF' => 'French Polynesia',
			'TF' => 'French Southern And Antarctic Lands',
			'GA' => 'Gabon',
			'GM' => 'Gambia, The',
			'GE' => 'Georgia',
			'DE' => 'Germany',
			'GH' => 'Ghana',
			'GI' => 'Gibraltar',
			'GR' => 'Greece',
			'GL' => 'Greenland',
			'GD' => 'Grenada',
			'GP' => 'Guadeloupe',
			'GU' => 'Guam',
			'GT' => 'Guatemala',
			'GG' => 'Guernsey',
			'GN' => 'Guinea',
			'GW' => 'Guinea-bissau',
			'GY' => 'Guyana',
			'HT' => 'Haiti',
			'HM' => 'Heard Island And Mcdonald Islands',
			'HN' => 'Honduras',
			'HK' => 'Hong Kong',
			'HU' => 'Hungary',
			'IS' => 'Iceland',
			'IN' => 'India',
			'ID' => 'Indonesia',
			'IR' => 'Iran',
			'IQ' => 'Iraq',
			'IE' => 'Ireland',
			'IM' => 'Isle Of Man',
			'IL' => 'Israel',
			'IT' => 'Italy',
			'JM' => 'Jamaica',
			'JP' => 'Japan',
			'JE' => 'Jersey',
			'JO' => 'Jordan',
			'KZ' => 'Kazakhstan',
			'KE' => 'Kenya',
			'KI' => 'Kiribati',
			'KP' => 'Korea, North',
			'KR' => 'Korea, South',
			'KW' => 'Kuwait',
			'KG' => 'Kyrgyzstan',
			'LA' => 'Laos',
			'LV' => 'Latvia',
			'LB' => 'Lebanon',
			'LS' => 'Lesotho',
			'LR' => 'Liberia',
			'LY' => 'Libya',
			'LI' => 'Liechtenstein',
			'LT' => 'Lithuania',
			'LU' => 'Luxembourg',
			'MO' => 'Macau',
			'MK' => 'Macedonia',
			'MG' => 'Madagascar',
			'MW' => 'Malawi',
			'MY' => 'Malaysia',
			'MV' => 'Maldives',
			'ML' => 'Mali',
			'MT' => 'Malta',
			'MH' => 'Marshall Islands',
			'MQ' => 'Martinique',
			'MR' => 'Mauritania',
			'MU' => 'Mauritius',
			'YT' => 'Mayotte',
			'MX' => 'Mexico',
			'FM' => 'Micronesia, Federated States Of',
			'MD' => 'Moldova',
			'MC' => 'Monaco',
			'MN' => 'Mongolia',
			'ME' => 'Montenegro',
			'MS' => 'Montserrat',
			'MA' => 'Morocco',
			'MZ' => 'Mozambique',
			'NA' => 'Namibia',
			'NR' => 'Nauru',
			'NP' => 'Nepal',
			'NL' => 'Netherlands',
			'NC' => 'New Caledonia',
			'NZ' => 'New Zealand',
			'NI' => 'Nicaragua',
			'NE' => 'Niger',
			'NG' => 'Nigeria',
			'NU' => 'Niue',
			'NF' => 'Norfolk Island',
			'MP' => 'Northern Mariana Islands',
			'NO' => 'Norway',
			'OM' => 'Oman',
			'PK' => 'Pakistan',
			'PW' => 'Palau',
			'PA' => 'Panama',
			'PG' => 'Papua New Guinea',
			'PY' => 'Paraguay',
			'PE' => 'Peru',
			'PH' => 'Philippines',
			'PN' => 'Pitcairn Islands',
			'PL' => 'Poland',
			'PT' => 'Portugal',
			'PR' => 'Puerto Rico',
			'QA' => 'Qatar',
			'RE' => 'Reunion',
			'RO' => 'Romania',
			'RU' => 'Russia',
			'RW' => 'Rwanda',
			'BL' => 'Saint Barthelemy',
			'SH' => 'Saint Helena, Ascension, And Tristan Da Cunha',
			'KN' => 'Saint Kitts And Nevis',
			'LC' => 'Saint Lucia',
			'MF' => 'Saint Martin',
			'PM' => 'Saint Pierre And Miquelon',
			'VC' => 'Saint Vincent And The Grenadines',
			'WS' => 'Samoa',
			'SM' => 'San Marino',
			'ST' => 'Sao Tome And Principe',
			'SA' => 'Saudi Arabia',
			'SN' => 'Senegal',
			'RS' => 'Serbia',
			'SC' => 'Seychelles',
			'SL' => 'Sierra Leone',
			'SG' => 'Singapore',
			'SX' => 'Sint Maarten',
			'SK' => 'Slovakia',
			'SI' => 'Slovenia',
			'SB' => 'Solomon Islands',
			'SO' => 'Somalia',
			'ZA' => 'South Africa',
			'GS' => 'South Georgia And South Sandwich Islands',
			'SS' => 'South Sudan',
			'ES' => 'Spain',
			'LK' => 'Sri Lanka',
			'SD' => 'Sudan',
			'SR' => 'Suriname',
			'SZ' => 'Swaziland',
			'SE' => 'Sweden',
			'CH' => 'Switzerland',
			'SY' => 'Syria',
			'TW' => 'Taiwan',
			'TJ' => 'Tajikistan',
			'TZ' => 'Tanzania',
			'TH' => 'Thailand',
			'TL' => 'Timor-leste',
			'TG' => 'Togo',
			'TK' => 'Tokelau',
			'TO' => 'Tonga',
			'TT' => 'Trinidad And Tobago',
			'TN' => 'Tunisia',
			'TR' => 'Turkey',
			'TM' => 'Turkmenistan',
			'TC' => 'Turks And Caicos Islands',
			'TV' => 'Tuvalu',
			'UG' => 'Uganda',
			'UA' => 'Ukraine',
			'AE' => 'United Arab Emirates',
			'GB' => 'United Kingdom',
			'US' => 'United States',
			'UY' => 'Uruguay',
			'UZ' => 'Uzbekistan',
			'VU' => 'Vanuatu',
			'VA' => 'Vatican City',
			'VE' => 'Venezuela',
			'VN' => 'Vietnam',
			'VG' => 'Virgin Islands, British',
			'VI' => 'Virgin Islands, United States ',
			'WF' => 'Wallis And Futuna',
			'EH' => 'Western Sahara',
			'YE' => 'Yemen',
			'ZM' => 'Zambia',
			'ZW' => 'Zimbabwe',
		);
		return $country_list;
	}
}

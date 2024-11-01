<?php
/**
 * Webhooks related functions
 *
 * @package momowsw
 * @author MoMo Themes
 * @since v1.5.0
 */
class MoMo_WSW_Webhooks_Functions {
	/**
	 * List all Webhooks
	 *
	 * @param string $source Source.
	 */
	public function momowsw_get_list_all_hooks( $source ) {
		global $momowsw;
		if ( 'shopify' === $source ) {
			$details  = $momowsw->fn->momo_wsw_run_rest_api( 'GET', 'webhooks.json' );
			$webhooks = isset( $details->webhooks ) ? $details->webhooks : array();
			return $webhooks;
		}
	}
	/**
	 * Generate all location list
	 */
	public function momowsw_get_shopify_location_list() {
		global $momowsw;
		$details   = $momowsw->fn->momo_wsw_run_rest_api( 'GET', 'locations.json' );
		$locations = isset( $details->locations ) ? $details->locations : $details;
		return $locations;
	}
}

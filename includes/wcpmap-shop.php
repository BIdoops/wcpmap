<?php
if (!defined('ABSPATH')) {
	exit;
}

/**
 *
 * @version		1.0.0
 * @package		WCPMap/Includes
 * @author 		WCPetroMap
 */

function sync_shop($vendor_id)
{
	if (!$vendor_id) {
		error_log('vendor_id empty');

		return;
	}
	$vendor_pmap_store_id = get_user_meta($vendor_id, '_vendor_pmap_store_id', true);
	if (!$vendor_pmap_store_id) {
		error_log('vendor_pmap_store_id empty');

		return;
	}

	$request = wp_remote_get(WCPMAP_PETROMAP_SITE . '/api/v1/shops/' . $vendor_pmap_store_id);
	if (is_wp_error($request)) {
		error_log(WCPMAP_PETROMAP_SITE . '/api/v1/shops/' . $vendor_pmap_store_id);

		return;
	}
	$body = wp_remote_retrieve_body($request);
	$data = json_decode($body);

	if (!empty($data) && !empty($data->geometry)) {
		$coordinates = $data->geometry->coordinates;
		update_user_meta($vendor_id, '_store_lng', $coordinates[0]);
		update_user_meta($vendor_id, '_store_lat', $coordinates[1]);
	}
}

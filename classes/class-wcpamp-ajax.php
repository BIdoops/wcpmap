<?php

/**
 * @class		WCPMap Ajax Class
 *
 * @version     1.0.0
 * @package     WCPMap/Classes
 * @author      WCPetroMap
 */
class WCPMap_Ajax
{

	public function __construct()
	{
		add_action('wp_ajax_update_petromap', array($this, 'update_petromap'));
	}

	/**
	 * Ajax callback
	 * Update PetroMap fields
	 */
	public function update_petromap()
	{

		global $WCMp;
		$vendor = get_wcmp_vendor(get_current_vendor_id());
		check_ajax_referer('update_petromap', 'security');

		$fieldkeys = array(
			'vendor_pmap_store_id',
			'store_lng',
			'store_lat'
		);

		foreach ($fieldkeys as $fieldkey) {
			if (isset($_POST[$fieldkey])) {
				update_user_meta($vendor->id, '_' . $fieldkey, $_POST[$fieldkey]);
			}
		}

		wp_send_json_success('OK');
	}
}

new WCPMap_Ajax();

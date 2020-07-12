<?php
if (!defined('ABSPATH')) {
	exit;
}

/**
 * @class 		WCPMap User Class
 *
 * @version		1.0.0
 * @package		WCPMap/Classes
 * @author 		WCPetroMap
 */

class WCPMap_User
{
	/**
	 * Get vendor details
	 *
	 * @param $user_id
	 * @access public
	 * @return array
	 */

	public function __construct()
	{
		add_filter('wcmp_vendor_fields', array($this, 'get_vendor_fields'), 100, 1);
		//add_filter('wcmp_vendor_user_fields', array($this, 'log_vendor_fields'), 100,2);
	}

	public function get_vendor_fields($vendor_fields)
	{

		$vendor_fields["vendor_pmap_store_id"] = array(
			'label' => '',
			'type' => 'hidden',
			'value' => '',
			'class' => "user-profile-fields regular-text"
		); // Text

		return $vendor_fields;
	}

	public function log_vendor_fields($vendor_fields, $user_id)
	{

		error_log(json_encode($vendor_fields, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
		return $vendor_fields;
	}
}

new WCPMap_User();

<?php
if (!defined('ABSPATH')) {
	exit;
}

/**
 * @class 		WCPMap Dependencies
 *
 * @version		1.0.0
 * @package		WCPMap/Classes
 * @author 		WCPetroMap
 */
class WCPMap_Dependencies
{
	private static $active_plugins;

	public static function init()
	{
		self::$active_plugins = (array) get_option('active_plugins', array());
		if (is_multisite())
			self::$active_plugins = array_merge(self::$active_plugins, get_site_option('active_sitewide_plugins', array()));
	}
	public static function dc_product_vendor_active_check()
	{
		if (!self::$active_plugins)
			self::init();
		return in_array('dc-woocommerce-multi-vendor/dc_product_vendor.php', self::$active_plugins)
			|| array_key_exists('dc-woocommerce-multi-vendor/dc_product_vendor.php', self::$active_plugins);
	}
}

if (!WCPMap_Dependencies::dc_product_vendor_active_check()) {
	exit;
}

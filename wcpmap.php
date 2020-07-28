<?php

/**
 * Plugin Name: WCPetroMap
 * Plugin URI: https://www.petromap.org/plugins/wcpmap
 * Description: PetroMap for WC Marketplace.
 * Author: PetroMap, Javier Rivas
 * Version: 1.1.0
 * Author URI: https://www.petromap.org/
 * Requires at least: 4.4
 * Tested up to:  5.4
 * WC Marketplace requires at least: 3.2.2
 * WC Marketplace tested up to: 3.2.2
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: wcpmap
 * Domain Path: /languages/
 */


//define('WCPMAP_DEBUG', true)
if (!defined('WCPMAP_CURRENT_VERSION')) {
	define('WCPMAP_CURRENT_VERSION', '1.1.0');
}
if (!defined('WCPMAP_PLUGIN_FILE')) {
	define('WCPMAP_PLUGIN_FILE', __FILE__);
}

if (!defined('WCPMAP_ABSPATH')) {
	define('WCPMAP_ABSPATH', dirname(WCPMAP_PLUGIN_FILE) . '/');
}
// include('wcpmap-config.dev.php');
include('wcpmap-config.php');
include('classes/class-wcpmap.php');
$WCPMap = new WCPMap();
$GLOBALS['WCPMap'] = $WCPMap;

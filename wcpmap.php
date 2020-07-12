<?php

/**
 * Plugin Name: WCPetroMap
 * Plugin URI: https://www.petromap.org/plugins/wc-petromap
 * Description: PetroMap for WC MArketplace.
 * Author: PetroMap, Javier Rivas
 * Version: 1.0.0
 * Author URI: https://www.petromap.org/
 * Requires at least: 1.0
 * Tested up to: 1.0
 * WC requires at least: 3.0
 * WC tested up to: 4.1.1
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: wcpmap
 * Domain Path: /languages/
 */


//define('WCPMAP_DEBUG', true)

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

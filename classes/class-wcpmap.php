<?php
if (!defined('ABSPATH')) {
	exit;
}

/**
 * @class 		WCPMap class
 *
 * @version		1.0.0
 * @package		WCPMap/Classes
 * @author 		WCPetroMap
 */

class WCPMap
{
	public static $petromap_api_key = "petromap_api_key";

	public function __construct()
	{
		include_once('class-wcpmap_dependencies.php');

		if (is_admin()) {
			include(WCPMAP_ABSPATH . 'admin/class-wcpmap-settings.php');
		}
		add_filter("pre_update_option_wcmp_vendor_dashboard_settings_name", array($this, "keep_wcmp_vendor_dashboard_settings_name"), 100, 3);
		add_filter("option_wcmp_vendor_dashboard_settings_name", array($this, 'get_wcmp_vendor_dashboard_settings_name'), 100, 2);
		add_action('wcmp_init', array($this, 'init'));
		add_action('widgets_init', array($this, 'register_widgets'));
	}

	public function init()
	{
		$is_petromap_enable = get_wcmp_vendor_settings('is_petromap_enable');
		if (!empty($is_petromap_enable)) {
			include('class-wcpamp-ajax.php');
			if (!is_admin()) {
				include('class-wcpmap-user.php');
				include_once(WCPMAP_ABSPATH . 'includes/wcpmap-shop.php');
				add_action('wcmp_before_shop_front',  array($this, 'init_petromap_script'), 110);
				add_filter('wp_enqueue_scripts', array($this, 'register_enqueue_pollyfill'), 0);
			}
		}
	}

	public function register_enqueue_pollyfill()
	{
		if (!is_admin()) {
			$minify = defined('WCPMAP_DEBUG') ? '' : '.min';
			wp_register_script('petromap-api-polyfills', plugins_url('/lib/petromap-js/polyfills' . $minify . '.js', WCPMAP_PLUGIN_FILE), array(), WCPMAP_CURRENT_VERSION);
			wp_enqueue_script('petromap-api-polyfills');
		}
	}

	public function register_widgets()
	{
		require_once(WCPMAP_ABSPATH . 'widgets/class-wcpmap-widget-vendor-location.php');
		register_widget('WCPMap_Store_Location_Widget');
	}


	/** Never set petromap_api_key */

	public function keep_wcmp_vendor_dashboard_settings_name($value, $old_value, $option)
	{
		if ($option == 'wcmp_vendor_dashboard_settings_name') {
			if (empty($value)) {
				return $value;
			}

			if (!empty($value['google_api_key']) && $value['google_api_key'] == self::$petromap_api_key) {
				$value['google_api_key'] =  '';
			}
		}
		return $value;
	}

	/** Always return a value a no empty value **/
	public function get_wcmp_vendor_dashboard_settings_name($value, $option)
	{
		if ($option == 'wcmp_vendor_dashboard_settings_name') {
			if (empty($value)) {
				return array('google_api_key' => self::$petromap_api_key);
			}
			if (empty($value['google_api_key']) && !empty($value['is_petromap_enable'])) {
				return array_merge($value, array('google_api_key' => self::$petromap_api_key));
			}
		}
		return $value;
	}

	public function init_petromap_script()
	{
		global $WCPMap;
		global $user;
		$vendor = get_current_vendor();
		if (!$vendor) {
			return;
		}
		sync_shop($vendor->id);
		$user = wp_get_current_user();

		wp_deregister_script('wcmp-gmaps-api');

		$dc_vendors_permalinks_array = get_option('dc_vendors_permalinks');
		$store_slug = trailingslashit('vendor');
		if (isset($dc_vendors_permalinks_array['vendor_shop_base']) && !empty($dc_vendors_permalinks_array['vendor_shop_base'])) {
			$store_slug = trailingslashit($dc_vendors_permalinks_array['vendor_shop_base']);
		}

		$vendor_pmap_store_id = get_user_meta($vendor->id, '_vendor_pmap_store_id', true);

		$pmap_config = array(
			"api_host" => WCPMAP_PETROMAP_SITE,
			"host" => WCPMAP_GATEWAY,
			"mode" => 'place',
			"mapContainer" => 'vendor_store_map',
			"lonField" => 'store_lng',
			"latField" => 'store_lat',
			"admin_url" => admin_url('admin-ajax.php'),
			"action" => 'update_petromap',
			"security" => wp_create_nonce('update_petromap'),
			"gateway" => array(
				"url" => WCPMAP_GATEWAY,
				"params" => array(
					"sname" => $vendor->page_title,
					"ssite" => preg_replace('/https?:\/\//', '', trailingslashit(get_home_url()) . $store_slug . $vendor->page_slug),
					"sphone" => $vendor->phone,
					"semail" => $vendor->user_data->user_email,
					"ufname" => $user->first_name,
					"ulname" => $user->last_name,
					"uemail" =>  $vendor->user_data->user_email,
					"sel" => $vendor_pmap_store_id
				)
			)

		);

		$minify = defined('WCPMAP_DEBUG') ? '' : '.min';

		wp_register_script('petromap-api-main', plugins_url('/lib/petromap-js/main' . $minify . '.js', WCPMAP_PLUGIN_FILE), array('petromap-api-polyfills'), WCPMAP_CURRENT_VERSION);
		wp_register_script('petromap-api-stylejs', plugins_url('/lib/petromap-js/styles' . $minify . '.js', WCPMAP_PLUGIN_FILE), array('petromap-api-polyfills', 'petromap-api-main'), WCPMAP_CURRENT_VERSION);

		wp_register_script('petromap-vendor', plugins_url('/assets/frontend/js/vendor.js', WCPMAP_PLUGIN_FILE), array('jquery_spinner_js'), WCPMAP_CURRENT_VERSION);
		wp_register_script('petromap-init', plugins_url('/assets/frontend/js/init.js', WCPMAP_PLUGIN_FILE), array('jquery', 'petromap-api-main'), WCPMAP_CURRENT_VERSION);
		wp_register_script('jquery_spinner_js',  plugins_url('/lib/petromap-js/PMapSpinner' . $minify . '.js', WCPMAP_PLUGIN_FILE), array('jquery'), WCPMAP_CURRENT_VERSION);

		wp_localize_script('petromap-vendor', 'pmap_config', array_filter($pmap_config));

		wp_register_style('petromap-api-style', plugins_url('/lib/petromap-js/styles' . $minify . '.css', WCPMAP_PLUGIN_FILE), array(), WCPMAP_CURRENT_VERSION);
		wp_register_style('jquery_spinner_css', plugins_url('/lib/petromap-js/PMapSpinner' . $minify . '.css', WCPMAP_PLUGIN_FILE), array(), WCPMAP_CURRENT_VERSION);



		wp_enqueue_script('petromap-api-main');
		wp_enqueue_script('petromap-api-stylejs');

		wp_enqueue_script('jquery_spinner_js');

		wp_enqueue_script('petromap-vendor');
		wp_enqueue_script('petromap-init');



		wp_enqueue_style('petromap-api-style');
		wp_enqueue_style('jquery_spinner_css');
		//echo fields 

?>
		<input type="hidden" name="vendor_pmap_store_id" id="vendor_pmap_store_id" value="<?php echo $vendor_pmap_store_id ?>">
<?php
	}
}

<?php
if (!defined('ABSPATH')) {
	exit;
}

/**
 * @class 		WCPMap Admin Settings Class
 *
 * @version		1.0.0
 * @package		WCPMap/Admin
 * @author 		WCPetroMap
 */

class WCPMap_Admin_Settings
{
	public function __construct()
	{
		add_filter("settings_vendor_dashboard_tab_options", array($this, "vendor_dashboard_settings_init"));
		add_filter("settings_vendor_dashboard_tab_new_input", array($this, "petromap_general_settings_sanitize"), 100, 2);
	}


	public function vendor_dashboard_settings_init($settings_tab_options)
	{
		$is_petromap_enable = get_wcmp_vendor_settings('is_petromap_enable');

		if (
			!empty($settings_tab_options["sections"]) &&
			!empty($settings_tab_options["sections"]["wcmp_vendor_dashboard_settings"])
		) {
			$fields = $settings_tab_options["sections"]["wcmp_vendor_dashboard_settings"]["fields"];
			if (!empty($fields)) {
				$pmap_setting_enable = array(
					"petromap_enable" => array(
						'title' => 'Activar PetroMap',
						'type' => 'checkbox',
						'value' => 'Enable',
						'id' => 'is_petromap_enable',
						'label_for' => 'goois_petromap_enablegle_api_key',
						'name' => 'is_petromap_enable',
						'hints' => __('Used for vendor store maps', 'dc-woocommerce-multi-vendor'),
						'text' => 'Activa functionalidades de PetroMap'
					),
				);
				if ($is_petromap_enable) {
					$fields['google_api_key']['attributes'] = array('readonly' => 'readonly');
				}

				$first_array = array_splice($fields, 0, 2);
				$new_fields = array_merge($first_array, $pmap_setting_enable, $fields);
				$settings_tab_options["sections"]["wcmp_vendor_dashboard_settings"]["fields"] = $new_fields;
			}
		}
		return $settings_tab_options;
	}

	public function petromap_general_settings_sanitize($new_input, $input)
	{
		if (isset($input['is_petromap_enable'])) {
			$new_input['is_petromap_enable'] = sanitize_text_field($input['is_petromap_enable']);
		}
		return $new_input;
	}
}
new WCPMap_Admin_Settings();

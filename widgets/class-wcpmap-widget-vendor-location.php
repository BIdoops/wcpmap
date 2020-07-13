<?php

/**
 * WCPMap Store Location Widget
 *
 * @author    WC PetroMap
 * @category  Widgets
 * @package   WCPMap/Widgets
 * @version   1.0.0
 * @extends   WP_Widget
 */
if (!defined('ABSPATH'))
	exit; // Exit if accessed directly

class WCPMap_Store_Location_Widget extends WP_Widget
{

	/**
	 * constructor
	 *
	 * @access public
	 * @return void
	 */
	function __construct()
	{
		global $wp_version;

		// Widget variable settings
		$this->widget_idbase = 'pmap-vendor-store-location';
		$this->widget_title = 'WCPMap: Ubicación de la tienda del vendedor';
		$this->widget_description = 'Mostrar la ubicación de la tienda del vendedor en PetroMap.';
		$this->widget_cssclass = 'widget_wcmp_store_location';

		// Widget settings
		$widget_ops = array('classname' => $this->widget_cssclass, 'description' => $this->widget_description);

		// Widget control settings
		$control_ops = array('width' => 250, 'height' => 350, 'id_base' => $this->widget_idbase);

		// Create the widget
		if ($wp_version >= 4.3) {
			parent::__construct($this->widget_idbase, $this->widget_title, $widget_ops, $control_ops);
		} else {
			$this->WP_Widget($this->widget_idbase, $this->widget_title, $widget_ops, $control_ops);
		}
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	public function widget($args, $instance)
	{
		global $WCMp, $woocommerce;
		extract($args, EXTR_SKIP);
		$vendor_id = false;
		$vendors = false;
		// Only show current vendor widget when showing a vendor's product(s)
		$show_widget = false;

		if (is_tax($WCMp->taxonomy->taxonomy_name)) {
			$vendor_id = get_queried_object()->term_id;
			if ($vendor_id) {
				$vendor = get_wcmp_vendor_by_term($vendor_id);
				$show_widget = true;
			}
		}

		if (is_singular('product')) {
			global $post;
			$vendor = get_wcmp_product_vendors($post->ID);
			if ($vendor) {
				$show_widget = true;
			}
		}

		if ($show_widget && isset($vendor->id)) {
			$this->load_pmap_api();
			sync_shop($vendor->id);
			$vendor_pmap_store_id = get_user_meta($vendor->id, '_vendor_pmap_store_id', true);
			$store_lat = get_user_meta($vendor->id, '_store_lat', true);
			$store_lng = get_user_meta($vendor->id, '_store_lng', true);
			$pmap_config = array(
				"api_host" => WCPMAP_PETROMAP_SITE,
				"host" => WCPMAP_GATEWAY,
				"mode" => 'place',
				"mapContainer" => 'store-maps',
				"lat" => $store_lat,
				"lon" => $store_lng,
			);
			wp_localize_script('petromap-api-main', 'pmap_config', $pmap_config);
			$args = array(
				'instance' => $instance,
				'pmap_link' => esc_url(add_query_arg(array('id' => urlencode($vendor_pmap_store_id)),  WCPMAP_PETROMAP_SITE . '/')),
				'vendor_pmap_store_id' => $vendor_pmap_store_id,
				'store_lat' => $store_lat,
				'store_lng' => $store_lng
			);

			// Set up widget title
			if ($instance['title']) {
				$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
			} else {
				$title = false;
			}

			// Before widget (defined by themes)
			echo $before_widget;

			// Display the widget title if one was input (before and after defined by themes).
			if ($title) {
				echo $before_title . $title . $after_title;
			}

			// Action for plugins/themes to hook onto
			do_action($this->widget_cssclass . '_top');

			//  $WCMp->template->get_template('widget/pmap-store-location.php', $args);

			extract($args);


			include(WCPMAP_ABSPATH . 'templates/widget/wcpmap-store-location.php');

			// Action for plugins/themes to hook onto
			do_action($this->widget_cssclass . '_bottom');

			// After widget (defined by themes).
			echo $after_widget;
		}
	}
	function load_pmap_api()
	{
		global $WCPMap;
		$minify = defined('WCPMAP_DEBUG') ? '.min' : '';

		wp_register_script('petromap-api-main', plugins_url('/lib/petromap-js/main' . $minify . '.js', WCPMAP_PLUGIN_FILE), array('petromap-api-polyfills'), WCPMAP_CURRENT_VERSION);
		wp_register_script('petromap-api-stylejs', plugins_url('/lib/petromap-js/styles' . $minify . '.js', WCPMAP_PLUGIN_FILE), array('petromap-api-polyfills', 'petromap-api-main'), WCPMAP_CURRENT_VERSION);
		wp_register_style('petromap-api-style', plugins_url('/lib/petromap-js/styles' . $minify . '.css', WCPMAP_PLUGIN_FILE), array(), WCPMAP_CURRENT_VERSION);
		wp_register_script('petromap-widget', plugins_url('/assets/frontend/js/widget.js', WCPMAP_PLUGIN_FILE), array('petromap-api-main'), array(), WCPMAP_CURRENT_VERSION);

		wp_enqueue_script('petromap-api-main');
		wp_enqueue_script('petromap-api-stylejs');
		wp_enqueue_style('petromap-api-style');
		wp_enqueue_script('petromap-widget');
	}
	/**
	 * update function.
	 *
	 * @see WP_Widget->update
	 * @access public
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	public function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	/**
	 * The form on the widget control in the widget administration area
	 * @since  1.0.0
	 * @param  array $instance The settings for this instance.
	 * @return void
	 */
	public function form($instance)
	{
		global $WCMp, $woocommerce;
		$defaults = array(
			'title' =>  __('Store Location', 'dc-woocommerce-multi-vendor'),
		);

		$instance = wp_parse_args((array) $instance, $defaults);
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'dc-woocommerce-multi-vendor') ?>:
				<input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" class="widefat" />
			</label>
		</p>
<?php
	}
}

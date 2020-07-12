(function ($, config) {
	pmap_map = new MapComponent(
		config.api_host,
		config.host,
		config.mode,
		config.lon,
		config.lat,
		config.mapContainer
	);
	pmap_map.init();
})(jQuery, pmap_config)
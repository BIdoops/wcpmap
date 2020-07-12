(function ($, config) {
	$("#searchStoreAddress").remove();
	$("#"+config.mapContainer).css("position","relative");

	var lonFieldElem = document.getElementById(config.lonField);
	var latFieldElem = document.getElementById(config.latField);

	pmap_map = new MapComponent(
		config.api_host,
		config.host,
		config.mode,
		config.lon? config.lon : parseFloat(lonFieldElem.value),
		config.lat? config.lat :parseFloat(latFieldElem.value),
		config.mapContainer
	);
	pmap_map.init();

	pmap_map.onLocationChanged( function(coordinates) {
		if (lonFieldElem && latFieldElem) {
			lonFieldElem.value = coordinates[0]
			latFieldElem.value = coordinates[1]
		}
	});
})(jQuery, pmap_config)
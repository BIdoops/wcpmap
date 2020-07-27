(function ($, config) {
	var data = {
		action: config.action,
		security: config.security,
	};
	var labels = {
		connect: "Connectar a PetroMap",
		select: "Seleccionar establecimiento",
		update: "Modificar en PetroMap",
		disconnect: "Desconectar de PetroMap",
	};
	var params = config.gateway.params;
	var form = $("form.wcmp_shop_settings_form .panel .panel-body .wcmp_form1");
	form.append('<div class="form-group">'+
			'<label class="control-label col-sm-3 col-md-3">PetroMap</label>'+
			'<div class="col-md-6 col-sm-9">'+
				'<div class="row col-md-12 inp-btm-margin">'+
					'<button id="petromap_connect_link" type="button" class="btn btn-default" name="petromap_connect_link">'+
						(params.sel ? labels.select : labels.connect)+
					'</button>'+
				'</div>'+
				'<div class="row col-md-12 inp-btm-margin">'+
					'<button id="petromap_edit_link" href="'+ config.gateway.url+'/shop/update/'+params.sel+'" target="_blank" title="Modificar en PetroMap" style="'+(params.sel ? "" : "display: none")+
					'type="button" class="btn btn-default" name="petromap_edit_link">'+labels.update+'</button>'+
				'</div>'+
				'<div class="row col-md-12 inp-btm-margin">'+
					'<button id="petromap_disconnect_link" type="button" class="btn btn-default"  style="'+(params.sel ? "" : "display: none")+
					'" name="petromap_disconnect_link">'+labels.disconnect+'</button>'+
				'</div>'+
				'<div class="row col-md-12 inp-btm-margin"><i>* Recuerde <strong>guardar</strong> las opciones</i></div>'+
			'</div>'+
		'</div>');

	var connect_button = $("#petromap_connect_link");
	var disconnect_button = $("#petromap_disconnect_link");
	var edit_button = $("#petromap_edit_link");
	var buttons = [connect_button, disconnect_button, edit_button];
	
	connect_button.on("disconnected", function () {
		$(this).text(labels.connect);
	});
	connect_button.on("connected", function () {
		$(this).text(labels.select);
	});

	disconnect_button.on("disconnected", function () {
		$(this).hide();
	});
	disconnect_button.on("connected", function () {
		$(this).show();
	});

	edit_button.on("disconnected", function () {
		$(this).hide();
	});
	edit_button.on("connected", function () {
		$(this).show();
	});

	connect_button.on("click", function () {
		var url = config.gateway.url + "/shop/select/?" + $.param(params);
		window.open(
			url,
			"_blank",
			'height=${screen.height * 0.9},width=${screen.with * 0.33},left='+connect_button.offset().left+',resizable=0,menubar=0,toolbar=0,scrollbars=0'
		);
	});
	edit_button.on("click", function () {
		var url = config.gateway.url+'/shop/update/'+params.sel;
		window.open(
			url,
			"_blank",
			'height=${screen.height * 0.9},width='+(screen.with * 0.33)+',left='+connect_button.offset().left+',resizable=0,menubar=0,toolbar=0,scrollbars=0'
		);
	});
	disconnect_button.on("click", function () {
		params.sel = data.vendor_pmap_store_id = "";
		config.lon = data.store_lng = "";
		config.lat = data.store_lat = "";
		$("#vendor_pmap_store_id").val("");
		$("#store_lng").val("");
		$("#store_lat").val("");
		spinner.show();
		$.post(config.admin_url, data, function (response) {
			console.log("response" + response);
			pmap_map.overlay(true);
			buttons.forEach(function (button) {
				button.trigger("disconnected");
			});
		}).always(function () {
			spinner.hide();
		});
	});
	var spinner = new PMapSpinner({
		parentId: "wrapper",
	});
	window.onmessage = function (e) {
		if (e.data) {
			$("#vendor_pmap_store_id").val(e.data._id);
			$("#store_lng").val(e.data.geometry.coordinates[0]);
			$("#store_lat").val(e.data.geometry.coordinates[1]);
			params.sel = data.vendor_pmap_store_id = e.data._id;
			config.lon = data.store_lng = e.data.geometry.coordinates[0];
			config.lat = data.store_lat = e.data.geometry.coordinates[1];
			spinner.show();
			$.post(config.admin_url, data, function (response) {
				console.log("response" + response);
				pmap_map.update([parseFloat(data.store_lng), parseFloat(data.store_lat)]);
				pmap_map.overlay(false);
				buttons.forEach(function (button) {
					button.trigger("connected");
				});
			}).always(function () {
				spinner.hide();
			});
		}
	};
})(jQuery, pmap_config);

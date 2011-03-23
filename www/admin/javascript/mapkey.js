var map;
var rectangle;

function initMap(lat,lng,zoom,rectbounds) {
	var latlng = new google.maps.LatLng(lat,lng);
	var myOptions = {
		zoom: zoom,
		center: latlng,
		mapTypeId: google.maps.MapTypeId.SATELLITE,		
		panControl: false,
		zoomControl: true,
		zoomControlOptions: {
			style: google.maps.ZoomControlStyle.DEFAULT
		},
		mapTypeControl: false,
		scaleControl: true,
		streetViewControl: false,
		overviewMapControl: false
	};
	/*
	mapTypeId: google.maps.MapTypeId.SATELLITE,			
	* ROADMAP displays the normal, default 2D tiles of Google Maps.
	* SATELLITE displays photographic tiles.
	* HYBRID displays a mix of photographic tiles and a tile layer for prominent features (roads, city names).
	* TERRAIN displays physical relief tiles for displaying elevation and water features (mountains, rivers, etc.).
	*/
	map = new google.maps.Map(document.getElementById("map_canvas"),myOptions);

	if (rectbounds)
		bounds =
			new google.maps.LatLngBounds(
				new google.maps.LatLng(rectbounds[0],rectbounds[1]),
				new google.maps.LatLng(rectbounds[2],rectbounds[3])
			);
	else
		bounds = null;
	
	rectangle = new google.maps.Rectangle({
	  strokeColor: "yellow",
	  strokeOpacity: 0.8,
	  strokeWeight: 2,
	  fillColor: "#fff",
	  fillOpacity: 0.2,
	  bounds: bounds,
	  map: map
	});

}

var selecting = false;
var dragging = false;
var startLatLng;
var endLatLng;
var prevLatLng;

function setFormParameters(clear) {

	$('#coordinate1_lat').val(clear ? null : startLatLng.lat());
	$('#coordinate1_lng').val(clear ? null : startLatLng.lng());
	$('#coordinate2_lat').val(clear ? null : endLatLng.lat());
	$('#coordinate2_lng').val(clear ? null : endLatLng.lng());
	$('#zoom').val(clear ? null : map.getZoom());

}

function removeRectangle() {

	rectangle.setMap(null);

}

function toggleSelecting() {

	selecting = !selecting;
	
	$('#btn-enable').val(selecting ? 'stop selection' : 'enable selection');

}

function _mouseUpEvent(event,caller) {

	// overlay creates a mystery second mouseup when finishing the first rectangle after loading!?
	if (!dragging && caller!='overlay') {
		if (!selecting) return;
		rectangle.setMap(map);
		dragging = true;
		startLatLng = event.latLng;
		endLatLng = new google.maps.LatLng;
		$('#coordinates-start').html(startLatLng.toString());
	} else
	if (dragging) {
		dragging = false;
		endLatLng = event.latLng;
		setFormParameters();
		toggleSelecting();
	}

}

function _mouseMoveEvent(event) {

	if (dragging) {

		var currentLatLng = event.latLng;
		if (startLatLng.lng()<currentLatLng.lng())
			var bounds = new google.maps.LatLngBounds(startLatLng,currentLatLng);
		else
			var bounds = new google.maps.LatLngBounds(currentLatLng,startLatLng);
		rectangle.setBounds(bounds);
		//$('#coordinates-end').html(currentLatLng.toString());
		prevLatLng = currentLatLng;

	}
		
}

function addMouseClickHandlers() {

	google.maps.event.addListener(map, 'mouseup', function(event) {_mouseUpEvent(event,'map');});
	google.maps.event.addListener(rectangle, 'mouseup', function(event) {_mouseUpEvent(event,'overlay');});
	google.maps.event.addListener(map, 'mousemove', function(event) {_mouseMoveEvent(event,'map');});
	google.maps.event.addListener(rectangle, 'mousemove', function(event) {_mouseMoveEvent(event,'overlay');});

}
	
	
function drawPolygon(bounds,style,info) {

	if (!bounds) return;

	var polyCoordinates = Array();
	var centreLat = 0;
	var centreLng = 0;

	for (var i=0;i<bounds.length;i++)  {
	
		polyCoordinates[polyCoordinates.length] = new google.maps.LatLng(bounds[i][0], bounds[i][1]);
		centreLat = centreLat + bounds[i][0];
		centreLng = centreLng + bounds[i][1];

	}

	if (polyCoordinates.length<2) return;

	var polygon;

	if (!style) {
		style = {
			strokeColor: '#fff',
			strokeOpacity: 0.8,
			strokeWeight: 2,
			fillColor: "#fff",
			fillOpacity: 0.35,
			geodesic: false
		};
	}

	// Construct the polygon
	// Note that we don't specify an array or arrays, but instead just
	// a simple array of LatLngs in the paths property
	polygon = new google.maps.Polygon({
		paths: polyCoordinates,
		strokeColor: style.strokeColor,
		strokeOpacity: style.strokeOpacity,
		strokeWeight: style.strokeWeight,
		fillColor: style.fillColor,
		fillOpacity: style.fillOpacity,
		geodesic: style.geodesic,
	});
	
	polygon.setMap(map);

	if (!info) return;

	if (info.addMarker) {

		placeMarker(
			[(centreLat / polyCoordinates.length),(centreLng / polyCoordinates.length)],
			info,
			style
		);

	}

}

function placeMarker(coordinates,info,style) {

	var marker = new google.maps.Marker({
		position: new google.maps.LatLng(coordinates[0], coordinates[1]),
		map: map,
		title: info.name
	});

	if (info.description) {

		var contentString = '<div id="content">'+
			'<b>'+info.name+'</b>'+
			'<div id="bodyContent">'+
			'<p>'+info.description+'</p>'+
			(style && style.strokeColor ?
				'<p style="width:100px;height:10px;background-color:'+style.strokeColor+';border:1px solid #666;">&nbsp;</p>' :
				'') +
			'</div>'+
			'</div>';

		var infowindow = new google.maps.InfoWindow({
			content: contentString
		});
		
		marker.setCursor('pointer');
		marker.setIcon('../../media/system/icons/map-marker-info.png');

		google.maps.event.addListener(marker, 'click', function() {
			infowindow.open(map,marker);
		});

	} else {

		marker.setCursor('default');
		marker.setIcon('../../media/system/icons/map-marker.png');

	}
		
}
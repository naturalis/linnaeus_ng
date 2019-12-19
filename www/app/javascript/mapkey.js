var map;
var polygon;
var markers = Array();
var polygons = Array();
var finishedLoading = false;
var preDefPolygon = false;
var drawingManager;

function initMap(init) {

	if (init==undefined) init = {};
	if (!init.lat) init.lat = 0;
	if (!init.lng) init.lng = 0;
	if (!init.zoom) init.zoom = 2;
	
//	if (!init) init = {lat:0,lng:0,zoom:7};

	var myOptions = {
		zoom: init.zoom,
		center: new google.maps.LatLng(init.lat,init.lng),
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

	google.maps.event.addListener(map, 'idle', function() {
		if (finishedLoading) return;
		if (map.getZoom() > 9) map.setZoom(9);
		finishedLoading = true;
	});

	google.maps.event.addListener(map, 'mousemove', function(event) {mouseMove(event); });

	if (init.drawingmanager) mapDrawingManagerInit();

}

var shapeOptions = {
	strokeColor: '#FFFF00',
	strokeOpacity: 0.8,
	strokeWeight: 3,
	fillColor: "#FFFF00",
	fillOpacity: 0.1,
	geodesic: false
}

var searchArea = null;

function mapDrawingManagerInit() {

	drawingManager = new google.maps.drawing.DrawingManager({
		drawingMode: google.maps.drawing.OverlayType.RECTANGLE,
		drawingControl: true,
		drawingControlOptions: {
			position: google.maps.ControlPosition.TOP_LEFT,
			drawingModes: [
				//google.maps.drawing.OverlayType.MARKER,
				//google.maps.drawing.OverlayType.CIRCLE,
				google.maps.drawing.OverlayType.RECTANGLE
				//google.maps.drawing.OverlayType.POLYGON
			]
		}
	});

	google.maps.event.addListener(drawingManager, 'overlaycomplete', function(event) {
		clearPolygon();
		searchArea = event.overlay;
	});
	
	drawingManager.setOptions({rectangleOptions: shapeOptions});

	drawingManager.setMap(map);

}

function initMapSearch() {

	map.setOptions({draggableCursor:'pointer'});

}

function mouseMove(event) {

	var p = event.latLng.toString().replace(/\(|\)/g,'').split(',');
	$('#coordinates').html((p[0].substring(0,p[0].indexOf('.')+4))+','+p[1].substring(0,p[1].indexOf('.')+4));

}

function drawSearchArea(coord) {
	
	var southWest = new google.maps.LatLng(coord[0][0],coord[0][1]);
	var northEast = new google.maps.LatLng(coord[1][0],coord[1][1]);
	var bounds = new google.maps.LatLngBounds(southWest,northEast);

	rectangle = new google.maps.Rectangle();
	rectangle.setOptions(shapeOptions);
	rectangle.setBounds(bounds);
	rectangle.setMap(map);
	searchArea = rectangle;

}

function clearPolygon() {

	if (searchArea) {
		searchArea.setMap(null);
		searchArea = null;
	}

}

function doMapSearch() {
	
	if (!searchArea) return;
	$('<input type="hidden" name="coordinates">').val(searchArea.getBounds()).appendTo('#theForm');	
	$('#theForm').submit();	
	
}

function placeMarker(coordinates,info,style) {

	var marker = new google.maps.Marker({
		position: new google.maps.LatLng(coordinates[0], coordinates[1]),
		map: map,
		title: info.name
	});

	markers[markers.length] = [marker,info.typeId,info.taxonId];

	marker.setIcon('../../media/system/map_marker.php?c='+info.colour);

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
		if (style.icon) marker.setIcon(style.icon);

		google.maps.event.addListener(marker, 'click', function() {
			infowindow.open(map,marker);
		});

	} else {

		if (style && style.icon) marker.setIcon(style.icon);
		marker.setCursor('default');

	}
		
}

function drawPolygon(bounds,style,info) {

	if (!bounds) return;

	var polygonCoordinates = Array();
	var centreLat = 0;
	var centreLng = 0;

	for (var i=0;i<bounds.length;i++)  {

		// google maps has no map above and below 85.0511 deg
		if (bounds[i][0]>85.05) bounds[i][0] = 85.05;
		if (bounds[i][0]<-85.05) bounds[i][0] = -85.05;
		
		polygonCoordinates[polygonCoordinates.length] = new google.maps.LatLng(bounds[i][0], bounds[i][1]);
		centreLat = centreLat + bounds[i][0];
		centreLng = centreLng + bounds[i][1];

	}

	if (polygonCoordinates.length<2) return;

	var polygon;

	if (!style) {
		style = {
			strokeColor: (info.colour ? '#'+info.colour : '#fff'),
			strokeOpacity: 0.8,
			strokeWeight: 2,
			fillColor: (info.colour ? '#'+info.colour : '#fff' ),
			fillOpacity: 0.35,
			geodesic: false
		};
	}

	polygon = new google.maps.Polygon({
		paths: polygonCoordinates,
		strokeColor: style.strokeColor,
		strokeOpacity: style.strokeOpacity,
		strokeWeight: style.strokeWeight,
		fillColor: style.fillColor,
		fillOpacity: style.fillOpacity,
		geodesic: style.geodesic
	});
	
	
	if (info) polygons[polygons.length] = [polygon,info.typeId,info.taxonId];

	polygon.setMap(map);
	
	return polygon;

}

function doMapTypeToggle(id,taxon) {
	
	var checkboxId = '#toggle-'+id + (taxon ? '-'+taxon : '');
	
	var isVisible = !$(checkboxId).attr('checked');
	
	for (var i=0;i<markers.length;i++) {
		if (id==undefined && markers[i][2]==taxon) markers[i][0].setMap(isVisible ? null : map);
		if (markers[i][1]==id && (taxon==undefined || taxon==markers[i][2])) markers[i][0].setMap(isVisible ? null : map);
	}
	
	for (var i=0;i<polygons.length;i++) {
		if (id==undefined && polygons[i][2]==taxon) polygons[i][0].setMap(isVisible ? null : map);
		if (polygons[i][1]==id && (taxon==undefined || taxon==polygons[i][2])) polygons[i][0].setMap(isVisible ? null : map);
	}

}

function clearSearchResults() {

	for (var i=0;i<markers.length;i++) markers[i][0].setMap(null);
	for (var i=0;i<polygons.length;i++) polygons[i][0].setMap(null);

}

function doMapCompare() {

	if($('#idA').val()=='' || $('#idB').val()=='') {
	
		alert(_('You must select two taxa to compare.'));		
	
		if ($('#idA').val()=='')
			$('#idA').focus();
		else
			$('#idB').focus();
		
	} else
	if($('#idA').val()==$('#idB').val()) {

		alert(_('You cannot compare a taxon to itself.'));		
		$('#idA').focus();

	} else {

		$('#theForm').submit();
	
	}

}


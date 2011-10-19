var map;
var polygon;
var markers = Array();
var polygons = Array();
var finishedLoading = false;
var polygonCoordinates = Array();
var isDrawing = false;
var searchPolygonInit = {
		strokeColor: '#FFFF00',
		strokeOpacity: 0.8,
		strokeWeight: 3,
		fillColor: "#FFFF00",
		fillOpacity: 0.1,
		geodesic: false
	};
var preDefPolygon = false;

function initMap(init) {

	if (!init) init = {lat:52,lng:5,zoom:7};
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

}

function initMapSearch() {

	addMouseUpHandler();
	map.setOptions({draggableCursor:'pointer'});

}

function addMouseUpHandler() {

	google.maps.event.addListener(map, 'mouseup', function(event) {mouseUp(event); });

}

function mouseMove(event) {

	var p = event.latLng.toString().replace(/\(|\)/g,'').split(',');
	$('#coordinates').html((p[0].substring(0,p[0].indexOf('.')+4))+','+p[1].substring(0,p[1].indexOf('.')+4));

}

function mouseUp(event) {

	if (!isDrawing) return;

	polygonCoordinates[polygonCoordinates.length] = event.latLng;
	polygon.setMap(map);
	polygon.setPaths(polygonCoordinates);

}

function setPredefPolygon(coord) {

	polygon = new google.maps.Polygon(searchPolygonInit);
	google.maps.event.addListener(polygon, 'mouseup', function(event) {mouseUp(event); });
	if (map) polygon.setMap(map);

	var c = coord.split('),(');
	for (var i=0;i<c.length;i++) {
		var g = c[i].replace(/\(|\)/,'').split(',');
		polygonCoordinates[polygonCoordinates.length] = new google.maps.LatLng(g[0],g[1]);
	}

	polygon.setPaths(polygonCoordinates);
	
	preDefPolygon = true;

}


function startPolygonDraw(init) {

	if (isDrawing) {

		$('#button-draw').val(_('draw area to search'));
		isDrawing = false;
		return;

	}

	if (preDefPolygon) clearPolygon();
	preDefPolygon = false;

	if (!init) init = searchPolygonInit;

	polygon = new google.maps.Polygon(init);
	google.maps.event.addListener(polygon, 'mouseup', function(event) {mouseUp(event); });

	if (map) polygon.setMap(map);
	
	$('#button-draw').val(_('finish drawing'));
	
	isDrawing = true;

}

function clearPolygon() {

	polygonCoordinates.length = 0;
	polygon.setMap(map);

}

function doMapSearch() {
	
	var coord = polygonCoordinates.toString();
	
	if (!coord) return;

	$('<input type="hidden" name="coordinates">').val(coord).appendTo('#theForm');	
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
	
	polygons[polygons.length] = [polygon,info.typeId,info.taxonId];

	polygon.setMap(map);

	if (!info) return;

	if (info.addMarker) {
		
		style = {
			icon: '../../media/system/map-point.png'
		};

		placeMarker(
			[(centreLat / polygonCoordinates.length),(centreLng / polygonCoordinates.length)],
			info,
			style
		);

	}

}

function doMapTypeToggle(ele,id,taxon) {

	var isVisible = $(ele).attr('hidden')==0;
	
	for (var i=0;i<markers.length;i++) {
		if (id==undefined && markers[i][2]==taxon) markers[i][0].setMap(isVisible ? null : map);
		if (markers[i][1]==id && (taxon==undefined || taxon==markers[i][2])) markers[i][0].setMap(isVisible ? null : map);
	}
	
	for (var i=0;i<polygons.length;i++) {
		if (id==undefined && polygons[i][2]==taxon) polygons[i][0].setMap(isVisible ? null : map);
		if (polygons[i][1]==id && (taxon==undefined || taxon==polygons[i][2])) polygons[i][0].setMap(isVisible ? null : map);
	}
	
	$(ele).attr('hidden',isVisible ? '1' : '0');
	$(ele).html(isVisible ? _('show') : _('hide'));

}

function clearSearchResults() {

	for (var i=0;i<markers.length;i++) markers[i][0].setMap(null);
	for (var i=0;i<polygons.length;i++) polygons[i][0].setMap(null);

}

function doMapSearch() {

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




























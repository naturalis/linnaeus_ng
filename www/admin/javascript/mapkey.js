var map;
var polygon;
var rectangle;
var markers = Array();
var polygons = Array();
var polygonCoordinates = Array();
var occurrenceType;
var prevPolygons = Array();
var prevMarkers = Array();


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

}

function initPolygon(init) {

	if (!init) init = {
		strokeColor: '#fff',
		strokeOpacity: 0.8,
		strokeWeight: 2,
		fillColor: "#fff",
		fillOpacity: 0.35,
		geodesic: false
	}

	var poly = new google.maps.Polygon(init);

	if (map) poly.setMap(map);

	return poly;

}

function initRectangle(init) {

	if (!init) init = {
	  strokeColor: "yellow",
	  strokeOpacity: 0.8,
	  strokeWeight: 2,
	  fillColor: "#fff",
	  fillOpacity: 0.2
	}

	rectangle = new google.maps.Rectangle(init);

	if (map) rectangle.setMap(map);

}

function addMouseHandlers() {

	google.maps.event.addListener(map, 'mouseup', function(event) {mouseUp(event); });
	google.maps.event.addListener(map, 'mousemove', function(event) {mouseMove(event); });

}

function createPolygon() {
	
	setOccurrenceType('polygon');

	if (polygonCoordinates.length==0) {

		map.setOptions({draggableCursor: 'pointer'});

		$('#polygon-button').val('finish polygon');

		polygon = initPolygon();
	
		google.maps.event.addListener(polygon, 'mouseup', function(event) {
			mouseUp(event);
		});
		
		var i = polygons.length;

		google.maps.event.addListener(polygon, 'rightclick', function(event) {
			polygons[i] = null;
			this.setMap(null);
		});


	} else {

		map.setOptions({draggableCursor: 'default'});

		$('#polygon-button').val('draw a polygon');

		if (polygon) polygons[polygons.length] = polygonCoordinates.slice();

		polygonCoordinates.length=0;

	}

}

function setOccurrenceType(type) {

	occurrenceType = (type==occurrenceType ? '' : (type=='marker' ? 'marker' : 'polygon'));

	$('#selection-type').html(occurrenceType=='' ? '(none)' : occurrenceType);

}

function mouseUp(event) {

	if (occurrenceType=='marker') {

		var marker = new google.maps.Marker({
			position: event.latLng,
			map: map,
			title: '(click to delete)'
		});

		marker.setCursor('pointer');
		
		markers[markers.length] = marker;
		var i = (markers.length-1);
		
		google.maps.event.addListener(marker, 'rightclick', function() {
			markers[i] = null;
			this.setMap(null);
		});

	} else
	if (occurrenceType=='polygon') {

		if (!polygon) return;
	
		polygonCoordinates[polygonCoordinates.length] = event.latLng;
		polygon.setMap(map);
		polygon.setPaths(polygonCoordinates);

	}

}

function mouseMove(event) {

	$('#coordinates').html(event.latLng.toString());

}

function saveAll() {

	// automatically finish open polygon
	createPolygon();

	for (var i=0;i<markers.length;i++) {
	
		if (markers[i]) $('<input type="hidden" name="markers[]">').val(markers[i].getPosition()).appendTo('#theForm');

	}

	for (var i=0;i<polygons.length;i++) {
	
		if (polygons[i])  $('<input type="hidden" name="polygons[]">').val(polygons[i]).appendTo('#theForm');

	}

	$('<input type="hidden" name="mapCentre">').val(map.getCenter().toString()).appendTo('#theForm');
	$('<input type="hidden" name="mapZoom">').val(map.getZoom()).appendTo('#theForm');
	
	$('#theForm').submit();

}

function placeMarker(coordinates,info,style) {

	var marker = new google.maps.Marker({
		position: new google.maps.LatLng(coordinates[0], coordinates[1]),
		map: map,
		title: info.name
	});

	if (info.occurrenceId) {
		
		prevMarkers[info.occurrenceId] = marker;
	}

	if (info.addDelete) {

		var o = info.occurrenceId;

		google.maps.event.addListener(marker, 'rightclick', function(event) {
			if (confirm('Are you sure?')) {
				this.setMap(null);
				if (prevPolygons[o]) prevPolygons[o].setMap(null);
				deleteOccurrence(o);
			}
		});

	}


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
			strokeColor: '#fff',
			strokeOpacity: 0.8,
			strokeWeight: 2,
			fillColor: "#fff",
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
	
	polygon.setMap(map);

	if (!info) return;
	
	if (info.addDelete) {
		
		var b = info.addMarker;
		var o = info.occurrenceId;

		prevPolygons[o] = polygon;

		google.maps.event.addListener(polygon, 'rightclick', function(event) {
			if (confirm('Are you sure?')) {
				if (b) prevMarkers[o].setMap(null);
				this.setMap(null);
				deleteOccurrence(o);
			}
		});

	}

	if (info.addMarker) {
		
		style = {
			icon: '../../media/system/icons/map-point.png'
		};

		placeMarker(
			[(centreLat / polygonCoordinates.length),(centreLng / polygonCoordinates.length)],
			info,
			style
		);

	}

}

function deleteOccurrence(id) {

	allAjaxHandle = $.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : 'delete_occurrence' ,
			'id' : id , 
			'time' : allGetTimestamp()
		}),
		success : function (data) {
			return (parseInt(data)==1);
		}
	});

}
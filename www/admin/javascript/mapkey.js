/* map */
var map;
var drawingManager;
var shapeOptions;
var markersArray = [];
var finishedLoading = false;

function initMap(init) {

	if (init==undefined) init = {};
	if (!init.lat) init.lat = 0;
	if (!init.lng) init.lng = 0;

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
		if (map.getZoom() < 2) map.setZoom(2);
		finishedLoading = true;
	});
	
	if (init.editable) mapDrawingManagerInit();

}

var polygonOptions = {
	strokeColor: '#fff',
	strokeOpacity: 0.8,
	strokeWeight: 2,
	fillColor: '#fff',
	fillOpacity: 0.4,
	geodesic: false,
	editable: false,
	clickable: true,
	zIndex: 1,
	overlayType: 'polygon'
}

function mapDrawingManagerShapeInit() {

	if (!drawingManager) return;

	var shapeOptions = polygonOptions;
	var markerColour = $('#geodatatype :selected').attr('colour');

	shapeOptions.fillColor = '#'+markerColour;
	shapeOptions.editable = true

	//drawingManager.setOptions({circleOptions: shapeOptions});
	//drawingManager.setOptions({rectangleOptions: shapeOptions});
	drawingManager.setOptions({polygonOptions: shapeOptions});
	drawingManager.setOptions({markerOptions: {icon: new google.maps.MarkerImage('../../media/system/map_marker.php?c='+markerColour)}});

}

function mapToggleEditorMode(mode) {

	drawingManager.setMap(null);

	var d = [];

	if (mode=='both' || mode=='marker' || mode=='') d.push(google.maps.drawing.OverlayType.MARKER);
	if (mode=='both' || mode=='polygon' || mode=='') d.push(google.maps.drawing.OverlayType.POLYGON);

	drawingManager = new google.maps.drawing.DrawingManager({
		drawingControl: true,
		drawingControlOptions: {
			position: google.maps.ControlPosition.TOP_LEFT,
			drawingModes: d
		}
	});

	drawingManager.setMap(map);
}

function mapDrawingManagerInit() {

	drawingManager = new google.maps.drawing.DrawingManager({
		//drawingMode: google.maps.drawing.OverlayType.POLYGON,
		drawingControl: true,
		drawingControlOptions: {
			position: google.maps.ControlPosition.TOP_LEFT,
			drawingModes: [
				google.maps.drawing.OverlayType.MARKER,
				//google.maps.drawing.OverlayType.CIRCLE,
				//google.maps.drawing.OverlayType.RECTANGLE,
				google.maps.drawing.OverlayType.POLYGON
			]
		}
	});

	google.maps.event.addListener(drawingManager, 'overlaycomplete', function(event) {
		event.overlay.dataTypeId =  mapGetCurrentDataType();
		event.overlay.overlayType =  event.type;
		event.overlay.draggable = true;
		var arrayIndex = markersArray.length;
		markersArray.push(event.overlay);

		google.maps.event.addListener(event.overlay, 'rightclick', function(event) {
			if (confirm('Are you sure you want to delete?')) {
				this.setMap(null);
				markersArray[arrayIndex].deleted = true;
			}
		});

	});

	mapDrawingManagerShapeInit();

	drawingManager.setMap(map);

}

function mapGetCurrentDataType() {

	return $('#geodatatype :selected').val();

}

function mapDoChangeDataType() {

	mapDrawingManagerShapeInit();
	mapToggleEditorMode($('#geodatatype :selected').attr('type'));

}

function drawPolygon(bounds,info) {

	if (!bounds) return;

	var polygonCoordinates = Array();

	for (var i=0;i<bounds.length;i++) {

		// google maps has no map above and below 85.0511 deg
		if (bounds[i][0]>85.05) bounds[i][0] = 85.05;
		if (bounds[i][0]<-85.05) bounds[i][0] = -85.05;

		polygonCoordinates[polygonCoordinates.length] = new google.maps.LatLng(bounds[i][0], bounds[i][1]);
	}

	if (polygonCoordinates.length<2) return;

	var polygon = new google.maps.Polygon(polygonOptions);

	polygon.setOptions({
		paths: polygonCoordinates,
		strokeColor: (info.colour ? '#'+info.colour : '#fff'),
		fillColor: (info.colour ? '#'+info.colour : '#fff' ),
		editable: (info.editable ? info.editable : false ),
		dataTypeId: info.dataTypeid
	});
	
	polygon.setMap(map);

	if (info.addDelete) {
		
		var arrayIndex = markersArray.length;

		google.maps.event.addListener(polygon, 'rightclick', function(event) {
			if (confirm('Are you sure you want to delete?')) {
				this.setMap(null);
				markersArray[arrayIndex].deleted = true;
			}
		});

	}

	markersArray.push(polygon);

}

function placeMarker(coordinates,info) {

	var marker = new google.maps.Marker({
		position: new google.maps.LatLng(coordinates[0], coordinates[1]),
		map: map,
		title: info.name,
		dataTypeId: info.dataTypeid,
		overlayType: 'marker',
		icon: '../../media/system/map_marker.php?c='+info.colour,
		cursor: 'default',
		draggable: (info.editable ? info.editable : false ),
	});

	if (info.addDelete) {

		var arrayIndex = markersArray.length;

		google.maps.event.addListener(marker, 'rightclick', function(event) {
			if (confirm('Are you sure you want to delete?')) {
				this.setMap(null);
				markersArray[arrayIndex].deleted = true;
			}
		});

	}

	markersArray.push(marker);
		
}

function mapClearMap() {

	if (markersArray) {
	
		for (i in markersArray) {
			markersArray[i].setMap(null);
			markersArray[i].deleted = true;
		}
		
		markersArray.length = 0;
	
	}

}

function mapSaveMap() {

	for (var i=0;i<markersArray.length;i++) {
		
		if (markersArray[i].deleted!=true) {
	
			if (markersArray[i].overlayType=='marker') {
				var nodes = markersArray[i].getPosition();
			} else
			if (markersArray[i].overlayType=='polygon') {
				var vertices = markersArray[i].getPath();
				var nodes = [];
				for (var k =0; k < vertices.length; k++) {
					var xy = vertices.getAt(k);
					nodes.push('('+xy.lat() +',' + xy.lng()+')');
				}
				nodes = '('+nodes+')';
	
			}
			
			$('<input type="hidden" name="mapItems[]">').val(markersArray[i].overlayType+'|'+markersArray[i].dataTypeId+'|'+nodes).appendTo('#theForm');

		}

	}

	$('<input type="hidden" name="mapCentre">').val(map.getCenter().toString()).appendTo('#theForm');
	$('<input type="hidden" name="mapZoom">').val(map.getZoom()).appendTo('#theForm');
	
	$('#theForm').submit();

}


function addMouseHandlers() {

	google.maps.event.addListener(map, 'mousemove', function(event) {mouseMove(event); });

}

function mouseMove(event) {

	$('#coordinates').html(event.latLng.toString());

}



/* functions not directly related to map */
var allChecked = false;

function mapToggleAllSpecies() {

	if (allChecked)
		$('input[id*=species-]').attr('checked',false)
	else
		$('input[id*=species-]').attr('checked',true)

	allChecked = !allChecked;
	
	$('#select-all').val(allChecked ? 'deselect all' : 'select all');

}

function mapDoCopyForm(taxon) {

	if (!confirm(sprintf(_('Are you sure you want to copy data from %s to %s?'),taxon,$('#target :selected').text()))) return;
	
	$('#theForm').submit();

}


/* functions not directly related to map */
function mapSaveTypelabel(id,value,type) {

	$.ajax({
		url : "ajax_interface.php" ,
		type: "POST",
		data : ({
			'action' : 'save_type_label' ,
			'language' : (type == 'default' ? allDefaultLanguage : allActiveLanguage) ,
			'id' : id ,
			'value' : value ,
			'time' : allGetTimestamp()			
		}),
		success : function (data) {
			allSetMessage(data);
		}
	});

}

function mapSaveTypeColour(id,value) {

	$.ajax({
		url : "ajax_interface.php" ,
		type: "POST",
		data : ({
			'action' : 'save_type_colour' ,
			'id' : id ,
			'value' : value ,
			'time' : allGetTimestamp()			
		}),
		success : function (data) {
			allSetMessage(data);
		}
	});

}

function mapGetTypeLabels(language) {

	$.ajax({
		url : "ajax_interface.php" ,
		type: "POST",
		data : ({
			'action' : 'get_type_labels' ,
			'language' : language ,
			'time' : allGetTimestamp()			
		}),
		success : function (data) {
			if (language == allActiveLanguage) $('input[id*=other-]').val('');
			obj = $.parseJSON(data);
			if (!obj) return;			
			for (var i=0;i<obj.length;i++) $('#'+(language == allDefaultLanguage ? 'default-' : 'other-' )+obj[i].type_id).val(obj[i].title);
		}
	});

}


function mapGetTypeColours() {

	$.ajax({
		url : "ajax_interface.php" ,
		type: "POST",
		data : ({
			'action' : 'get_type_colours' ,
			'time' : allGetTimestamp()			
		}),
		success : function (data) {
			obj = $.parseJSON(data);
			if (!obj) return;			
			for (var i=0;i<obj.length;i++) {
				if (obj[i].colour) document.getElementById('color-'+obj[i].id).color.fromString(obj[i].colour);
			}
		}
	});

}

function mapDeleteType(id,value) {
	
	if (!allDoubleDeleteConfirm(_('data type'),value)) return;

	$('<input type="hidden" name="del_type">').val(id).appendTo('#theForm');
	$('#theForm').submit();

}

function mapMoveType(id,action) {

	$('#id').val(id);
	$('#action').val(action);
	$('#theForm').submit()

}

var lon1 = lon2 = lat1 = lat2 = mapWPx = mapHPx = mapW = mapH = 0;

function mapL2bindMouseMove() {
	
	var offset = $('#map-overlay').offset();
	
	$('#map-overlay').mousemove(function(event) {
		
		var x = event.pageX-offset.left;
		var y = event.pageY-offset.top;
		
		var mapWDeg = (((x / mapWPx) * mapW) + lon2);
		var mapHDeg = (((y / mapHPx) * mapH) + lat2);

		mapWDeg = Math.floor(mapWDeg) + ((Math.round((mapWDeg - Math.floor(mapWDeg)) * 100))/100);
		mapHDeg = Math.floor(mapHDeg) + ((Math.round((mapHDeg - Math.floor(mapHDeg)) * 100))/100);

		$('#coordinates').html(mapWDeg+' x '+mapHDeg);


	});

}

function mapClickCell(ele) {

	var type = $('input[name=selectedType]:checked').val();
	var col = $('#color-'+type).css('background-color');
	var erase = $(ele).css('background-color')==col;
	$(ele).css('background-color',(erase?'':col));
	$(ele).attr('type_id',(erase?'':type));
	
}

function mapl2ClearMap(type) {

	$('td[id^=cell-]').each(function(){
		if ((type && $(this).attr('type_id')==type) || !type) {
			$(this).css('background-color','');
			$(this).attr('type_id','');
		}
	})

}

function mapl2SaveMap() {

	$('td[id^=cell-]').each(function(){
		if ($(this).attr('type_id')!='') {

			$('<input type="hidden" name="mapItems[]">').val($(this).attr('type_id')+'|'+$(this).attr('id').replace('cell-','')).appendTo('#theForm');

		}
	})
	
	$('#action').val('save');
	$('#theForm').submit();

}
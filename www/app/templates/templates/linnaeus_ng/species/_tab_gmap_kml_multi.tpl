<style>
#urls  {
	font-size:0.9em;
	cursor:pointer;
}​
</style>

<script type="text/javascript">
function baseName(str)
{
   var base = new String(str).substring(str.lastIndexOf('/') + 1); 
//    if(base.lastIndexOf(".") != -1)       
//        base = base.substring(0, base.lastIndexOf("."));
   return base;
}
</script>

<script src="//maps.google.com/maps?file=api&v=2&sensor=false" type="text/javascript"></script>

<script type="text/javascript">

var gmap = null;
var dynMapOv = null;
var geoXml = null;
var centerat = null;
var zoomlevel = null;
var initCenterAt = { lat:11.0, lng:11.0 };
{if $external_content->template_params_decoded->initCenterAt->lat}
initCenterAt.lat={$external_content->template_params_decoded->initCenterAt->lat};
{/if}
{if $external_content->template_params_decoded->initCenterAt->lng}
initCenterAt.lng={$external_content->template_params_decoded->initCenterAt->lng};
{/if}

var urls=Array();
var layers=[];

function GMapInitialize()
{
	//Load Google Maps
	gmap = new GMap2(document.getElementById("map"));
	gmap.addMapType(G_PHYSICAL_MAP);
	
	var centerat = new GLatLng(initCenterAt.lat, initCenterAt.lng);
	var topRight = new GControlPosition(G_ANCHOR_TOP_RIGHT, new GSize(10,10));
	var topLeft = new GControlPosition(G_ANCHOR_TOP_LEFT, new GSize(10,10));
	var bottomRight = new GControlPosition(G_ANCHOR_BOTTOM_RIGHT, new GSize(10,10));
	gmap.addControl(new GLargeMapControl(),topRight);
	gmap.addControl(new GScaleControl());
	gmap.addControl(new GOverviewMapControl());
	
	// Create a hierarchical map type control
	var hierarchy = new GHierarchicalMapTypeControl();
	// Make Hybrid the Satellite default
	hierarchy.addRelationship(G_SATELLITE_MAP, G_HYBRID_MAP, "Labels", false);
	// Add the control to the map
	gmap.addControl(hierarchy);
	gmap.setMapType(G_HYBRID_MAP);
	gmap.addControl(new GNavLabelControl (), topLeft);
	gmap.setCenter(centerat, 4);
	
	for(var i=0;i<urls.length;i++)
	{
		geoXml = new GGeoXml( urls[i].url );
		gmap.addOverlay(geoXml);
		layers[i]=geoXml;
	}
	
	gmap.enableScrollWheelZoom();
	
}

function toggleLayer(i)
{
	if ( layers[i].isHidden() )
	{
		layers[i].show();
	}
	else
	{
		layers[i].hide();
	}
}

$(document).ready(function()
{
	$('body').on('unload',function() { GUnload(); } );

	{foreach item=v from="\n"|explode:$external_content->full_url}
	
	var parser = document.createElement('a');
		parser.href = "{$v|@trim|@escape}";
	var u = 
		parser.protocol + '//' +
		parser.host + 
		parser.pathname + 
		parser.search +
		(parser.search ? '&' : '?' ) + 'rnd=' + Math.random() +
		parser.hash;
	
	    urls.push( { url: u, display: unescape( baseName( parser.href ) ) } );
    {/foreach} 
		
	$(urls).each(function(index, value)
	{
		$('#urls').append('<li onclick="toggleLayer('+index+');">'+value.display+'</li>');
	});

	GMapInitialize();
});
</script>

    <br />

    {if $content}
    <p>
        {$content}
    </p>
    {/if}
    
    <div id="map" style="width:{if $external_content->template_params_decoded->width}{$external_content->template_params_decoded->width}{else}660px{/if}; height:{if $external_content->template_params_decoded->height}{$external_content->template_params_decoded->height}{else}550px{/if}"></div>
    
    Layers (click to toggle):
    <ul id="urls"></ul>


{*

<!-- below is an example implementation of Leaflet with KML/KMZ -->

<link rel="stylesheet" href="https://npmcdn.com/leaflet@1.0.0-rc.3/dist/leaflet.css" />

<br />

<div id="mapid" style="width:800px; height:800px"></div>
<ul id="urls"></ul>

<script src="https://npmcdn.com/leaflet@1.0.0-rc.3/dist/leaflet.js"></script>
<script src='//api.tiles.mapbox.com/mapbox.js/plugins/leaflet-omnivore/v0.3.1/leaflet-omnivore.min.js'></script>
<script>

var urls=Array();
var layers=[];
var map;
var colors=['#ff1a1a','#33cc33','#6666ff'];
var toggles=Array();

function toggleLayer( id )
{
	for (var i=0;i<urls.length;i++)
	{
		value=urls[i];
		
		if (value.id==id)
		{
			if (value.visible)
			{
				map.removeLayer(layers[i]);
			}
			else
			{
				map.addLayer(layers[i]);
			}
			urls[i].visible=!urls[i].visible;		
		}
	}
}

$(document).ready(function()
{

{foreach "\n"|explode:$external_content->full_url v k}
urls.push( { id: {$k}, url : '{$v|@trim|@escape}', visible: true, color: colors[{$k}] } );
{/foreach} 

{literal}
	
	$(urls).each(function(index, value)
	{
		$('#urls').append('<li onclick="toggleLayer('+value.id+');">'+
			value.url+
			'<span style="color:'+value.color+'" onclick="toggleLayer('+value.id+');" >toggle</span>'+
			'</li>');
	});

	map = L.map('mapid').setView([51.505, -0.09], 13);

	L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpandmbXliNDBjZWd2M2x6bDk3c2ZtOTkifQ._QA7i5Mpkd_m30IGElHziw', {
		maxZoom: 18,
		attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
			'<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
			'Imagery © <a href="http://mapbox.com">Mapbox</a>',
		id: 'mapbox.streets'
	}).addTo(map);

	map.setView(new L.LatLng(40.737, -73.923), 8);

/*
	L.marker([51.5, -0.09]).addTo(mymap)
		.bindPopup("<b>Hello world!</b><br />I am a popup.").openPopup();

	L.circle([51.508, -0.11], 500, {
		color: 'red',
		fillColor: '#f03',
		fillOpacity: 0.5
	}).addTo(mymap).bindPopup("I am a circle.");

	L.polygon([
		[51.509, -0.08],
		[51.503, -0.06],
		[51.51, -0.047]
	]).addTo(mymap).bindPopup("I am a polygon.");


	var popup = L.popup();

	function onMapClick(e) {
		popup
			.setLatLng(e.latlng)
			.setContent("You clicked the map at " + e.latlng.toString())
			.openOn(mymap);
	}

	mymap.on('click', onMapClick);

*/

	for(var i=0;i<urls.length;i++)
	{

		$.ajax({
			url : "http://127.0.0.1/linnaeus_ng/shared/tools/remote_service.php",
			type: "POST",
			data : ({
				url : encodeURIComponent(urls[i].url),
				original_headers : 1
			}),
			success : function( response )
			{
				//var data = $.parseJSON( response );
				var l=omnivore.kml.parse( response );
				l.setStyle({color: colors[layers.length]});
				/*
				var group = new L.featureGroup([marker1, marker2, marker3]);
				map.fitBounds(group.getBounds());
				*/				
				
				map.setView(l.getBounds().getCenter(), 8);
				map.fitBounds(l.getBounds());

				l.addTo(map);
				layers.push(l);
				//console.dir(layers);
			}
		});
	}

});

</script>

{/literal}

*}

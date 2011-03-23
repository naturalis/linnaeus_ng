{include file="../shared/admin-header.tpl"}

<div id="page-main">
<div id="map_canvas" style="width:650px; height:500px">{if !$isOnline}Unable to display map.{/if}</div>
<div id="map_options" style="width:250px; height:500px;position:relative;margin-top:-500px;left:660px;">
Name: {$snapshot.name}<br />
Start - lat {$snapshot.coordinate1_lat},{$snapshot.coordinate1_lng}<br />
End - lat: {$snapshot.coordinate2_lat},{$snapshot.coordinate2_lng}<br />
Zoom level: {$snapshot.zoom}<br />
<a href="snapshot.php?id={$snapshot.id}">edit</a>
</div>
<div id="coordinates"><span id="coordinates-start"></span><span id="coordinates-end"></span></div>
</div>
{literal}




<script type="text/JavaScript">

var info1 = {
	name: 'The Clown',
	description: 'It\'sssss... the clown!',
	addMarker: true
};

var poly1 = Array();
poly1[0] = [25.774252, -80.190262];
poly1[1] = [18.466465, -66.118292];
poly1[2] = [32.321384, -64.75737];

var polyStyle1 = {
	strokeColor: 'red',
    strokeOpacity: 0.8,
    strokeWeight: 2,
    fillColor: "red",
    fillOpacity: 0.2,
	geodesic: true
};

var info2 = {
	name: 'Satan!',
	description: 'Lucifer Rising!',
	addMarker: false
};

var poly2 = Array();
poly2[0] = [23, -70];
poly2[1] = [19, -60];
poly2[2] = [30, -68];
poly2[3] = [27, -72];

var polyStyle2 = {
	strokeColor: 'yellow',
    strokeOpacity: 0.8,
    strokeWeight: 2,
    fillColor: "yellow",
    fillOpacity: 0.2,
	geodesic: true
};

var poly3 = Array();
poly3[0] = [21, -77];
poly3[1] = [11, -62];
poly3[2] = [13, -59];
poly3[3] = [31, -74];
poly3[4] = [28, -76];

var info3 = {
	name: 'eeeeeeeeeeeek!',
	addMarker: true
};




var info4 = {
	name: 'FUCK A DUCK',
	description: 'Neuk toch eens een eend',
	addMarker: true
};


$(document).ready(function(){
{/literal}
{if $isOnline}
	initMap({$middelLat}, {$middelLng}, {$initZoom} );
	drawPolygon(poly1,polyStyle1,info1);
	drawPolygon(poly2,polyStyle2,info2);
	drawPolygon(poly3,null,info3);
	placeMarker([31, -58],info4);
{else}
alert('Your computer appears to be offline.\nUnable to display map.');
{/if}
{literal}
});
</script>
{/literal}

{include file="../shared/admin-footer.tpl"}
















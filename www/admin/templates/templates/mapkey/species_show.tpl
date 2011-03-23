{include file="../shared/admin-header.tpl"}

<div id="page-main">
<div id="map_canvas" style="width:650px; height:500px">{if !$isOnline}Unable to display map.{/if}</div>
<div id="coordinates"><span id="coordinates-start"></span><span id="coordinates-end"></span></div>
</div>
{literal}
<script type="text/JavaScript">

var polyStyle = {
	strokeColor: 'yellow',
    strokeOpacity: 0.8,
    strokeWeight: 2,
    fillColor: "yellow",
    fillOpacity: 0.2,
	geodesic: true
};

$(document).ready(function(){
{/literal}
{if $isOnline}
	initMap({$middelLat}, {$middelLng}, {$initZoom});
	{if $marker}
	placeMarker([{$marker.latitude},{$marker.longitude}],{literal}{{/literal}
		name: '{$marker.taxon.taxon}',
		addMarker: true
	{literal}});{/literal}
	{/if}
	{if $polygon}
	var nodes = Array();
	{foreach from=$polygon.nodes key=k item=v}
	nodes[{$k}] = [{$v[0]}, {$v[1]}];
	{/foreach}
	drawPolygon(nodes,polyStyle,{literal}{{/literal}
		name: '{$polygon.taxon.taxon}',
		addMarker: true
	{literal}});{/literal}
	{/if}
{else}
alert('Your computer appears to be offline.\nUnable to display map.');
{/if}
{literal}
});
</script>
{/literal}

{include file="../shared/admin-footer.tpl"}

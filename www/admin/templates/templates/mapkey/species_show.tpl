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

{foreach from=$occurrences key=k item=v}

{if $v.type=='marker'}

	placeMarker([{$v.latitude},{$v.longitude}],{literal}{{/literal}
		name: '{$v.taxon.taxon}',
		addMarker: true
	{literal}});{/literal}

{else}

	var nodes{$k} = Array();
	{foreach from=$v.nodes key=kn item=vn}
	nodes{$k}[{$kn}] = [{$vn[0]}, {$vn[1]}];
	{/foreach}
	drawPolygon(nodes{$k},polyStyle,{literal}{{/literal}
		name: '{$v.taxon.taxon}',
		addMarker: true
	{literal}});{/literal}

{/if}

{/foreach}



{else}
alert('Your computer appears to be offline.\nUnable to display map.');
{/if}
{literal}
});
</script>
{/literal}

{include file="../shared/admin-footer.tpl"}

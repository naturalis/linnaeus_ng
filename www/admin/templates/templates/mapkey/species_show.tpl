{include file="../shared/admin-header.tpl"}

<div id="page-main">
<div id="map_canvas" style="width:650px; height:500px">{if !$isOnline}{t}Unable to display map.{/t}{/if}</div>
	<div id="map_options">
		<b>{t _s1=$taxon.taxon}Data for "%s"{/t}</b><br/>
		{t}Legend:{/t}<br />
		{foreach from=$geodataTypes key=k item=v name=x}
		<span style="background-color:#{$v.colour};border:1px solid #999">&nbsp;&nbsp;&nbsp;&nbsp;</span>
		{$v.title}<br />
		{/foreach}
	</div>
<div id="coordinates"><span id="coordinates-start"></span><span id="coordinates-end"></span></div>
</div>
{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
{if $isOnline}

	initMap({$mapInitString});
	{if $mapBorder}
	map.fitBounds(new google.maps.LatLngBounds(new google.maps.LatLng({$mapBorder.sw.lat}, {$mapBorder.sw.lng}), new google.maps.LatLng({$mapBorder.ne.lat}, {$mapBorder.ne.lng})));
	{/if}

{foreach from=$occurrences key=k item=v}

{if $taxon}
{assign var=taxonName value=$taxon.taxon}
{else if $v.taxon.taxon}
{assign var=taxonName value=$v.taxon.taxon}
{/if}

{if $v.type=='marker' && $v.latitude && $v.longitude}
	placeMarker([{$v.latitude},{$v.longitude}],{literal}{{/literal}
		name: '{$taxonName}',
		addMarker: true,
		addDelete: false,
		occurrenceId: {$v.id},
		colour:'{$v.colour}'	
	{literal}});{/literal}
{elseif $v.type=='polygon' && $v.nodes}
	var nodes{$k} = Array();
	{foreach from=$v.nodes key=kn item=vn}
	nodes{$k}[{$kn}] = [{$vn[0]}, {$vn[1]}];
	{/foreach}
	drawPolygon(nodes{$k},null,{literal}{{/literal}
		name: '{$taxonName}',
		addMarker: true,
		addDelete: false,
		occurrenceId: {$v.id},
		colour:'{$v.colour}'
	{literal}});{/literal}

{/if}
{/foreach}

{else}
alert({t}'Your computer appears to be offline.\nUnable to display map.'{/t});
{/if}
{literal}
});
</script>
{/literal}

{include file="../shared/admin-footer.tpl"}

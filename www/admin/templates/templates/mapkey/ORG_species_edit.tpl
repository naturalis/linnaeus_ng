{include file="../shared/admin-header.tpl"}

<div id="page-main">
	<div id="map_canvas" style="width:650px; height:500px">{if !$isOnline}{t}Unable to display map.{/t}{/if}</div>
	<div id="map_options">
		<b>{t _s1=$taxon.taxon}Data for "%s"{/t}</b><br/>
		{t}Selection type:{/t} <span id="selection-type">{t}(none){/t}</span><br />
		{t}Coordinates:{/t}<br /><span id="coordinates">(-1,-1)</span><br />
		<hr style="height:1px;color:#999" />
		{t}Select the type of data you are drawing on the map:{/t}<br />
		{foreach from=$geodataTypes key=k item=v name=x}
		<label>
		<input type="radio" value="{$v.id}" name="geodatatype" id="geodatatype-{$k}" colour="{$v.colour}" {if $smarty.foreach.x.index==0} checked="checked"{/if}>
		<span style="background-color:#{$v.colour};border:1px solid #999">&nbsp;&nbsp;&nbsp;&nbsp;</span>
		{$v.title}
		</label><br />
		{/foreach}
		{t _s1='<a href="data_types.php">' _s2='</a>'}(%sadd or change datatypes.%s){/t}
		
		<form action="" method="post" id="theForm">
		<input type="hidden" name="id" value="{$taxon.id}" />
		<input type="hidden" name="rnd" value="{$rnd}" />
		<p style="text-align:justify">
		{t}To enable setting markers (points on the map), click the button below.{/t}
		{t}Then click on the appropriate spot on the map to place a marker. To remove a marker, right-click on it.{/t}<br />
		<input type="button" value="set markers" onclick="setOccurrenceType('marker');" />
		</p>
		<p style="text-align:justify">
		{t}To enable drawing polygons, click the button below.{/t}
		{t}Then draw the polygon by clicking the appropriate spots on the map. When finished drawing, click the button again. To remove a polygon, right-click on it.{/t}<br />
		<input type="button" value="draw a polygon" id="polygon-button" onclick="createPolygon();" />
		</p>
		<p>
		{t}When you are done, click 'save' to store all occurrences.{/t}<br />
		<input type="button" onclick="saveAll()" value="save" />
		</p>
		</form>
	</div>
</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
{if $isOnline}

	initMap({$mapInitString});	
	addMouseHandlers();
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
		addDelete: true,
		occurrenceId: {$v.id},
		colour:'{$v.colour}'
	{literal}});{/literal}
{elseif $v.type=='polygon' && $v.nodes}
	drawPolygon({$v.boundary_nodes},null,{literal}{{/literal}
		name: '{$taxonName}',
		addMarker: false,
		addDelete: true,
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

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
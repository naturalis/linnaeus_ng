{include file="../shared/header.tpl"}
<div id="page-main">
{if !$isOnline}
{t}Your computer appears to be offline. Unfortunately, the map key doesn't work without an internet connection.{/t}
{else}

	<div id="map_canvas">{if !$isOnline}{t}Unable to display map.{/t}{/if}</div>
	<div id="map_options">
		{t}Select an area to search{/t}<br />
		 ({t}switch to {/t}<a href="examine.php">{t}examine species{/t}</a>{t} or {/t}<a href="compare.php">{t}compare species{/t}</a>)
		<br /><br />
		<form method="post" action="" id="theForm">
		{t}Coordinates:{/t} <span id="coordinates">(-1,-1)</span><br />
		<hr style="height:1px;color:#999" />
	
		<input type="button" onclick="startPolygonDraw()" id="button-draw" value="{t}draw area{/t}" /><br/>
		<input type="button" onclick="doMapSearch()" value="{t}search{/t}" />
		<input type="button" onclick="clearPolygon();clearSearchResults();" value="{t}clear{/t}" />
		</form>
		<hr style="height:1px;color:#999" />
		{if $geoDataTypes}
		<table>
		{foreach from=$geoDataTypes key=k item=v}
		{if $count.data[$k]}
			<tr style="vertical-align:top">
				<td style="width:25px;border:1px solid black;background-color:#{$v.colour}"></td>
				<td style="width:5px;"></td>
				<td style="width:215px;">{$v.title} ({$count.data[$k]})</td>
				<td style="width:25px;" hidden="0" onclick="doMapTypeToggle(this,{$v.id})" class="a">hide</td>
			</tr>
			<tr><td colspan="4" style="height:1px;"></td></tr>
		{/if}
		{/foreach}
		</table>
		<hr style="height:1px;color:#999" />
		{/if}
		<table>
			<tr><td colspan="2" ><b>{t}Found species{/t}</b></td></tr>
		{assign var=prev value=false}
		{foreach from=$results key=k item=v}
		{if $prev.taxon_id!=$v.taxon_id}
			<tr style="vertical-align:top">
				<td style="width:245px;">{$taxa[$v.taxon_id].taxon} ({$count.taxa[$v.taxon_id]})</td>
				<td style="width:25px;" hidden="0" onclick="doMapTypeToggle(this,null,{$v.taxon_id})" class="a">hide</td>
			</tr>
			<tr><td colspan="2" style="height:1px;"></td></tr>
		{/if}
		{assign var=prev value=$v}
		{/foreach}
		{if $count.total==0}
			<tr><td colspan="2">{t}nothing found{/t}</td></tr>
		{/if}
		</table>
	</div>

</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}

	initMap({$mapInitString});
	initMapSearch();
	{if $mapBorder}
	map.fitBounds(new google.maps.LatLngBounds(new google.maps.LatLng({$mapBorder.sw.lat}, {$mapBorder.sw.lng}), new google.maps.LatLng({$mapBorder.ne.lat}, {$mapBorder.ne.lng})));
	{/if}

{foreach from=$results key=k item=v}

{if $v.type=='marker' && $v.latitude && $v.longitude}
	placeMarker([{$v.latitude},{$v.longitude}],{literal}{{/literal}
		name: '{$taxa[$v.taxon_id].taxon}: {$geoDataTypes[$v.type_id].title}',
		addMarker: true,
		addDelete: false,
		occurrenceId: {$v.id},
		taxonId: {$v.taxon_id},
		colour:'{$geoDataTypes[$v.type_id].colour}',
		typeId:{$v.type_id}
	{literal}});{/literal}
{elseif $v.type=='polygon' && $v.nodes}
	var nodes{$k} = Array();
	{foreach from=$v.nodes key=kn item=vn}
	nodes{$k}[{$kn}] = [{$vn[0]}, {$vn[1]}];
	{/foreach}
	drawPolygon(nodes{$k},null,{literal}{{/literal}
		name: '{$taxa[$v.taxon_id].taxon}: {$geoDataTypes[$v.type_id].title}',
		addMarker: true,
		addDelete: false,
		occurrenceId: {$v.id},
		taxonId: {$v.taxon_id},
		colour:'{$geoDataTypes[$v.type_id].colour}',
		typeId:{$v.type_id}
	{literal}});{/literal}

{/if}
{/foreach}
	{if $coordinates}
	setPredefPolygon('{$coordinates}');
	{/if}
{literal}
});
</script>
{/literal}




{/if}
</div>

{include file="../shared/footer.tpl"}

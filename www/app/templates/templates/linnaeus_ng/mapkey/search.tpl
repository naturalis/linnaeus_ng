{include file="../shared/header.tpl"}
<div id="page-main">
{if !$isOnline}
{t}Your computer appears to be offline. Unfortunately, the map doesn't work without an internet connection.{/t}
{else}

	<div id="map_canvas">{if !$isOnline}{t}Unable to display map.{/t}{/if}</div>
	<div id="map_search">
		<input type="button" onclick="doMapSearch()" value="{t}search{/t}" />
		<input type="button" onclick="clearPolygon();clearSearchResults();" value="{t}clear{/t}" />
		<table>
		{assign var=prev value=false}
		{foreach from=$results key=k item=v}
		{if $prev.taxon_id!=$v.taxon_id}
			<tr style="vertical-align:top">
				<td style="width:245px;">
                <a href="../species/taxon.php?id={$v.taxon_id}">{$taxa[$v.taxon_id].taxon}</a>
				</td>
				
				<td style="width:25px;"><input type="checkbox" id="toggle-{$v.type_id}-{$v.taxon_id}" onchange="doMapTypeToggle({$v.type_id},{$v.taxon_id})" checked="checked"></td>
			</tr>
			<tr><td colspan="2" style="height:1px;"></td></tr>
		{/if}
		{assign var=prev value=$v}
		{/foreach}
		{if $count.total==0 && $results}
			<tr><td colspan="2">{t}nothing found{/t}</td></tr>
		{/if}
		</table>

		{if $geoDataTypes}
		<p>
		<table>
		{foreach from=$geoDataTypes key=k item=v}
		{if $count.data[$k]}
			<tr style="vertical-align:top">
				<td style="width:25px;border:1px solid black;background-color:#{$v.colour}" onclick="$('#toggle-{$v.id}').attr('checked',!$('#toggle-{$v.id}').attr('checked'));doMapTypeToggle({$v.id});"></td>
				<td style="width:5px;"></td>
				<td style="width:215px;"><label for="toggle-{$v.id}">{$v.title}</label></td>
				<td style="width:25px;"><input type="checkbox" id="toggle-{$v.id}" onchange="doMapTypeToggle({$v.id})" checked="checked"></td>
			</tr>
			<tr><td colspan="4" style="height:1px;"></td></tr>
		{/if}
		{/foreach}
		</table>
		</p>
		{/if}
	</div>
    
    {include file="_phased-out.tpl"}

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
	drawSearchArea({$coordinates|replace:'(':'['|replace:')':']'});
	{/if}
{literal}
});
</script>
{/literal}




{/if}
</div>

{include file="../shared/footer.tpl"}

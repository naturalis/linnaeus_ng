{include file="../shared/admin-header.tpl"}

<div id="page-main">
	<div id="data-type-div">
		<div id="data-legend-div">
		<div id=xx>
		{foreach from=$geodataTypes key=k item=v name=x}
		{if $geodataTypesPresent[$v.id]}
			<p style="margin:4px;">
				<span style="background-color:#{$v.colour};border:1px solid #999;">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;{$v.title}
			</p>
		{/if}
		{/foreach}
			<input type="button" onclick="window.open('species_edit.php?id={$taxon.id}','_self')" value="{t}edit{/t}" style="margin:2px 0px 3px 4px"/>
			</div>
		</div>
	</div>

	<div id="map_canvas" style="width:880px; height:700px">{if !$isOnline}{t}Unable to display map.{/t}{/if}</div>
</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}

	allLookupNavigateOverrideUrl('species_show.php?id=%s');

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
		occurrenceId: {$v.id},
		colour:'{$v.colour}'	
	{literal}});{/literal}
{elseif $v.type=='polygon' && $v.nodes}
	drawPolygon({$v.boundary_nodes},{literal}{{/literal}
		name: '{$taxonName}',
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

{include file="../shared/admin-header.tpl"}

<div id="page-main">

	<form action="" method="post" id="theForm">
	<input type="hidden" name="id" value="{$taxon.id}" />
	<input type="hidden" name="rnd" value="{$rnd}" />
	<input type="hidden" name="action" id="action" value="" />
	</form>
		
	<div id="data-type-div">
		<div id="data-select-div">
			<select name="geodatatype" id="geodatatype" onchange="mapDoChangeDataType()">
				{foreach from=$geodataTypes key=k item=v name=x}
				<option value="{$v.id}" id="geodatatype-{$k}" colour="{$v.colour}" type="{$v.type}" {if $smarty.foreach.x.index==0} selected="selected"{/if}>
				{$v.title}
				</option>
				{/foreach}
			</select><br />
		</div>
		<input type="button" class="map-save-button" onclick="mapSaveMap()" value="{t}save{/t}" />
		{* <input type="button" class="map-save-button" onclick="$('#action').val('preview');mapSaveMap()" value="{t}preview{/t}" /> *}
		<input type="button" class="map-save-button" onclick="window.open('copy.php?id={$taxon.id}','_self')" value="{t}copy{/t}" /><br />
		<input type="button" class="map-save-button" onclick="window.open('species_edit.php?id={$taxon.id}','_self')" value="{t}reset{/t}" />
		<input type="button" class="map-save-button" onclick="mapClearMap()" value="{t}clear{/t}" />
		{*<input type="button" class="map-save-button" onclick="window.open('species_show.php?id={$taxon.id}','_self')" value="{t}back{/t}" />*}
		{*<span id="coordinates">(-1,-1)</span><br />*}
	</div>

	<div id="map_canvas" style="width:880px; height:700px">{if !$isOnline}{t}Unable to display map.{/t}{/if}</div>
</div>
{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
{if $isOnline}

	allLookupNavigateOverrideUrl('species_edit.php?id=%s');

	initMap({$mapInitString});
	mapToggleEditorMode($('#geodatatype :selected').attr('type'));
	addMouseHandlers();
	{if $mapBorder}
	map.fitBounds(new google.maps.LatLngBounds(new google.maps.LatLng({$mapBorder.sw.lat}, {$mapBorder.sw.lng}), new google.maps.LatLng({$mapBorder.ne.lat}, {$mapBorder.ne.lng})));
	{else}
	map.fitBounds(new google.maps.LatLngBounds(new google.maps.LatLng(-45,-45), new google.maps.LatLng(45,45)));
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
		dataTypeid: '{$v.type_id}',
		colour:'{$v.colour}',
		editable: true
	{literal}});{/literal}
{elseif $v.type=='polygon' && $v.nodes}
	drawPolygon({$v.boundary_nodes},{literal}{{/literal}
		name: '{$taxonName}',
		addMarker: false,
		addDelete: true,
		occurrenceId: {$v.id},
		dataTypeid: {$v.type_id},
		colour:'{$v.colour}',
		editable: true
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
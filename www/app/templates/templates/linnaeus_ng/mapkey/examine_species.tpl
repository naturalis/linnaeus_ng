{include file="../shared/header.tpl"}

<div id="page-main">
	
	{if !$isOnline}
	{t}Your computer appears to be offline. Unfortunately, the map doesn't work without an internet connection.{/t}
	{else}

	<div id="map_canvas">

		{if !$isOnline}{t}Unable to display map.{/t}{/if}
		
		<div id="map_options">
			<b>
			<a href="../species/taxon.php?id={$taxon.id}">{$taxon.taxon}</a>
			</b><br/><br/>
			{*{t}Coordinates:{/t} <span id="coordinates">(-1,-1)</span><br />
			<hr style="height:1px;color:#999" />*}
			<table>
			{foreach from=$geoDataTypes key=k item=v}
			{if $count.data[$k]}
				<tr style="vertical-align:top">
					<td style="width:25px;border:1px solid black;background-color:#{$v.colour}" onclick="$('#toggle-{$v.id}').attr('checked',!$('#toggle-{$v.id}').attr('checked'));doMapTypeToggle({$v.id});"></td>
					<td style="width:5px;"></td>
					<td style="padding-right:5px;"><label for="toggle-{$v.id}">{$v.title}</label></td>{* ({$count.data[$k]})*}
					<td><input type="checkbox" checked="checked" id="toggle-{$v.id}" onchange="doMapTypeToggle({$v.id})"></td>
				</tr>
				<tr><td colspan="4" style="height:1px;"></td></tr>
			{/if}
			{/foreach}
			{if $count.total==0}
				<tr><td colspan="4">{t}no data available{/t}</td></tr>
			{/if}
			</table>
			{if $showBackToSearch && $session.app.user.search.hasSearchResults}
			<hr style="height:1px;color:#999" />
			<p>
			<span class="back-link" onclick ="window.open('../search/redosearch.php','_self')">{t}Back to{/t} {t}search results{/t}</span>
			</p>
			{/if}

		</div><!-- /#map_options -->

	</div><!-- /#map-canvas -->
    
    {include file="_phased-out.tpl"}


{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}

	initMap({if $mapInitString}{$mapInitString}{else}{literal}{}{/literal}{/if});
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
		colour:'{$v.colour}',
		typeId:{$v.type_id}
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
		colour:'{$v.colour}',
		typeId:{$v.type_id}
	{literal}});{/literal}

{/if}
{/foreach}

{literal}
});
</script>
{/literal}

{/if}
</div>

{include file="../shared/footer.tpl"}

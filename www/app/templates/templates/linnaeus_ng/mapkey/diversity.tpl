{include file="../shared/header.tpl"}

<div id="page-main">
{if !$isOnline}
{t}Your computer appears to be offline. Unfortunately, the map doesn't work without an internet connection.{/t}
{else}

	<div id="map_canvas">{if !$isOnline}{t}Unable to display map.{/t}{/if}</div>
	<div id="map_options">
		<table>
		{foreach from=$geoDataTypes key=k item=v}
			<tr style="vertical-align:top">
				<td style="width:25px;border:1px solid black;background-color:#{$v.colour}" onclick="$('#toggle-{$v.id}').attr('checked',!$('#toggle-{$v.id}').attr('checked'));doMapTypeToggle({$v.id});"></td>
				<td style="width:5px;"></td>
				<td style="padding-right:5px;"><label for="toggle-{$v.id}">{$v.title}</label></td>{* ({$count.data[$k]})*}
				<td><input type="checkbox" checked="checked" id="toggle-{$v.id}" onchange="doMapTypeToggle({$v.id})"></td>
			</tr>
			<tr><td colspan="4" style="height:1px;"></td></tr>
		{/foreach}
		</table>
	</div>
    
    {include file="_phased-out.tpl"}

</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}

	initMap({if $mapInitString}{$mapInitString}{else}{literal}{}{/literal}{/if});
	{if 1==2 && $mapBorder}
	map.fitBounds(new google.maps.LatLngBounds(new google.maps.LatLng({$mapBorder.sw.lat}, {$mapBorder.sw.lng}), new google.maps.LatLng({$mapBorder.ne.lat}, {$mapBorder.ne.lng})));
	{/if}

{foreach from=$data item=v}
	{foreach from=$v.nodes item=nodes}
		drawPolygon([
		{foreach from=$nodes item=node}
		{if $node[0]|is_numeric && $node[1]|is_numeric && $v.type_id!=-1}
			[{$node[0]},{$node[1]}],
		{/if}
		{/foreach}]
			,null,{literal}{{/literal}
			colour:'{$geoDataTypes[$v.type_id].colour}',
			typeId:{$v.type_id}
		{literal}});{/literal}
	{/foreach}
{/foreach}

{literal}
});
</script>
{/literal}

{/if}
</div>

{include file="../shared/footer.tpl"}

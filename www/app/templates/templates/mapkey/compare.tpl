{include file="../shared/header.tpl"}
{literal}
<style>
.taxon-select {
	font-size:inherit;
	height:25px;
}
</style>
{/literal}

<div id="page-main">
{if !$isOnline}
{t}Your computer appears to be offline. Unfortunately, the map key doesn't work without an internet connection.{/t}
{else}

	<div id="map_canvas">{if !$isOnline}{t}Unable to display map.{/t}{/if}</div>
	<div id="map_options">

		<form method="post" action="">
		<p>
		Taxon A:
		<select name="idA" class="taxon-select">	
		<option value="" {if !$taxonA}selected="selected"{/if}>--choose taxon--</option>
		{foreach from=$taxa key=k item=v}
		<option value="{$v.id}" {if $taxonA.id==$v.id}selected="selected"{/if}>{$v.taxon}</option>
		{/foreach}
		</select>	
		</p>
		<p>
		Taxon B:
		<select name="idB" class="taxon-select">	
		<option value="" {if !$taxonB}selected="selected"{/if}>--choose taxon--</option>
		{foreach from=$taxa key=k item=v}
		<option value="{$v.id}" {if $taxonB.id==$v.id}selected="selected"{/if}>{$v.taxon}</option>
		{/foreach}
		</select>	
		</p>
		<p>
		<input type="submit" value="compare" />
		</p>
		</form>

		<hr style="height:1px;color:#999" />
		{t}Coordinates:{/t} <span id="coordinates">(-1,-1)</span><br />

		{if $taxonA && $taxonB}
		<hr style="height:1px;color:#999" />
		<b>{t}Comparison{/t}</b><br />
		{foreach from=$overlap key=k item=v}
			<i>{$geoDataTypes[$v.type_id].title}</i>:
			{t _s1=$taxonA.taxon _s2=$taxonB.taxon _s3=$v.total}%s intersects or overlaps %s in %s instances.{/t}<br /><br />
		{/foreach}
		{if !$overlap}{t}There is no overlap between these two species.{/t}{/if}
		<hr style="height:1px;color:#999" />
		{/if}

		<table>
		{if $taxonA}
			<tr style="vertical-align:top">
				<td colspan="4"><b>{$taxonA.taxon}</b>{if $countA.total>0} ({$countA.total}){/if}</td>
			</tr>
			{foreach from=$geoDataTypes key=k item=v}
			{if $countA.data[$k]}
				<tr style="vertical-align:top">
					<td style="width:25px;border:1px solid black;background-color:#{$v.colour}"></td>
					<td style="width:5px;"></td>
					<td style="width:215px;">{$v.title} ({$countA.data[$k]})</td>
					<td style="width:25px;" hidden="0" onclick="doMapTypeToggle({$v.id},this,{$taxonA.id})" class="a">hide</td>
				</tr>
				<tr><td colspan="4" style="height:1px;"></td></tr>
			{/if}
			{/foreach}
			{if $countA.total==0}
				<tr><td colspan="4">{t}no data available{/t}</td></tr>
			{/if}
		{/if}
		
		{if $taxonA && $taxonB}
			<tr style="vertical-align:top">
				<td colspan="4">&nbsp;</td>
			</tr>
		{/if}
	
		{if $taxonB}
			<tr style="vertical-align:top">
				<td colspan="4"><b>{$taxonB.taxon}</b>{if $countB.total>0} ({$countB.total}){/if}</td>
			</tr>
			{foreach from=$geoDataTypes key=k item=v}
			{if $countB.data[$k]}
				<tr style="vertical-align:top">
					<td style="width:25px;border:1px solid black;background-color:#{$v.colour_inverse}"></td>
					<td style="width:5px;"></td>
					<td style="width:215px;">{$v.title} ({$countB.data[$k]})</td>
					<td style="width:25px;" hidden="0" onclick="doMapTypeToggle({$v.id},this,{$taxonB.id})" class="a">hide</td>
				</tr>
				<tr><td colspan="4" style="height:1px;"></td></tr>
			{/if}
			{/foreach}
			{if $countB.total==0}
				<tr><td colspan="4">{t}no data available{/t}</td></tr>
			{/if}
		{/if}
		</table>

	</div>

</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}

	initMap({$mapInitString});
	{if $mapBorder}
	map.fitBounds(new google.maps.LatLngBounds(new google.maps.LatLng({$mapBorder.sw.lat}, {$mapBorder.sw.lng}), new google.maps.LatLng({$mapBorder.ne.lat}, {$mapBorder.ne.lng})));
	{/if}

{foreach from=$occurrencesA key=k item=v}

{if $taxon}
{assign var=taxonName value=$taxon.taxon}
{else if $v.taxon.taxon}
{assign var=taxonName value=$v.taxon.taxon}
{/if}

{if $v.type=='marker' && $v.latitude && $v.longitude}
	placeMarker([{$v.latitude},{$v.longitude}],{literal}{{/literal}
		name: '{$taxonA.taxon}: {$v.type_title}',
		addMarker: true,
		addDelete: false,
		occurrenceId: {$v.id},
		taxonId: {$taxonA.id},
		colour:'{$v.colour}',
		typeId:{$v.type_id}
	{literal}});{/literal}
{elseif $v.type=='polygon' && $v.nodes}
	var nodes{$k} = Array();
	{foreach from=$v.nodes key=kn item=vn}
	nodes{$k}[{$kn}] = [{$vn[0]}, {$vn[1]}];
	{/foreach}
	drawPolygon(nodes{$k},null,{literal}{{/literal}
		name: '{$taxonA.taxon}: {$v.type_title}',
		addMarker: true,
		addDelete: false,
		occurrenceId: {$v.id},
		taxonId: {$taxonA.id},
		colour:'{$v.colour}',
		typeId:{$v.type_id}
	{literal}});{/literal}

{/if}
{/foreach}

{foreach from=$occurrencesB key=k item=v}

{if $taxon}
{assign var=taxonName value=$taxon.taxon}
{else if $v.taxon.taxon}
{assign var=taxonName value=$v.taxon.taxon}
{/if}

{if $v.type=='marker' && $v.latitude && $v.longitude}
	placeMarker([{$v.latitude},{$v.longitude}],{literal}{{/literal}
		name: '{$taxonB.taxon}: {$v.type_title}',
		addMarker: true,
		addDelete: false,
		occurrenceId: {$v.id},
		taxonId: {$taxonB.id},
		colour:'{if $occurrencesA && $occurrencesB}{$v.colour_inverse}{else}{$v.colour}{/if}',
		typeId:{$v.type_id}
	{literal}});{/literal}
{elseif $v.type=='polygon' && $v.nodes}
	var nodes{$k} = Array();
	{foreach from=$v.nodes key=kn item=vn}
	nodes{$k}[{$kn}] = [{$vn[0]}, {$vn[1]}];
	{/foreach}
	drawPolygon(nodes{$k},null,{literal}{{/literal}
		name: '{$taxonB.taxon}: {$v.type_title}',
		addMarker: true,
		addDelete: false,
		occurrenceId: {$v.id},
		taxonId: {$taxonB.id},
		colour:'{if $occurrencesA && $occurrencesB}{$v.colour_inverse}{else}{$v.colour}{/if}',
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

{include file="../shared/header.tpl"}

<div id="page-main">
{if !$isOnline}
{t}Your computer appears to be offline. Unfortunately, the map doesn't work without an internet connection.{/t}
{else}

	<div id="map_canvas">{if !$isOnline}{t}Unable to display map.{/t}{/if}</div>
	<div id="map_compare">
		{t}Select two species to compare{/t}<br />
		<p>
		{t}Species A:{/t}
		<select name="idA" id="idA" class="taxon-select">	
		<option value="" {if !$taxonA}selected="selected"{/if}>{t}--choose species--{/t}</option>
		{foreach from=$taxa key=k item=v}
		<option value="{$v.id}" {if $taxonA.id==$v.id}selected="selected"{/if}>{$v.taxon}</option>
		{/foreach}
		</select>	
		<br />
		{t}Species B:{/t}
		<select name="idB" id="idB" class="taxon-select">	
		<option value="" {if !$taxonB}selected="selected"{/if}>{t}--choose species--{/t}</option>
		{foreach from=$taxa key=k item=v}
		<option value="{$v.id}" {if $taxonB.id==$v.id}selected="selected"{/if}>{$v.taxon}</option>
		{/foreach}
		</select><br />	
		<input id="map_compare_button" type="button" value="compare" onclick="doMapCompare()" />
		</p>

		{if $taxonA && $taxonB}
		<hr style="height:1px;color:#eee" />
		<b>{t}Comparison{/t}</b><br />
		{foreach from=$overlap key=k item=v}
			<i>{$geoDataTypes[$v.type_id].title}</i>:
			{t _s1=$taxonA.taxon _s2=$taxonB.taxon _s3=$v.total}%s intersects or overlaps %s in %s instances.{/t}<br /><br />
		{/foreach}
		{if !$overlap}{t}There is no overlap between these two species.{/t}{/if}
		<hr style="height:1px;color:#eee" />
		{/if}

		<table>
		{if $taxonA}
			<tr style="vertical-align:top">
				<td colspan="4"><b>{$taxonA.taxon}</b>{* if $countA.total>0} ({$countA.total}){/if *}</td>
			</tr>
			{foreach from=$geoDataTypes key=k item=v}
			{if $countA.data[$k]}
				<tr style="vertical-align:top">
					<td style="width:25px;border:1px solid black;background-color:#{$v.colour}" onclick="$('#toggle-{$v.id}-{$taxonA.id}').attr('checked',!$('#toggle-{$v.id}-{$taxonA.id}').attr('checked'));doMapTypeToggle({$v.id},{$taxonA.id});"></td>
					<td style="width:5px;"></td>
					<td style="width:215px;"><label for="toggle-{$v.id}-{$taxonA.id}">{$v.title}</label></td>{* ({$countA.data[$k]}) *}
					<td style="width:25px;"><input type="checkbox" id="toggle-{$v.id}-{$taxonA.id}" onclick="doMapTypeToggle({$v.id},{$taxonA.id})" checked="checked"></td>
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
				<td colspan="4"><b>{$taxonB.taxon}</b>{* if $countB.total>0} ({$countB.total}){/if *}</td>
			</tr>
			{foreach from=$geoDataTypes key=k item=v}
			{if $countB.data[$k]}
				<tr style="vertical-align:top">
					<td style="width:25px;border:1px solid black;background-color:#{$v.colour_inverse}" onclick="$('#toggle-{$v.id}-{$taxonB.id}').attr('checked',!$('#toggle-{$v.id}-{$taxonB.id}').attr('checked'));doMapTypeToggle({$v.id},{$taxonB.id});"></td>
					<td style="width:5px;"></td>
					<td style="width:215px;"><label for="toggle-{$v.id}-{$taxonB.id}">{$v.title}</label></</td>{* ({$countB.data[$k]}) *}
					<td style="width:25px;"><input type="checkbox" id="toggle-{$v.id}-{$taxonB.id}" onclick="doMapTypeToggle({$v.id},{$taxonB.id})" checked="checked"></td>
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

	{include file="_phased-out.tpl"}

</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}

	initMap({if $mapInitString}{$mapInitString}{else}{literal}{}{/literal}{/if});
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
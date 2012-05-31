{include file="../shared/header.tpl"}

{assign var=map value=$maps[$mapId]}

<div id="page-main">

{if $map.mapExists}
	<div>{$map.name} (<span id=coordinates>0,0</span>)</div>
{else}
	<div>{t _s1=$map.name}The image file for the map "%s" is missing.{/t}</div>
{/if}

<table>
	<tr style="vertical-align:top">
		<td>
		{if $map.mapExists}
			<table id="mapTable">
			{assign var=cellNo value=1}
			{section name=rows start=1 loop=$map.rows+1 step=1}
				<tr>
				{section name=cols start=1 loop=$map.cols+1 step=1}
					<td id="cell-{$cellNo}" datatype="{$occurrences[$cellNo].type_id}" class="mapCell{$overlap[$cellNo]}"></td>
					{assign var=cellNo value=$cellNo+1}
				{/section}
				</tr>
			{/section}
			</table>
		{/if}
		<!-- added ui dialog 2012.05.30 -->
		{if $maps|@count>1}
			<p>
				<span class="a" onclick="
					showDialog(
						'{t}Choose a map{/t}',
						{foreach item=v from=$maps}'{if $v.id!=$mapId}<a href=?id={$taxon.id}&m={$v.id}&idA={$taxonA.id}&idB={$taxonB.id}>{/if}{$v.name|escape:'htmlall'}{if $v.id!=$mapId}</a>{/if}<br />'+
						{/foreach}' '
					);">{t}Switch to another map{/t}</span>
			</p>
		{/if}	
		<!-- /added ui dialog 2012.05.30 -->
		</td>
		<td style="padding-left:4px;">
		<!-- added ui dialog 2012.05.30 -->

			{t}Select two species to compare{/t}<br /><br />
			<table>
				<tr>
					<td class="mapCellA mapCellLegend" style="width:15px;"></td>
					<td style="padding-left:5px;width:75px">{t}Species A:{/t}</td>
					<td id="speciesNameA" class="a" onclick="
						allLookupSetExtraVars('l2_must_have_geo','1');
						allLookupNavigateOverrideUrl('javascript:l2SetCompareSpecies(1,%s);');
						allLookupShowDialog()
					">
						{if $taxonA}{$taxonA.taxon}{else}{t}choose species{/t}{/if}
					</td>
				</tr>
				<tr>
					<td class="mapCellB mapCellLegend" style="width:15px;"></td>
					<td style="padding-left:5px;width:75px">{t}Species B:{/t}</td>
					<td id="speciesNameB" class="a" onclick="
						allLookupSetExtraVars('l2_must_have_geo','1');
						allLookupNavigateOverrideUrl('javascript:l2SetCompareSpecies(2,%s);');
						allLookupShowDialog()
					">
						{if $taxonB}{$taxonB.taxon}{else}{t}choose species{/t}{/if}
					</td>
				</tr>
			</table>

			<br />	
			<input type="hidden" name="idA" id="idA" value="{if $taxonA}{$taxonA.id}{/if}" />
			<input type="hidden" name="idB" id="idB" value="{if $taxonB}{$taxonB.id}{/if}" />
			<input id="map_compare_button" type="button" value="{t}compare{/t}" onclick="l2DoMapCompare()" />
			<br /><br />

			<!-- /added ui dialog 2012.05.30 -->

		
			<p style="margin:0px;">
			<span class="mapCellAB mapCellLegend">&nbsp;&nbsp;&nbsp;&nbsp;</span>{t}Show overlap in:{/t}
			{foreach from=$geoDataTypes key=k item=v name=x}
				<p class="mapPCheckbox">
					<label>
						<input 
							type="checkbox" 
							name="selectedDataTypes[{$v.id}]" 
							value="{$v.id}" 
							onchange="l2DataTypeToggle();"
							{if $selectedDataTypes=='*' || $selectedDataTypes[$v.id]==$v.id}checked="checked"{/if}/>
						{$v.title}
					</label>
				</p>
			{/foreach}
			</p>
		</td>
	</tr>
</table>
</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
{if $map.mapExists}
l2SetMap(
	'{$map.imageFullName|replace:' ':'%20'}',
	{$map.size[0]},
	{$map.size[1]},
	'{$map.coordinates.original}',
	{math equation="(floor( x / y ))-z" x=$map.size[0] y=$map.cols z=1},
	{math equation="(floor( x / y ))-z" x=$map.size[1] y=$map.rows z=1}
);
{/if}
{literal}
$("#mapTable").mousemove(function(event) {
	l2MapMouseOver(event.pageX,event.pageY);
});

});
</script>
{/literal}

{include file="../shared/footer.tpl"}
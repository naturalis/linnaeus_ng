{include file="../shared/header.tpl"}

{assign var=map value=$maps[$mapId]}

<div id="page-main">

{if $map.mapExists}
	<div>{$map.name} <span id=coordinates></span></div>
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
					<td id="cell-{$cellNo}" datatype="{$occurrences[$cellNo].type_id}" class="mapCell mapCell{$overlap[$cellNo]}"></td>
					{assign var=cellNo value=$cellNo+1}
				{/section}
				</tr>
			{/section}
			</table>
		{/if}
		<p>
		{if $maps|@count>1}{t}Switch to another map:{/t}<br />{/if}
		{if $maps|@count>1}
		{foreach item=v from=$maps}{if $v.id!=$mapId}<a href="?id={$taxon.id}&m={$v.id}&idA={$taxonA.id}&idB={$taxonB.id}">{/if}{$v.name}{if $v.id!=$mapId}</a>{/if}<br />{/foreach}
		{/if}
		</p>		
		</td>
		<td style="padding-left:4px;">
			{t}Select two species to compare{/t}<br /><br />
			<span class="mapCellA mapCellLegend">&nbsp;&nbsp;&nbsp;&nbsp;</span>{t}Species A:{/t}
			<select name="idA" id="idA" class="taxon-select">	
				<option value="" {if !$taxonA}selected="selected"{/if}>{t}--choose species--{/t}</option>
				{foreach from=$taxa key=k item=v}
					<option value="{$v.id}" {if $taxonA.id==$v.id}selected="selected"{/if}>{$v.taxon}</option>
				{/foreach}
			</select>
			&nbsp;&nbsp;&nbsp;&nbsp;</span>
			<br />
			<span class="mapCellB mapCellLegend">&nbsp;&nbsp;&nbsp;&nbsp;</span>{t}Species B:{/t}
			<select name="idB" id="idB" class="taxon-select">	
				<option value="" {if !$taxonB}selected="selected"{/if}>{t}--choose species--{/t}</option>
				{foreach from=$taxa key=k item=v}
					<option value="{$v.id}" {if $taxonB.id==$v.id}selected="selected"{/if}>{$v.taxon}</option>
				{/foreach}
			</select>
			<br />	
			<input id="map_compare_button" type="button" value="compare" onclick="l2DoMapCompare()" />
			<br /><br />
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

l2SetMap('{$session.app.project.urls.project_media_l2_maps}{$map.image|replace:' ':'%20'}',{$map.size[0]},{$map.size[1]});
l2ScaleCells({math equation="(floor( x / y ))-z" x=$map.size[0] y=$map.cols z=1},{math equation="(floor( x / y ))-z" x=$map.size[1] y=$map.rows z=1});

{literal}
});
</script>
{/literal}

{include file="../shared/footer.tpl"}
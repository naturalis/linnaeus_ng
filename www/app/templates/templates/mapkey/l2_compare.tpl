{include file="../shared/header.tpl"}

<div id="page-main">
{assign var=map value=$maps[$mapId]}
<div id="page-main">

{if $map.mapExists}
<div>{$map.name} ({$map.coordinates.topLeft.lat}, {$map.coordinates.topLeft.long} x {$map.coordinates.bottomRight.lat}, {$map.coordinates.bottomRight.long}) <span id=coordinates></span></div>
{else}
<div>
{t _s1=$map.name}The image file for the map "%s" is missing.{/t}
</div>
{/if}
{if $map.mapExists}
<style>
{literal}
.mapCell,.mapCellA,.mapCellB,.mapCellAB,.mapCellLegend {
	width:{/literal}{math equation="(floor( x / y ))-z" x=$map.size[0] y=$map.cols z=1}{literal}px;
	height:{/literal}{math equation="(floor( x / y ))-z" x=$map.size[1] y=$map.rows z=1}{literal}px;
	padding:0px;
	margin:0px;
	border-right:1px dotted #777;
	border-bottom:1px dotted #777;
	filter: alpha(opacity=70);
	-moz-opacity: .70;
	-khtml-opacity: 0.70;
	opacity: .70;
}
.mapCellLegend {
	border:1px solid #000;
	width:7px;
	height:7px;
	padding:0px;
	margin:0px;
}

.mapCellA {
	background-color:#FF0000;
}

.mapCellB {
	background-color:#0000FF;
}

.mapCellAB {
	background-color:#FF00FF;
}

{/literal}
</style>
<table>
	<tr style="vertical-align:top">
		<td>


<table 
	style="
		background:url({$session.app.project.urls.project_media_l2_maps}{$map.image|replace:' ':'%20'});
		width:{$map.size[0]}px;
		height:{$map.size[1]}px;
		padding:0px;
		margin:0px;
		border-collapse:collapse;"
>
{assign var=cellNo value=1}
{section name=rows start=1 loop=$map.rows+1 step=1}
	<tr>
	{section name=cols start=1 loop=$map.cols+1 step=1}
		<td 
			id="cell-{$cellNo}"
			datatype="{$occurrences[$cellNo].type_id}" 
			class="mapCell{$overlap[$cellNo]}">
			</td>
		{assign var=cellNo value=$cellNo+1}
	{/section}
	</tr>
{/section}
</table>

<div id="map-overlay" style="width:{$map.size[0]}px;height:{$map.size[1]}px;position:relative;margin-top:-{$map.size[1]}px;"></div>

{/if}
</td>
<td style="padding-left:4px;">
		{t}Select two species to compare{/t}<br /><br />
		<span class="mapCellA" style="border:1px solid #666;margin-right:3px;">&nbsp;&nbsp;&nbsp;&nbsp;</span>{t}Species A:{/t}
		<select name="idA" id="idA" class="taxon-select">	
		<option value="" {if !$taxonA}selected="selected"{/if}>{t}--choose species--{/t}</option>
		{foreach from=$taxa key=k item=v}
		<option value="{$v.id}" {if $taxonA.id==$v.id}selected="selected"{/if}>{$v.taxon}</option>
		{/foreach}
		</select>
		&nbsp;&nbsp;&nbsp;&nbsp;</span>
		<br />
		<span class="mapCellB" style="border:1px solid #666;margin-right:3px;">&nbsp;&nbsp;&nbsp;&nbsp;</span>{t}Species B:{/t}
		<select name="idB" id="idB" class="taxon-select">	
		<option value="" {if !$taxonB}selected="selected"{/if}>{t}--choose species--{/t}</option>
		{foreach from=$taxa key=k item=v}
		<option value="{$v.id}" {if $taxonB.id==$v.id}selected="selected"{/if}>{$v.taxon}</option>
		{/foreach}
		</select>
		
		<br />	
		<input id="map_compare_button" type="button" value="compare" onclick="l2DoMapCompare()" />
	</p>
	<p style="margin:0px;">
	<span class="mapCellAB" style="border:1px solid #666;margin-right:3px;">&nbsp;&nbsp;&nbsp;&nbsp;</span>{t}Show overlap in:{/t}
	{foreach from=$geoDataTypes key=k item=v name=x}
		<p style="margin:1px 0px 0px -4px;">
			<label>
				<input 
					type="checkbox" 
					name="selectedDataTypes[{$v.id}]" 
					value="{$v.id}" 
					onchange="l2DataTypeToggle();"
					{if $selectedDataTypes=='*' || $selectedDataTypes[$v.id]==$v.id}checked="checked"{/if}
				/><!--span class="mapCellLegend" style="background-color:#{$v.colour};">&nbsp;&nbsp;&nbsp;&nbsp;</span-->&nbsp;{$v.title}</label>
		</p>
	{/foreach}
	</p>
</td>
</tr>
</table>	

<p>
{if $maps|@count>1}{t}Switch to another map:{/t}<br />{/if}
{if $maps|@count>1}
{foreach item=v from=$maps}{if $v.id!=$mapId}<a href="?id={$taxon.id}&m={$v.id}&idA={$taxonA.id}&idB={$taxonB.id}">{/if}{$v.name}{if $v.id!=$mapId}</a>{/if}<br />{/foreach}
{/if}
</p>






</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}

{foreach from=$occurrences key=k item=v}

{if $taxon}
{assign var=taxonName value=$taxon.taxon}
{else if $v.taxon.taxon}
{assign var=taxonName value=$v.taxon.taxon}
{/if}


{/foreach}

{literal}
});
</script>
{/literal}

</div>

{include file="../shared/footer.tpl"}

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
.mapCell,.mapCellLegend {
	width:{/literal}{math equation="(floor( x / y ))-z" x=$map.size[0] y=$map.cols z=1}{literal}px;
	height:{/literal}{math equation="(floor( x / y ))-z" x=$map.size[1] y=$map.rows z=1}{literal}px;
	padding:0px;
	margin:0px;
	border-right:1px dotted #777;
	border-bottom:1px dotted #777;
	filter: alpha(opacity=60);
	-moz-opacity: .60;
	-khtml-opacity: 0.60;
	opacity: .60;
}
.mapCellLegend {
	border:1px solid #000;
	width:7px;
	height:7px;
	padding:0px;
	margin:0px;
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
			class="mapCell" {if $occurrences[$cellNo].square_number==$cellNo}style="background-color:#{$occurrences[$cellNo].colour}"{/if}></td>
		{assign var=cellNo value=$cellNo+1}
	{/section}
	</tr>
{/section}
</table>

<div id="map-overlay" style="width:{$map.size[0]}px;height:{$map.size[1]}px;position:relative;margin-top:-{$map.size[1]}px;"></div>

{/if}
</td>
<td style="padding-left:4px;">
	<span style="margin-left:4px;font-weight:bold">{$taxon.taxon}</span><br />
	{foreach from=$geoDataTypes key=k item=v name=x}
		<p style="margin:0px;">
			<label><input type="checkbox" checked="checked" value="{$v.id}" onchange="l2ToggleDatatype(this)"/><span class="mapCellLegend" style="background-color:#{$v.colour};">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;{$v.title}</label>
		</p>
	{/foreach}
</td>
</tr>
</table>	

<p>
{if $maps|@count>1}{t}Switch to another map:{/t}<br />{/if}
{if $maps|@count>1}
{foreach item=v from=$maps}{if $v.id!=$mapId}<a href="?id={$taxon.id}&m={$v.id}">{/if}{$v.name}{if $v.id!=$mapId}</a>{/if}<br />{/foreach}
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

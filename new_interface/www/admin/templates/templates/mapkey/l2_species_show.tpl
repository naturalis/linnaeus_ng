{include file="../shared/admin-header.tpl"}
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
		background:url({$map.imageFullName|replace:' ':'%20'});
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
			lat="{math equation="((((a-b)/c) * d) + b)" a=$map.coordinates.bottomRight.lat b=$map.coordinates.topLeft.lat c=$map.rows d=$smarty.section.rows.index}"
			long="{math equation="((((a-b)/c) * d) + b)" a=$map.coordinates.bottomRight.long b=$map.coordinates.topLeft.long c=$map.cols d=$smarty.section.cols.index}"
			id="cell-{$cellNo}" 
			class="mapCell" {if $occurrences[$cellNo].square_number==$cellNo}style="background-color:#{$occurrences[$cellNo].colour}"{/if}></td>
		{assign var=cellNo value=$cellNo+1}
	{/section}
	</tr>
{/section}
</table>

<div id="map-overlay" style="width:{$map.size[0]}px;height:{$map.size[1]}px;position:relative;margin-top:-{$map.size[1]}px;"></div>

{/if}
</td>
<td>

	{foreach from=$geodataTypes key=k item=v name=x}
		<p style="margin:4px;">
			<span class="mapCellLegend" style="background-color:#{$v.colour};">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;{$v.title}
		</p>
	{/foreach}
	<input type="button" onclick="window.open('species_edit.php?id={$taxon.id}','_self')" value="{t}edit{/t}" style="margin:2px 0px 3px 4px"/>
</td>
</tr>
</table>	

<p>
{if $maps|@count>1}{t}Switch to another map:{/t}<br />{/if}
{if $maps|@count>1}
{foreach item=v from=$maps}{if $v.id!=$mapId}<a href="?id={$taxon.id}&mapId={$v.id}">{/if}{$v.name}{if $v.id!=$mapId}</a>{/if}, {/foreach}
{else}{t}Switch to{/t}{/if}
<a href="species_edit.php?id={$taxon.id}">{t}editable map{/t}</a>
</p>




</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){

	allLookupNavigateOverrideUrl('l2_species_show.php?id=%s&mapId={/literal}{$mapId}{literal}');


{/literal}

	var lon1 = {$map.coordinates.topLeft.long};
	var lon2 = {$map.coordinates.bottomRight.long};
	var lat1 = {$map.coordinates.topLeft.lat};
	var lat2 = {$map.coordinates.bottomRight.lat};
	var mapWPx = {$map.size[0]};
	var mapHPx = {$map.size[1]};

{literal}

	var mapW = (lon1 >= lon2 ? lon1 - lon2 : 360 + lon1 - lon2);
	var mapH = (lat1 - lat2);
	
	$('#coordinates').html(mapW+'x'+mapH);

	var offset = $('#map-overlay').offset();

	$('#map-overlay').mousemove(function(event){
		
		var x = event.pageX-offset.left;
		var y = event.pageY-offset.top;
		
		var mapWDeg = (((x / mapWPx) * mapW) + lon1);
		var mapHDeg = (((y / mapHPx) * mapH) + lat1);
		

		//$('#coordinates').html(mapWDeg+'x'+mapHDeg);
		$('#coordinates').html(mapWDeg+'x'+mapHDeg);

	});


{/literal}

var cellData = Array();
{foreach item=v from=$occurrences}
	cellData[{$v.square_number}] = ['{$v.coordinates}','{$v.legend}'];
{/foreach}

{literal}
});
</script>
{/literal}

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
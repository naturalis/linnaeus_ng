{include file="../shared/header.tpl"}

{assign var=map value=$maps[$mapId]}

<div id="page-main">

{if $map.mapExists}
	<div>{$map.name} (<span id=coordinates>0,0</span>) <span id=species-number></span></div>
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
					<td id="cell-{$cellNo}" row="{$smarty.section.rows.index}" col="{$smarty.section.cols.index}" class="mapCell{if $index.index[$cellNo]} mapCellDiversity mapCellDiversity{$index.index[$cellNo].class}{/if}{if $cellNo==$selectedCell} mapCellSelected{/if}" onmouseover="l2DiversityCellMouseOver({$index.index[$cellNo].total})" {if $index.index[$cellNo]}onclick="l2DiversityCellClick(this)"{/if}>{if $firstRowOrCol}x{/if}</td>
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
							{foreach item=v from=$maps}'{if $v.id!=$mapId}<a href=?id={$taxon.id}&m={$v.id}>{/if}{$v.name}{if $v.id!=$mapId}</a>{/if}<br />'+
							{/foreach}' '
						);">{t}Switch to another map{/t}</span>
				</p>
			{/if}
			<!-- /added ui dialog 2012.05.30 -->
		</td>
		<td style="padding-left:4px;">
		{foreach from=$geoDataTypes key=k item=v name=x}
			<p style="margin:0px;">
				<label>
					<input 
						type="checkbox" 
						name="selectedDatatypes[]" 
						{if $selectedDatatypes[$v.id] || !$selectedDatatypes}checked="checked"{/if}
						value="{$v.id}" 
						onchange="$('#theForm').submit();"/>
					<!-- span class="mapCellLegend" style="background-color:#{$v.colour};">&nbsp;&nbsp;&nbsp;&nbsp;</span>
					&nbsp; -->{$v.title}
				</label>
			</p>
		{/foreach}
		<input type="hidden" name="m" id="mapId" value="{$mapId}" />
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

<!-- added ui dialog 2012.05.30 -->
{if $taxa}
showDialog(
	_('Taxa in that cell'),
	'<p style="height:250px;overflow-y:scroll">'+
	{foreach from=$taxa item=v}'<a href="l2_examine_species.php?id={$v.id}&m={$mapId}&ref=diversity">{$v.taxon|escape:'htmlall'}</a><br />'+
	{/foreach}'</p>'
);
{/if}
<!-- /added ui dialog 2012.05.30 -->

{/if}
{literal}
$("#mapTable").mousemove(function(event) {
	l2MapMouseOver(event.pageX,event.pageY);
});	

});
</script>
{/literal}

{include file="../shared/footer.tpl"}


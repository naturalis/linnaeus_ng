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
			{section name=rows start=1 loop=$map.rows step=1}
				<tr>
				{section name=cols start=1 loop=$map.cols step=1}
					<td 
						id="cell-{$cellNo}"
						class="mapCell {if $selectedCells[$cellNo]==true}mapCellTagged{/if}"
						onclick="l2TagMapCell(this)">
					</td>
					{assign var=cellNo value=$cellNo+1}
				{/section}
				</tr>
			{/section}
			</table>
			{/if}
			<p>
			{if $maps|@count>1}{t}Switch to another map:{/t}<br />{/if}
			{if $maps|@count>1}
			{foreach item=v from=$maps}{if $v.id!=$mapId}<a href="?mapId={$v.id}">{/if}{$v.name}{if $v.id!=$mapId}</a>{/if}<br />{/foreach}
			{/if}
			</p>	
		</td>
		<td style="padding-left:4px;">
			{t}Select the area you want to search by clicking the relevant squares.{/t}<br />
			{t}When finished, click 'search'.{/t}<br />
			<input type="button" value="{t}clear{/t}" onclick="l2DoClearSearch()" />&nbsp;
			<input type="button" value="{t}search{/t}" onclick="l2DoSearchMap()" />
			<input type="hidden" name="mapId" value="{$mapId}" />
			{if $taxa}
			<br /><br />
			{t}Found taxa:{/t}
			<p id="mapTaxaBox">
			{foreach from=$taxa item=v}
			<a href="l2_examine_species.php?id={$v.id}&m={$mapId}&ref=search">{$v.taxon}</a><br />
			{/foreach}
			</p>
			{/if}
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

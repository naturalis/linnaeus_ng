{include file="../shared/header.tpl"}

<div id="header-titles"></div>
<div id="categories">
<ul>
<li>
    <a class="category{if $taxonType=='lower'}-active{/if} category-first" 
    href="examine_species.php">
    {t}Examine{/t}</a>
</li>
<li>
    <a class="category{if $taxonType=='higher'}-active{/if}" 
    href="compare.php">
    {t}Compare{/t}</a>
</li>
<li>
    <a class="category{if $taxonType=='higher'}-active{/if}" 
    href="search.php">
    {t}Search{/t}</a>
</li>
<li>
    <a class="category{if $taxonType=='common'}-active{/if} category-last" 
    href="diversity.php">
    {t}Diversity index{/t}</a>
</li>
</ul>
</div>


{include file="../shared/_search-main.tpl"}

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
					<td 
						id="cell-{$cellNo}"
						datatype="{$occurrences[$cellNo].type_id}" 
						class="mapCell" {if $occurrences[$cellNo].square_number==$cellNo}style="background-color:#{$occurrences[$cellNo].colour}"{/if}></td>
					{assign var=cellNo value=$cellNo+1}
				{/section}
				</tr>
			{/section}
			</table>
		{/if}
			<p>
			{if $maps|@count>1}{t}Switch to another map:{/t}<br />{/if}
			{if $maps|@count>1}
			{foreach item=v from=$maps}{if $v.id!=$mapId}<a href="?id={$taxon.id}&m={$v.id}">{/if}{$v.name}{if $v.id!=$mapId}</a>{/if}<br />{/foreach}
			{/if}
			</p>
		</td>
		<td style="padding-left:4px;">
			<span id="mapTaxonName">{$taxon.taxon}</span><br />
			{foreach from=$geoDataTypes key=k item=v name=x}
			<p style="margin:0px;">
				<label>
					<input type="checkbox" checked="checked" value="{$v.id}" onchange="l2ToggleDatatype(this)"/>
					<span class="mapCellLegend" style="background-color:#{$v.colour};">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;{$v.title}
				</label>
			</p>
			{/foreach}
			{if $session.app.user.map.search.taxa}<p><a href="l2_search.php?action=research">{t}Back to search results.{/t}</a></p>{/if}
			{if $session.app.user.map.index}<p><a href="l2_diversity.php?action=reindex">{t}Back to diversity index.{/t}</a></p>{/if}
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
{/literal}

{literal}
});
</script>
{/literal}

{include file="../shared/footer.tpl"}

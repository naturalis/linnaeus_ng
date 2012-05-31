{include file="../shared/header.tpl"}
{assign var=currentPage value=$session.app.system.path.filename}
<div id="header-titles"></div>
{include file="_categories.tpl"}
{include file="../shared/_search-main.tpl"}


{assign var=map value=$maps[$mapId]}

<div id="page-main">

{if !$map.mapExists}
    <div>{t _s1=$map.name}The image file for the map "%s" is missing.{/t}</div>
{else}

<table id="mapGrid">
    <tr id="topBar">
    <td>
            <input type="button" value="{t}Search{/t}" onclick="l2DoSearchMap()" />
            <input type="button" value="{t}Clear map{/t}" onclick="l2DoClearSearch()" />&nbsp;
            <input type="hidden" name="mapId" value="{$mapId}" />
        <span id="coordinates">0,0</span>
    </td><td id="mapName">
        {$map.name}
    </td>
    </tr>
    <tr>
    <td id="gridMapCell" class="searchMap">    
		{if $map.mapExists}
			<table id="mapTable">
			{assign var=cellNo value=1}
			{section name=rows start=1 loop=$map.rows+1 step=1}
				<tr>
				{section name=cols start=1 loop=$map.cols+1 step=1}
					<td 
						id="cell-{$cellNo}"
						{if $selectedCells[$cellNo]==true}mapCellTagged{/if}"
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
		<td id="legend">
			<p>{t}Select the area you want to search by clicking the relevant squares.{/t}</p>
			<p>{t}When finished, click 'Search'.{/t}</p>
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

{/if}

{include file="_mapJquery.tpl"}
{include file="../shared/footer.tpl"}

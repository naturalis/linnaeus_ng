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
        <span id="taxonName">{$taxon.taxon}</span> 
        <span id="coordinates">0,0</span>
    </td><td id="mapName">
        {$map.name}
	</td>
	</tr>
	<tr>
	<td id="gridMapCell">	
		{if $map.mapExists}
			<table id="mapTable">
			{assign var=cellNo value=1}
			{section name=rows start=1 loop=$map.rows+1 step=1}
				<tr>
				{section name=cols start=1 loop=$map.cols+1 step=1}
					<td 
						id="cell-{$cellNo}"
						{if $occurrences[$cellNo].type_id}datatype="{$occurrences[$cellNo].type_id}"{/if}
						{if $occurrences[$cellNo].square_number==$cellNo}style="background-color:#{$occurrences[$cellNo].colour}"{/if}></td>
					{assign var=cellNo value=$cellNo+1}
				{/section}
				</tr>
			{/section}
			</table>
		{/if}
		</td>
		<td id="legend">
			{foreach from=$geoDataTypes key=k item=v name=x}
			<p>
				<label>
					<input type="checkbox" checked="checked" value="{$v.id}" onchange="l2ToggleDatatype(this)"/>
					<span class="mapCellLegend" style="background-color:#{$v.colour};">&nbsp;&nbsp;&nbsp;&nbsp;</span>{$v.title}
				</label>
			</p>
			{/foreach}
			
            <p>
            {if $maps|@count>1}{t}Switch to another map:{/t}<br />{/if}
            {if $maps|@count>1}
            {foreach item=v from=$maps}{if $v.id!=$mapId}<a href="?id={$taxon.id}&m={$v.id}">{/if}{$v.name}{if $v.id!=$mapId}</a>{/if}<br />{/foreach}
            {/if}
            </p>
		</td>
	</tr>
</table>
</div>

{/if}


{include file="_mapJquery.tpl"}
{include file="../shared/footer.tpl"}

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
        {if $maps|@count>1}
            <span class="selectIcon" onclick="
                showDialog('{t}Choose a map{/t}',
                    {foreach item=v from=$maps}'{if $v.id!=$mapId}<a href=?id={$taxon.id}&m={$v.id}>{/if}{$v.name}{if $v.id!=$mapId}</a>{/if}<br />'+
                    {/foreach}' '
                );">{$map.name}</span>
        {else}
            {$map.name}
        {/if}   
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
						class="{if $index.index[$cellNo]}mapCellDiversity mapCellDiversity{$index.index[$cellNo].class}{/if}{if $cellNo==$selectedCell} mapCellSelected{/if}"
						{if $index.index[$cellNo]}onclick="l2DiversityCellClick(this)"{/if}></td>
					{assign var=cellNo value=$cellNo+1}
				{/section}
				</tr>
			{/section}
			</table>
			{/if}
		</td>
		<td id="legendCell">
		<div id="legend">
		{foreach from=$geoDataTypes key=k item=v name=x}
			<p class="mapCheckbox">
				<label>
					<input 
						type="checkbox" 
						name="selectedDatatypes[]" 
						{if $selectedDatatypes[$v.id] || !$selectedDatatypes}checked="checked"{/if}
						value="{$v.id}" 
						onchange="$('#theForm').submit();"/>
					{$v.title}
				</label>
			</p>
		{/foreach}
		<input type="hidden" name="m" value="{$mapId}" />
		</div>
		</td>
	</tr>
</table>
</div>
{/if}

{include file="_mapJquery.tpl"}
{include file="../shared/footer.tpl"}

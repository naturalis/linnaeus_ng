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
    <tr id="grid-header">
    <td>
     </td>
    <td id="push"></td>
    <td id="mapName">
        {if $maps|@count>1}
            <span class="selectIcon" title="{t}Select a different map{/t}" onclick="
                showDialog('{t}Choose a map{/t}',
                '<div id=\'lookup-DialogContent\'>'+
                    {foreach item=v from=$maps}'<p class=\'row{if $v.id==$mapId} row-selected{/if}\'><a href=?id={$taxon.id}&m={$v.id}>{$v.name|escape:'htmlall'}{if $v.id!=$mapId}</a>{/if}</p>'+
                    {/foreach}' ' + '</div>',
                    false, true

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
					<td id="cell-{$cellNo}" row="{$smarty.section.rows.index}" col="{$smarty.section.cols.index}" 
						{if $index.index[$cellNo]}class="mapCellDiversity{$index.index[$cellNo].class}{if $cellNo==$selectedCell} mapCellSelected{/if}"{/if}
						onmouseover="l2DiversityCellMouseOver(this)"
						total="{$index.index[$cellNo].total}"
						{if $index.index[$cellNo]}
							onclick="l2DiversityCellClick(this)"
						{/if}></td>
					{assign var=cellNo value=$cellNo+1}
				{/section}
				</tr>
			{/section}
			</table>
			{/if}
		</td>
		<td></td>
		<td id="legendCell">
		<div id="types">
		{foreach from=$geoDataTypes key=k item=v name=x}
			<div class="mapCheckbox">
				<label>
					<input 
						type="checkbox" 
						name="selectedDatatypes[]" 
						{if $selectedDatatypes[$v.id] || !$selectedDatatypes}checked="checked"{/if}
						value="{$v.id}" 
						onchange="l2DiversityTypeClick(this)"/>
					{$v.title}
				</label>
			</div>
		{/foreach}
		<input type="hidden" name="m" id="mapId" value="{$mapId}" />
		</div>
		

		<div id="legend">
			{foreach from=$index.legend key=k item=v name=x}
			<div class="mapCheckbox">
				<label>
					<span class="opacity"><span class="mapCellLegend mapCellDiversity{$k}">&nbsp;&nbsp;&nbsp;&nbsp;</span></span>{$v.min}-{$v.max} {t}records{/t}
				</label>
			</div>
			{/foreach}
		</div>

		
		</td>
	</tr>
	<tr id="grid-footer">
		<td>
			<span id="taxonName">{$taxon.taxon}</span> 
       		<span id="coordinates"></span> <span id=species-number></span>
		</td>
		<td></td>
		<td></td>
	</tr>
</table>

</div>
{/if}

{include file="_mapJquery-start.tpl"}
{if $taxa}
allLookupNavigateOverrideDialogTitle('Taxa in that square');
allLookupShowDialog('{$taxa}');
{/if}
{include file="_mapJquery-end.tpl"}
{include file="../shared/footer.tpl"}

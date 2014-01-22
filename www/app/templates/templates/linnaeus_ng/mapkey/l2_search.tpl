{include file="../shared/header.tpl"}
{assign var=currentPage value=$session.app.system.path.filename}
<div id="header-titles">
    <span id="header-title">
        {t}Distribution{/t}
    </span>
</div>
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
            <input type="button" value="{t}Search{/t} &gt;&gt" onclick="l2DoSearchMap()" />&nbsp;
            <input type="button" value="{t}Clear map{/t}" onclick="l2DoClearSearch()" />
            <input type="hidden" name="mapId" value="{$mapId}" />
        
    </td>
    <td id="push"></td>
    <td id="mapName">

        {if $maps|@count>1}
            <span class="selectIcon" title="{t}Select a different map{/t}" onclick="
                  showDialog('{t}Choose a map{/t}',
                '<div id=\'lookup-DialogContent\'>'+
                    {foreach item=v from=$maps}'<p class=\'row{if $v.id==$mapId} row-selected{/if}\'><a href=?mapId={$v.id}>{$v.name|escape:'htmlall'}{if $v.id!=$mapId}</a>{/if}</p>'+
                    {/foreach}' ' + '</div>',
                    false, true
                 );">{$map.name}</span>
        {else}
            {$map.name}
        {/if}   
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
						{if $selectedCells[$cellNo]==true}class="mapCellTagged"{/if}
						onclick="l2TagMapCell(this)">
					</td>
					{assign var=cellNo value=$cellNo+1}
				{/section}
				</tr>
			{/section}
			</table>
			{/if}
		</td>
		<td></td>
		<td id="legendCell">
		<div id="legend">
            {foreach from=$geoDataTypes key=k item=v name=x}
            <div class="mapPCheckbox">
                <label>
                    <input type="checkbox" 
                        name="dataTypes[]" 
                        {if $selectedDataTypes[$v.id]==true || $didSearch==false}checked="checked"{/if}  value="{$v.id}" />
                    {$v.title}
                </label>
            </div>
            {/foreach}
		</div>
            <p style="margin-bottom: 8px;">{t}Select the area you want to search by clicking the relevant squares.{/t}</p>
            <p>{t}When finished, click 'Search'.{/t}</p>
		</td>
	</tr>
	<tr id="grid-footer">
		<td><span id="coordinates"></span></td>
		<td></td>
		<td></td>
	</tr>
</table>
</div>

{/if}

{include file="_mapJquery-start.tpl"}
{if $didSearch==true}

allLookupNavigateOverrideDialogTitle('Found {$numOfTaxa} taxa');
allLookupShowDialog('{$taxa}');

{/if}
{include file="_mapJquery-end.tpl"}
{include file="../shared/footer.tpl"}

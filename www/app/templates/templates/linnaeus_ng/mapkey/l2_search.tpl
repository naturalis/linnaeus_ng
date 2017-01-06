{assign var=currentPage value=$session.app.system.path.filename}
{include file="../shared/header.tpl" title="Distribution" subtitle="Please note, the distribution module is being phased out"}

{assign var=map value=$maps[$mapId]}

<div id="page-main">
    <div class="map-page">
        {include file="_categories.tpl"}
        {if !$map.mapExists}
            <div>{t _s1=$map.name}The image file for the map "%s" is missing.{/t}</div>
        {else}
        <form method="post" id="theForm" action="">
            <div class="map-legend-container">
                <div class="map">
                    <div id="mapName">
                       {if $maps|@count>1}
                            <select id="map-select">
                                {foreach item=v from=$maps}
                                <option {if $v.id==$mapId}selected="selected"{/if} value="?id={$taxon.id}&m={$v.id}">
                                    {$v.name|escape:'htmlall'}
                                </option>
                                {/foreach}
                            </select>
                        {else}
                            {$map.name}
                        {/if}
                        <div id="coordinates"></div>
                    </div> 
                    {if $map.mapExists}
                        <div class="map-padding">
                            <div class="mapContainer">
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
                            </div>
                        </div>
                    {/if}
                    <div id="coordinates"></div>
                </div>
                <div class="legend-container">
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
                    <div class="map_controls">
                        <input type="button" value="{t}Search{/t}" onclick="l2DoSearchMap()" />&nbsp;
                        <input type="button" value="{t}Clear map{/t}" onclick="l2DoClearSearch()" />
                        <input type="hidden" name="mapId" value="{$mapId}" />
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript" src="{$baseUrl}app/javascript/map.js"></script>
{/if}

{include file="_mapJquery-start.tpl"}
{if $didSearch==true}

allLookupNavigateOverrideDialogTitle('Found {$numOfTaxa} taxa');
allLookupShowDialog('{$taxa}');

{/if}
{include file="_mapJquery-end.tpl"}
{include file="../shared/footer.tpl"}

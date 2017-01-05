{assign var=currentPage value=$session.app.system.path.filename}
{include file="../shared/header.tpl" title="Distribution"}


{assign var=map value=$maps[$mapId]}

<div id="page-main">
    <div>
        {include file="_categories.tpl"}

        {if !$map.mapExists}
            <div>{t _s1=$map.name}The image file for the map "%s" is missing.{/t}</div>
        {else}
        <form method="post" id="theForm" action="">
            <div class="map-legend-container">
                <div class="map">
                    <div id="mapName">
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
                    </div>
                    {if $map.mapExists}
                        <table id="mapTable">
                        {assign var=cellNo value=1}
                        {section name=rows start=1 loop=$map.rows+1 step=1}
                            <tr>
                            {section name=cols start=1 loop=$map.cols+1 step=1}
                                <td id="cell-{$cellNo}" datatype="{$occurrences[$cellNo].type_id}" class="mapCell mapCell{$overlap[$cellNo]}"></td>
                                {assign var=cellNo value=$cellNo+1}
                            {/section}
                            </tr>
                        {/section}
                        </table>
                    {/if}
                    <div id="coordinates"></div>
                </div>
                <div class="legend-container">
                    <div id="legend">
                        {foreach from=$geoDataTypes key=k item=v name=x}
                            <div class="mapPCheckbox">
                                <label class="checkbox-input">
                                    <input 
                                        type="checkbox" 
                                        name="selectedDataTypes[{$v.id}]" 
                                        value="{$v.id}" 
                                        onchange="l2DataTypeToggle();"
                                        {if $selectedDataTypes=='*' || $selectedDataTypes[$v.id]==$v.id}checked="checked"{/if}/>
                                    <span class="mock-checkbox"></span>
                                    {$v.title}
                                </label>
                            </div>
                        {/foreach}
                        <p>
                            <span class="mapCellA mapCellLegend">&nbsp;&nbsp;&nbsp;&nbsp;</span>
                            <span 
                                id="speciesNameA" 
                                onclick="
                                    allLookupSetExtraVar( { name: 'l2_must_have_geo', value: 1 } );
                                    allLookupNavigateOverrideUrl('javascript:l2SetCompareSpecies(1,%s);');
                                    allLookupShowDialog()
                                " 
                                class="selectIcon{if $taxonA} italics">{$taxonA.taxon}</span>{else}">{t}Select...{/t}{/if}</span>
                        </p>
                        
                        <p>
                            <span class="mapCellB mapCellLegend">&nbsp;&nbsp;&nbsp;&nbsp;</span>
                            <span id="speciesNameB" onclick="
                                allLookupSetExtraVar( { name: 'l2_must_have_geo', value: 1 } );
                                allLookupNavigateOverrideUrl('javascript:l2SetCompareSpecies(2,%s);');
                                allLookupShowDialog()
                            " 
                            class="selectIcon{if $taxonB} italics">{$taxonB.taxon}</span>{else}">{t}Select...{/t}{/if}</span>
                        </p>
                        
                        <input type="hidden" name="idA" id="idA" value="{if $taxonA}{$taxonA.id}{/if}" />
                        
                        <input type="hidden" name="idB" id="idB" value="{if $taxonB}{$taxonB.id}{/if}" />
                        
                        <p><span class="mapCellAB mapCellLegend">&nbsp;&nbsp;&nbsp;&nbsp;</span>{t}Displays overlap between two taxa.{/t}</p>
                        
                        <div class="map_controls">
                            <input id="map_compare_button" type="button" value="{t}Compare{/t}" onclick="l2DoMapCompare()" />
                        </div>
                    </div>

                    
                </div>
            </div>
            
        </form>

    </div>
</div>
{/if}

{include file="_mapJquery-start.tpl"}
{include file="_mapJquery-end.tpl"}
{include file="../shared/footer.tpl"}
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
            <input type="hidden" name="idA" id="idA" value="{if $taxonA}{$taxonA.id}{/if}" />
            <input type="hidden" name="idB" id="idB" value="{if $taxonB}{$taxonB.id}{/if}" />

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
                                        <td id="cell-{$cellNo}" datatype="{$occurrences[$cellNo].type_id}" class="mapCell mapCell{$overlap[$cellNo]}"></td>
                                        {assign var=cellNo value=$cellNo+1}
                                    {/section}
                                    </tr>
                                {/section}
                                </table>
                            </div>
                        </div>
                    {/if}
                </div>
                <div class="legend-container">
                    <div id="legend">
						{foreach from=$geoDataTypes key=k item=v name=x}
                        
                        {$checked = $selectedDataTypes=='*' || $selectedDataTypes[$v.id]==$v.id}
                        
						<div class="mapCheckbox">
	                        <input 
                            	type="checkbox" 
								name="selectedDataTypes[{$v.id}]" 
                                value="{$v.id}" 
                                id="legend-toggle-{$v.id}" 
                                onchange="l2ToggleDatatype(this)" 
                                {if $checked}checked="checked"{/if}/>
                            <span class="clickzone" onclick="$('#legend-toggle-{$v.id}').trigger('click');$('.legend-{$v.id}').toggle();">
                                <span class="icon legend-{$v.id}" style="display:{if $checked}none{else}inline{/if};">&#9744;</span>
                                <span class="icon legend-{$v.id}" style="display:{if $checked}inline{else}none{/if};">&#9745;</span>
                                {$v.title}
                            </span>
                        </div>
						{/foreach}

                        <p>
                            <span class="mapCellA mapCellLegend">&nbsp;&nbsp;&nbsp;&nbsp;</span>
                            <span id="speciesNameA" onclick="l2TaxonSelection(1)" class="selectIcon{if $taxonA} italics">{$taxonA.taxon}</span>{else}">{t}Select...{/t}{/if}</span>
                        </p>
                        
                        <p>
                            <span class="mapCellB mapCellLegend">&nbsp;&nbsp;&nbsp;&nbsp;</span>
                            <span id="speciesNameB" onclick="l2TaxonSelection(2)" class="selectIcon{if $taxonB} italics">{$taxonB.taxon}</span>{else}">{t}Select...{/t}{/if}</span>
                        </p>
                        
                        <p>
                        	<span class="mapCellAB mapCellLegend">&nbsp;&nbsp;&nbsp;&nbsp;</span>{t}Displays overlap between two taxa.{/t}
						</p>
                        
                        <div class="map_controls">
                            <input id="map_compare_button" type="submit" value="{t}Compare{/t}" />
                        </div>

						<p>
							<a id="toggleGrid" href="#" onclick="l2ToggleGrid(this);"><span style="display:block">{t}Hide grid{/t}</span><span style="display:none">{t}Show grid{/t}</span></a>
				        </p>
                        
                    </div>
                </div>
            </div>
            
        </form>
        {/if}
    </div>
</div>
<script type="text/javascript" src="{$baseUrl}app/javascript/map.js"></script>

{include file="_mapJquery-start.tpl"}
{include file="_mapJquery-end.tpl"}
{include file="../shared/footer.tpl"}
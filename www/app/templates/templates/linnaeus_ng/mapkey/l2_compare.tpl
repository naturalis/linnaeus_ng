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
        <td colspan="2">
                <span class="mapCellA mapCellLegend">&nbsp;&nbsp;&nbsp;&nbsp;</span><span id="speciesNameA" onclick="
                    allLookupSetExtraVars('l2_must_have_geo','1');
                    allLookupNavigateOverrideUrl('javascript:l2SetCompareSpecies(1,%s);');
                    allLookupShowDialog()
                " class="selectIcon{if $taxonA} italics">{$taxonA.taxon}</span>{else}">{t}Select...{/t}{/if}</span>
                <span class="mapCellB mapCellLegend">&nbsp;&nbsp;&nbsp;&nbsp;</span><span id="speciesNameB" onclick="
                    allLookupSetExtraVars('l2_must_have_geo','1');
                    allLookupNavigateOverrideUrl('javascript:l2SetCompareSpecies(2,%s);');
                    allLookupShowDialog()
                " class="selectIcon{if $taxonB} italics">{$taxonB.taxon}</span>{else}">{t}Select...{/t}{/if}</span>
                <input type="hidden" name="idA" id="idA" value="{if $taxonA}{$taxonA.id}{/if}" />
                <input type="hidden" name="idB" id="idB" value="{if $taxonB}{$taxonB.id}{/if}" />
                <input id="map_compare_button" type="button" value="{t}Compare{/t}" onclick="l2DoMapCompare()" />
            

          
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
    					<td id="cell-{$cellNo}" datatype="{$occurrences[$cellNo].type_id}" class="mapCell mapCell{$overlap[$cellNo]}"></td>
    					{assign var=cellNo value=$cellNo+1}
    				{/section}
    				</tr>
    			{/section}
    			</table>
    		{/if}

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

		</td>

		<td id="legendCell">
		    <div id="legend">
			{foreach from=$geoDataTypes key=k item=v name=x}
				<div class="mapPCheckbox">
					<label>
						<input 
							type="checkbox" 
							name="selectedDataTypes[{$v.id}]" 
							value="{$v.id}" 
							onchange="l2DataTypeToggle();"
							{if $selectedDataTypes=='*' || $selectedDataTypes[$v.id]==$v.id}checked="checked"{/if}/>
						{$v.title}
					</label>
				</div>
			{/foreach}
            </div>
            <p><span class="mapCellAB mapCellLegend">&nbsp;&nbsp;&nbsp;&nbsp;</span>{t}Displays overlap between two taxa.{/t}</p>
		</td>
	</tr>
	<tr id="grid-footer">
		<td><span id="coordinates"></span></td>
		<td></td>
	</tr>
</table>
</div>
{/if}

{include file="_mapJquery-start.tpl"}
{include file="_mapJquery-end.tpl"}
{include file="../shared/footer.tpl"}
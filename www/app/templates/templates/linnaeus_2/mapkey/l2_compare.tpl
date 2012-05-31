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
            <span class="mapCellA mapCellLegend">&nbsp;&nbsp;&nbsp;&nbsp;</span>
            <span id="speciesNameA" class="selectIcon" onclick="
                allLookupSetExtraVars('l2_must_have_geo','1');
                allLookupNavigateOverrideUrl('javascript:l2SetCompareSpecies(1,%s);');
                allLookupShowDialog()
            ">{if $taxonA}{$taxonA.taxon}{else}{t}Species A{/t}{/if}</span>
             <span class="mapCellB mapCellLegend">&nbsp;&nbsp;&nbsp;&nbsp;</span>
            <span id="speciesNameB" class="selectIcon" onclick="
                allLookupSetExtraVars('l2_must_have_geo','1');
                allLookupNavigateOverrideUrl('javascript:l2SetCompareSpecies(2,%s);');
                allLookupShowDialog()
            ">{if $taxonB}{$taxonB.taxon}{else}{t}Species B{/t}{/if}</span>
            <input type="hidden" name="idA" id="idA" value="{if $taxonA}{$taxonA.id}{/if}" />
            <input type="hidden" name="idB" id="idB" value="{if $taxonB}{$taxonB.id}{/if}" />
            <input id="map_compare_button" type="button" value="{t}Compare{/t}" onclick="l2DoMapCompare()" />
        

        <span id="coordinates">0,0</span>
    </td><td id="mapName">

        {if $maps|@count>1}
            <span class="selectIcon" onclick="
                showDialog('{t}Choose a map{/t}',
                    {foreach item=v from=$maps}'{if $v.id!=$mapId}<a href=?id={$taxon.id}&m={$v.id}&idA={$taxonA.id}&idB={$taxonB.id}>{/if}{$v.name|escape:'htmlall'}{if $v.id!=$mapId}</a>{/if}<br />'+
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
					<td id="cell-{$cellNo}" datatype="{$occurrences[$cellNo].type_id}" class="mapCell mapCell{$overlap[$cellNo]}"></td>
					{assign var=cellNo value=$cellNo+1}
				{/section}
				</tr>
			{/section}
			</table>
		{/if}
		</td>
		<td id="legendCell">
			<p class="legendLabel"><span class="mapCellAB mapCellLegend">&nbsp;&nbsp;&nbsp;&nbsp;</span>{t}Show overlap in:{/t}</p>
			{foreach from=$geoDataTypes key=k item=v name=x}
				<p class="mapPCheckbox">
					<label>
						<input 
							type="checkbox" 
							name="selectedDataTypes[{$v.id}]" 
							value="{$v.id}" 
							onchange="l2DataTypeToggle();"
							{if $selectedDataTypes=='*' || $selectedDataTypes[$v.id]==$v.id}checked="checked"{/if}/>
						{$v.title}
					</label>
				</p>
			{/foreach}
			</p>

		</td>
	</tr>
</table>
</div>
{/if}

{include file="_mapJquery.tpl"}
{include file="../shared/footer.tpl"}
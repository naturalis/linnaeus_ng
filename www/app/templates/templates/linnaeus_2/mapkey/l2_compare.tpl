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
            <select name="idA" id="idA" class="taxon-select">   
                <option value="" {if !$taxonA}selected="selected"{/if}>{t}Species A{/t}</option>
                {foreach from=$taxa key=k item=v}
                    <option value="{$v.id}" {if $taxonA.id==$v.id}selected="selected"{/if}>{$v.taxon}</option>
                {/foreach}
            </select>
            </span>
          
            <span class="mapCellB mapCellLegend">&nbsp;&nbsp;&nbsp;&nbsp;</span>
            <select name="idB" id="idB" class="taxon-select">   
                <option value="" {if !$taxonB}selected="selected"{/if}>{t}Species B{/t}</option>
                {foreach from=$taxa key=k item=v}
                    <option value="{$v.id}" {if $taxonB.id==$v.id}selected="selected"{/if}>{$v.taxon}</option>
                {/foreach}
            </select>
             
            <input id="map_compare_button" type="button" value="{t}Compare{/t}" onclick="l2DoMapCompare()" />
            

        <span id="coordinates">0,0</span>
    </td><td id="mapName">
        {$map.name}
    </td>
    </tr>
    <tr>
    <td>    
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
		<td id="legend">
			<p>
			<span class="mapCellAB mapCellLegend">&nbsp;&nbsp;&nbsp;&nbsp;</span>{t}Show overlap in:{/t}
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

            <p>
            {if $maps|@count>1}{t}Switch to another map:{/t}<br />{/if}
            {if $maps|@count>1}
            {foreach item=v from=$maps}{if $v.id!=$mapId}<a href="?id={$taxon.id}&m={$v.id}&idA={$taxonA.id}&idB={$taxonB.id}">{/if}{$v.name}{if $v.id!=$mapId}</a>{/if}<br />{/foreach}
            {/if}
            </p>        
		</td>
	</tr>
</table>
</div>
{/if}

{include file="_mapJquery.tpl"}
{include file="../shared/footer.tpl"}
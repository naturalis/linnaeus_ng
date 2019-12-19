{assign var=currentPage value=$session.app.system.path.filename}
{include file="../shared/header.tpl" title="Distribution" subtitle="Please note, the distribution module is being phased out"}



{assign var=map value=$maps[$mapId]}

<div id="page-main">
	<div class="map-page">
		{include file="_categories.tpl"}
		{if !$map.mapExists}
		    <div>{t _s1=$map.name}The image file for the map "%s" is missing.{/t}</div>
		{else}
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
						</div>
					</div>
				{/if}
				
            </div>
            <div class="legend-container">

				<div id="legend">
					<!-- div id="types">
						{foreach from=$geoDataTypes key=k item=v name=x}
							<div class="mapPCheckbox">
								<label class="checkbox-input">
									<input 
										type="checkbox" 
										name="selectedDatatypes[]" 
										{if $selectedDatatypes[$v.id] || !$selectedDatatypes}checked="checked"{/if}
										value="{$v.id}" 
										onchange="l2DiversityTypeClick(this)"/>
									<span class="mock-checkbox"></span>
									{$v.title}
								</label>
							</div>
						{/foreach}
						<input type="hidden" name="m" id="mapId" value="{$mapId}" />
					</div -->
					{foreach from=$index.legend key=k item=v name=x}
                    <div class="mapCheckbox">
                        <span class="opacity"><span class="mapCellLegend mapCellDiversity{$k}">&nbsp;&nbsp;&nbsp;&nbsp;</span></span>{$v.min}-{$v.max} {t}records{/t}
                    </div>
					{/foreach}

                    <p>
                        <a id="toggleGrid" href="#" onclick="l2ToggleGrid(this);"><span style="display:block">{t}Hide grid{/t}</span><span style="display:none">{t}Show grid{/t}</span></a>
                    </p>
                    
				</div>
            </div>
        </div>
	</div>
</div>
{/if}
<script type="text/javascript" src="{$baseUrl}app/javascript/map.js"></script>
{include file="_mapJquery-start.tpl"}
{if $taxa}
allLookupNavigateOverrideDialogTitle('Taxa in that square');
allLookupShowDialog('{$taxa}');
{/if}
{include file="_mapJquery-end.tpl"}
{include file="../shared/footer.tpl"}

{assign var=currentPage value=$session.app.system.path.filename}
{include file="../shared/header.tpl" title="Distribution" subtitle="Please note, the distribution module is being phased out"}
{assign var=map value=$maps[$mapId]}

<div id="page-main">
	<div class="map-page">
		{include file="_categories.tpl"}
		{if !$map.mapExists}
		    <div>{t _s1=$map.name}The image file for the map "%s" is missing.{/t}</div>
		{else}
			<a id="taxonName" href="../species/taxon.php?id={$taxon.id}"
			        	title="{t}Go to this taxon{/t}">
			        {$taxon.taxon}</a>
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
											{if $occurrences[$cellNo].type_id}datatype="{$occurrences[$cellNo].type_id}"{/if}
											{if $occurrences[$cellNo].square_number==$cellNo}style="background-color:#{$occurrences[$cellNo].colour}"{/if}
					                        ></td>
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
						<div class="mapCheckbox">
	                        <input type="checkbox" checked="checked" value="{$v.id}" id="legend-toggle-{$v.id}" onchange="l2ToggleDatatype(this)" />
                            <span class="clickzone" onclick="$('#legend-toggle-{$v.id}').trigger('click');$('.legend-{$v.id}').toggle();">
                                <span class="icon legend-{$v.id}" style="color:#{$v.colour};display:none;">&#9744;</span>
                                <span class="icon legend-{$v.id}" style="color:#{$v.colour};display:inline;">&#9745;</span>
                                {$v.title}
                            </span>
                        </div>
						{/foreach}
						<p>
							<a id="toggleGrid" href="#" onclick="l2ToggleGrid(this);"><span style="display:block">{t}Hide grid{/t}</span><span style="display:none">{t}Show grid{/t}</span></a>
				        </p>
					</div>
				</div>
            </div>
	   {/if}
	</div>
</div>

<script type="text/javascript" src="{$baseUrl}app/javascript/map.js"></script>

{include file="_mapJquery-start.tpl"}
allLookupShowDialog('{$taxa}');
{include file="_mapJquery-end.tpl"}
{include file="../shared/footer.tpl"}

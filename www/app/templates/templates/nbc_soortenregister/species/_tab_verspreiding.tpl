		<div>
			<h2>Voorkomen</h2>
			
			<p>
				<table>
					{if $presenceData.presence_label}<tr><td>Status</td><td>{$presenceData.presence_label}{if $presenceData.presence_information} (<span class="link" 
	onmouseover="hint(this,'<p><b>{$presenceData.presence_index_label|@escape} {$presenceData.presence_information_title|@escape}</b><br />{$presenceData.presence_information_one_line|@escape}</p>');" onmouseout="hintHide()">{$presenceData.presence_index_label}</span>){/if}</td></tr>{/if}
					{if $presenceData.habitat_label}<tr><td style="white-space:nowrap">Habitat</td><td>{$presenceData.habitat_label}</td></tr>{/if}
					{if $presenceData.reference_label}<tr><td style="white-space:nowrap">Referentie</td><td><a href="../literature2/reference.php?id={$presenceData.reference_id}">{$presenceData.reference_label}</a></td></tr>{/if}
					{* if $presenceData.presence82_label}<tr><td>Status 1982</td><td>{$presenceData.presence82_label}</td></tr>{/if *}
					{if $presenceData.expert_name}<tr><td style="white-space:nowrap">Expert</td><td>{$presenceData.expert_name}{if $presenceData.organisation_name} ({$presenceData.organisation_name}){/if}</td></tr>{/if}
					{if $statusRodeLijst}<tr><td style="white-space:nowrap">Status rode lijst</td><td><a href="{$statusRodeLijst.url}" target="_blank">{$statusRodeLijst.status}</a></td></tr>{/if}
				</table>
			</p>

			{if $distributionMaps.count>0}
			<h2>Verspreiding</h2>
			{foreach from=$distributionMaps.data item=v}

			<a class="zoomimage" rel="prettyPhoto[gallery]" href="http://images.naturalis.nl/comping/{$v.image}" pTitle="{if $v.meta_map_description}{$v.meta_map_description|@ucfirst|@escape}{/if}
			{if $v.meta_map_source && $v.meta_map_description}<br />{/if}{if $v.meta_map_source}Bron: {$v.meta_map_source}{/if}">
				<img class="verspreidingskaartje" title="Foto {$v.photographer}" src="http://images.naturalis.nl/comping/{$v.image}" />
			</a>
			{if $v.meta_map_description}<br />{$v.meta_map_description|@ucfirst}{/if}
			{if $v.meta_map_source}<br />Bron: {$v.meta_map_source}{/if}
			{/foreach}
			{/if}

			{if $atlasData.content}

			<div id="atlasdata">
				<p>
				{$atlasData.content}
				</p>

				{if $atlasData.distributionmap}
				<h2>Verspreidingskaart</h2>
				<p>
					<img class="verspreidingskaart" src="{$atlasData.distributionmap}" />
				</p>
				{/if}

				{if $atlasData.author}
				<h2>Bron</h2>
				<p>
					<h4 class="source">Auteur(s)</h4>
					{$atlasData.author}
				</p>
				{/if}
				
				{if $atlasData.general_url}
				<p>
				<a href="{$atlasData.general_url}" target="_blank">Meer over deze soort in de BLWG Verspreidingsatlas</a>
				</p>
				{/if}

			</div>
		
			{/if}
		
			
			{assign var=trendByYear value=$trendData.byYear|@count>0}
			{assign var=trendByTrend value=$trendData.byTrend|@count>0}
			{assign var=trendSources value=$trendData.sources|@count>0}

			{if $trendByYear || $trendByTrend}

			<p>
				<h2>Trend</h2>
				{if $trendByYear}
				<div id="graph" style="height:300px;"></div>
				{/if}
				{if $trendByTrend}
				{foreach from=$trendData.byTrend item=v}
				{$v.trend_label}: {$v.trend}<br />
				{/foreach}
				{/if}
				{if $trendSources}
				<br />
				Bron:
				{foreach from=$trendData.sources item=v key=k}
				{if $k>0}, {/if}{$v}
				{/foreach}
				(via <a href="http://www.netwerkecologischemonitoring.nl" target="_blank">Netwerk Ecologische Monitoring</a>)
				{/if}
			</p>
			
			{/if}
			
			<!-- p>
				<h2>Waarnemingen</h2>
			</p -->

			<p>
				{$content}
			</p>

			<script type="text/JavaScript">
			$(document).ready(function()
			{
				// remove inherited html-embedded (and ouddated) status
				$('div.nsr[params*="template=presence"]').closest('div.mceTmpl').remove();

				{if $trendByYear}
				Morris.Bar({
				  element: 'graph',
				  data: [
				  {foreach from=$trendData.byYear item=v}
					{ y: '{$v.trend_year}', a: {$v.trend} },
				  {/foreach}
				  ],
				  xkey: 'y',
				  ykeys: ['a'],
				  labels: [''],
				  hideHover: true
				});
				{/if}

			});
			</script>

			
		</div>

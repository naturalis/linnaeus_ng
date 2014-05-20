{include file="../shared/header.tpl"}

<div id="dialogRidge">

	{include file="_left_column.tpl"}

	<div id="content" class="taxon-detail">

		<div id="taxonHeader" class="hasImage">
			<div id="titles" class="full">
				{if $names.preffered_name}
					<h1>{$names.preffered_name}</h1>
					<h2>{$names.nomen}</h2>
				{else}
					<h1 class="no-subtitle">{$names.nomen}</h1>
					<h2></h2>
				{/if}
			</div>
			{if $overviewImage.image}
			<div id="taxonImage">
				<img src="http://images.naturalis.nl/510x272/{$overviewImage.image}" />
				<div id="taxonImageCredits">
					<span class="photographer-title">{*{if $names.preffered_name}{$names.preffered_name} ({$names.nomen}){else}{$names.nomen}{/if} - *}Foto</span> {$overviewImage.label} 
				</div>
			</div>
			{/if}
		</div>

		{if $activeCategory==$smarty.const.TAB_BEELD_EN_GELUID || $activeCategory==$smarty.const.CTAB_MEDIA}

			<div>
			
				{if $mediaType=='collected'}
					<h4>
						Afbeelding{if $results.totalCount!=1}en{/if}: {$results.totalCount} /
						soorten met afbeelding: {$results.species}
						</h4>
					<div>

					{foreach from=$results.data item=v}
						<div class="imageInGrid3 taxon-page collected">
							<div class="thumbContainer">
								<a href="nsr_taxon.php?id={$v.taxon_id}&cat=media">
									<img class="speciesimage" alt="Foto {$v.photographer}" title="Foto {$v.photographer}" src="http://images.naturalis.nl/160x100/{$v.thumb}" />
								</a>
							</div>
							<dl>
								{if $v.name}<dd>{$v.name}</dd>{/if}
								<dd><i>{$v.taxon}</i></dd>
							</dl>
						</div>
					{/foreach}

				{else}

					<h4>Afbeelding{if $results.count!=1}en{/if}: {$results.count}</h4>
					<div>

					{foreach from=$results.data item=v}
						{if $search.img && $search.img==$v.image}
							{$pp_popup=[{$v.image},{$v.meta_data}]}
						{/if}
						<div class="imageInGrid3 taxon-page">
							<div class="thumbContainer">
								<a class="zoomimage" rel="prettyPhoto[gallery]" href="http://images.naturalis.nl/comping/{$v.image}" pTitle="<div style='margin-left:125px;'>{$v.meta_data|@escape}</div>">
									<img class="speciesimage" alt="Foto {$v.photographer}" title="Foto {$v.photographer}" src="http://images.naturalis.nl/160x100/{$v.thumb}" />
								</a>
							</div>
							<dl>
								<dt>Foto</dt><dd>{$v.photographer}</dd>
							</dl>
						</div>
					{/foreach}

				{/if}
				</div>

				{if $mediaType=='collected'}
				{assign var=pgnResultCount value=$results.totalCount}
				{else}
				{assign var=pgnResultCount value=$results.count}
				{/if}
				{assign var=pgnResultsPerPage value=$results.perpage}
				{assign var=pgnCurrPage value=$search.page}
				{assign var=pgnURL value=$smarty.server.PHP_SELF}
				{assign var=pgnQuerystring value=$querystring}
				{include file="../shared/_paginator.tpl"}

				{if $showMediaUploadLink}			
				<div>
					<p>&nbsp;</p>
					<p>
						<!-- Heeft u mooie foto's van deze soort? Voeg ze dan <a href="">hier</a> toe en draag zo bij aan het Soortenregister.. -->
					</p>
				</div>
				{/if}
			
			</div>
			
		{elseif $activeCategory==$smarty.const.CTAB_DNA_BARCODES}

			<div>
				<p>
Naturalis is een project gestart om van zoveel mogelijk Nederlandse planten, dieren en schimmels de DNA barcode te bepalen. Een DNA barcode is een internationaal vastgesteld stukje DNA waaraan je een soort kunt herkennen. Het doel van het project is de opbouw van een collectie goed geïdentificeerde soorten met hun DNA barcodes. Deze collectie dient als ijkpunt voor de genetische herkenning van planten, dieren en schimmels. Meer info
				</p>
				<p>
Van de soort <i>{$taxon_display_name}</i> zijn onderstaande exemplaren verzameld voor barcodering. Gegevens bijgewerkt tot 26 augustus 2013.
				</p>
				<p>
					NB: de hieronder vermelde wetenschappelijke naam kan afwijken van de naam in het Soortenregister.
				</p>
				
				<table class="taxon-dna-table">
					<tr>
						<th>Registratienummer</th>
						<th>Verzameldatum, plaats</th>
						<th>Verzamelaar</th>
						<th>Soort</th>
					</tr>
					{foreach from=$content item=v}
					<tr><td>{$v.barcode}</td><td>{$v.date_literal}, {$v.location}</td><td>{$v.specialist}</td><td>{$v.taxon_literal}</td></tr>
					{/foreach}
				</table>

			</div>

		{elseif $activeCategory==$smarty.const.TAB_VERSPREIDING}

			<div>
				<h2>Voorkomen</h2>
				
				<p>
					<table>
						{if $presenceData.presence_label}<tr><td>Status</td><td>{$presenceData.presence_label}{if $presenceData.presence_information} (<span class="link" onmouseover="hint(this,'<p><b>{$presenceData.presence_index_label|@escape} {$presenceData.presence_information_title|@escape}</b><br />{$presenceData.presence_information_one_line|@escape}</p>');">{$presenceData.presence_index_label}</span>){/if}</td></tr>{/if}
						{if $presenceData.habitat_label}<tr><td>Habitat</td><td>{$presenceData.habitat_label}</td></tr>{/if}
						{if $presenceData.reference_label}<tr><td>Referentie</td><td><a href="../literature2/reference.php?id={$presenceData.reference_id}">{$presenceData.reference_label}</a></td></tr>{/if}
						{* if $presenceData.presence82_label}<tr><td>Status 1982</td><td>{$presenceData.presence82_label}</td></tr>{/if *}
						{if $presenceData.expert_name}<tr><td>Expert</td><td>{$presenceData.expert_name}{if $presenceData.organisation_name} ({$presenceData.organisation_name}){/if}</td></tr>{/if}
						{if $statusRodeLijst}<tr><td>Status rode lijst</td><td><a href="{$statusRodeLijst.url}" target="_blank">{$statusRodeLijst.status}</a></td></tr>{/if}
					</table>
				</p>

				{if $distributionMaps.count>0}
				<h2>Verspreiding</h2>
				{foreach from=$distributionMaps.data item=v}

				<a class="zoomimage" rel="prettyPhoto[gallery]" href="http://images.naturalis.nl/comping/{$v.image}" pTitle="{if $v.meta_map_description}{$v.meta_map_description|@ucfirst|@escape}{/if}
				{if $v.meta_map_source && $v.meta_map_description}<br />{/if}{if $v.meta_map_source}Bron: {$v.meta_map_source}{/if}">
					<img class="speciesimage" title="Foto {$v.photographer}" src="http://images.naturalis.nl/240/{$v.image}" />
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

		{elseif $activeCategory==$smarty.const.CTAB_NAMES || $activeCategory==$smarty.const.TAB_NAAMGEVING}
					
			<p>
				<h2 id="name-header">Naamgeving</h2>
				
				<table id="names-table">
					{foreach from=$names.list item=v}
						{if $v.expert.name}{assign var=expert value=$v.expert.name}{/if}
						{if $v.organisation.name}{assign var=organisation value=$v.organisation.name}{/if}
						{if $v.nametype=='isValidNameOf' && $taxon.base_rank_id<$smarty.const.SPECIES_RANK_ID}
							<tr><td style="white-space:nowrap">{$v.nametype_label|@ucfirst}</td><td><b>{$v.name}</b></td></tr>
						{else}
							{if $v.language_id==$smarty.const.LANGUAGE_ID_SCIENTIFIC && $v.nametype!='isValidNameOf'}
							{assign var=another_name value="`$v.uninomial` `$v.specific_epithet` `$v.infra_specific_epithet`"}
							{if $another_name!=''}
								<tr><td style="white-space:nowrap">{$v.nametype_label|@ucfirst}</td><td><a href="name.php?id={$v.id}"><i>{$another_name}</i> {$v.authorship}</a></td></tr>
							{else}
								<tr><td style="white-space:nowrap">{$v.nametype_label|@ucfirst}</td><td><a href="name.php?id={$v.id}">{$v.name}</a></td></tr>
							{/if}
							{else}
							<tr><td style="white-space:nowrap">{$v.nametype_label|@ucfirst}</td><td><a href="name.php?id={$v.id}">{$v.name}</a></td></tr>
							{/if}
						{/if}
					{/foreach}
					{if $expert || $organisation}
						{if $expert}
						<tr><td>Expert</td><td colspan="2">{$expert}{if $organisation} ({$organisation}){/if}</td></tr>
						{else}
						<tr><td>Organisatie</td><td colspan="2">{$organisation}</td></tr>
						{/if}
					{/if}
				</table>
			</p>

			<p>
				<h2>Indeling</h2>
				<ul class="taxonoverzicht">
					<li class="root">
					{foreach from=$classification item=v key=x}
					{if $v.parent_id!=null}{* skipping top most level "life" *}
						<span class="classification-preffered-name"><a href="?id={$v.id}">{$v.taxon}</a></span>
						<span class="classification-rank">[{$v.rank}]</span>
						{if $v.common_name}<br />
						<span class="classification-accepted-name">{$v.common_name}</span>{/if}
						<ul class="taxonoverzicht">
							<li>
					{/if}
					{/foreach}
					{foreach from=$classification item=v key=x}
					{if $v.parent_id!=null}{* skipping top most level "life" *}
					</li></ul>
					{/if}
					{/foreach}
					</li>				
				</ul>
			</p>

			<p>
				{$content}
			</p>

		{elseif $activeCategory==$smarty.const.TAB_BEDREIGING_EN_BESCHERMING}

			<div>
			
				{if $wetten}
			
					<h2>Beschermingsstatus</h2>

					{foreach from=$wetten item=soort key=naam}

					<p>
						{if $naam!=$names.nomen_no_tags}
						<h3><i>{$naam}</i></h3><br />
						{/if}
						
						<ul>
							{foreach from=$soort.wetten item=v key=wet}
							<li>
								<b>{$wet}</b>
								<ul>
									{foreach from=$v item=w}
									<li>
										{$w.categorie}<br />
										{$w.publicatie}
									</li>
									{/foreach}
								</ul>
							</li>
							{/foreach}
						</ul>
						<br />
						
						Zie ook: <a href="{$soort.url}">EL&I wettelijke bescherming, beleid en signalering</a><br /><br />
			
					</p>
					{/foreach}
					
				{/if}

				<p>
					{$content}
				</p>

				<script type="text/JavaScript">
				$(document).ready(function()
				{
					// remove inherited html-embedded (and ouddated) status
//					$('#lnv').remove();
				});
				</script>

			</div>


		{else}

			{if $content|@is_array}
			<ul>
				{foreach from=$content item=v key=k}
				{if $k>0}<li><a href="nsr_taxon.php?id={$v.id}">{$v.label}</a></li>{/if}
				{/foreach}
			</ul>
			{else}
			<p>
				{$content}
			</p>
			{/if}

		{/if}

		{if $rdf}
		
			{assign var=hasAuthor value=false}
			{capture name=authors}
			{foreach from=$rdf item=v}{if $v.predicate=='hasAuthor'}{if $hasAuthor}, {/if}{$v.data.name}{assign var=hasAuthor value=true}{/if}{/foreach}		
			{/capture}
	
			{if $hasAuthor}			
			<h2>Bron</h2>
			<p>
				<h4 class="source">Auteur(s)</h4>
				{$smarty.capture.authors}
			</p>
			{/if}
			{assign var=hasReferences value=false}
			{capture name=references}
			{foreach from=$rdf item=v}
				{if $v.predicate=='hasReference' && $v.data.citation!=''}
				{assign var=hasReferences value=true}
				<li><a href="../literature2/reference.php?id={$v.data.id}">{$v.data.citation}</a></li>
				{elseif $v.object_type=='reference' && $v.data.label!=''}
				{assign var=hasReferences value=true}
				<li>{if $v.data.actor.name}{$v.data.actor.name} {$v.data.date}{else}{$v.data.source}{/if}. <a href="../literature2/reference.php?id={$v.data.id}">{$v.data.label}</a></li>
				{/if}
			{/foreach}
			{/capture}
				
			{if $hasReferences}			
			<p>
				<h4 class="source">Publicatie</h4>
				<ul class="reference">
				{$smarty.capture.references}
				</ul>
			</p>
			{/if}

		{/if}
	</div>

	{include file="../shared/_right_column.tpl"}

</div>


<script type="text/JavaScript">
$(document).ready(function() {
	if(jQuery().prettyPhoto) {
	 	$("a[rel^='prettyPhoto']").prettyPhoto( { 
	 		opacity: 0.70, 
			animation_speed:50,
			show_title: false,
	 		overlay_gallery: false,
	 		social_tools: false,
			changepicturecallback:function() { prettyPhotoCycle(); }
	 	} );
	}
	
	$('img[class=intern]').each(function() { $(this).remove(); } )

	{if $taxon.NsrId!=''}
	$('#name-header').on( 'click' , function(event) { 
	
		if ($('#nsr-id-row').html()==undefined)
		{
			if (event.altKey!==true) return;
			$('#names-table').append('<tr id="nsr-id-row"><td>NSR ID</td><td>{$taxon.NsrId}</td></tr>');
		}
		else
		{
			$('#nsr-id-row').toggle();
		}
	});
	{/if}
	
	{if $pp_popup}
	$.prettyPhoto.open('http://images.naturalis.nl/comping/{$pp_popup[0]}','','<div style="margin-left:125px;">{$pp_popup[1]}</div>');
	{/if}
} );
</script>


{include file="../shared/footer.tpl"}
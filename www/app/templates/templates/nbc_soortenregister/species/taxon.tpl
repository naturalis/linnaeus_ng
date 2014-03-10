{include file="../shared/header.tpl"}
{if $names.list[$names.prefId]}
{assign var=taxon_display_name value=$names.list[$names.prefId].name}
{elseif $names.list[$names.sciId]}
{assign var=taxon_display_name value="<i>`$names.list[$names.sciId].uninomial` `$names.list[$names.sciId].specific_epithet`</i>"}
{else}
{assign var=taxon_display_name value=$taxon.label}
{/if}
<div id="dialogRidge">

	{include file="_left_column.tpl"}

	<div id="content" class="taxon-detail">

		<div id="taxonHeader" class="hasImage">
			<div id="titles" class="full">
				{if $names.list[$names.prefId] && $names.list[$names.sciId]}
					<h1>{$taxon_display_name}</h1>
					<h2 style="width:510px"><i>{$names.list[$names.sciId].uninomial} {$names.list[$names.sciId].specific_epithet}</i></h2>
				{else}
					<h1 class="no-subtitle">{$taxon_display_name}</h1>
					<h2></h2>
				{/if}
			</div>
			{if $overviewImage.image}
			<div id="taxonImage">
				<img src="http://images.naturalis.nl/510x272/{$overviewImage.image}" />
				<div id="taxonImageCredits">
					<span class="photographer-title">Foto</span> {$overviewImage.label} 
				</div>
			</div>
			{/if}
		</div>

		{if $activeCategory==$smarty.const.TAB_MEDIA || $activeCategory==$smarty.const.CTAB_MEDIA}

			<div>
				<h4>Afbeelding{if $results.count!=1}en{/if}: {$results.count}</h4>
				<div>

				{foreach from=$results.data item=v}
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
				</div>

				{assign var=pgnResultCount value=$results.count}
				{assign var=pgnResultsPerPage value=$results.perpage}
				{assign var=pgnCurrPage value=$search.page}
				{assign var=pgnURL value=$smarty.server.PHP_SELF}
				{assign var=pgnQuerystring value=$querystring}
				{include file="../shared/_paginator.tpl"}

				{if showMediaUploadLink}			
				<div>
					<p>&nbsp;</p>
					<p>
						Bij deze soort kunnen beelden worden toegevoegd, voor aanbieding klik <a href="">hier</a>.
						<!-- a href="http://www.naturalisbeeldbibliotheek.nl/get?alias=nbb&amp;page_alias=afbeeldingenaanbieden&amp;conceptId=0AHCYFOOGKTT&amp;conceptName=Abacoproeces%20saltuum%20(L.%20Koch%2C%201872)&amp;comments=soort: Abacoproeces%20saltuum%20(L.%20Koch%2C%201872)" title="beeld aanbieden" target="_blank" -->
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

		{elseif $activeCategory==$smarty.const.TAB_DISTRIBUTION ||  $activeCategory==$smarty.const.TAB_PRESENCE}

			<div>
				<h2>Voorkomen</h2>
				
				<p>
					<table>
						{if $presenceData.presence_label}<tr><td>Status</td><td>{$presenceData.presence_label}{if $presenceData.presence_information} (<span class="link" onmouseover="hint(this,'<p><b>{$presenceData.presence_index_label|@escape} {$presenceData.presence_information_title|@escape}</b><br />{$presenceData.presence_information|@escape}</p>');">{$presenceData.presence_index_label}</span>){/if}</td></tr>{/if}
						{if $presenceData.habitat_label}<tr><td>Habitat</td><td>{$presenceData.habitat_label}</td></tr>{/if}
						{if $presenceData.reference_label}<tr><td>Referentie</td><td><a href="../literature2/reference.php?id={$presenceData.reference_id}">{$presenceData.reference_label} {$presenceData.reference_date}</a></td></tr>{/if}
						{* if $presenceData.presence82_label}<tr><td>Status 1982</td><td>{$presenceData.presence82_label}</td></tr>{/if *}
						{if $presenceData.expert_name}<tr><td>Expert</td><td>{$presenceData.expert_name}{if $presenceData.organisation_name} ({$presenceData.organisation_name}){/if}</td></tr>{/if}
					</table>
				</p>

				<!-- p>
					<h2>Trend</h2>
				</p>
				<p>
					<h2>Waarnemingen</h2>
				</p -->

				<p>
					{$content}
				</p>

				{literal}
				<script type="text/JavaScript">
				$(document).ready(function(){
					// removes inherited html-embedded (and ouddated) status
					$('div.nsr[params*="template=presence"]').closest('div.mceTmpl').remove();
				});
				</script>
				{/literal}
				
			</div>

		{elseif $activeCategory==$smarty.const.CTAB_NAMES || $activeCategory==$smarty.const.TAB_NOMENCLATURE}
					
			<p>
				<h2>Naamgeving</h2>
				<table>
					{foreach from=$names.list item=v}
					{if $v.expert.name}{assign var=expert value=$v.expert.name}{/if}
					{if $v.organisation.name}{assign var=organisation value=$v.organisation.name}{/if}
						<tr><td>{$v.nametype|@ucfirst}</td><td><a href="name.php?id={$v.id}">{$v.label}</a></td></tr>
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
				<table id="name-tree">
					{foreach from=$classification item=v key=x}
					{if $v.parent_id!=null}{* skipping top most level "life" *}
					{math equation="((x-2) * 5)" x=$x assign=buffercount}
					<tr><td>
						{if $x>1}
						{'&nbsp;'|str_repeat:$buffercount}
						<span class="classification-connector">&lfloor;</span>
						{/if}
						<span class="classification-preffered-name"><a href="?id={$v.id}">{$v.taxon}</a></span>
						<span class="classification-rank">[{$v.rank}]</span>
						{if $v.dutch_name}<br />
						{if $x>1}
						{'&nbsp;'|str_repeat:$buffercount}
						<span class="classification-connector-invisible">&lfloor;</span>
						{/if}
						<span class="classification-accepted-name">{$v.dutch_name}</span>{/if}
					</td></tr>
					{/if }
					{/foreach}			
				</table>
			</p>

			<p>
				{$content}
			</p>

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
		
		<h2>Bron</h2>
		<p>
			<h4 class="source">Auteur(s)</h4>
			{foreach from=$rdf item=v}
			{if $v.predicate=='hasAuthor'}
			{$v.data.name}
			{/if}
			{/foreach}
		</p>
		<p>
			<h4 class="source">Publicatie</h4>
			<ul class="reference">
			{foreach from=$rdf item=v}

			{if $v.predicate=='hasReference'}
			<li>{$v.data.citation}</li>
			{elseif $v.object_type=='reference'}
			<li>{$v.data.source}, {$v.data.label}</li>
			{/if}

			{/foreach}
			</ul>
		</p>
		{/if}
	</div>

	{include file="../shared/_right_column.tpl"}

</div>


{literal}
<script type="text/JavaScript">
$(document).ready(function(){
	if(jQuery().prettyPhoto) {
	 	$("a[rel^='prettyPhoto']").prettyPhoto({
	 		opacity: 0.70, 
			animation_speed:50,
			show_title: false,
	 		overlay_gallery: false,
	 		social_tools: false,
			changepicturecallback:function(){prettyPhotoCycle();}
	 	});
	}
});
</script>
{/literal}

{include file="../shared/footer.tpl"}
{include file="../shared/header.tpl"}
{literal}
<style>
.taxon-image-table {
	font-size:9px;
	color:#666;
}
.taxon-dna-table {
	font-size:11px;
}
</style>
{/literal}

{if $names.list[$names.prefId]}
{assign var=taxon_display_name value=$names.list[$names.prefId].name}
{elseif $names.list[$names.sciId]}
{assign var=taxon_display_name value="`$names.list[$names.sciId].uninomial` `$names.list[$names.sciId].specific_epithet`"}
{else}
{assign var=taxon_display_name value=$taxon.label}
{/if}

<div id="dialogRidge">

	{include file="_left_column.tpl"}
	
	<div id="content">
		<div id="taxonHeader" class="hasImage">
			<div id="titles" class="">
				<h1>
				{$taxon_display_name}
				</h1>
				{if $names.list[$names.prefId] && $names.list[$names.sciId]}
					<h2 style="width:510px"><i>{$names.list[$names.sciId].uninomial} {$names.list[$names.sciId].specific_epithet}</i></h2>
				{else}
					<h2 style="width:510px">&nbsp;</h2>
				{/if}
			</div>
			{if $overviewImage.image}
			<div id="taxonImage">
				<img src="{$overviewImage.image}" />
				<div id="taxonImageCredits">
					<span class="photographer-title">Foto</span>
					{assign var=name value=", "|explode:$overviewImage.label} 
					{$name[1]} {$name[0]}
				</div>
			</div>
			{/if}
		</div>

		{if $activeCategory=='media'}

			<h4>Afbeelding{if $content|@count!=1}en{/if}: {$content|@count}</h4>
			<div>
				{foreach from=$content item=v}
				{assign var=name value=", "|explode:$v.description} 
				<div class="thumbholder">
					<div class="thumbnail">
						<a class="zoomimage" href="{$v.file_name}">
							<img src="{$v.thumb_name}" title="foto {$name[1]} {$name[0]}" alt="foto {$name[1]} {$name[0]}">
						</a>
					</div>
					<p class="author">
						<span class="photographer-title">Foto</span>
						{$name[1]} {$name[0]}
					</p>
				</div>
				{/foreach}
			</div>

		{elseif $activeCategory=='dna barcodes'}

			<div>
				<p>
Naturalis is een project gestart om van zoveel mogelijk Nederlandse planten, dieren en schimmels de DNA barcode te bepalen. Een DNA barcode is een internationaal vastgesteld stukje DNA waaraan je een soort kunt herkennen. Het doel van het project is de opbouw van een collectie goed geïdentificeerde soorten met hun DNA barcodes. Deze collectie dient als ijkpunt voor de genetische herkenning van planten, dieren en schimmels. Meer info
				</p>
				<p>
Van de soort <i>{$taxon_display_name}</i> zijn onderstaande exemplaren verzameld voor barcodering. Gegevens bijgewerkt tot 26 augustus 2013.
				</p>
				<p>
<!-- NB: de hierboven vermelde wetenschappelijke naam kan afwijken van de naam in het Soortenregister.-->
				</p>
				
				
				
				<table class="taxon-dna-table">
					<tr><th>Registratienummer</th><th>Verzameldatum, plaats</th><th>Verzamelaar</th><th>Soort</th></tr>
					{foreach from=$content item=v}
					<tr><td>{$v.barcode}</td><td>{$v.location}{if $v.date}, {$v.date}{/if}</td><td>{$v.specialist}</td><td>{$taxon_display_name}</td></tr>
					{/foreach}
				</table>
			</div>

		{else}
		<p>
		
			{if $categorySysList[$activeCategory]=='Nomenclature'}
				<p>
					<h2>Naamgeving</h2>
					<table>
						{foreach from=$names.list item=v}
						{if $v.expert.name}
							{assign var=expert value=$v.expert.name}
							{assign var=organisation value=$v.organisation.name}
						{/if}
							<tr><td>{$v.nametype}</td><td><a>{$v.name}</a></td></tr>
						{/foreach}
						{if $expert}
						<tr><td>Expert</td><td colspan="2">{$expert} {if $organisation}({$organisation}){/if}</td></tr>
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
							<span class="classification-rank">[{$ranks[$v.rank_id].rank}]</span>
							{if $v.preferredName}<br />
							{if $x>1}
							{'&nbsp;'|str_repeat:$buffercount}
							<span class="classification-connector-invisible">&lfloor;</span>
							{/if}
							<span class="classification-preffered-name">{$v.preferredName}</span>{/if}
						</td></tr>
						{/if }
						{/foreach}			
					</table>
				</p>
			{/if}
			
			{if $presenceData}
			<h2>Voorkomen</h2>
			<table>
				{if $presenceData.presence}<tr><td>Status</td><td>{$presenceData.presence}</td></tr>{/if}
				{if $presenceData.habitat}<tr><td>Habitat</td><td>{$presenceData.habitat}</td></tr>{/if}
				{if $presenceData.reference}<tr><td>Referentie</td><td><a href="">{$presenceData.reference} {$presenceData.reference_date}</a></td></tr>{/if}
				{if $presenceData.presence82}<tr><td>Status 1982</td><td>{$presenceData.presence82}</td></tr>{/if}
				{if $presenceData.presence}<tr><td>Expert</td><td>{$presenceData.actor} {if $presenceData.organisation} ({$presenceData.organisation}){/if}</td></tr>{/if}
				<tr><td>Status rode lijst</td><td>...</td></tr>
			</table>
			{/if}
		
			{if $content|@is_array}
			<ul>
			{foreach from=$content item=v key=k}
			{if $k>0}<li><a href="taxon.php?id={$v.id}">{$v.label}</a></li>{/if}
			{/foreach}
			</ul>
			{else}
			<p>
			{$content}
			</p>
			{/if}
		</p>
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
			{/if}
			{/foreach}
			</ul>
		</p>
		{/if}
		{/if}
	</div>

	{include file="../shared/_right_column.tpl"}

</div>
	
	
    
{literal}
<script type="text/JavaScript">
$(document).ready(function(){
	
	$('#presence').remove();
	
});
</script>
{/literal}

{include file="../shared/footer.tpl"}
{include file="../shared/header.tpl"}

<div id="dialogRidge">

	<div id="left">

		<div id="treebranchContainer">

			<div class="top5">
				<h2>Top 5 fotografen</h2>
				<h4>Fotograaf (foto’s/soorten)</h4>
				<ul>
				{foreach from=$photographers item=v name=foo}
					{if $smarty.foreach.foo.index < 5}
					<li>
						<a href="nsr_search_pictures.php?photographer={$v.photographer}">{$v.photographer} ({$v.total} / {$v.taxon_count})</a>
					</li>
					{/if}
				{/foreach}
				</ul>
				<p>
					<a href="nsr_photographers.php"><i>Bekijk volledige lijst</i></a>
				</p>
			</div>
			
			<br />

			<div class="top5">
				<h2>Top 5 validatoren</h2>
				<h4>Validator (foto’s/soorten)</h4>
				<i>nog niet weten te exporteren uit de beeldbankdump</i>
			</div>

		</div>  

	</div>

	<div id="content">
		<div class="hasImage" id="taxonHeader">
			<div id="titles" style="border-bottom: 1px solid #666666;margin-top:-13px"><h1>Recente afbeeldingen</h1>
		</div>
		
	<div id="taxonImage">
		<a href="http://www.nederlandsesoorten.nl/get?site=nsr&amp;view=nsr&amp;page_alias=imageview&amp;version=popup&amp;cid=0AHCYFCKDGPR&amp;image=248242.jpg" class="zoomimage">
			<img src="http://images.ncbnaturalis.nl/510x272/248242.jpg" title="Foto Anneke van der Veen" alt="Foto Anneke van der Veen" class="speciesimage">
		</a>
		<div id="taxonImageCredits">
			<span class="photographer-title">Foto</span>&nbsp;Anneke van der Veen, 9&nbsp;mei&nbsp;2012, Opende, Groningen
		</div>
	</div>
</div>





		<div>
			<h4><span id="resultcount-header">{$results.count}</span></h4>
			<div>
				{foreach from=$results.data item=v}
					<div class="imageInGrid3">
						<div class="thumbContainer">
							<a class="zoomimage" rel="prettyPhoto[gallery]" href="http://images.naturalis.nl/comping/{$v.image}" pTitle="<div style='margin-left:125px;'>{$v.meta_data|@escape}</div>">
								<img class="speciesimage" alt="Foto {$v.photographer}" title="Foto {$v.photographer}" src="http://images.naturalis.nl/160x100/{$v.thumb}" />
							</a>
						</div>
							
						{if $v.dutch_name}		
						<h3>{$v.dutch_name}</h3>
						<span class="wetenschappelijkenaam"><i>{$v.name}</i></span>
						{else}
						<h3 class="wetenschappelijkenaam"><i>{$v.name}</i></h3>
						{/if}
						<dl>
							<dt>Foto</dt><dd>{$v.photographer}</dd>
							<dt>Geplaatst op</dt><dd>{$v.meta_datum}</dd>
						</dl>
						<div style="clear: both;"><a href="../species/nsr_taxon.php?id={$v.taxon_id}">Naar deze soort</a></div>
					</div>
				{/foreach}
			</div>

			{assign var=pgnResultCount value=$results.count}
			{assign var=pgnResultsPerPage value=$results.perpage}
			{assign var=pgnCurrPage value=$search.page}
			{assign var=pgnURL value=$smarty.server.PHP_SELF}
			{assign var=pgnQuerystring value=$querystring}
			{include file="../shared/_paginator.tpl"}
			
		</div>
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
	 		social_tools: false
	 	});
	}
	bindKeys();
});
</script>
{/literal}

{include file="../shared/footer.tpl"}
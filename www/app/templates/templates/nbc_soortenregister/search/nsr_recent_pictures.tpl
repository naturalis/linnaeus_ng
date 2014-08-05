{include file="../shared/header.tpl"}

<div id="dialogRidge">

	<div id="left">

		<div id="treebranchContainer">

			{include file="_photographers.tpl"}
			
			<br />

			{include file="_validators.tpl"}

		</div>  

	</div>

	<div id="content">
		<div class="hasImage" id="taxonHeader">
			<div id="titles" style="border-bottom: 1px solid #666666;margin-top:-13px;margin-bottom:5px;width:510px;"><h1>Recente afbeeldingen</h1>
		</div>
		
	<div id="taxonImage">
		{assign var=v value=$results.data[0]}
		<a class="zoomimage" rel="prettyPhoto[gallery]" href="http://images.naturalis.nl/comping/{$v.image}" pTitle="<div style='margin-left:125px;'>{$v.meta_data|@escape}</div>">
			<img class="speciesimage" alt="Foto {$v.photographer}" title="Foto {$v.photographer}" src="http://images.naturalis.nl/510x272/{$v.thumb}" />
		</a>
		<div id="taxonImageCredits">
			<span class="photographer-title">Foto</span>&nbsp;{$v.photographer}, {$v.meta_datum}, {$v.meta_geografie}
		</div>
	</div>
</div>

		<div>
			<div>
				{foreach from=$results.data item=v}
					<div class="imageInGrid3">
						<div class="thumbContainer">
							<a class="zoomimage" rel="prettyPhoto[gallery]" href="http://images.naturalis.nl/comping/{$v.image}" pTitle="<div style='margin-left:125px;'>{$v.meta_data|@escape}</div>">
								<img class="speciesimage" alt="Foto {$v.photographer}" title="Foto {$v.photographer}" src="http://images.naturalis.nl/160x100/{$v.thumb}" />
							</a>
						</div>
							
						{if $v.common_name}		
						<h3>{$v.common_name}</h3>
						<span class="wetenschappelijkenaam"><i>{$v.name}</i></span>
						{else}
						<h3 class="wetenschappelijkenaam"><i>{$v.name}</i></h3>
						{/if}
						<dl>
							<dt>Foto</dt><dd>{$v.photographer}</dd>
							<dt>Geplaatst op</dt><dd>{$v.meta_datum_plaatsing}</dd>
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

	$('title').html('Recente afbeeldingen - '+$('title').html());
	
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
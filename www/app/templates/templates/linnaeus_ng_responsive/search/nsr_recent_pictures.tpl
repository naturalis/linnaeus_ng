{include file="../shared/header.tpl"}
{if $overviewImage.image}
<div id="taxonHeader">

	{assign var=overviewImage value=$results.data[0]}

	<div id="headerImage">
		<div class="titles">
			{if $overviewImage.photographer}
			<div id="taxonImageCredits">
				<span class="photographer-title">{t}Foto:{/t}</span> {$overviewImage.photographer} 
			</div>
			{/if}
		</div>
	</div>
	
	<div id="taxonImage">
		<img src="{$taxon_base_url_images_overview}{$overviewImage.image}" />
		<div class="imageGradient"></div>
	</div>
	
</div>
{/if}

<!-- div id="dialogRidge">

	<div id="content" class="image-search">
		<div class="hasImage" id="taxonHeader">
			<div id="titles" style="border-bottom: 1px solid #666666;margin-top:-13px;margin-bottom:5px;width:510px;"><h1>{t}Recente afbeeldingen{/t}</h1>
		</div>
		
	<div id="taxonImage">
		{assign var=v value=$results.data[0]}
		<a class="fancybox" href="{$taxon_base_url_images_main}{$v.image}" pTitle="<div>{$v.meta_data|@escape}</div>">
			<img class="speciesimage" alt="Foto {$v.photographer}" title="Foto {$v.photographer}" src="{$taxon_base_url_images_overview}{$v.thumb}" />
		</a>
		<div id="taxonImageCredits">
			<span class="photographer-title">{t}Foto{/t}</span>&nbsp;{$v.label}
		</div>
	</div>
</div -->


<div id="dialogRidge">


	<div id="left">
		<div id="treebranchContainer">
			{include file="_photographers.tpl"}
			<br />
			{include file="_validators.tpl"}
		</div>  
	</div>
	
	<div id="content" class="image-search">
		<div>
			<div class="searchPictures">
				<div class="searchHeader">
					<h2>{t}Recente afbeeldingen{/t}</h2>
		        </div>
            
				<h4><span id="resultcount-header">{$results.count}</span></h4>

	   			<div id="images-container">
				{foreach from=$results.data item=v}
					<div class="imageInGrid3">
						<div class="thumbContainer">
							<a class="fancybox" data-fancybox-group="gallery" href="{$taxon_base_url_images_main}{$v.image}" pTitle="<div>{$v.meta_data|@escape}</div>">
								<div class="imageGradient"></div>
								<img class="speciesimage" alt="Foto {$v.photographer}" title="Foto {$v.photographer}" src="{$taxon_base_url_images_thumb}{$v.thumb}" />
								<ul>
									<li>{t}Foto{/t}: {$v.photographer}</li>
									<li>{$v.meta_datum_plaatsing}</li>
								</ul>
							</a>
						</div>
						<a class="resultDetails">
							{if $v.common_name}		
								<span class="resultLink" href="../species/nsr_taxon.php?id={$v.taxon_id}">{$v.common_name}</span>	
							{/if}
							<span class="wetenschappelijkenaam"><i>{$v.name}</i></span>						
						</a>
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
	
	<!-- div>
		tijdelijke link t.b.v. test:<br />
		<a href="nsr_recent_pictures.php">Recente afbeeldingen</a>
	</div -->

</div>

<script type="text/JavaScript">
$(document).ready(function()
{
	$('title').html("{t}Foto's zoeken{/t} - "+$('title').html());

	if(jQuery().prettyPhoto) {
	 	$("a[rel^='prettyPhoto']").prettyPhoto({
	 		opacity: 0.70, 
			animation_speed:50,
			show_title: false,
	 		overlay_gallery: false,
	 		social_tools: false,
			changepicturecallback:function(){ prettyPhotoCycle() }
	 	});
	}
	bindKeys();
});
</script>

{include file="../shared/footer.tpl"}

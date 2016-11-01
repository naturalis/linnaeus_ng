{include file="../shared/header.tpl"}
{include file="../shared/flexslider.tpl"}

<div id="dialogRidge">

	{include file="_searchtabs.tpl" activeTab="searchPictures" responsiveTabs="mobile"}

	<div id="left">
		<a class="filterSearchText">
		  Filter zoekopdracht
		  <i class="ion-chevron-down"></i>
		</a>
		<div id="treebranchContainer" class="filterPictures">
			{include file="_filterPictures.tpl"}
		</div>  
		{include file="_toolbox.tpl"}
	</div>
	
	<div id="content" class="image-search">
		{include file="../search/_searchtabs.tpl" activeTab="searchPictures" responsiveTabs="desktop"}
		<div>
			<div class="searchPictures">
				<div class="searchHeader">
					<h2>{if $search.header}{$search.header}{else}{t}Foto's zoeken{/t}{/if}</h2>
	        <!-- <select name="sort" class="customSelect">
	          <option value="validName"{if $search.sort=='validName'} selected="selected"{/if}>{t}Wetenschappelijk naam{/t}</option>
	          <option value="photographer"{if $search.sort=='photographer'} selected="selected"{/if}>{t}Fotograaf{/t}</option>
	        </select> -->
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
						<div class="resultDetails">
							{if $v.common_name}		
								<a class="resultLink" href="../species/nsr_taxon.php?id={$v.taxon_id}">{$v.common_name}</a>	
							{/if}
							<span class="wetenschappelijkenaam"><i>{$v.name}</i></span>						
						</div>
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

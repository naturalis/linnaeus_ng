{include file="../shared/header.tpl"}

<div id="dialogRidge">

	{include file="_searchtabs.tpl" activeTab="searchPictures" responsiveTabs="mobile"}

	<div id="left">
		<a class="filterSearchText">
		  {t}Filter zoekopdracht{/t}
		  <i class="ion-chevron-down"></i>
		</a>
		<div class="treebranchContainer filterPictures">
			{include file="_filterPictures.tpl"}
		</div>  

	</div>
	
	<div id="content" class="image-search">

       {include file="../search/_searchtabs.tpl" activeTab="searchPictures" responsiveTabs="desktop"}

		<div class="whiteBox">

            <div>

                
                <div class="searchHeader">
                    <h2>{if $search.header}{$search.header}{else}{t}Foto's zoeken{/t}{/if}</h2>
                    <br /><br />
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
						<a class="resultDetails" href="../species/nsr_taxon.php?id={$v.taxon_id}">
							{if $v.common_name}		
								<span class="resultLink" >{$v.common_name}</span>	
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
	acquireInlineTemplates();
});
</script>

<div class="inline-templates" id="lineTpl">
<!--
	<li id="item-%IDX%" ident="%IDENT%" onclick="window.open('../species/nsr_taxon.php?id=%IDENT%','_self');" onmouseover="activesuggestion=-1">
    <div class="common">%COMMON_NAME%</div>
    <div class="scientific">%SCIENTIFIC_NAME%</div>
	</li>
-->
</div>

{include file="../shared/footer.tpl"}

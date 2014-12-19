{include file="../shared/header.tpl"}

<body class="html not-front not-logged-in two-sidebars page-node page-node- page-node-17 node-type-naturalis-page section-content" >

    <!--.page -->
    <div role="document" class="page">

	{include file="../shared/page_header.tpl"}

    <main role="main" class="row l-main">

        <div class="large-6 large-push-3 main columns">
      
            <a id="main-content"></a>
           
			<h2>{if $search.header}{$search.header}{else}{t}Foto's zoeken{/t}{/if}</h2>
            
            <div id="search-panel">
			
                <form method="get" action="" id="formSearchFacetsSpecies" name="formSearchFacetsSpecies">
            
                    <input type="hidden" id="name_id" name="name_id" value="{$search.name_id}">
                    <input type="hidden" id="group_id" name="group_id" value="{$search.group_id}">
            
                    <fieldset class="block">
                        <div>
                            <label accesskey="g" for="name">{t}Soortnaam{/t}</label>
                            <input type="text" class="field" value="{$search.name}" id="name" name="name" autocomplete="off">
                            <div id="name_suggestion" match="start" class="auto_complete" style="display: none;"></div>
                        </div>
                        <div>
                            <label accesskey="g" for="group">{t}Soortgroep{/t}</label>
                            <input type="text" size="60" class="field" value="{$search.group}" id="group" name="group" autocomplete="off">
                            <div id="group_suggestion" match="start" class="auto_complete" style="display:none;"></div>
                        </div>
                        <div>
                            <label accesskey="g" for="photographer">{t}Fotograaf{/t}</label>
                            <input type="text" size="60" class="field" value="{$search.photographer}" id="photographer" name="photographer" autocomplete="off">
                            <div id="photographer_suggestion" match="start" class="auto_complete" style="display:none;"></div>
                        </div>
                    </fieldset>
    
                    <fieldset>
                        <div>
                            <label for="" accesskey="g">{t}Resultaten sorteren op:{/t}</label>
                            <select name="sort">
                                <!-- option value="dateModified desc" selected="selected">Datum plaatsing</option -->
                                <option value="validName"{if $search.sort=='validName'} selected="selected"{/if}>{t}Wetenschappelijk naam{/t}</option>
                                <option value="photographer"{if $search.sort=='photographer'} selected="selected"{/if}>{t}Fotograaf{/t}</option>
                            </select>
                        </div>
        
                        <input type="submit" class="zoekknop" value="{t}zoek{/t}">
                    </fieldset>
                </form>
			</div>
            <div id="results">

                <h3><span id="resultcount-header">{$results.count}</span></h3>

				{foreach from=$results.data item=v}

					<div class="imageInGrid3">
						<div class="thumbContainer">
							<a class="zoomimage" rel="prettyPhoto[gallery]" href="http://images.naturalis.nl/comping/{$v.image}" pTitle="<div style='margin-left:125px;'>{$v.meta_data|@escape}</div>">
								<img class="speciesimage" alt="{t}Foto{/t} {$v.photographer}" title="{t}Foto{/t} {$v.photographer}" src="http://images.naturalis.nl/160x100/{$v.thumb}" />
							</a>
						</div>
							
						<h3>{$v.nomen}</h3>
						<dl>
							<dd><b>{t}Foto:{/t}</b> {$v.photographer}</dd>
							<dd>{$v.meta_geografie}</dd>
						</dl>
						<div style="clear: both;"><a href="../species/nsr_taxon.php?id={$v.taxon_id}&cat=media">{t}Naar deze soort{/t}</a></div>
					</div>
				{/foreach}


			{assign var=pgnResultCount value=$results.count}
			{assign var=pgnResultsPerPage value=$results.perpage}
			{assign var=pgnCurrPage value=$search.page}
			{assign var=pgnURL value=$smarty.server.PHP_SELF}
			{assign var=pgnQuerystring value=$querystring}
			{include file="../shared/_paginator.tpl"}
            
		</div>
     </div>

    <!--/.main region -->

	{include file="../shared/_left_column_just_search.tpl"}

	{include file="../shared/_right_column.tpl"}
    
</main>
<!--/.main-->

  
  
  </div>
<!--/.page -->
			

<script type="text/JavaScript">
$(document).ready(function(){
	
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
//	allLookupAlwaysFetch=true;
});
</script>

{include file="../shared/header.tpl"}

<body class="html not-front not-logged-in two-sidebars page-node page-node- page-node-17 node-type-naturalis-page section-content" >

    <!--.page -->
    <div role="document" class="page">

	{include file="../shared/page_header.tpl"}

    <main role="main" class="row l-main">

        <div class="large-6 large-push-3 main columns">
      
            <a id="main-content"></a>
           
			<h2>{t}Zoekresultaten{/t}</h2>
			<h3>{t _s1=$search.search}Gezocht op "%s":{/t} <span id="resultcount-header">{$results.count}</span></h3>
			
            {*
			<p>
				<label for="" accesskey="g">{t}Resultaten sorteren op:{/t}</label>
				<select name="sort" onChange="sortResults(this);">
					<option value="sort_relevance"selected="selected">{t}Relevantie{/t}</option>
					<option value="sort_name">{t}Wetenschappelijke naam{/t}</option>
					<option value="sort_common">{t}Engelse naam{/t}</option>
				</select>
			</p>
            *}
			

            <div id="results">
                <p>
                    {if $results.data}
                    {assign var=i value=0}
                    {foreach from=$results.data item=v}
                    <div class="result" sort_name="{$v.taxon}" sort_relevance="{$i++}" sort_common="{if $v.common_name}{$v.common_name}{else}_{/if}">
                        {if $v.overview_image}
                        <img src="http://images.naturalis.nl/120x75/{$v.overview_image}"/>
                        {/if}
                        <strong><a href="../species/nsr_taxon.php?id={$v.taxon_id}">{$v.taxon}</a></strong>
                        {if $v.common_rank} ({$v.common_rank}){/if}<br />
                        {* if $v.common_name}{$v.common_name}<br />{/if *}
                        {* if $v.presence_information_index_label || $v.presence_information_title}
                        {t}Status voorkomen:{/t} {$v.presence_information_index_label} {$v.presence_information_title}
                        {/if *}
                    </div>
                    {/foreach}
    
                    {assign var=pgnResultCount value=$results.count}
                    {assign var=pgnResultsPerPage value=$results.perpage}
                    {assign var=pgnCurrPage value=$search.page}
                    {assign var=pgnURL value=$smarty.server.PHP_SELF}
                    {assign var=pgnQuerystring value=$querystring}
                    {include file="../shared/_paginator.tpl"}
                    
                    {else}
                    {t}Niets gevonden.{/t}
                    {/if}
    
                </p>
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
$(document).ready(function() {
	$( '#inlineformsearchInput' ).focus();


	$('title').html('Zoekresultaten - '+$('title').html());
	
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

});
</script>		

{include file="../shared/footer.tpl"}
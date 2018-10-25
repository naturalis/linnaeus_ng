{include file="../shared/_head.tpl"}
{include file="../shared/_body-start.tpl"}
<div class="page-title">
	<span class="set-as-title">{t}Filter species{/t}</span>
</div>

<style>
.options-panel {
	margin-top:5px;
}
.zoekknop {
	cursor:pointer;
}
.traits-legend-cell {
	width:150px;
}	
.arrow-container {
	width:15px;
}
.arrow-e, .arrow-se, .arrow-s {
	width: 0;
	height: 0;
	margin-right:2px;
}
.arrow-e {
	margin-top:2px;
	border-top: 5px solid transparent;
	border-bottom: 5px solid transparent;
	border-left: 10px solid black;
}
.arrow-se {
	margin-top:2px;
	border-top:10px dashed transparent;
	border-right:10px solid black;
}
.arrow-s {
	margin-top:3px;
	border-left: 5px solid transparent;
	border-right: 5px solid transparent;
	border-top: 10px solid black;
}
#search-parameters {
	margin-top:5px;
}
</style>
                
<div id="main-body">

	{include file="_searchtabs.tpl" activeTab="extendedSearch" responsiveTabs="mobile"}
	<div class="sidebar__container">
		{include file="_extendedSearchFilters.tpl"}
		{include file="_toolbox.tpl"}
	</div>  
	
	<div id="page-container">

<!--
	<div class="page-generic-div" {$responsiveTabs}>
		<ul class="tabs" style="background-color: white;">
			<li class="tab-active">
				<a href="../search/nsr_search_extended.php">{t}Filter species{/t}</a>
			</li>
			<li class="tab">
				<a href="../search/search.php">{t}Full search{/t}</a>
			</li>
			<li class="tab">
				<a href="../species/tree.php">{t}Taxonomic tree{/t}</a>
			</li>
		</ul>
	</div>
	<div class="extendedSearch">
	    <input type="text" size="60" class="field focusfirst" id="{$responsiveTabs}group" name="group" autocomplete="off" placeholder="{t}Filter by species group...{/t}" value="{$search.group}">
	    <div id="{$responsiveTabs}group_suggestion" match="like" class="auto_complete" style="display:none;"></div>
	</div>
-->

	<div id="dialogRidge">
	
	<div id="content" class="simple-search">
		{include file="_searchtabs.tpl" activeTab="extendedSearch" responsiveTabs="desktop"}
	
		<div id="results" class="searchResultContainer"> 
		<!--
			<div class="searchHeader">
	      <h2>
	      	{t}Zoekresultaten{/t}
	      </h2>
          
	      <div class="formrow orderList">
		      <select name="sort" id="sort" class="customSelect" onchange="submitSearchParams();">
	          <option value="name-valid"{if $search.sort!='name-valid'} selected="selected"{/if}>{t}Wetenschappelijke naam{/t}</option>
	          <option value="name-pref-nl"{if $search.sort=='name-pref-nl'} selected="selected"{/if}>{t}Nederlandse naam{/t}</option>
		      </select>
	      </div>
     	</div>
	      -->
	      
         <div style="margin-bottom:40px;"><span id="resultcount-header" style="float:left"></span>
            <a href="#" id="just-species-toggle" style="float:left;margin-left:10px;" onclick="toggleJustSpeciesToggle();submitSearchParams();return false;">
            {t}hide infraspecies{/t}
            </a>
        </div>


      <ul class="searchResult">
			{foreach from=$results.data item=v}
        <li class="result">
        	<a href="../species/nsr_taxon.php?id={$v.taxon_id}" class="clicklink"></a>	
          <a href="../species/nsr_taxon.php?id={$v.id}">{$v.taxon}</a>
	          	<span class="commonName">
				{if $show_all_preferred_names_in_results}
					{foreach $v.common_names n nk}
                    {$n.name}
                    {if $nk<$v.common_names|@count}<br />{/if}
					{/foreach}
                {else}
					{if $v.common_name}{$v.common_name}<br />{/if}
				{/if}
			</span>
      		<span class="status">
            {if $show_presence_in_results}
                {if $v.presence_information_index_label || $v.presence_information_title}
                {t}Status voorkomen:{/t} {$v.presence_information_index_label} {$v.presence_information_title}
                {/if}
            {/if}
        	</span>
        	{if $v.overview_image}
        		<div class="image" style="background-image: url('{$taxon_base_url_images_thumb_s}{$v.overview_image}');"></div>
          {/if}
        </li>
			{/foreach}
			</ul>

        {if $search.just_species==1}
        {capture A}{t}species{/t}{/capture}
        {capture B}{t}species{/t}{/capture}
        {else}
        {capture A}{t}species (and infraspecies){/t}{/capture}
        {capture B}{t}species (and infraspecies){/t}{/capture}
        {/if}

			{assign var=pgnEntityNames value=[$smarty.capture.A,$smarty.capture.B]}
			{assign var=pgnResultCount value=$results.count}
			{assign var=pgnResultsPerPage value=$results.perpage}
			{assign var=pgnCurrPage value=$search.page}
			{assign var=pgnURL value=$smarty.server.PHP_SELF}
			{assign var=pgnQuerystring value=$querystring}
			{include file="../shared/_paginator.tpl"}
		</div>
	</div>
	</div>
	</div>



<script>
$(document).ready(function()
{
	{if $search}
	{foreach from=$search.presence item=v key=k}
	$("#presenceStatusList").val('presence[{$k}]');
	addSearchParameter('presenceStatusList');
	{/foreach}

	{foreach from=$search item=v key=k}
	{if $k=='images' || $k=='distribution' || $k=='trend'}
	$("#multimedia-options").val('{$k}');
	addSearchParameter('multimedia-options');
	{else if $k=='dna' || $k=='dna_insuff'}
	$("#dna-options").val('{$k}');
	addSearchParameter('dna-options');
	{else if $k=='images_on' || $k=='images_off'}
	$("#multimedia-images").val('{$k}');
	addSearchParameter('multimedia-images');
	{else if $k=='distribution_on' || $k=='distribution_off'}
	$("#multimedia-distribution").val('{$k}');
	addSearchParameter('multimedia-distribution');
	{else if $k=='trend_on' || $k=='trend_off'}
	$("#multimedia-trend").val('{$k}');
	addSearchParameter('multimedia-trend');
	{/if}
	{/foreach}
	
	{if $search.traits}

	var h=$.parseJSON(decodeURIComponent('{$search.traits}'));

	for (var i in h)
	{
		var d=h[i];
		
		if (d.valueid)
		{
			$('select[trait-id='+d.traitid+']').val(d.valueid);
		}
		else
		{
			$('select.operator[trait-id='+d.traitid+']').val(d.operator).trigger('change');
			$('input[type=text][trait-id='+d.traitid+']').val(d.value);

			if (d.value2)
			{
				$('input[trait-id='+d.traitid+'][second-value=1]').val(d.value2);
			}
			else
			{
				$('input[trait-id='+d.traitid+'][second-value=1]').val('');
			}

		}

		$('input.add-trait[trait-id='+d.traitid+']').trigger('click');
	}

	{if $search.trait_group!=''}
		setTraitGroup({$search.trait_group});
		setTraitGroupName('{$trait_group_name}');
		printParameters();
	{/if}



	{/if}
	{/if}

	{if $search.panels}

	var h=$.parseJSON(decodeURIComponent('{$search.panels}'));

	$.each(h, function(i,v)
	{
		if (v.visible)
		{
			$('label[panel='+v.id+']').trigger('click').trigger('mouseout');
		}
	});
	
	{else}
	
		$('label[for=presenceStatusList]').trigger('click').trigger('mouseout');

	{/if}

	{if $search.just_species}
	setJustSpeciesToggle({$search.just_species});
	{/if}
	
	$('#just-species-toggle').html(getJustSpeciesToggle()==0 ? '{t}hide infraspecies{/t}' : '{t}show infraspecies{/t}' );

	$('title').html('{t}Filter species{/t} - '+$('title').html());

	bindKeys();

	$("[id$=group]").keyup(function(e)
	{ 
		var code = e.which;
		if(code==13)
		{
			submitSearchParams();
		}
	});
	
	init=false;
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
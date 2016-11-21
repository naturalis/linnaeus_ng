{include file="../shared/header.tpl"}

<div id="dialogRidge">
	<div id="left">
	{include file="_toolbox.tpl"}
	</div>
	<div id="content" class="simple-search">
		{include file="_searchtabs.tpl" activeTab="quickSearch"}
		<div id="results" class="quickSearch">
			<div class="searchHeader">
				<h2>{t}Zoekresultaten{/t}</h2>
				<select name="sort" class="customSelect" onchange="sortResults(this);">
					<option value="sort_relevance"selected="selected">{t}Relevantie{/t}</option>
					<option value="sort_name">{t}Wetenschappelijke naam{/t}</option>
					<option value="sort_common">{t}Nederlandse naam{/t}</option>
				</select>
			</div>
			<span id="resultcount-header">{$results.count}</span>
			{if $results.data}
			{assign var=i value=0}
			<ul class="searchResult">
			
			{foreach $results.data v}
				<li class="result" sort_name="{$v.taxon|@strip_tags}" sort_relevance="{$i++}" sort_common="{if $v.common_name}{$v.common_name}{else}_{/if}">
					<a href="../species/nsr_taxon.php?id={$v.taxon_id}" class="clicklink"></a>		
					<a href="../species/nsr_taxon.php?id={$v.taxon_id}">
						{$v.taxon} 
						{if $v.presence_information_index_label || $v.presence_information_title}{else}
							({$v.common_rank})
						{/if}
					</a>
					{if $v.common_name}
						<span class="commontName">{$v.common_name}</span>
					{/if}
					{if $v.presence_information_index_label || $v.presence_information_title}
						<span class="status">{t}Status:{/t} {$v.presence_information_index_label} {$v.presence_information_title} 
							<!-- {if $v.common_rank} ({$v.common_rank}){/if} -->
						</span>
					{/if}
					{if $v.overview_image}
						<div class="image" style="background-image:url('{$taxon_base_url_images_thumb_s}{$v.overview_image}');"></div>
					{/if}
				</li>
			{/foreach}
			</ul>
			{assign var=pgnResultCount value=$results.count}
			{assign var=pgnResultsPerPage value=$results.perpage}
			{assign var=pgnCurrPage value=$search.page}
			{assign var=pgnURL value=$smarty.server.PHP_SELF}
			{assign var=pgnQuerystring value=$querystring}
			{include file="../shared/_paginator.tpl"}
			
			{else}
			{t}Begin hierboven met zoeken in het zoekvenster.{/t}
			{/if}
		</div>
	</div>
</div>

{include file="../shared/footer.tpl"}

<script type="text/JavaScript">
$(document).ready(function(){
	
	$('title').html('{t}Zoekresultaten{/t} - '+$('title').html());

});
</script>
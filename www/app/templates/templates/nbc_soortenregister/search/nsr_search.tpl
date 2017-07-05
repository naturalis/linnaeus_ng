{include file="../shared/header.tpl"}

<div id="dialogRidge">

	<div id="left">

	{include file="_toolbox.tpl"}

	</div>
    
	<div id="content">
	
		<div id="results">
			<h1 style="width:500px;color:#FA7001;font-size:30px;font-weight:normal;margin-top:-13px;border-bottom:1px solid #666666;margin-bottom:5px;">{t}Zoekresultaten{/t}</h1>
			<h4>
				{t}Gezocht op{/t} "{$search.search}": <span id="resultcount-header">{$results.count}</span>
			</h4>
			
			<p>
			</p>
			<p>
				<label for="" accesskey="g">{t}Resultaten sorteren op:{/t}</label>
				<select name="sort" onchange="sortResults(this);">
					<option value="sort_relevance"selected="selected">{t}Relevantie{/t}</option>
					<option value="sort_name">{t}Wetenschappelijke naam{/t}</option>
					<option value="sort_common">{t}Nederlandse naam{/t}</option>
				</select>
			</p>
		
			<p>
				{if $results.data}
				{assign var=i value=0}
				{foreach $results.data v}
				<div class="result" sort_name="{$v.taxon|strip_tags:false}" sort_relevance="{$i++}" sort_common="{if $v.common_name}{$v.common_name}{else}_{/if}">
					{if $v.overview_image}
					<img src="{$taxon_base_url_images_thumb_s}{$v.overview_image}"/>
					{/if}
					<strong><a href="../species/nsr_taxon.php?id={$v.taxon_id}">{$v.taxon}</a></strong>
					{if $show_taxon_rank_in_results && $v.common_rank} ({$v.common_rank}){/if}<br />

				{if $show_all_preferred_names_in_results}
					{foreach $v.common_names n nk}
                    {$n.name}
                    {if $nk<$v.common_names|@count}<br />{/if}
					{/foreach}
                {else}
					{if $v.common_name}{$v.common_name}<br />{/if}
				{/if}

                {if $show_presence_in_results}
                    {if $v.presence_information_index_label || $v.presence_information_title}
                    {t}Status voorkomen:{/t} {$v.presence_information_index_label} {$v.presence_information_title}
                    {/if}
                {/if}

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

	{include file="../shared/_right_column.tpl"}


</div>

{include file="../shared/footer.tpl"}

<script type="text/JavaScript">
$(document).ready(function()
{
	$('title').html('{t}Zoekresultaten{/t} - '+$('title').html());
});
</script>
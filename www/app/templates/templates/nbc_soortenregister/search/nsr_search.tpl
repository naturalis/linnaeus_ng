{include file="../shared/header.tpl"}

<div id="dialogRidge">

	<div id="left">

	{include file="_toolbox.tpl"}

	</div>
    
	<div id="content">
	
		<div id="results">
			<h1 style="width:500px;color:#FA7001;font-size:30px;font-weight:normal;margin-top:-13px;border-bottom:1px solid #666666;margin-bottom:5px;">Zoekresultaten</h1>
			<h4>
				Gezocht op "{$search.search}": <span id="resultcount-header">{$results.count}</span>
			</h4>

			<p>
				{if $results.data}

				{foreach from=$results.data item=v}
				<div class="result">
					{if $v.overview_image}
					<img src="http://images.naturalis.nl/120x75/{$v.overview_image}"/>
					{/if}				
					<strong><a href="../species/nsr_taxon.php?id={$v.taxon_id}">{$v.taxon}</a></strong>
					{if $v.common_rank} ({$v.common_rank}){/if}<br />
					{if $v.common_name}{$v.common_name}<br />{/if}
					{if $v.presence_information_index_label || $v.presence_information_title}
					{t}Status voorkomen:{/t} {$v.presence_information_index_label} {$v.presence_information_title}
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

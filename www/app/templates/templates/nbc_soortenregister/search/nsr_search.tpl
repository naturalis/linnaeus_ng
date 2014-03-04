{include file="../shared/header.tpl"}

<div id="dialogRidge">

	{include file="_left_column.tpl"}
    
	<div id="content">
	
		<div id="results">
			<h2>
			Gezocht op "{$search.search}": <span id="resultcount-header">{$results.count}</span>
			</h2>

			<p>
				{if $results.data}

				{foreach from=$results.data item=res}
				<div class="result">
					{if $res.overview_image}
					<img src="http://images.naturalis.nl/160x100/{$res.overview_image}" style="height:100px;max-width:140px;float:right"/>
					{/if}
					
					<strong><a href="../species/nsr_taxon.php?id={$res.taxon_id}">{$res.taxon}</a></strong><br />
					{$res.dutch_name}<br /><br />
					Status voorkomen: {$res.presence_information_index_label} {$res.presence_information_title}
				</div>
				{/foreach}

				{assign var=pgnResultCount value=$results.count}
				{assign var=pgnResultsPerPage value=$results.perpage}
				{assign var=pgnCurrPage value=$search.page}
				{assign var=pgnURL value=$smarty.server.PHP_SELF}
				{assign var=pgnQuerystring value=$querystring}
				{include file="../shared/_paginator.tpl"}
				
				{else}
				Niets gevonden.
				{/if}




			</p>
		</div>
		
	</div>

	{include file="../shared/_right_column.tpl"}

</div>

{include file="../shared/footer.tpl"}

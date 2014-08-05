{include file="../shared/header.tpl"}

<div id="dialogRidge">

	<div id="left">
	
	{include file="_toolbox.tpl"}
	
	</div>
    
	<div id="content">

		<div id="results">
			<p>
				<h2>
				Gezocht op "{$search.search}": <span id="resultcount-header">{$results.data.species.results[$CONSTANTS.C_TAXA_ALL_NAMES].numOfResults}</span>
				</h2>
			</p>
			<p>
				{if $results.data.species.results[$CONSTANTS.C_TAXA_ALL_NAMES].data}
				<ol>
					{foreach from=$results.data.species.results[$CONSTANTS.C_TAXA_ALL_NAMES].data item=res}
					<li style="margin-bottom:5px">
					<a href="../species/nsr_taxon.php?id={$res.taxon_id}">
					{if $res.label}{$res.label}{else}{$res.matches[0]}{/if}
					</a>
					{if $res.predicate=='isPreferredNameOf'}<br />{$res.subject.label}{/if}
					</li>
					{/foreach}
				</ol>
				{else}
				Niets gevonden.
				{/if}
			</p>
		</div>
		
	</div>

	{include file="../shared/_right_column.tpl"}

</div>

<script type="text/JavaScript">
$(document).ready(function(){

	$('title').html('Zoekresultaten - '+$('title').html());

	var n=parseInt($('#resultcount-header').html());
	$('#resultcount-header').html($('#resultcount-header').html()+' '+(n==1 ? 'resultaat' : 'resultaten'));

});
</script>


{include file="../shared/footer.tpl"}

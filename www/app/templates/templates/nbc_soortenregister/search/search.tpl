{include file="../shared/header.tpl"}

<div id="dialogRidge">

	{include file="_left_column.tpl"}
    
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
					<a class="result"href="../species/taxon.php?id={$res.taxon_id}">
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

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
	var n=parseInt($('#resultcount-header').html());
	$('#resultcount-header').html($('#resultcount-header').html()+' '+(n==1 ? 'resultaat' : 'resultaten'));
{literal}
});
</script>
{/literal}

{include file="../shared/footer.tpl"}

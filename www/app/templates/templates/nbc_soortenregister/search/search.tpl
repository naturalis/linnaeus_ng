{include file="../shared/header.tpl"}

<div id="dialogRidge">

	{include file="_left_column.tpl"}
    
	<div id="content">


		<div id="results">
			<p>
				<h2>
				Gezocht op "{$search.search}": <span id="resultcount-header"></span>
				</h2>
			</p>
			<p>
				{if $results}
				<ol>
					{foreach from=$results.data item=res}
					<li style="margin-bottom:5px">
					<a class="result"href="../species/taxon.php?id={$res.taxon_id}">
					{if $res.subject.label}{$res.subject.label}{elseif $res.label}{$res.label}{else}{$res.matches[0]}{/if}
					</a>
					{if $res.preferredName}<br />{$res.preferredName}{elseif $res.label}<br />{$res.label}{/if}
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
	$('#resultcount-header').html('{$results.count} '+({$results.count}==1 ? 'resultaat' : 'resultaten'));
{literal}
});
</script>
{/literal}

{include file="../shared/messages.tpl"}
{include file="../shared/footer.tpl"}

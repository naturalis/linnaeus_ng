<div class="top5">
	<h2>{t}Top 5 validatoren{/t}</h2>
	<h4>{t}Validator (foto’s/soorten){/t}</h4>
	<ul>
	{foreach from=$validators item=v name=foo}
		{if $smarty.foreach.foo.index < 5}
		<li>
			<a href="nsr_search_pictures.php?validator={$v.validator}">{$v.validator} ({$v.total} / {$v.taxon_count})</a>
		</li>
		{/if}
	{/foreach}
	</ul>
	<p>
		<a href="nsr_validators.php"><i>{t}Bekijk volledige lijst{/t}</i></a>
	</p>

</div>
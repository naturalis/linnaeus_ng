<div id="top5-validators" class="top5">
	<h2>{t}Top 5 validatoren{/t}</h2>
	<h4>{t}Validator (foto’s/soorten){/t}</h4>
	<ul>
	{foreach $validators v}
		<li>
			<a href="nsr_search_pictures.php?validator={$v.validator}">{$v.validator} ({$v.total} / {$v.taxon_count})</a>
		</li>
	{/foreach}
	</ul>
	<p>
		<a href="nsr_validators.php"><i>{t}Bekijk volledige lijst{/t}</i></a>
	</p>
</div>

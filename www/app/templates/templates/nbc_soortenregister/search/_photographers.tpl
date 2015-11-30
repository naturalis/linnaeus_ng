<div class="top5">
	<h2>{t}Top 5 fotografen{/t}</h2>
	<h4>{t}Fotograaf (foto’s/soorten){/t}</h4>
	<ul>
	{foreach $photographers v}
		<li>
			<a href="nsr_search_pictures.php?photographer={$v.photographer}">{$v.photographer} ({$v.total} / {$v.taxon_count})</a>
		</li>
	{/foreach}
	</ul>
	<p>
		<a href="nsr_photographers.php"><i>{t}Bekijk volledige lijst{/t}</i></a>
	</p>
</div>

<div class="top5">
	<h2>Top 5 fotografen</h2>
	<h4>Fotograaf (foto’s/soorten)</h4>
	<ul>
	{foreach from=$photographers item=v name=foo}
		{if $smarty.foreach.foo.index < 5}
		<li>
			<a href="nsr_search_pictures.php?photographer={$v.photographer}">{$v.photographer} ({$v.total} / {$v.taxon_count})</a>
		</li>
		{/if}
	{/foreach}
	</ul>
	<p>
		<a href="nsr_photographers.php"><i>Bekijk volledige lijst</i></a>
	</p>
</div>

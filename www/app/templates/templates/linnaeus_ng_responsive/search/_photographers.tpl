<div class="top5">
	<h2>{t}Top 5 fotografen{/t}</h2>
	<h4>{t}Fotograaf (foto’s/soorten){/t}</h4>
	<ul>
	{foreach from=$photographers item=v name=foo}
		{if $smarty.foreach.foo.index < 5}
		<li>
			<a href="nsr_search_pictures.php?photographer={$v.photographer}">{$v.photographer} 
				<div class="count">({$v.total} / {$v.taxon_count})</div>
			</a>
		</li>
		{/if}
	{/foreach}
	</ul>
	<a href="nsr_photographers.php"><i>{t}Bekijk volledige lijst{/t}</i></a>
</div>


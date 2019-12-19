<div class="alphabet">
	{assign var=foo value=$alpha|@count}	
	{foreach $alpha v k}
		{if $letter==$v}
			<a class="alphabet-active-letter" href="contents.php?letter={$v}">{$v|upper}</a>
		{else}
			<a class="alphabet-letter" href="contents.php?letter={$v}">{$v|upper}</a>
		{/if}
	{/foreach}
</div>


<div class="alphabet">
	{assign var=foo value=$alpha|@count}
	{foreach $alpha v k}
		{if $letter==$v}
			<span class="alphabet-active-letter" style="width: {math equation="100/x" x=$foo}%"  href="contents.php?letter={$v}">{$v}</span>
		{else}
			<a class="alphabet-letter" style="width: {math equation="100/x" x=$foo}%"  href="contents.php?letter={$v}">{$v}</a>
		{/if}
	{/foreach}
</div>

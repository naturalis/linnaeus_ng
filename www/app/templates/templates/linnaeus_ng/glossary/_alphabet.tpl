<div id="alphabet">
	{assign var=foo value=$alpha|@count}	
	
	{foreach from=$alpha key=k item=v}
		{if $letter==$v}
			<a class="alphabet-active-letter" style="width: {math equation="100/x" x=$foo}%" href="contents.php?letter={$v}">{$v|upper}</a>
		{else}
			<a class="alphabet-letter" style="width: {math equation="100/x" x=$foo}%" href="contents.php?letter={$v}">{$v|upper}</a>
		{/if}
	{/foreach}
</div>


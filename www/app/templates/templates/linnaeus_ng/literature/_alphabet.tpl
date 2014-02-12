<div id="alphabet">
	{foreach from=$alpha key=k item=v}
		{if $letter==$v}
			<span class="alphabet-active-letter" href="contents.php?letter={$v}">{$v}</span>
		{else}
			<a class="alphabet-letter" href="contents.php?letter={$v}">{$v}</a>
		{/if}
	{/foreach}
</div>

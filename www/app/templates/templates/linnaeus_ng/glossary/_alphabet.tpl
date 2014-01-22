<div id="alphabet">
	{foreach from=$alpha key=k item=v}
	{if $letter==$v}
	<span class="letter-active">{$v}</span>
	{else}
	<a class="letter" href="?letter={$v}">{$v}</a>
	{/if}
	{/foreach}
</div>

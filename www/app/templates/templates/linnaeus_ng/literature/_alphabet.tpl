{literal}
<style>
.letter-active {
	font-weight:bold;
}
</style>
{/literal}
<div id="alphabet">
	{foreach from=$alpha key=k item=v}
	{if $letter==$v}
	<a class="letter-active" href="contents.php?letter={$v}">{$v}</a>
	{else}
	<a class="letter" href="contents.php?letter={$v}">{$v}</a>
	{/if}
	{/foreach}
</div>
<br />

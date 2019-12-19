{include file="../shared/header.tpl"}

{if $alpha}
<div class="alphabet">
	{foreach $alpha v k}
	{if $letter==$v}
	<span class="letter-active">{$v}</span>
	{else}
	<a class="letter" href="?letter={$v}">{$v}</a>
	{/if}
	{/foreach}
</div>
{/if}

<div id="page-main">
	{if !$alpha}
	{t}No pages have been defined in this module.{/t}
	{else}
	{foreach $refs v k}
	<a class="topic" href="../module/topic.php?id={$v.id}">{$v.topic}</a>
	<br />
	{/foreach}
	{/if}
</div>

{include file="../shared/footer.tpl"}

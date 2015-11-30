{include file="../shared/header.tpl"}

{if $alpha}
<div id="alphabet">
	{foreach $alpha v k}
	{if $letter==$v}
	<span class="letter-active">{$v}</span>
	{else}
	{if $useJavascriptLinks}
	<span class="letter" onclick="goAlpha('{$v}')">{$v}</span>
	{else}
	<a class="letter" href="?letter={$v}">{$v}</a>
	{/if}
	{/if}
	{/foreach}
</div>
{/if}

<div id="page-main">
	{if !$alpha}
	{t}No pages have been defined in this module.{/t}
	{else}
	{foreach $refs v k}
	{if $useJavascriptLinks}
	<span class="topic" onclick="goModuleTopic({$v.id})">{$v.topic}</span>
	{else}
	<a class="topic" href="../module/topic.php?id={$v.id}">{$v.topic}</a>
	{/if}
	<br />
	{/foreach}
	{/if}
</div>

{include file="../shared/footer.tpl"}

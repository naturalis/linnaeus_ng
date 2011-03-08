{include file="../shared/header.tpl"}
{include file="../shared/messages.tpl"}

{if $alpha}
<div id="alphabet">
	{foreach from=$alpha key=k item=v}
	{if $letter==$v}
	<span class="letter-active">{$v}</span>
	{else}
	<span class="letter" onclick="goAlpha('{$v}')">{$v}</span>
	{/if}
	{/foreach}
</div>
{/if}

<div id="page-main">
	{if !$alpha}
	{t}No pages have been defined in this module.{/t}
	{else}
	{foreach from=$refs key=k item=v}
	<span class="topic" onclick="goModuleTopic({$v.id})">{$v.topic}</span><br />
	{/foreach}
	{/if}
</div>

{include file="../shared/footer.tpl"}

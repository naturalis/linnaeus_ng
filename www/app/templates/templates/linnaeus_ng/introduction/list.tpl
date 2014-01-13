{include file="../shared/header.tpl"}

<div id="page-main">
	{foreach from=$refs key=k item=v}

	{if $useJavascriptLinks}
	<span class="topic" onclick="goIntroductionTopic({$v.id})">{$v.topic}</span>
	{else}
	<a class="topic" href="../introduction/topic.php?id={$v.id}">{$v.topic}</a>
	{/if}
	<br />

	{/foreach}
</div>

{include file="../shared/footer.tpl"}

{include file="../shared/header.tpl"}

<div id="page-main">
	{foreach from=$refs key=k item=v}
	<span class="topic" onclick="goIntroductionTopic({$v.id})">{$v.topic}</span><br />
	{/foreach}
</div>

{include file="../shared/footer.tpl"}

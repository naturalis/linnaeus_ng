{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">

<ul>
	<li><a href="update.php">{t}Update hotwords{/t}</a></li>
	<li><a href="browse.php">{t}Browse all hotwords{/t}</a></lix>
	{foreach item=v from=$controllers}
	<li><a href="browse.php?c={$v.controller}">{t}Browse hotwords in {/t}{$v.controller}</a> ({$v.tot})</li>
	{/foreach}
</ul>

</div>

{include file="../shared/admin-footer.tpl"}
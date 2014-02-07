{include file="../shared/header.tpl"}
{include file="_alphabet.tpl"}

<div id="page-main">
{if $alpha|@count==0}
{t}(no references have been defined){/t}
{else}
	<ul>
	{foreach from=$gloss item=v}
		<li><a href="term.php?id={$v.id}">{$v.term}</a></li>
	{/foreach}
	</ul>
{/if}
</div>

{include file="../shared/footer.tpl"}
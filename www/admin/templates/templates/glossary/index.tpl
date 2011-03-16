{include file="../shared/admin-header.tpl"}
<div id="page-main">
<ul>
{if $totalCount > 0}
	<li><a href="browse.php">{t}Browse glossary terms{/t}</a></li>
	<li><a href="search.php">{t}Search glossary terms{/t}</a></li>
{/if}
	<li><a href="edit.php">{t}Create new glossary term{/t}</a></li>
</ul>

</div>

{include file="../shared/admin-footer.tpl"}

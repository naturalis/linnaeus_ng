{include file="../shared/admin-header.tpl"}

<div id="page-main">
<ul class="admin-list">
{section name=i loop=$taxa}
<li>
	<a href="?project_id={$projects[i].id}">{$taxa[i].taxon}</a>
</li>
{/section}

</ul>
</div>

{include file="../shared/admin-footer.tpl"}

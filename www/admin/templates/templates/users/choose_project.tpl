{include file="../shared/admin-header.tpl"}

<div id="admin-titles">
<span id="admin-title">Administration menu</span><br />
<span id="admin-subtitle">Choose you project</span>
</div>

<div id="admin-main">
<ul class="admin-list">
{section name=project loop=$projects}
<li><a href="?project_id={$projects[project].id}">{$projects[project].name}</a></li>
{/section}

</ul>
</div>

{include file="../shared/admin-bottom.tpl"}
{include file="../shared/admin-footer.tpl"}

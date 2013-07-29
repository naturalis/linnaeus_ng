{include file="../shared/admin-header.tpl"}

<div id="page-main">
<ul class="admin-list">
	<li><a href="data.php">{t}Manage basic project information{/t}</a></li>
	<li><a href="modules.php">{t}Manage project modules{/t}</a></li>
	<li><a href="collaborators.php">{t}Assign collaborators to modules{/t}</a></li>
	<li><a href="settings.php">{t}Manage system settings{/t}</a></li>
	<li><a href="get_info.php">{t}Entity count for current project{/t}</a></li>
</ul>
<ul class="admin-list">
	<li><a href="../import/merge.php">{t}Merge other project into current project{/t}</a></li>
</ul>
</div>

{include file="../shared/admin-footer.tpl"}

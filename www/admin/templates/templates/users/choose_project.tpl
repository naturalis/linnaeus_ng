{include file="../shared/admin-header.tpl"}

{if !empty($messages)}
<div id="page-block-messages">
{section name=i loop=$messages}
<span class="admin-message">{$messages[i]}</span><br />
{/section}
</div>
{/if}

<div id="page-main">
{t}Select a project to work on:{/t}
<ul class="admin-list">
{section name=i loop=$projects}
{if $projects[i].active == '1' && $projects[i].id != 0}
<li>
	<a href="?project_id={$projects[i].id}">{$projects[i].name}</a>
{if $session.project.id==$projects[i].id}<span title="current active project">{t}(current){/t}</span>{/if}
{/if}
</li>
{/section}
</ul>

{if $isSysAdmin}

{t}System administration tasks:{/t}
<ul>
	<li><a href="../../views/projects/create.php">{t}Create a project{/t}</a></li>
	<li><a href="../../views/projects/delete.php">{t}Delete a project{/t}</a></li>
	<li><a href="../../views/import/linnaeus2.php">{t}Import Linnaeus 2 data{/t}</a></li>
	<!-- li><a href="">{t}Set rights{/t}</a></li -->
</ul>

{/if}


</div>

{include file="../shared/admin-footer.tpl"}

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
{if $projects[i].active == '1'}
<li>
	<a href="?project_id={$projects[i].id}">{$projects[i].name}</a>
{if $session.project.id==$projects[i].id}<span title="current active project">{t}(current){/t}</span>{/if}
{/if}
</li>
{/section}

</ul>
</div>

{include file="../shared/admin-footer.tpl"}

{include file="../shared/admin-header.tpl"}

{if !empty($messages)}
<div id="admin-messages">
{section name=i loop=$messages}
<span class="admin-message">{$messages[i]}</span><br />
{/section}
</div>
{/if}

<div id="admin-main">
<ul class="admin-list">
{section name=i loop=$projects}
<li>
	<a href="?project_id={$projects[i].id}">{$projects[i].sys_name}</a>
{if $session._current_project_id==$projects[i].id}<span title="current active project">*</span>{/if}
</li>
{/section}

</ul>
</div>

{include file="../shared/admin-footer.tpl"}

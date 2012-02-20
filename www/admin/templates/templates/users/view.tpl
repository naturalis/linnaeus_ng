{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">
{if $user}
<table>
	<tr>
		<td>{t}Username:{/t}</td><td>{$user.username}</td>
	</tr>
	<tr>
		<td>{t}First name:{/t}</td><td>{$user.first_name}</td>
	</tr>
	<tr>
		<td>{t}Last name:{/t}</td><td>{$user.last_name}</td>
	</tr>
	<tr>
		<td>{t}E-mail address:{/t}</td>
		<td>{$user.email_address}</td>
	</tr>
	<tr>
		<td>{t}Timezone:{/t}</td>
		<td>{$zone.timezone}{$zone.timezone} ({$zone.locations})</td>
	</tr>
	<tr>
		<td>{t}E-mail notifications:{/t}</td>
		<td>{if $user.email_notifications=='1'}y{else}n{/if}</td>
	</tr>
	<tr>
		<td>{t}Active:{/t}</td>
		<td>{if $user.active=='1'}y{else}n{/if}</td>
	</tr>
	<tr>
		<td>{t}Role in current project:{/t}</td>
		<td>
			{if $currentRole.role.role}
				{$currentRole.role.role}
				[<span class="a" onclick="userRemoveFromProject({$user.id},'view.php?id={$user.id}');">{t}remove from project{/t}</span>]
			{else}
				{t}(none){/t} [<span class="a" onclick="userAddToProject({$user.id},'view.php?id={$user.id}');">{t}add{/t}</span>] [A]
			{/if}
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="button" value="{t}edit{/t}" onclick="window.open('edit.php?id={$user.id}','_self');" />
			<input type="button" value="{t}delete{/t}" onclick="window.open('edit.php?id={$user.id}','_self');" />!!!!!
			
			{if $currentUserRoleId==1 || $currentUserRoleId==2 || $session.admin.user.superuser==1}{/if}
			
		</td>
	</tr>
</table>
<p>
	{t _s1=$session.admin.project.title}Module assignment for this user in "%s":{/t}

<table>
{foreach item=v from=$modules.modules}
	<tr><td>{$v.module}</td>
{if $v.isAssigned}
	<td class="userModuleAssigned">assigned</td><td>[<span class="a" onclick="userRemoveFromModule({$user.id},{$v.module_id},'view.php?id={$user.id}');">{t}remove{/t}</span>]</td>
{else}
	<td class="userModuleUnassigned">not assigned</td><td>[<span class="a" onclick="userAddToModule({$user.id},{$v.module_id},'view.php?id={$user.id}');">{t}assign{/t}</span>]</td>
{/if}
</tr>
{/foreach}
{foreach item=v from=$modules.freeModules}
	<tr><td>{$v.module}</td>
{if $v.isAssigned}
	<td class="userModuleAssigned">assigned</td><td>[<span class="a" onclick="userRemoveFromFreeModule({$user.id},{$v.id},'view.php?id={$user.id}');">{t}remove{/t}</span>]</td>
{else}
	<td class="userModuleUnassigned">not assigned</td><td>[<span class="a" onclick="userAddToFreeModule({$user.id},{$v.id},'view.php?id={$user.id}');">{t}assign{/t}</span>]</td>
{/if}
</tr>
{/foreach}
</table>
</p>

{/if}
</div>
<form method="post" action="" id="theForm">
<input type="hidden" name="id" value="{$user.id}" />
<input type="hidden" id="action" name="action" value="" />
</form>

{include file="../shared/admin-footer.tpl"}

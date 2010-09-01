{include file="../shared/admin-header.tpl"}

<div id="admin-main">
<div class="admin-text-block">
Select the standard modules you wish to use in your project:<br />
<table>
{section name=i loop=$modules}
	<tr>
	{if $modules[i].active=='y'}
		<td title="in use in your project" class="admin-td-module-inuse">&nbsp;</td>
		<td style="width:100px">
			<span class="admin-td-module-title-inuse" id="cell-{$modules[i].id}d">
	{else}
		<td title="in use in your project, but inactive" class="admin-td-module-inactive" >&nbsp;</td>
		<td style="width:100px">
			<span class="admin-td-module-title-deactivated" id="cell-{$modules[i].id}d">
	{/if}
				<span class="admin-td-module-title">{$modules[i].module}</span>
			</span>
		</td>
		<td>
			<span onclick="toggleModuleUsers({$modules[i].id});" style="cursor:pointer"><span id="cell-{$modules[i].id}n">{$modules[i].collaborators|@count}</span> collaborators</span>
		</td>
	</tr>
	<tr id="users-{$modules[i].id}" class="admin-modusers-hidden">
		<td colspan="3">
			<table>
			{section name=j loop=$users}
				{assign var=x value=$users[j].id}
				<tr>
					<td style="width:15px;">
					</td>
				{if $modules[i].collaborators[$x].user_id == $users[j].id}
					<td 
						id="cell-{$modules[i].id}-{$users[j].id}a"
						class="admin-td-module-title-inuse">
							{$users[j].first_name} {$users[j].last_name}
						</td>
					<td>{$users[j].role}</td>
					<td 
						title="remove collaborator" 
						class="admin-td-moduser-remove"
						id="cell-{$modules[i].id}-{$users[j].id}b"
						onclick="moduleUserAction(this,{$modules[i].id},{$users[j].id},'remove')">
					</td>
				{else}
					<td
						id="cell-{$modules[i].id}-{$users[j].id}a"
						class="">
						{$users[j].first_name} {$users[j].last_name}
					</td>
					<td>{$users[j].role}</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-{$modules[i].id}-{$users[j].id}b"
						onclick="moduleUserAction(this,{$modules[i].id},{$users[j].id},'add')">
					</td>
				{/if}
				</tr>
			{/section}			
			</table>
		</td>
	</tr>
{/section}
</table>
</div>

<br />

<div class="admin-text-block">
Besides these standard modules, you can add up to five extra content modules to your project:<br />
<table>
{assign var=n value=1}
{section name=i loop=$free_modules}
	<tr id="row-f{$free_modules[i].id}">
	{if $free_modules[i].active=='y'}
		<td title="in use in your project" class="admin-td-module-inuse" id="cell-f{$free_modules[i].id}a">&nbsp;</td>
		<td>
			<span class="admin-td-module-title-inuse" id="cell-f{$free_modules[i].id}d">
	{else}
		<td title="in use in your project, but inactive" class="admin-td-module-inactive" >&nbsp;</td>
		<td>
			<span class="admin-td-module-title-deactivated" id="cell-f{$free_modules[i].id}d">
	{/if}
				<span class="admin-td-module-title">{$free_modules[i].module}</span>
			</span>
		</td>
</tr>
{/section}
</table>

</div>

</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

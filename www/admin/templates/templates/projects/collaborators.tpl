{include file="../shared/admin-header.tpl"}

<div id="page-main">
	<div class="text-block">

	Assign collaborators to work on modules:<br />

	<table>
	{section name=i loop=$modules}
		<tr>
		{if $modules[i].active=='y'}
			<td title="in use in your project" class="cell-module-in-use">&nbsp;</td>
			<td style="width:100px">
				<span class="cell-module-title-in-use" id="cell-{$modules[i].module_id}d">
		{else}
			<td title="in use in your project, but inactive" class="cell-module-inactive" >&nbsp;</td>
			<td style="width:100px">
				<span class="cell-module-title-inactive" id="cell-{$modules[i].module_id}d">
		{/if}
					<span class="cell-module-title">{$modules[i].module}</span>
				</span>
			</td>
			<td>
				<span onclick="moduleToggleModuleUserBlock({$modules[i].module_id});" class="modusers-block-toggle">
					<span id="cell-{$modules[i].module_id}n">{$modules[i].collaborators|@count}</span> 
					collaborators
				</span>
			</td>
		</tr>
		<tr id="users-{$modules[i].module_id}" class="modusers-block-hidden">
			<td colspan="3">
				<table>
				{section name=j loop=$users}
					{assign var=x value=$users[j].id}
					<tr>
						<td class="modusers-block-buffercell"></td>
					{if $modules[i].collaborators[$x].user_id == $users[j].id}
						<td 
							id="cell-{$modules[i].module_id}-{$users[j].id}a"
							class="cell-module-title-in-use">
							{$users[j].first_name} {$users[j].last_name}
						</td>
						<td>{$users[j].role}</td>
						<td 
							title="remove collaborator" 
							class="cell-moduser-remove"
							id="cell-{$modules[i].module_id}-{$users[j].id}b"
							onclick="moduleChangeModuleUserStatus(this,{$modules[i].module_id},{$users[j].id},'remove')">
						</td>
					{else}
						<td
							id="cell-{$modules[i].module_id}-{$users[j].id}a"
							class="">
							{$users[j].first_name} {$users[j].last_name}
						</td>
						<td>{$users[j].role}</td>
						<td
							title="add collaborator" 
							class="cell-moduser-inactive"
							id="cell-{$modules[i].module_id}-{$users[j].id}b"
							onclick="moduleChangeModuleUserStatus(this,{$modules[i].module_id},{$users[j].id},'add')">
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

	<div class="text-block">

	Assign collaborators to work on free modules:<br />

	<table>
	{assign var=n value=1}
	{section name=i loop=$free_modules}
		<tr id="row-f{$free_modules[i].id}">
		{if $free_modules[i].active=='y'}
			<td
				title="in use in your project" 
				class="cell-module-in-use" 
				id="cell-f{$free_modules[i].id}a">&nbsp;
				
			</td>
			<td>
				<span class="cell-module-title-in-use" id="cell-f{$free_modules[i].id}d">
		{else}
			<td 
				title="in use in your project, but inactive" 
				class="cell-module-inactive">&nbsp;
				
			</td>
			<td>
				<span class="cell-module-title-inactive" id="cell-f{$free_modules[i].id}d">
		{/if}
					<span class="cell-module-title">{$free_modules[i].module}</span>
				</span>
			</td>
			<td>
				<span 
					onclick="moduleToggleModuleUserBlock('f'+{$free_modules[i].id});" 
					 class="modusers-block-toggle">
						<span id="cell-f{$free_modules[i].id}n">
							{$free_modules[i].collaborators|@count}
						</span> 
					collaborators
				</span>
			</td>
		</tr>
		<tr id="users-f{$free_modules[i].id}" class="modusers-block-hidden">
			<td colspan="3">
				<table>
				{section name=j loop=$users}
					{assign var=x value=$users[j].id}
					<tr>
						<td class="modusers-block-buffercell"></td>
					{if $free_modules[i].collaborators[$x].user_id == $users[j].id}
						<td 
							id="cell-f{$free_modules[i].id}-{$users[j].id}a"
							class="cell-module-title-in-use">
							{$users[j].first_name} {$users[j].last_name}
							</td>
						<td>{$users[j].role}</td>
						<td 
							title="remove collaborator" 
							class="cell-moduser-remove"
							id="cell-f{$free_modules[i].id}-{$users[j].id}b"
							onclick="moduleChangeModuleUserStatus(this,{$free_modules[i].id},{$users[j].id},'remove')">
						</td>
					{else}
						<td
							id="cell-f{$modules[i].module_id}-{$users[j].id}a"
							class="">
							{$users[j].first_name} {$users[j].last_name}
						</td>
						<td>{$users[j].role}</td>
						<td
							title="add collaborator" 
							class="cell-moduser-inactive"
							id="cell-f{$free_modules[i].id}-{$users[j].id}b"
							onclick="moduleChangeModuleUserStatus(this,{$free_modules[i].id},{$users[j].id},'add')">
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

</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
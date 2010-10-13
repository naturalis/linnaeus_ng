{include file="../shared/admin-header.tpl"}

<div id="page-main">
<table>
	<tr>
		<td>Username:</td><td>{$data.username}</td>
	</tr>
	<tr>
		<td>First_name:</td><td>{$data.first_name}</td>
	</tr>
	<tr>
		<td>Last_name:</td><td>{$data.last_name}</td>
	</tr>
	<tr>
		<td>E-mail address:</td>
		<td>{$data.email_address}</td>
	</tr>
	<tr>
		<td>Language:</td>
		<td>{$data.language}</td>
	</tr>
	<tr>
		<td>Timezone:</td>
		<td>{$zone.timezone}</td>
	</tr>
	<tr>
		<td>E-mail notifications:</td>
		<td>{if $data.email_notifications=='1'}y{else}n{/if}</td>
	</tr>
	<tr>
		<td>Active:</td>
		<td>{if $data.active=='1'}y{else}n{/if}</td>
	</tr>
	<tr>
		<td>Role in current project:</td>
		<td>{$userRole.role.role}</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2">Assigned to the following modules:</td>
	</tr>

{section name=i loop=$modules}
{if $modules[i].isAssigned}
	<tr><td colspan="2">&#149; {$modules[i].module}</td></tr>
{/if}
{/section}
{section name=i loop=$freeModules}
{if $freeModules[i].isAssigned}
<tr><td colspan="2">&#149; {$freeModules[i].module}</td></tr>
{/if}
{/section}
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>	
	<tr>
		<td colspan="2">
			<input type="button" value="edit" onclick="window.open('edit.php?id={$data.id}','_self');" />
			<input type="button" value="back" onclick="window.open('index.php','_top')" />
		</td>
	</tr>
</table>
</div>

</div>


{include file="../shared/admin-footer.tpl"}

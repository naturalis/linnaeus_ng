{include file="../shared/admin-header.tpl"}

<div id="page-main">
<table>
	<tr>
		<td>{t}Username:{/t}</td><td>{$data.username}</td>
	</tr>
	<tr>
		<td>{t}First name:{/t}</td><td>{$data.first_name}</td>
	</tr>
	<tr>
		<td>{t}Last name:{/t}</td><td>{$data.last_name}</td>
	</tr>
	<tr>
		<td>{t}E-mail address:{/t}</td>
		<td>{$data.email_address}</td>
	</tr>
	<tr>
		<td>{t}Timezone:{/t}</td>
		<td>{$zone.timezone}: {$zone.locations}</td>
	</tr>
	<tr>
		<td>{t}E-mail notifications:{/t}</td>
		<td>{if $data.email_notifications=='1'}y{else}n{/if}</td>
	</tr>
	<tr>
		<td>{t}Active:{/t}</td>
		<td>{if $data.active=='1'}y{else}n{/if}</td>
	</tr>
	<tr>
		<td>{t}Role in current project:{/t}</td>
		<td>{$userRole.role.role}</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2">{t}The following modules have been assigned to this user:{/t}</td>
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
			<input type="button" value="{t}edit{/t}" onclick="window.open('edit.php?id={$data.id}','_self');" />
			<input type="button" value="{t}back{/t}" onclick="window.open('index.php','_top')" />
		</td>
	</tr>
</table>
</div>
<form method="post" action="" id="theForm">
<input type="hidden" name="id" value="{$data.id}" />
<input type="hidden" id="action" name="action" value="" />
</form>

{include file="../shared/admin-footer.tpl"}

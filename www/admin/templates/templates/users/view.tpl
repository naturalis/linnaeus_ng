{include file="../shared/admin-header.tpl"}

<div id="page-main">
<table>
	<tr>
		<td>first_name:</td><td>{$data.first_name}</td>
	</tr>
	<tr>
		<td>last_name:</td><td>{$data.last_name}</td>
	</tr>
	<tr>
		<td>username:</td><td>{$data.username}</td>
	</tr>
	<tr>
		<td>email address:</td>
		<td>{$data.email_address}</td>
	</tr>
	<tr>
		<td>role in current project:</td>
		<td>{$userRole.role.role}</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="button" value="edit" onclick="window.open('edit.php?id={$data.id}','_self');" />
			<input type="button" value="back" onclick="window.open('{$session.system.referer.url}','_top')" />
		</td>
	</tr>
</table>
</div>

{include file="../shared/admin-footer.tpl"}

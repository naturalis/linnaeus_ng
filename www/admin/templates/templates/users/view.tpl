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
		<td>gender:</td>
		<td>{$data.gender}</td>
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
			<input type="button" value="Back" onclick="window.open('user_overview.php','_self');" />
			<input type="button" value="Edit" onclick="window.open('edit.php?id={$data.id}','_self');" />
		</td>
	</tr>
</table>
</div>

{include file="../shared/admin-footer.tpl"}

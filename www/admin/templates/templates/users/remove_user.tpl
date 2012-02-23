{literal}
<style>
#addUserDialog tr {
	height:25px;
}
#addUserDialog #page-block-errors {
	width:300px;
}
</style>
{/literal}
<div id="addUserDialog">
<form method="post" action="remove_user.php">
<input type="hidden" value="{$uid}" name="uid" />
<input type="hidden" value="remove" name="action" />
<input type="hidden" value="{$returnUrl}" name="returnUrl" />
<table>
	<tr>
		<td>
			{t}Remove user{/t} {$user.first_name} {$user.last_name} 
			{t}with the role of{/t} {$role.role} 
			{t}from project{/t} "{$session.admin.project.title}"?
		</td>
	</tr>
	<tr>
		<td>
			<input type="submit" value="{t}remove{/t}" />&nbsp;&nbsp;&nbsp;<input type="button" value="{t}cancel{/t}" onclick="$('#dialog-close').click();"/>
		</td>
	</tr>
</table>
{include file="../shared/admin-messages.tpl"}
</div>
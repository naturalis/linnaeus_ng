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
<form method="post" action="remove_user_module.php">
<input type="hidden" value="{$requestData.uid}" name="uid" />
<input type="hidden" value="{$requestData.modId}" name="modId" />
<input type="hidden" value="{$requestData.type}" name="type" />
<input type="hidden" value="{$requestData.returnUrl}" name="returnUrl" />
<input type="hidden" value="remove" name="action" />
<table>
	<tr>
		<td>
			{t}Remove user{/t} "{$user.first_name} {$user.last_name}" 
			{t}from the module{/t} "{$module}"
			{t}in the project{/t} "{$session.admin.project.title}"?
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
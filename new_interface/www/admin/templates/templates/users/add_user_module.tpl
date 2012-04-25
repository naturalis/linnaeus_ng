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
<form method="post" action="add_user_module.php">
<input type="hidden" value="{$requestData.uid}" name="uid" />
<input type="hidden" value="{$requestData.modId}" name="modId" />
<input type="hidden" value="{$requestData.type}" name="type" />
<input type="hidden" value="{$requestData.returnUrl}" name="returnUrl" />
<input type="hidden" value="add" name="action" />
<table>
	<tr>
		<td>
			{t}Assign user{/t} {$user.first_name} {$user.last_name}
			{t}to the module{/t} "{$module}"
			{t}in the project{/t} "{$session.admin.project.title}"?
		</td>
	</tr>
	<tr>
		<td>
			<input type="submit" value="{t}assign{/t}" />&nbsp;&nbsp;&nbsp;<input type="button" value="{t}cancel{/t}" onclick="$('#dialog-close').click();"/>
		</td>
	</tr>
</table>
{include file="../shared/admin-messages.tpl"}
</div>
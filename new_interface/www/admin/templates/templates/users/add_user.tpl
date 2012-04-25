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
<form method="post" action="add_user.php">
<input type="hidden" value="{$user.id}" name="id" />
<input type="hidden" value="save" name="action" />
<input type="hidden" value="{$returnUrl}" name="returnUrl" />
<table>
	<tr>
		<td>
{if $user}
{t}Add user{/t} {$user.first_name} {$user.last_name}<br />
{/if}
{t}to project{/t} "{$session.admin.project.title}" <br />
{if $roles}
{t}in the role of{/t}
	<select name="role_id">
	{section name=i loop=$roles}
		<option 
			title="{$roles[i].role}: {$roles[i].description}{if $roles[i].id==$userRole.role.id} (current){/if}" 
			value="{$roles[i].id}"
			{if $roles[i].id==$userRole.role.id} selected class="option-selected" {/if}
		>{$roles[i].role}</option>
	{/section}
	</select><br />
{/if}
<input type="submit" value="{t}save{/t}" />&nbsp;&nbsp;&nbsp;<input type="button" value="{t}cancel{/t}" onclick="$('#dialog-close').click();"/>
		</td>
	</tr>
</table>
{include file="../shared/admin-messages.tpl"}
</div>
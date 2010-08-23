{include file="../shared/admin-header.tpl"}

<div id="admin-titles">
<span id="admin-title">Administration menu</span><br />
<span id="admin-subtitle">User management: create user</span>
</div>

<div id="admin-main">
<form method="post" action="" name="theForm" id="theForm">
<input name="id" value="-1" type="hidden" />
{if $check==true}
<input name="checked" id="checked" value="1" type="hidden" />
{/if}
<table>
	<tr>
		<td>username</td><td>
		{if $check==true}
			{$data.username}
		{else}
			<input type="text" name="username" value="{$data.username}" maxlength="16" />
		{/if}
		</td>
	</tr>
	<tr>
		<td>password</td><td><input type="text" name="password" value="{$data.password}" maxlength="16" /></td>
	</tr>
	<tr>
		<td>password (repeat)</td><td><input type="text" name="password_2" value="{$data.password_2}" maxlength="16" /></td>
	</tr>
	<tr>
		<td>first_name</td><td><input type="text" name="first_name" value="{$data.first_name}" maxlength="16" /></td>
	</tr>
	<tr>
		<td>last_name</td><td><input type="text" name="last_name" value="{$data.last_name}" maxlength="16" /></td>
	</tr>
	<tr>
		<td>gender</td>
		<td>
			<label for="gender-f"><input type="radio" id="gender-f" name="gender" value="f" {if $data.gender!='m'}checked{/if}/>f</label>
			<label for="gender-m"><input type="radio" id="gender-m" name="gender" value="m" {if $data.gender=='m'}checked{/if} />m</label>
		</td>
	</tr>
	<tr>
		<td>email_address</td><td><input type="text" name="email_address" value="{$data.email_address}" maxlength="64" /></td>
	</tr>
	<tr>
		<td colspan="2">
		{if $check==true}
			<input type="button" value="back" onclick="document.getElementById('checked').value='0';document.getElementById('theForm').submit()" />
		{/if}
			<input type="submit" value="save" />
		</td>
	</tr>
</table>

</form>

</div>

<div id="admin-messages">
{if !empty($errors)}
{section name=error loop=$errors}
<span class="admin-message-error">{$errors[error]}</span><br />
{/section}
{/if}
</div>

{include file="../shared/admin-bottom.tpl"}
{include file="../shared/admin-footer.tpl"}

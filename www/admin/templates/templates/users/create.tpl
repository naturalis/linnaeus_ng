{include file="../shared/admin-header.tpl"}

<div id="admin-main">
{if $check==true}
Please verify the data below. Click 'Save' to save the user data; or 'Back' to return to the previous screen.
{/if}
<form method="post" action="" name="theForm" id="theForm">
<input name="id" value="-1" type="hidden" />
{if $check==true}
<input name="checked" id="checked" value="1" type="hidden" />
{/if}
<table>
	<tr>
		<td>username:</td><td>
		{if $check==true}
			{$data.username}
		{else}
			<input type="text" name="username" value="{$data.username}" maxlength="16" />
		{/if}
		</td>
	</tr>
	<tr>
		<td>password:</td><td>
		{if $check==true}
			{$data.password}
		{else}
			<input type="password" name="password" value="{$data.password}" maxlength="16" /></td>
	</tr>
	<tr>
		<td>password (repeat)</td><td><input type="password" name="password_2" value="{$data.password_2}" maxlength="16" />
		{/if}
		</td>
	</tr>
	<tr>
		<td>first_name:</td><td>
		{if $check==true}
			{$data.first_name}
		{else}
			<input type="text" name="first_name" value="{$data.first_name}" maxlength="16" />
		{/if}
		</td>
	</tr>
	<tr>
		<td>last_name:</td><td>
		{if $check==true}
			{$data.last_name}
		{else}
			<input type="text" name="last_name" value="{$data.last_name}" maxlength="16" />
		{/if}
		</td>
	</tr>
	<tr>
		<td>gender:</td>
		<td>
		{if $check==true}
			{$data.gender}
		{else}
			<label for="gender-f"><input type="radio" id="gender-f" name="gender" value="f" {if $data.gender!='m'}checked="checked"{/if}/>f</label>
			<label for="gender-m"><input type="radio" id="gender-m" name="gender" value="m" {if $data.gender=='m'}checked="checked"{/if} />m</label>
		{/if}
		</td>
	</tr>
	<tr>
		<td>email_address:</td><td>
		{if $check==true}
			{$data.email_address}
		{else}
			<input type="text" name="email_address" value="{$data.email_address}" maxlength="64" /></td>
		{/if}
	</tr>
	<tr>
		<td>role in current project:</td>
		<td>
		{if $check==true}
{section name=i loop=$roles}
{if $roles[i].id==$data.role_id}			{$roles[i].role}{/if}
{/section}
		{else}
		<select name="role_id">
{section name=i loop=$roles}
			<option title="{$roles[i].role}: {$roles[i].description}" value="{$roles[i].id}"{if $roles[i].id==$data.role_id} selected class="option-selected" {/if}>{$roles[i].role}</option>
{/section}
		</select>
		{/if}
</td>
	</tr>
	<tr>
		<td colspan="2">
		{if $check==true}
			<input type="button" value="Back" onclick="document.getElementById('checked').value='-1';document.getElementById('theForm').submit()" />
		{/if}
			<input type="submit" value="Save" />
		</td>
	</tr>
</table>

</form>

</div>

{if !empty($errors)}
<div id="admin-errors">
{section name=error loop=$errors}
<span class="admin-message-error">{$errors[error]}</span><br />
{/section}
</div>
{/if}
{if !empty($messages)}
<div id="admin-messages">
{section name=i loop=$messages}
<span class="admin-message">{$messages[i]}</span><br />
{/section}
</div>
{/if}

{include file="../shared/admin-footer.tpl"}

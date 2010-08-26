{include file="../shared/admin-header.tpl"}

{if !empty($errors)}
<div id="admin-errors">
{section name=error loop=$errors}
<span class="admin-message-error">{$errors[error]}</span><br />
{/section}
</div>
{/if}

<div id="admin-main">
	<form method="post" action="login.php">
	<table>
		<tr><td colspan="2">Please enter your username and password and click 'Login'.</td></tr>
		<tr><td>your username:</td><td><input type="text" name="username" value="" maxlength="32" /></td></tr>
		<tr><td>your password:</td><td><input type="password" name="password" value="" maxlength="32" /><br /></td></tr>
		<tr><td colspan="2"><input type="submit" value="login" /></td></tr>
	</table>
	</form>
</div>

{include file="../shared/admin-footer.tpl"}

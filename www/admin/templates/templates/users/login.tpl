{assign var=breadcrumbs value=false}
{include file="../shared/admin-header.tpl"}

{if !empty($errors)}
<div id="page-block-errors">
{section name=error loop=$errors}
<span class="message-error">{$errors[error]}</span><br />
{/section}
</div>
{/if}

<div id="page-main">
<p>
	{t}Log in to administer your Linnaeus project{/t}
	<form method="post" action="login.php">
	<table id="login-table">
		<tr><td>{t}Your username:{/t}</td><td><input type="text" name="username" id="username" value="" maxlength="128" /></td></tr>
		<tr><td>{t}Your password:{/t}</td><td><input type="password" name="password" value="" maxlength="32" /></td></tr>
		<tr><td></td><td><label><input type="checkbox" name="remember_me" value="1" />{t}Remember me{/t}</label></td></tr>
		<tr><td colspan="2"><input type="submit" value="Log in" /></td></tr>
	</table>
	</form>
</p>
<p>
	{t}I forgot my password:{/t} <a href="password.php">{t}reset my password{/t}</a>.<br />
	{t}My password doesn't work, I have forgotten my username or my account may have been compromised: please{/t} <a href="mailto:{$support_email}?subject=Recover%20username%20Linnaeus%20NG">{t}contact the helpdesk at{/t} {$support_email}</a>.
</p>
<p>
	<a href="{$baseUrl}">{t}Back to{/t} {t}Linnaeus NG root{/t}</a>
</p>
</div>


<script type="text/JavaScript">
$(document).ready(function()
{
	$('#username').focus();
});
</script>

{include file="../shared/admin-footer.tpl"}

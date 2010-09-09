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
	<form method="post" action="login.php">
	<table>
		<tr><td colspan="2">Please enter your username and password and click 'Login'.</td></tr>
		<tr><td>Your username:</td><td><input type="text" name="username" id="username" value="" maxlength="32" /></td></tr>
		<tr><td>Your password:</td><td><input type="password" name="password" value="" maxlength="32" /><br /></td></tr>
		<tr><td colspan="2"><input type="submit" value="login" /></td></tr>
	</table>
	</form>
</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
	$('#username').focus();
});
</script>
{/literal}

{include file="../shared/admin-footer.tpl"}

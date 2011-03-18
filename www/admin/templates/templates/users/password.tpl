{assign var=breadcrumbs value=false}
{include file="../shared/admin-header.tpl"}

<div id="page-main">
<p>
	{t}To reset your password, enter you e-mail address and press "reset password":{/t}
	<form method="post" action="">
	<input type="hidden" name="rnd" value="{$rnd}" />
	<table id="login-table">
		<tr>
			<td>{t}Your e-mailaddress:{/t}</td>
			<td><input type="text" name="email" id="email" value="" maxlength="64" /></td>
			<td><input type="submit" value="Reset password" /></td>
		</tr>
	</table>
	</form>
</p>
<p>
<a href="login.php">{t}Back to login{/t}</a>
</p>
</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
	$('#email').focus();
});
</script>
{/literal}

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

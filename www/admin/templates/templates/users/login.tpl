{include file="../shared/admin-header.tpl"}

<div id="admin-titles">
<span id="admin-title">Administration</span><br />
<span id="admin-subtitle">Login page</span>
</div>

<div id="admin-messages">
{if !empty($errors)}
{section name=error loop=$errors}
<span class="admin-message-error">{$errors[error]}</span><br />
{/section}
{/if}
</div>

<div id="admin-main">
<form method="post" action="login.php">
your username:<input type="text" name="username" value="mdschermer" maxlength="32" /><br />
your password:<input type="text" name="password" value="balance" maxlength="32" /><br />
<input type="submit" value="login" />
</form>
</div>

<div id="admin-bottom">
</div>


{include file="../shared/admin-footer.tpl"}

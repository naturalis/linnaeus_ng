{assign var=breadcrumbs value=false}
{include file="../shared/admin-header.tpl"}

<div id="page-main">
    {if !$sent_email}
    <p>
        {t}To reset your password, enter your username and click "reset password":{/t}
        <form method="post" action="">
        <input type="hidden" name="rnd" value="{$rnd}" />
        <table id="login-table">
            <tr>
                <td>{t}Your username:{/t}</td>
                <td><input type="text" name="username" id="username" value="" maxlength="128" /></td>
                <td><input type="submit" value="Reset password" /></td>
            </tr>
        </table>
        </form>
    </p>
    {/if}
</div>

{include file="../shared/admin-messages.tpl"}

<div class="page-generic-div">

    <p>
        <a href="login.php">{t}Back to login{/t}</a>
    </p>

</div>

<script type="text/JavaScript">

noMessageFade=true;

$(document).ready(function()
{
	$('#email').focus();
});
</script>

{include file="../shared/admin-footer.tpl"}

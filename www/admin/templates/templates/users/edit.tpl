{include file="../shared/admin-header.tpl"}

<style>
table tr th {
	text-align:right;
}
</style>


<div id="page-main">

	{if $user}
    <h2>{t}edit{/t} {$user.first_name} {$user.last_name}</h2>
	{else}
    <h2>{t}new user{/t}</h2>
    {/if}

    
    <form method="post">
    <input name="id" value="{$user.id}" type="hidden" />
    <input type="hidden" name="action" id="action" value="save"  />
    <input type="hidden" name="rnd" value="{$rnd}" />
    
    <table>
        <tr>
            <th>{t}Username:{/t}</th>
            <td><input type="text" name="username" value="{$user.username}" /></td>
        </tr>
        <tr>
            <th>{t}First name:{/t}</th>
            <td><input type="text" name="first_name" value="{$user.first_name}" /></td>
        </tr>
        <tr>
            <th>{t}Last name:{/t}</th>
            <td><input type="text" name="last_name" value="{$user.last_name}" /></td>
        </tr>
        <tr>
            <th>{t}E-mail address:{/t}</th>
            <td><input type="text" name="email_address" value="{$user.email_address}" /></td>
        </tr>
        <tr>
            <th>{t}Active:{/t}</th>
            <td>
                <input type="radio" value="1" name="active" {if $user.active!='0'} checked="checked"{/if} />y
                <input type="radio" value="0" name="active" {if $user.active=='0'} checked="checked"{/if} />n
            </td>
        </tr>
        <tr>
            <th>{t}Password:{/t}</th>
            <td><input type="password" name="password" value="" /></td>
        </tr>
        <tr>
            <th>{t}Password (again):{/t}</th>
            <td><input type="password" name="password_repeat" value="" /></td>
        </tr>
    </table>
    
   
    <p>
		<input type="submit" value="save" />
    </p>
    </form>
    
    module_access
    item_access
        <tr>
            <th>{t}Project role:{/t}</th>
            <td>{$user.project_role.role}</td>
        </tr>

    <p>
        <a href="view.php?id={$user.id}">{t}back{/t}</a>
    </p>
    <p>
        <a href="index.php">{t}index{/t}</a>
    </p>

</div>

{include file="../shared/admin-messages.tpl"}

<script type="text/JavaScript">
$(document).ready(function()
{
	$('#username').focus();
});
</script>

{include file="../shared/admin-footer.tpl"}

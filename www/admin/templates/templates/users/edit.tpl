{include file="../shared/admin-header.tpl"}

<style>
table tr {
	vertical-align:text-top;
}
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

    
    <form method="post" onsubmit="return checkform();">
    <input id="id" name="id" value="{$user.id}" type="hidden" />
    <input type="hidden" name="action" id="action" value="save"  />
    <input type="hidden" name="rnd" value="{$rnd}" />
    
    <table>
        <tr>
            <th>{t}Username:{/t}</th>
            <td><input type="text" id="username" name="username" value="{$user.username}" /></td>
        </tr>
        <tr>
            <th>{t}First name:{/t}</th>
            <td><input type="text" id="first_name" name="first_name" value="{$user.first_name}" /></td>
        </tr>
        <tr>
            <th>{t}Last name:{/t}</th>
            <td><input type="text" id="last_name" name="last_name" value="{$user.last_name}" /></td>
        </tr>
        <tr>
            <th>{t}E-mail address:{/t}</th>
            <td><input type="text" id="email_address" name="email_address" value="{$user.email_address}" /></td>
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
            <td><input type="password" id="password" name="password" value="" /></td>
        </tr>
        <tr>
            <th>{t}Password (repeat):{/t}</th>
            <td><input type="password" id="password_repeat" name="password_repeat" value="" /></td>
        </tr>

        <tr>
            <th>{t}Role:{/t}</th>
            <td>
            	<select id="roles" name="role_id">
                {foreach $roles v}
                <option value="{$v.id}"{if $v.id==$user.project_role.role_id} selected="selected"{/if}>{$v.role}</option>
                {/foreach}
                </select>
            </td>
        </tr>

		{if $user}
        <tr>
            <th>{t}Modules:{/t}</th>
            <td>
				{include file="_module_access.tpl"}
            </td>
        </tr>
	    {/if}

    </table>
    
    <p>
		<input type="submit" value="save" />
    </p>


    </form>
    
    
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

function checkform()
{
	var msg=Array();

	if ( $('#id').val().length==0 )
	{
		if ( $('#password').val().length==0 && $('#password_repeat').val().length==0 )
		{
			msg.push('{t}A password is required.{/t}');
		}
	}

	if ( $('#username').val().trim().length==0 )
	{
		msg.push('{t}A username is required.{/t}');
	}

	if ( $('#first_name').val().trim().length==0 )
	{
		msg.push('{t}First name is required.{/t}');
	}

	if ( $('#last_name').val().trim().length==0 )
	{
		msg.push('{t}Last name is required.{/t}');
	}

	if ( $('#email_address').val().trim().length==0 )
	{
		msg.push('{t}Email address is required.{/t}');
	}

	if ( $('#password').val() != $('#password_repeat').val() )
	{
		msg.push('{t}Passwords not the same.{/t}');
	}
	
	if ( msg.length>0 ) 
	{
		alert( msg.join('\n') );
		return false;
	}
	else
	{
		return true;
	}
}

</script>
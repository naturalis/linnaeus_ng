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

	{if $user.id}
    <h2>{t}edit{/t} {$user.first_name} {$user.last_name}</h2>
	{else}
    <h2>{t}new user{/t}</h2>
    {/if}

    <form id="theForm" method="post" onsubmit="return submitUserEditForm();">
    <input id="id" name="id" value="{$user.id}" type="hidden" />
    <input type="hidden" name="action" id="action" value="save"  />
    <input type="hidden" name="rnd" value="{$rnd}" />

    <table>
        <tr>
            <th>{t}Username:{/t}</th>
            <td>
            {if $user.id==$current_user || $can_edit}
            <input type="text" id="username" name="username" value="{$user.username}" />
            {else}
            {$user.username}
            {/if}
            </td>
        </tr>
        {if $user.id==$current_user || $can_edit}
        <tr>
            <th>{t}Password:{/t}</th>
            <td><input type="password" id="password" name="password" value="" /></td>
        </tr>
        <tr>
            <th>{t}Password (repeat):{/t}</th>
            <td><input type="password" id="password_repeat" name="password_repeat" value="" /></td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
		{/if}

        <tr>
            <th>{t}First name:{/t}</th>
            <td>
            {if $user.id==$current_user || $can_edit}
            <input type="text" id="first_name" name="first_name" value="{$user.first_name}" />
            {else}
            {$user.first_name}
            {/if}
            </td>
        </tr>
        <tr>
            <th>{t}Last name:{/t}</th>
            <td>
            {if $user.id==$current_user || $can_edit}
            <input type="text" id="last_name" name="last_name" value="{$user.last_name}" />
            {else}
            {$user.last_name}
            {/if}
            </td>
        </tr>
        <tr>
            <th>{t}E-mail address:{/t}</th>
            <td>
            {if $user.id==$current_user || $can_edit}
            <input type="text" id="email_address" name="email_address" value="{$user.email_address}" />
            {else}
            {$user.email_address}
            {/if}
            </td>
        </tr>

        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>

        <tr>
            <th>{t}Active:{/t}</th>
            <td>
                <input type="radio" value="1" name="active" {if $user.active!='0'} checked="checked"{/if} />y
                <input type="radio" value="0" name="active" {if $user.active=='0'} checked="checked"{/if} />n
            </td>
        </tr>

        <tr>
            <th>{t}Project role:{/t}</th>
            <td>
            	<select id="roles" name="role_id">
                {foreach $roles v}
                <option
                    {if !$user.project_role.role_id && $v.id==$smarty.const.ID_ROLE_EDITOR}
                    	selected="selected"
					{elseif $v.id==$user.role_id} {* when screen returns because of new user w/ incorrect data *}
                    	selected="selected"
					{elseif $v.id==$user.project_role.role_id}
                    	selected="selected"
					{/if} value="{$v.id}">{$v.role}</option>
                {/foreach}
                </select>
            </td>
        </tr>

        <tr>
            <th>{t}Can publish:{/t}</th>
            <td>
            	<label><input type="radio" value="1" name="can_publish" {if $user.can_publish} checked="checked"{/if} />{t}yes{/t}</label>
            	<label><input type="radio" value="0" name="can_publish" {if !$user.can_publish} checked="checked"{/if} />{t}no{/t}</label>
            </td>
        </tr>

        <tr>
            <th>{t}Modules:{/t}</th>
            <td>
				{include file="_module_access.tpl"}
            </td>
        </tr>
		{if $user.id}
        <tr>
            <td></td>
            <td>
                <a href="#" onclick="resetPermissions();return false;">reset permissions to role defaults</a>
            </td>
        </tr>
	    {/if}
        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <th>{t}Taxa:{/t}</th>
            <td>
				{include file="_taxon_access.tpl"}
            </td>
        </tr>

    </table>

    <p>
		<input type="submit" value="save" /> &nbsp;&nbsp;
	{if $currentUserId != $user.id && $isSysAdmin}
		<input type="button" value="delete user" onclick="deleteUser();" />
    {/if}
    </p>

    </form>

    <p>
        <a href="view.php?id={$user.id}">{t}back{/t}</a> | <a href="index.php">{t}index{/t}</a>
    </p>

</div>

{include file="../shared/admin-messages.tpl"}

<script type="text/JavaScript">
$(document).ready(function()
{
	roleID_sysAdmin={$smarty.const.ID_ROLE_SYS_ADMIN};
	roleID_leadExpert={$smarty.const.ID_ROLE_LEAD_EXPERT};
	roleID_editor={$smarty.const.ID_ROLE_EDITOR};

	$('#username').focus();

	{if !$user.id}

	$('#roles').on('change',function()
	{
		if ($('#roles :selected').val() < {$smarty.const.ID_ROLE_EDITOR})
		{
			$('[name=can_publish][value=1]').prop('checked',true);
		}
		else
		{
			$('[name=can_publish][value=0]').prop('checked',true);
		}
		setPermissions();
	});

	$('#roles').trigger('change');

	{/if}


});
</script>

{include file="../shared/admin-footer.tpl"}

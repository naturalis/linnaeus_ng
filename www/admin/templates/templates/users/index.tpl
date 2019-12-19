{include file="../shared/admin-header.tpl"}

{function table_head}
    <tr>
        <th>username</th>
        <th>first name</th>
        <th>last name</th>
        <th>email address</th>
        <th>role</th>
        <th>active</th>
        <!-- th>last login</th -->
        <th></th>
    </tr>
{/function}

{function table_row}
    <tr class="tr-highlight">
        <td>{$v.username}</td>
        <td>{$v.first_name}</td>
        <td>{$v.last_name}</td>
        <td>{$v.email_address}</td>
        <td>{$v.project_role.role}</td>
        <td>{if $v.active==1}y{else}n{/if}</td>
        <!-- td>{$v.last_login}</td -->
        <td>
        [ <a href="view.php?id={$v.id}">{t}view{/t}</a> ]
        {if $v.project_role}
       	{if $currentUserId != $v.id && !$v.is_sysadmin}
        [ <a href="remove_user.php?id={$v.id}">{t}remove from project{/t}</a> ]
        {/if}
        {else}
        [ <a href="add_user.php?id={$v.id}">{t}add to project{/t}</a> ]
        {/if}
        {if $v.hidden}<span title="user normally hidden">!</span>{/if}
        {if $v.id==$currentUserId}<span title="you">&deg;</span>{/if}
        </td>
    </tr>
{/function}


<style>
h4.inlineHeader {
	margin-bottom:2px;
}
table.collaborators th {
	color:#666;
}
table.collaborators th {
	text-align:left;
}
</style>

<div id="page-main">

    <h2>{t}Collaborators{/t}</h2>
    
    <table class="collaborators">
        <tr><td colspan="9"><h4 class="inlineHeader">{t}Project collaborators{/t}</h4></td></tr>
    
        {table_head}
    
        {foreach $users v}
        {if $v.hidden!=true || $isSysAdmin}
            {table_row data=$v}
        {/if}
        {/foreach}
    
        {if $non_users|@count>0}
        <tr><td colspan="9"></td></tr>
        <tr><td colspan="9"><h4 class="inlineHeader">{t _s=$session.admin.project.sys_name}Users currently not assigned to %s{/t}</h4></td></tr>
        {table_head}
        {/if}

        {foreach $non_users v}
        {if $v.hidden!=true}
            {table_row data=$v}
        {/if}
        {/foreach}
    </table>
    
    <p>
        <a href="create.php">{t}create new user{/t}</a>
    </p>

</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

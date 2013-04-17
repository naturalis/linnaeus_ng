{include file="shared/_admin-head.tpl"}
{include file="shared/_admin-body-start.tpl"}

    <div id="page-main">

{if $session.admin.system.server_addr=='127.0.0.1'}

	Choose your module:

	<ul>
    {foreach from=$modules item=v}
    {if $v._rights && $v.show_in_menu==1}
		<li>
        	<a href="views/{$v.controller}/">{$v.module}</a>{if $v.active=='y'} *{/if}
		</li>
    {/if}
    {/foreach}


    {foreach from=$currentUserRights item=v}
    {if $v._rights && $v.show_in_menu==1}
		<li>
        	<span class="a" onclick="$('#freeId').val('{$v.id}');$('#freeForm').submit();">{$v.module}</span>{if $v.active=='y'} *{/if}
		</li>
    {/if}
    {/foreach}
    </ul>


{else}

    <table>
        <tr>
    {assign var=i value=1}
    {section name=i loop=$modules}
    {if $modules[i]._rights && $modules[i].show_in_menu==1}
        {assign var=i value=$i+1}
            <td>
                <a href="views/{$modules[i].controller}/">
                    <img src="{$baseUrl}admin/media/system/module_icons/{$modules[i].icon}" style="width:32px;border:0px" />
                </a>
            </td>
            <td>
                <a href="views/{$modules[i].controller}/">{$modules[i].module}</a>{if $modules[i].active=='y'} *{/if}
            </td>
    {/if}
    {if $i % 2 == 1}
        <tr>
        </tr>
    {/if}
    {/section}
        </tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr>
    {assign var=i value=1}
    {section name=i loop=$freeModules}
    {if $freeModules[i].currentUserRights}
        {assign var=i value=$i+1}
            <td>
                <span onclick="$('#freeId').val('{$freeModules[i].id}');$('#freeForm').submit();">
                    <img src="{$baseUrl}admin/media/system/module_icons/custom.png" style="width:32px;border:0px" />
                </span>
            </td>
            <td>
                <span class="a" onclick="$('#freeId').val('{$freeModules[i].id}');$('#freeForm').submit();">{$freeModules[i].module}</span>{if $freeModules[i].active=='y'} *{/if}
            </td>
    {/if}
    {if $i % 2 == 1}
        <tr>
        </tr>
    {/if}
    {/section}
        </tr>
    </table>
    
{/if}

<form method="post" id="freeForm" action="views/module/">
<input type="hidden" id="freeId" name="freeId" value="" />
</form>

{if $currentUserRoleId==1 || $currentUserRoleId==2 || $session.admin.user.superuser==1}
<!--
NOTICES
- acquire images for matrix
- compress dick key
- define /shortname/
- project ubpublished
-->
<br />
{t}Management tasks:{/t}

<ul>
	<li><a href="views/projects/">{t}Project administration{/t}</a></li>
	<li><a href="views/hotwords/">{t}Hotwords{/t}</a></li>
	<li><a href="views/users/">{t}User administration{/t}</a></li>
</ul>

{/if}
</div>

{include file="shared/admin-footer.tpl"}

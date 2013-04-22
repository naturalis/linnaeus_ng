{include file="shared/_admin-head.tpl"}
{include file="shared/_admin-body-start.tpl"}

    <div id="page-main">

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

<form method="post" id="freeForm" action="views/module/">
<input type="hidden" id="freeId" name="freeId" value="" />
</form>

{if $currentUserRoleId==1 || $currentUserRoleId==2 || $session.admin.user.superuser==1}
<!--
NOTICES
- acquire images for matrix
- compress dich key
- define /shortname/
- project ubpublished
-->
<br />
{t}Management tasks:{/t}

<ul>
	<li><a href="views/projects/">{t}Project administration{/t}</a></li>
	<li><a href="views/hotwords/">{t}Hotwords{/t}</a></li>
	<li><a href="views/utilities/mass_upload.php">{t}Mass upload images{/t}</a></li>
	<li><a href="views/projects/clear_cache.php">{t}Clear cache{/t}</a></li>
	<li><a href="views/users/">{t}User administration{/t}</a></li>
</ul>

{/if}
</div>

{include file="shared/admin-footer.tpl"}

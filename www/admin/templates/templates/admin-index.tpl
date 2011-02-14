{include file="shared/_admin-head.tpl"}
{include file="shared/_admin-body-start.tpl"}

<div id="page-main">

<table>
	<tr><td colspan="2">Manage modules:</td></tr>
	<tr>
{assign var=i value=1}
{section name=i loop=$modules}
{if $modules[i]._rights}
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
			<a href="views/extra/index.php?id={$freeModules[i].id}">
				<img src="{$baseUrl}admin/media/system/module_icons/custom.png" style="width:32px;border:0px" />
			</a>
		</td>
		<td>
			<a href="views/extra/index.php?id={$freeModules[i].id}">{$freeModules[i].module}</a>{if $freeModules[i].active=='y'} *{/if}
		</td>
{/if}
{if $i % 2 == 1}
	<tr>
	</tr>
{/if}
{/section}
	</tr>
</table>


{if $currentRole.role_id==1 || $currentRole.role_id==2}
<br />
Management tasks:
<ul>
	<li><a href="views/users/">{t}User management{/t}</a></li>
	<li><a href="views/projects/">{t}Project management{/t}</a></li>
	<li><a href="views/users/choose_project.php">{t}Switch projects{/t}</a></li>
</ul>

{/if}
{if $currentRole.role_id==1}
{t}System administration tasks:{/t}
<ul>
	<li><a href="">{t}Set rights{/t}</a></li>
	<li><a href="">{t}Create a project{/t}</a></li>
</ul>

{/if}
</div>

{include file="shared/admin-footer.tpl"}

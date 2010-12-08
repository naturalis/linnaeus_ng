{include file="shared/admin-header.tpl"}

<div id="page-main">

<table>
	<tr><td colspan="2">Manage modules:</td></tr>
	<tr>
{section name=i loop=$modules}
		<td>
			<a href="views/{$modules[i].controller}/">
				<img src="{$baseUrl}admin/media/system/module_icons/{$modules[i].icon}" style="width:32px;border:0px" />
			</a>
		</td>
		<td>
			<a href="views/{$modules[i].controller}/">{$modules[i].module}</a>{if $modules[i].active=='y'} *{/if}
		</td>
{if $smarty.section.i.index % 2 == 1}
	<tr>
	</tr>
{/if}
{/section}
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
{section name=i loop=$freeModules}
		<td>
			<a href="views/extra/index.php?id={$freeModules[i].id}">
				<img src="{$baseUrl}admin/media/system/module_icons/custom.png" style="width:32px;border:0px" />
			</a>
		</td>
		<td>
			<a href="views/extra/index.php?id={$freeModules[i].id}">{$freeModules[i].module}</a>{if $freeModules[i].active=='y'} *{/if}
		</td>
{if $smarty.section.i.index % 2 == 1}
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
	<li><a href="views/users/">User management</a></li>
	<li><a href="views/projects/">Project management</a></li>
	<li><a href="views/users/choose_project.php">Switch projects</a></li>
</ul>

{/if}
{if $currentRole.role_id==1}
System administration tasks:
<ul>
	<li><a href="">Set rights</a></li>
	<li><a href="">Create a project</a></li>
</ul>

{/if}
</div>

{include file="shared/admin-footer.tpl"}

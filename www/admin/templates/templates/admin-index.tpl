{include file="shared/admin-header.tpl"}

<div id="page-main">

Manage modules:
<ul>
{section name=i loop=$modules}
	<li><a href="views/{$modules[i].controller}/">{$modules[i].module}</a>{if $modules[i].active=='y'} *{/if}</li>
{/section}
{section name=i loop=$freeModules}
	<li><a href="views/extra/index.php?id={$freeModules[i].id}">{$freeModules[i].module}</a>{if $freeModules[i].active=='y'} *{/if}</li>
{/section}
</ul>
<br />
Other tasks:
<ul>
	<li><a href="views/users/">User management</a></li>
	<li><a href="views/projects/">Project management</a></li>
</ul>
</div>

{include file="shared/admin-footer.tpl"}

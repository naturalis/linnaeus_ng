{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">

{if !$saved}
<div class="text-block">
{t}Enter the project's name, description and version below, and click 'save' to create the project.{/t}<br />
</div>

<form method="post" action="" name="theForm" id="theForm">
<table id="new-input" class="{if $freeModules|@count >= 5}module-new-input-hidden{/if}">
	<tr>
		<td>{t}Project name:{/t}</td>
		<td><input type="text" style="width:250px" name="title" value="{$data.title}"> <span class="asterisk-required-field">*</span></td>
	</tr>
	<tr>
		<td>{t}Project version:{/t}</td>
		<td><input type="text" style="width:25px" name="version" value="{$data.version}"></td>
	</tr>
	<tr style="vertical-align:top">
		<td>{t}Project description:{/t}<br />{t}(for reference only){/t}</td>
		<td><textarea style="width:300px;height:150px;" name="sys_description">{$data.sys_description}</textarea><span class="asterisk-required-field">*</span></td>
	</tr>
	<tr style="vertical-align:top">
		<td>&nbsp;</td>
		<td><input type="submit" value="{t}save{/t}" /></td>
	</tr>
</table>
</form>
<div class="text-block">
{t}As system administrator, you will automatically be made system administrator of the new project. In that capacity, you will be able to create users, add modules and execute other administrative tasks for the newly created project.{/t}<br />
</div>
{else}
{t _s1='<a href="../../admin-index.php">' _s2='</a>'}Click %shere%s to administrate the new project.{/t}
{/if}
</div>
{include file="../shared/admin-footer.tpl"}

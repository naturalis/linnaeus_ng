{include file="../shared/admin-header.tpl"}

<div id="admin-main">
{literal}
<style>
.admin-modusers-hidden {
	visibility:collapse;
}
.admin-modusers {
	visibility:visible;
}
</style>
<script>
function toggleModuleUsers(i) {
	classname = $('#users-'+i).attr('class');
	
	if (classname=='admin-modusers-hidden')
		$('#users-'+i).removeClass().addClass('admin-modusers');
	else
		$('#users-'+i).removeClass().addClass('admin-modusers-hidden');
}
function moduleUserAction(ele,removeIds) {
//	$.ajax({ url:"ajax_interface.php?v=collaborators&a="+encodeURIComponent(action)+"&i="+encodeURIComponent(id),

}
</script>
{/literal}
<div class="admin-text-block">
Select the standard modules you wish to use in your project:<br />
<table>
{section name=i loop=$modules}
	<tr>
	{if $modules[i].active=='y'}
		<td title="in use in your project" class="admin-td-module-inuse">&nbsp;</td>
		<td style="width:100px">
			<span class="admin-td-module-title-inuse" id="cell-{$modules[i].id}d">
	{else}
		<td title="in use in your project, but inactive" class="admin-td-module-inactive" >&nbsp;</td>
		<td style="width:100px">
			<span class="admin-td-module-title-deactivated" id="cell-{$modules[i].id}d">
	{/if}
				<span class="admin-td-module-title">{$modules[i].module}</span>
			</span>
		</td>
		<td>
			<span onclick="toggleModuleUsers({$modules[i].id});" style="cursor:pointer">4 collaborators</span>
		</td>
	</tr>
	<tr id="users-{$modules[i].id}" class="admin-modusers-hidden">
		<td colspan="3">
			<table>
			{section name=j loop=$users}
				<tr>
					<td style="width:5px;"></td>
					<td>{$users[j].first_name} {$users[j].last_name}</td>
					<td>{$users[j].role}</td>
					<td title="collaborator is not working on this module" class="admin-td-moduser-inactive"></td>
					<td title="collaborator is working on this module" class="admin-td-moduser-active"></td>
					<td title="add collaborator" class="admin-td-moduser-add"></td>
					<td title="remove collaborator" class="admin-td-moduser-remove"></td>
				</tr>
			{/section}			
			</table>
		</td>
	</tr>
{/section}
</table>
</div>

<br />

<div class="admin-text-block">
Besides these standard modules, you can add up to five extra content modules to your project:<br />
<table>
{assign var=n value=1}
{section name=i loop=$free_modules}
	<tr id="row-f{$free_modules[i].id}">
	{if $free_modules[i].active=='y'}
		<td title="in use in your project" class="admin-td-module-inuse" id="cell-f{$free_modules[i].id}a">&nbsp;</td>
		<td>
			<span class="admin-td-module-title-inuse" id="cell-f{$free_modules[i].id}d">
	{else}
		<td title="in use in your project, but inactive" class="admin-td-module-inactive" >&nbsp;</td>
		<td>
			<span class="admin-td-module-title-deactivated" id="cell-f{$free_modules[i].id}d">
	{/if}
				<span class="admin-td-module-title">{$free_modules[i].module}</span>
			</span>
		</td>
</tr>
{/section}
</table>

</div>

</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

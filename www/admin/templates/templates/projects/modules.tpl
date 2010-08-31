{include file="../shared/admin-header.tpl"}

<div id="admin-main">

<div class="admin-text-block">
Select the standard modules you wish to use in your project:<br />
<table>
{section name=i loop=$modules}
	<tr>
	{if !$modules[i].module_project_id}
		<td
			class="admin-td-module-unused" 
			id="cell-{$modules[i].id}a"
			title="not in use in your project" 
		>
			&nbsp;
		</td>
		<td
			class="admin-td-module-activate" 
			title="activate" 
			onclick="moduleAction(this)"
			id="cell-{$modules[i].id}b"
		>&nbsp;
			
		</td>
		<td
			class="admin-td-module-invisible" 
			title="" 
			onclick="moduleAction(this)"
			id="cell-{$modules[i].id}c"
		>&nbsp;
			
		</td>	
		<td>
			<span
				class="admin-td-module-title-unused" 
				id="cell-{$modules[i].id}d">
			<span class="admin-td-module-title">{$modules[i].module}</span> - {$modules[i].description}</span>
			<span id="cell-{$modules[i].id}e" style="visibility:hidden">{$modules[i].module}</span>
		</td>
	{else}
	{if $modules[i].active=='y'}
		<td
			title="in use in your project"
			class="admin-td-module-inuse" 
			id="cell-{$modules[i].id}a"
		>&nbsp;
			
		</td>
		<td 
			class="admin-td-module-deactivate" 
			title="deactivate (no data will be deleted)" 
			onclick="moduleAction(this)"
			id="cell-{$modules[i].id}b"
		>&nbsp;
			
		</td>
		<td
			class="admin-td-module-invisible" 
			title="" 
			onclick="moduleAction(this)"
			id="cell-{$modules[i].id}c"
		>&nbsp;
		</td>
		<td>
			<span 
				class="admin-td-module-title-inuse" 
				id="cell-{$modules[i].id}d">
			<span class="admin-td-module-title">{$modules[i].module}</span> - {$modules[i].description}</span>
			<span id="cell-{$modules[i].id}e" style="visibility:hidden">{$modules[i].module}</span>
		</td>
	{else}
		<td
			title="in use in your project, but inactive" 
			class="admin-td-module-inactive"
			id="cell-{$modules[i].id}a"
		>&nbsp;
			
		</td>
		<td 
			class="admin-td-module-reactivate" 
			title="re-activate" 
			onclick="moduleAction(this)"
			id="cell-{$modules[i].id}b"
		>&nbsp;
			
		</td>
		<td
			class="admin-td-module-delete" 
			title="delete module and data" 
			onclick="moduleAction(this)"
			id="cell-{$modules[i].id}c"
		>&nbsp;
			
		</td>
		<td>
			<span
				class="admin-td-module-title-deactivated" 
				id="cell-{$modules[i].id}d">
			<span class="admin-td-module-title">{$modules[i].module}</span> - {$modules[i].description}
			<span id="cell-{$modules[i].id}e" style="visibility:hidden">{$modules[i].module}</span>
			</span>
		</td>
	{/if}

{/if}
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
		<td
			title="in use in your project"
			class="admin-td-module-inuse" 
			id="cell-f{$free_modules[i].id}a"
		>&nbsp;
			
		</td>
		<td 
			class="admin-td-module-deactivate" 
			title="deactivate (no data will be deleted)" 
			onclick="moduleAction(this)"
			id="cell-f{$free_modules[i].id}b"
		>&nbsp;
			
		</td>
		<td
			class="admin-td-module-invisible" 
			title="" 
			onclick="moduleAction(this,['row-f{$free_modules[i].id}'])"
			id="cell-f{$free_modules[i].id}c"
		>&nbsp;
		</td>
		<td>
			<span 
				class="admin-td-module-title-inuse" 
				id="cell-f{$free_modules[i].id}d">
			<span class="admin-td-module-title">{$free_modules[i].module}</span></span>
			<span id="cell-f{$free_modules[i].id}e" style="visibility:hidden">{$free_modules[i].module}</span>
		</td>
	{else}
		<td
			title="in use in your project, but inactive" 
			class="admin-td-module-inactive"
			id="cell-f{$free_modules[i].id}a"
		>&nbsp;
			
		</td>
		<td 
			class="admin-td-module-reactivate" 
			title="re-activate" 
			onclick="moduleAction(this)"
			id="cell-f{$free_modules[i].id}b"
		>&nbsp;
			
		</td>
		<td
			class="admin-td-module-delete" 
			title="delete module and data" 
			onclick="moduleAction(this,['row-f{$free_modules[i].id}'])"
			id="cell-f{$free_modules[i].id}c"
		>&nbsp;
			
		</td>
		<td>
			<span
				class="admin-td-module-title-deactivated" 
				id="cell-f{$free_modules[i].id}d">
			<span class="admin-td-module-title">{$free_modules[i].module}</span>
			<span id="cell-f{$free_modules[i].id}e" style="visibility:hidden">{$free_modules[i].module}</span>
			</span>
		</td>
	{/if}
</tr>
{/section}
</table>

<table id="new-input" class="{if $free_modules|@count >= 5}admin-module-new-input-hidden{/if}">
<tr>
	<td colspan="4">&nbsp;</td>
</tr>
<tr >
	<td colspan="4">
		<form action="" method="post">
		<input type="hidden" name="rnd" value="{$rnd}">
		Enter new module's name: <input type="text" name="module_new" id="module_new" value="" maxlength="32" />
		<input type="submit" value="add module" onclick="addFreeModule();" />
		</form>	
	</td>
</tr>
</table>

</div>

</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

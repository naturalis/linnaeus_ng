{include file="../shared/admin-header.tpl"}

<div id="page-main">

<div class="text-block">
Select the standard modules you wish to use in your project:<br />
<table>
{section name=i loop=$modules}
	<tr>
	{if !$modules[i].module_project_id}
		<td
			class="cell-module-unused" 
			id="cell-{$modules[i].id}a"
			title="not in use in your project" 
		>&nbsp;
			
		</td>
		<td
			class="cell-module-activate" 
			title="activate" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-{$modules[i].id}b"
		>&nbsp;
			
		</td>
		<td
			class="cell-module-invisible" 
			title="" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-{$modules[i].id}c"
		>&nbsp;
			
		</td>	
		<td>
			<span
				class="cell-module-title-unused" 
				id="cell-{$modules[i].id}d">
			<span class="cell-module-title">{$modules[i].module}</span> - {$modules[i].description}</span>
			<span id="cell-{$modules[i].id}e" style="visibility:hidden">{$modules[i].module}</span>
		</td>
	{else}
	{if $modules[i].active=='y'}
		<td
			title="in use in your project"
			class="cell-module-in-use" 
			id="cell-{$modules[i].id}a"
		>&nbsp;
			
		</td>
		<td 
			class="cell-module-deactivate" 
			title="deactivate (no data will be deleted)" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-{$modules[i].id}b"
		>&nbsp;
			
		</td>
		<td
			class="cell-module-invisible" 
			title="" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-{$modules[i].id}c"
		>&nbsp;
		</td>
		<td>
			<span 
				class="cell-module-title-in-use" 
				id="cell-{$modules[i].id}d">
			<span class="cell-module-title">{$modules[i].module}</span> - {$modules[i].description}</span>
			<span id="cell-{$modules[i].id}e" style="visibility:hidden">{$modules[i].module}</span>
		</td>
	{else}
		<td
			title="in use in your project, but inactive" 
			class="cell-module-inactive"
			id="cell-{$modules[i].id}a"
		>&nbsp;
			
		</td>
		<td 
			class="cell-module-reactivate" 
			title="re-activate" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-{$modules[i].id}b"
		>&nbsp;
			
		</td>
		<td
			class="cell-module-delete" 
			title="delete module and data" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-{$modules[i].id}c"
		>&nbsp;
			
		</td>
		<td>
			<span
				class="cell-module-title-inactive" 
				id="cell-{$modules[i].id}d">
			<span class="cell-module-title">{$modules[i].module}</span> - {$modules[i].description}
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

<div class="text-block">
Besides these standard modules, you can add up to five extra content modules to your project:<br />
<table>
{assign var=n value=1}
{section name=i loop=$free_modules}
	<tr id="row-f{$free_modules[i].id}">
	{if $free_modules[i].active=='y'}
		<td
			title="in use in your project"
			class="cell-module-in-use" 
			id="cell-f{$free_modules[i].id}a"
		>&nbsp;
			
		</td>
		<td 
			class="cell-module-deactivate" 
			title="deactivate (no data will be deleted)" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-f{$free_modules[i].id}b"
		>&nbsp;
			
		</td>
		<td
			class="cell-module-invisible" 
			title="" 
			onclick="moduleChangeModuleStatus(this,['row-f{$free_modules[i].id}'])"
			id="cell-f{$free_modules[i].id}c"
		>&nbsp;
		</td>
		<td>
			<span 
				class="cell-module-title-in-use" 
				id="cell-f{$free_modules[i].id}d">
			<span class="cell-module-title">{$free_modules[i].module}</span></span>
			<span id="cell-f{$free_modules[i].id}e" style="visibility:hidden">{$free_modules[i].module}</span>
		</td>
	{else}
		<td
			title="in use in your project, but inactive" 
			class="cell-module-inactive"
			id="cell-f{$free_modules[i].id}a"
		>&nbsp;
			
		</td>
		<td 
			class="cell-module-reactivate" 
			title="re-activate" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-f{$free_modules[i].id}b"
		>&nbsp;
			
		</td>
		<td
			class="cell-module-delete" 
			title="delete module and data" 
			onclick="moduleChangeModuleStatus(this,['row-f{$free_modules[i].id}'])"
			id="cell-f{$free_modules[i].id}c"
		>&nbsp;
			
		</td>
		<td>
			<span
				class="cell-module-title-inactive" 
				id="cell-f{$free_modules[i].id}d">
			<span class="cell-module-title">{$free_modules[i].module}</span>
			<span id="cell-f{$free_modules[i].id}e" style="visibility:hidden">{$free_modules[i].module}</span>
			</span>
		</td>
	{/if}
</tr>
{/section}
</table>

<table id="new-input" class="{if $free_modules|@count >= 5}module-new-input-hidden{/if}">
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

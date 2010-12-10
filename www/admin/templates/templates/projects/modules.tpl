{include file="../shared/admin-header.tpl"}

<div id="page-main">

<div class="text-block">
Select the standard modules you wish to use in your project:<br />
<div id="module-table-div"></div>
</div>

<br />

<div class="text-block">

Besides these standard modules, you can add up to {$freeModuleMax} extra content modules to your project:<br />
<div id="free-module-table-div"></div>

<table id="new-input" class="{if $freeModules|@count >= 5}module-new-input-hidden{/if}">
	<tr>
		<td colspan="4">&nbsp;</td>
	</tr>
	<tr >
		<td colspan="4">
			<form action="" method="post">
			<input type="hidden" name="rnd" value="{$rnd}" />
			Enter new module's name: <input type="text" name="module_new" id="module_new" value="" maxlength="32" />
			<input type="submit" value="add module" onclick="addFreeModule();" />
			</form>	
		</td>
	</tr>
</table>

</div>

</div>

<script type="text/javascript">
{section name=i loop=$modules}
	moduleAddProjectModule([{$modules[i].id},'{$modules[i].module|addslashes}','{$modules[i].description|addslashes}','{if $modules[i].active==''}-{else}{$modules[i].active}{/if}',{if $modules[i].module_project_id==''}'-'{else}{$modules[i].module_project_id}{/if}]);
{/section}
	moduleDrawModuleBlock();

{section name=i loop=$freeModules}
	moduleAddProjectFreeModule([{$freeModules[i].id},'{$freeModules[i].module|addslashes}','{$freeModules[i].active}']);
{/section}

	moduleDrawFreeModuleBlock();

</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

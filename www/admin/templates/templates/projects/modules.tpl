{include file="../shared/admin-header.tpl"}

<div id="admin-main">
{if $check==true}
Please verify the data below. Click 'Save' to save the user data; or 'Back' to return to the previous screen.
{/if}

<form method="post" action="" name="theForm" id="theForm">
<div class="admin-text-block">
Select the standard modules you wish to use in your project:<br />
{section name=i loop=$modules}
	<label>
		<input 
			type="checkbox" 
			name="module[]" 
			value="{$modules[i].id}" 
			{if $modules[i]._in_project}checked{/if}
		/>
		<span class="admin-module-name">{$modules[i].module}</span>: {$modules[i].description}
	</label>
	{if $modules[i]._in_project}
	<input
		type="hidden"
		name="module_exist[]" 
		value="{$modules[i].id}" 		
	/>
	{/if}
	<br />
{/section}
<input type="submit" value="save" />
</div>
<div class="admin-text-block">
	Besides these standard modules, you can add up to five extra content modules to your poject.<br />
{assign var=n value=1}
{section name=i loop=$free_modules}
		<span style="margin:0px 4px 0px 4px">{$n++}.</span>
		<span class="admin-module-name" id="free-module-{$n-1}" onclick="editField(this)">{$free_modules[i].module}</span><br />
{/section}
{if $free_modules|@count < 5}
	<input type="text" name="module_new" value="" maxlength="32" />
	<input type="submit" value="add new" />
{/if}
</div>
</form>

</div>

{literal}
<script type="text/javascript">

function editField(ele) {
	return;
	var p = $('#'+ele.id).html();	
	$('#'+ele.id).html('<input type="text" value="'+p+'">');

}

</script>
{/literal}


















{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

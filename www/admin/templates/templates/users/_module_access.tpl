<table>
{foreach $modules.modules v}
<tr class="tr-highlight">
	<td>
	<label><input type="checkbox" value="{$v.module_id}" name="module" />{$v.module}</label>
	</td>
	<td>
        <label><input type="checkbox" value="{$v.module_id}" class="module_read" name="module_read" />read {$v.module_id}</label>
        <label><input type="checkbox" value="" class="module_write" name="module_write" />write {$v.module_id}</label>
	</td>
</tr>
{/foreach}
</table>

{if $modules.freeModules|@count>0}
<table>
{foreach $modules.freeModules v}
<tr><td>
	<label><input type="checkbox" value="{$v.module_id}" name="module" />{$v.module}</label>
</td></tr>
{/foreach}
</table>
{/if}

<script type="text/JavaScript">
$(document).ready(function()
{
	$('.module_write').on('change',function() {
		if ( $(this).prop('checked') )
		{
			$(this).closest( "input[type=checkbox]" ).prop('checked',true);

			console.log( $(this).closest( ".module_read" ).val() );
		}
	});
});
</script>
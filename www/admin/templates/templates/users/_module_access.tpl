<table>
{assign "k" "0"}
{foreach $modules.modules v}
    {assign "access" $v.access.id!=null}
    {assign "read" $v.access.can_read==1}
    {assign "write" $v.access.can_write==1}
    <tr class="tr-highlight">
        <td>
            <label><input type="checkbox" id="m{$k}" class="modules" name="module[{$v.module_id}]" {if $access} checked="checked"{/if} />{$v.module}</label>
        </td>
        <td>
            <label><input type="checkbox" data-module="m{$k}" class="module_rights module_read" name="module_read[{$v.module_id}]" {if $read} checked="checked"{/if} />read</label>
            <label><input type="checkbox" data-module="m{$k}" class="module_rights module_write" name="module_write[{$v.module_id}]" {if $write} checked="checked"{/if} />write</label>
        </td>
    </tr>
    {assign "k" $k+1}
{/foreach}
{if $modules.freeModules|@count>0}
{foreach $modules.freeModules v}
    {assign "access" $v.access.id!=null}
    {assign "read" $v.access.can_read==1}
    {assign "write" $v.access.can_write==1}
    <tr>
        <td>
            <label><input type="checkbox" id="m{$k}" class="modules" name="custom[{$v.id}]" {if $access} checked="checked"{/if} />{$v.module}</label>
        </td>
        <td>
            <label><input type="checkbox" data-module="m{$k}" class="module_rights module_read" name="custom_read[{$v.id}]" {if $read} checked="checked"{/if} />read</label>
            <label><input type="checkbox" data-module="m{$k}" class="module_rights module_write" name="custom_write[{$v.id}]" {if $write} checked="checked"{/if} />write</label>
        </td>
    </tr>
    {assign "k" $k+1}
{/foreach}
</table>
{/if}

<script type="text/JavaScript">
$(document).ready(function()
{
	$('.modules').each(function(i,mod)
	{
		if ($(mod).prop('checked')==false)
		{
			$('[data-module='+$(mod).attr('id')+']').each(function(j,e)
			{
				$(e).prop('disabled',true);
			});
		}
	});

	$('.modules').on('change',function()
	{
		var disable=!$(this).prop('checked');
		$('[data-module='+$(this).attr('id')+']').each(function(j,e)
		{

			$(e).prop('disabled',disable);

			if (disable==false && $(e).hasClass('module_read'))
			{
				$(e).prop('checked',true);
			}
		});
	});

	$('.module_write').on('change',function()
	{
		if ( $(this).prop('checked') )
		{
			$(this).closest( ":has(.module_read)" ).find('.module_read').prop('checked',true);
		}
	});
});
</script>
{include file="../shared/admin-header.tpl"}

<div id="page-main">
<form id="theForm" action="" method="post">
<input type="hidden" name="action" value="save" />
<input type="hidden" name="rnd" value="{$rnd}" />
Current settings:
<table>
<tr><th>setting</th><th>value (to delete, clear and save)</th></tr>
{foreach from=$settings item=v}
<tr><td>{$v.setting}</td><td><input type="text" name="setting[{$v.setting}]" value="{$v.value|@escape}" style="width:200px" /></td></tr>
{/foreach}

<tr><td colspan="2">&nbsp;</td></tr>
<tr><th colspan="2">new setting:</th></tr>
<tr>
	<td>
		<input type="text" name="new_setting" id="new_setting" value="{$new_setting}" maxlength=32 style="width:150px" />
   	</td>
    <td>
    	<input type="text" maxlength=255 name="new_value" id="new_value" value="{$new_value}" style="width:200px" />
	</td>
    {if $isSysAdmin}
	<td>
    	<span style="cursor:pointer;font-size:15px" onclick="{literal}$('#new_setting').val($('#settings :selected').attr('id'));$('#new_value').val($('#settings :selected').attr('value'));{/literal}">&larr;</span>
    	<select id="settings">
		{foreach from=$settingsAvailable item=v}
       	<option id="{$v[0]}" default="{$v[2]}">{$v[0]}{if $v[3]} ({$v[3]}){/if}: {$v[1]}{if $v[2]} ({$v[2]}){/if}</option>
        {/foreach}
        </select>
	</td>
    {/if}
</tr>
</table>
<input type="submit" value="save" />
</form>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

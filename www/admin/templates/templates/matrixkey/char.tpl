{include file="../shared/admin-header.tpl"}

<div id="page-main">

    <p>
    {if $characteristic.label}
    	{t _s1=$characteristic.label _s2=$matrix.label}Editing character "%s" for matrix "%s"{/t}
    {else}
	    {t _s1=$matrix.label}New charcteristic for matrix "%s"{/t}
    {/if}
    </p>

    <p>
    {t}Add the name and type of the charcteristic you want to add. The following types of charcteristics are available:{/t}
        <ul>
        {foreach $charTypes v k}
            <li>{t}{$v.name}{/t}: {t}{$v.info}{/t}</li>
        {/foreach}
        </ul>
    </p>
    
    <form id="theForm" method="post" action="">
    <input type="hidden" name="rnd" value="{$rnd}" />
    <input type="hidden" id="id" name="id" value="{$characteristic.id}" />
    <input type="hidden" name="action" id="action" value="save" />

    <table>
        <tr>
            <td>{t}Internal name{/t}:</td>
            <td><input type="text" name="sys_name" value="{$characteristic.sys_name}" maxlength="32" /></td>
        </tr>                
        <tr>
            <td>{t}Character type{/t}:</td>
            <td>	
            <select name="type" id="type">
                {foreach $charTypes v k}
                {$v.name|@var_dump}
                    <option value="{$v.name}" {if $characteristic.type==$v.name}selected="selected"{/if}>{t}{$v.name}{/t}</option>
                {/foreach}
                </select>
    		</td>
        </tr>                
    {foreach $languages v i}
        <tr>
            <td>{$v.language} {t}name{/t}:</td>
            <td><input type="text" name="name[{$v.language_id}]" value="{$characteristic.labels[$v.language_id].label}" maxlength="64" /></td>
        </tr>                
    {/foreach}		
    </table>

	<p>
        <input type="submit" value="{t}save{/t}" />&nbsp;
        <input type="button" value="{t}back{/t}" onclick="window.open('edit.php','_self')" />&nbsp;
        {if $characteristic.id}
        <input type="button" value="{t}delete{/t}" onclick="matrixDeleteCharacteristic('{$characteristic.label|@addslashes}')" />&nbsp;
        {/if}
    </p>


	{if $charLib && !$characteristic.label}
    <p>
    <table>
        <tr>
            <td colspan="3">
                {t}Instead, you can also use an existing character from one of your other matrices. To do so, select the name below and click "use".{/t}
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <select name="existingChar" id="existingChar">
                {foreach $charLib v k}
                    <option value="{$v.id}">{$v.label}</option>
                {/foreach}
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="3">
                <input type="button" onclick="$('#action').val('use');$('#theForm').submit();" value="{t}use{/t}" />&nbsp;
                <input type="button" value="{t}back{/t}" onclick="window.open('edit.php','_self')" />
            </td>
        </tr>
    </table>
    </p>
	{/if}

	</form>

</div>

<script type="text/javascript">
$(document).ready(function()
{
});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

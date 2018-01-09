{include file="../shared/admin-header.tpl"}

<div id="page-main">

<form id="theForm" method="post" action="">
    <input type="hidden" name="rnd" value="{$rnd}" />
    <input type="hidden" name="action" id="action" value="" />

    <table>
        <tr>
            <td>
                {t}Taxon to add:{/t}
            </td>
            <td>
                <select name="taxon[]" id="taxon" style="width:300px" size="10" multiple="true">
                {foreach $taxa v k}
                {if $v.keypath_endpoint==1 && $v.already_in_matrix==0}
                    <option value="{$v.id}">{$v.taxon}{if $v.name} ({$v.name}){/if}</option>
                {/if}
                {/foreach}
                </select>
            </td>
        </tr>
        {if $useVariations && $variations}
        <tr>
            <td>
                {t}Variation to add:{/t}
            </td>
            <td>
                <select name="variation[]" id="variation" style="width:300px" size="10" multiple="true">
                {foreach $variations v k}
                    <option value="{$v.id}">{$v.label}</option>
                {/foreach}
                </select>
            </td>
        </tr>
        {/if}
        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
    </table>
    
    <table>
        <tr>
            <td colspan="2">
                <input type="button" onclick="$('#action').val('');$('#theForm').submit();" value="{t}save and return to matrix{/t}" />&nbsp;
                <input type="button" onclick="$('#action').val('repeat');$('#theForm').submit();" value="{t _s1=$characteristic.characteristic}save and add another taxon{/t}" />
                <input type="button" value="{t}back{/t}" onclick="window.open('edit.php','_top')" />
            </td>
        </tr>
    </table>

</form>

</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

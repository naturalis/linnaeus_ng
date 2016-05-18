{include file="../shared/admin-header.tpl"}

<div id="page-main">

<h3>convert old settings</h3>

<form id="theForm" method="post">
<input type="hidden" id="action" name="action" value="convert" />

<table>
    <tr>
        <th>old setting</th><th>old value</th><th>new setting</th><th>convert?</th><th></th>
    </tr>
    {foreach $settings v}
    <tr class="tr-highlight">
        <td>{$v.old_setting}</td>
        <td>{$v.old_value}</td>
        <td>{$v.new_setting}</td>
        <td>{if $v.new_setting}<input name="values[]" value="{$v.old_value_id}" type="checkbox"{if $v.new_value==''} checked="checked"{/if}>{else}-{/if}</td>
        <td>{if $v.new_value!=''}a value already exists ('{$v.new_value}')! will be overridden if you choose.{/if}</td>
    </tr>
    {/foreach}
</table>

<p>
	<input type="submit" value="convert" />
</p>

</form>

<a href="index.php">back</a>

</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

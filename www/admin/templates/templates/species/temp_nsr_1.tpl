{include file="../shared/admin-header.tpl"}
<div id="result-list"></div>

<div id="page-main">
<form method="post">
<p>
<table>
<tr><td>image ID:</td><td><input type="text" name="id" value="{$id}" /></td></tr>
{if $current}
<tr><td>current:</td><td>{$current.taxon} ({$current.nsr_id})</td></tr>
<tr><td>new:</td><td><input type="text" name="newid" value="{$newid}" /></td></tr>
{if $new}<tr><td></td><td>{$new.taxon}{/if}</td></tr>
{/if}
<tr><td colspan="2">
{if $new}

<input type="hidden" name="image_id" value="{$image_id}" />
<input type="hidden" name="new_taxon_id" value="{$new.id}" />

<input type="submit" value="save">
{else}
<input type="submit" value="find">
{/if}
</td></tr>
</table>
</p>
</form>

</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

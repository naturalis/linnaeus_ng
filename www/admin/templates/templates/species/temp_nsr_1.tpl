{include file="../shared/admin-header.tpl"}
<div id="result-list"></div>

<div id="page-main">
<form id="theForm" method="post">
<p>
<table>
<tr><td>image ID:</td><td><input type="text" name="id" value="{$id}" /></td></tr>
{if $current}
<tr><td>current:</td><td>{$current.taxon} ({$current.nsr_id}) <a href="/linnaeus_ng/app/views/species/nsr_taxon.php?id={$current.id}&cat=media" target="_blank">link</a></td></tr>
{if !$new}<tr><td></td><td><a href="#" onclick="
if (confirm('are you sure?')) { 
	var input = $('<input>').attr('type', 'hidden').attr('name', 'del').val({$image_id});
	$('#theForm').append($(input));
	input = $('<input>').attr('type', 'hidden').attr('name', 'action').val('delete');
	$('#theForm').append($(input));
	$('#theForm').submit();
}; 
return false;
" target="_blank">delete</a></td></tr>{/if}
<tr><td>new:</td><td><input type="text" name="newid" value="{$newid}" /></td></tr>
{if $new}<tr><td></td><td>{$new.taxon} <a href="/linnaeus_ng/app/views/species/nsr_taxon.php?id={$new.id}&cat=media" target="_blank">link</a>{/if}</td></tr>
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

<a class="edit" style="margin-left:0" href="#" onclick="dropListDialog(this,'taxon');return false;" rel="taxon_id">add</a> <span class="edit">{t}(leave empty for all){/t}</span>
<input type="hidden" id="taxon_id" value="" label="taxon" droplistminlength="3" onchange="taxonToUserList();" />
<span id="taxon" style="display:none"></span>
<div><ul id="taxa"></ul></div>

<script type="text/JavaScript">
$(document).ready(function()
{
	{foreach $user.item_access v}
	addTaxaToUserList( { id: {$v.taxon_id}, name: '{$v.taxon|@escape}' } );{/foreach}	
	buildTaxaUserList();
});
</script>


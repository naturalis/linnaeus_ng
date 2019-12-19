{include file="../shared/admin-header.tpl"}

<div id="page-main">

	<p>

    <h3>Groups</h3>
    Drag and drop groups to change their show order; click "save group order" to save the new order.

    {function menu level=0}
      <ul class="level{$level} sortable">
      {foreach $data as $entry}
        <li id="group{$entry.id}">
            {$entry.sys_label} ({if $entry.taxa|@count>0}<a href="#" onclick="$('#taxa{$entry.id}').toggle();return false;">{/if}{$entry.taxa|@count} taxa{if $entry.taxa|@count>0}</a>{/if})
            <a class="edit" href="taxongroup.php?id={$entry.id}">edit group</a>
            <a class="edit" href="taxongroup_taxa.php?id={$entry.id}">add taxa</a>
            <div class="group-taxa" id="taxa{$entry.id}">
            {foreach $entry.taxa as $v}
            {$v.taxon} ({$v.rank})<br />
            {/foreach}
            </div>
	        {if $entry.children}{menu data=$entry.children level=$level+1}{/if}
        </li>
      {/foreach}
      </ul>
    {/function}
    
	{menu data=$groups}
	{if $groups|@count==0}(none){/if}
    </p>
    
    <p>
    	<input type="button" value="save group order" onclick="doSaveGroupOrder()" />
    </p>
    <p>
    	<a href="taxongroup.php">create new group</a><br />
    	<a href="taxongroup_clickthrough.php">group click-through</a><br />
    	<a href="taxongroup_orphaned_taxa.php">taxa not in any group</a>
    </p>
</div>


<script type="text/JavaScript">
$(document).ready(function(){

	$('.sortable').nestedSortable({
		items: 'li',
		listType: 'ul',
	});

	$('#page-block-messages').fadeOut(2000);
});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
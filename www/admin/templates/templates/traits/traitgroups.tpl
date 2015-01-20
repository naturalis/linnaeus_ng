{include file="../shared/admin-header.tpl"}

<div id="page-main">

	<p>

    <h3>Traitgroups</h3>
    Drag and drop groups to change their show order; click "save group order" to save the new order.

    {function menu level=0}
      <ul class="level{$level} sortable">
      {foreach $data as $entry}
        <li id="group{$entry.id}">
            {$entry.sysname}
            <a class="edit" href="traitgroup.php?id={$entry.id}">edit group</a>
            <a class="edit" href="traitgroup_traits.php?group={$entry.id}">edit traits</a>
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
    	<a href="traitgroup.php">create new traitgroup</a><br />
    </p>
</div>


<script type="text/JavaScript">
$(document).ready(function()
{
	$('.sortable').nestedSortable({
		items: 'li',
		listType: 'ul',
	});
	
	$('#page-block-messages').fadeOut(2000);
});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
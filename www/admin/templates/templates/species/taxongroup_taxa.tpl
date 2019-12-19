{include file="../shared/admin-header.tpl"}

<div id="page-main">

	<h3>group: {$group.sys_label}</h3>
    
    <p>
    <form id="theForm" method="post">
    <input type="hidden" name="rnd" value="{$rnd}" />
    <input type="hidden" name="group_id" value="{$group.id}" />
    Click the taxa under "all taxa" to expand the tree. Click "add" to add them to the
    current groups selection. A grey "add" link indicates that the same taxon already 
    belongs to another group or groups; nevertheless, it can be added to the present
    group as well.<br />
    Drag and drop the selected taxa to change their order.
    Click "save" when you are done to store your selection.<br /><br />
    <input type="button" id="save" value="save" />
    </form>
    </p>

	<table id="all-taxa"><tr>
    <td>
	    Taxa in group:
	    <ul id="selection" class="sortable">
        </ul>
	</td>
    <td>
        All taxa:
        {function menu level=0}
          <ul id="level{$level}">
          {foreach $data as $entry}
                <li>
                    {if $entry.children}<a href="#" onclick="$(this).nextUntil('ul').next().toggle();return false;">{/if}
                    <span id="taxon{$entry.id}">
	                    {if $entry.commonname}
                    	{$entry.commonname} (<i>{$entry.taxon}</i>; {$entry.rank})
                       	{else}
                    	{$entry.taxon} ({$entry.rank})
                        {/if}
					</span>
                    {if $entry.children}</a>{/if}
					<a href="#" id="add{$entry.id}" class="edit{if $entry.group_memberships|@count>0 && !$entry.group_memberships[$group.id]} non-zero{/if}" onclick="addTaxonToGroup({$entry.id});return false;">add</a>
                    {if $entry.children}{menu data=$entry.children level=$level+1}{/if}
                </li>
          {/foreach}
          </ul>
        {/function}
        <div id="all-taxa">
        {menu data=$taxa}
		<div>

	</td>
    </tr></table>

    
    <p>
    	<a href="taxongroups.php">back</a>
    </p>

</div>


<script type="text/JavaScript">
$(document).ready(function(){
	{foreach $taxongroupTaxa as $v}
	addTaxonToGroup({$v.id},'{if $v.commonname}{$v.commonname|@escape} (<i>{$v.taxon|@escape}</i>; {$v.rank|@escape}){else}{$v.taxon|@escape} ({$v.rank|@escape}){/if}');
	{/foreach}
	
	$('.sortable').nestedSortable({
		items: 'li',
		listType: 'ul',
	});

	$('#save').bind('click',function() { doTaxongroupTaxaFormSubmit(); } );
	$('#page-block-messages').fadeOut(2000);
});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
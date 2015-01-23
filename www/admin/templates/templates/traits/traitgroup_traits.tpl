{include file="../shared/admin-header.tpl"}

<div id="page-main">

	<p>
	    <h3>{t _s1=$group.name}%s: traits{/t}</h3>

    	Drag and drop traits to change their show order; click "save trait order" to save the new order.

        <ul class="level{$level} sortable">
            {foreach $group.traits as $trait}
            <li id="sortable{$trait.id}">{$trait.sysname} ({$trait.type_sysname})
            	<a class="edit" href="traitgroup_trait.php?id={$trait.id}">edit</a>
                {if $trait.type_allow_values==1}
            	<a class="edit" href="traitgroup_trait_values.php?trait={$trait.id}">values ({$trait.value_count})</a>
                {/if}
			</li>
            {/foreach}
		</ul>

        {if $group.traits|@count==0}<p>(none)</p>{/if}
        
    	<a class="edit" style="margin-left:0" href="traitgroup_trait.php?group={$group.id}">create new trait</a><br />

    </p>
    <p>
    	<input type="button" value="save trait order" onclick="doSaveOrder()" />
    </p>
    <p>
    	<a href="traitgroups.php">back</a><br />
    	<a href="index.php">index</a>
    </p>
</div>


<script type="text/JavaScript">
$(document).ready(function()
{
	$('.sortable').nestedSortable({
		items: 'li',
		listType: 'ul',
		disableParentChange : true
	});

	$('#page-block-messages').fadeOut(2000);
});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
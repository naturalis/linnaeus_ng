{include file="../shared/admin-header.tpl"}

<div id="page-main">

	<p>
	    <h3>{$group.sysname}: {$trait.sysname} {t}values{/t}</h3>
	</p>

        <!--

        trait type			template
        --------------------------------------
        boolean			->	(has no values)
        stringlist		->	_stringlist.tpl
        stringlistfree	->	_stringlist.tpl
        stringfree		->	(has no values)
        intlist			->	_intlist.tpl
        intlistfree		->	_intlist.tpl
        intfree			->	(has no values)
        intfreelimit
        floatlist		->	_floatlist.tpl
        floatlistfree	->	_floatlist.tpl
        floatfree		->	(has no values)
        floatfreelimit
        datelist		->	_datelist.tpl
        datelistfree	->	_datelist.tpl
        datefree		->	(has no values)
        datefreelimit
        
        -->

		{$novaluetraits = ['boolean','stringfree','intfree','floatfree','datefree']}

		{if !in_array($trait.type_sysname,$novaluetraits)}

			{include file="_`$trait.type_sysname`.tpl"}


        {else}
        
        	{t}This type of trait has no values.{/t}
        
        {/if}

    <p>
    	<a href="traitgroup_trait.php?id={$trait.id}">trait</a>&nbsp;&nbsp;
    	<a href="traitgroup_traits.php?group={$group.id}">back</a>&nbsp;&nbsp;
    	<a href="index.php">index</a>
    </p>
</div>

<script type="text/JavaScript">
$(document).ready(function()
{
	$('.sortable').nestedSortable({
		items: 'li',
		listType: 'ul',
		disableParentChange : true,
		relocate: function()
		{
			reorderValueList();
		}
	});
	
	$('#newvalue').on('keyup',function() { characterCount(); });

	$('#theForm').on("keypress", function(e)
	{
		var code=e.keyCode || e.which;
		if (code==13)
		{               
			e.preventDefault();
			addTraitValue();
			updateValueList();
			updateValueCount();
			return false;
		}
	});

	$(window).on('beforeunload',function() { return checkUnsavedValues() } );
	
	$('#page-block-messages').fadeOut(2000);

});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
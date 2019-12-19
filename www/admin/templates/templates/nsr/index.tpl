{include file="../shared/admin-header.tpl"}

<div id="page-main">

<p>
	<form>
		{t}search for a name:{/t} <input type="text" id="allLookupBox" onkeyup="allLookup()" placeholder="{t}type a name{/t}" autocomplete="off" />
	</form>
</p>
<p>
	{t}taxonomic tree:{/t}
	<div id="tree-container">&#9632; &hellip;</div>
</p>

{include file="../shared/left_column_admin_menu.tpl"}

</div>


<div class="inline-templates" id="localListItemTpl">
<!--
    <li>
        <a href="#"onclick="setAutoExpand(%ID%);return false;">%LABEL%</a> %COMMON% %TAXON% &nbsp;&nbsp;<a href="taxon.php?id=%ID%">&nbsp;&rarr;&nbsp;</a>
    </li>
-->
</div>


<script>
function localList(obj,txt)
{
	allLookupClearDiv();
	var buffer=Array();

	if (obj && obj.results)
	{
		for(var i=0;i<obj.results.length;i++) {

			var d = obj.results[i];

			if (d.id && d.label)
			{
				buffer.push( 
					fetchTemplate( 'localListItemTpl' )
						.replace(/%ID%/g,d.id)
						.replace('%LABEL%',d.label)
						.replace('%COMMON%',(d.common_name && d.common_name!=d.name ? "("+d.common_name+")": "" ))
						.replace('%TAXON%',(d.nametype=='isPreferredNameOf' && d.taxon ? "("+d.taxon+")": "" ))
					
					 );
			}
		}
		$('#'+allLookupListName).append('<ul>'+buffer.join('')+'</ul>');
	}
}

$(document).ready(function()
{
	setAjaxTreeUrl('tree_ajax_interface.php');
	setShowUpperTaxon({$tree_show_upper_taxon});
	{if $session.admin.project.title}
	setRootNodeLabel('{$session.admin.project.title|@escape}');
	{/if}

	{if $tree}
		$( "#"+container ).html( {$tree} );
	{elseif $nodes}
		growbranches( {$nodes} );
		storetree();
	{else}
		buildtree(false);
		//restoretree();
	{/if}
		
	allLookupNavigateOverrideUrl(taxonTargetUrl);
	allLookupNavigateOverrideListFunction(localList);

	$(document).ready(function()
	{
		$(window).keydown(function(event)
		{
			if(event.keyCode == 13)
			{
				event.preventDefault();
				return false;
			}
		});
	});

	$( '#admin-menu-bottom' ).toggle(true);
	
});
</script>

{include file="../shared/admin-footer.tpl"}

<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../../admin/style/rank-list.css">
    <link rel="stylesheet" type="text/css" href="../../../admin/style/lookup.css">
    <link rel="stylesheet" type="text/css" href="../../../admin/style/nsr_taxon_tree.css">
	<script type="text/javascript" src="../../../admin/javascript/lookup.js"></script>
	<script type="text/javascript" src="../../../admin/javascript/nsr_taxon_tree.js"></script>
</head>
<body>

<div style="margin:20px 0 0 10px;">

<p>
	<form>
		{t}search for a name:{/t} <input type="text" id="allLookupBox" onKeyUp="allLookup()" placeholder="{t}type a name{/t}" autocomplete="off" />
	</form>
	<br />
	<div id="tree-container"></div>
</p>

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
					'<li> \
						<a href="#"onclick="setAutoExpand('+d.id+');return false;">'+d.label+'</a> \
						'+ (d.common_name && d.common_name!=d.name ? "("+d.common_name+")": "" ) +' \
						'+ (d.nametype=='isPreferredNameOf' && d.taxon ? "("+d.taxon+")": "" ) +' \
						&nbsp;&nbsp;<a href="taxon.php?id='+d.id+'">&nbsp;&rarr;&nbsp;</a> \
					</li>'
				);
			}
		}
		$('#'+allLookupListName).append('<ul>'+buffer.join('')+'</ul>');
	}
}

$(document).ready(function()
{
	useHighLight=false;

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

	{if $node}
	setAutoExpand({$node});
	{/if}

});
</script>

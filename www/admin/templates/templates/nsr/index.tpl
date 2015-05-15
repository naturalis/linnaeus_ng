{include file="../shared/admin-header.tpl"}
<div id="page-main">
<p>
	<form>
		naam zoeken: <input type="text" id="allLookupBox" onkeyup="allLookup()" placeholder="typ een naam" autocomplete="off" />
	</form>
</p>
<p>
	taxonomische boom:
	<div id="tree-container"></div>
</p>
<p>
	taken:<br />
	<a href="taxon_new.php">nieuw taxonconcept maken</a><br />
	<a href="taxon_deleted.php">verwijderde taxonconcepten</a><br />
	<a href="update_parentage.php">indextabel bijwerken</a><br />
	<a href="activity_log.php">editor log</a><br /><br />
	<a href="nsr_id_resolver.php">NSR ID resolver</a><br />
	<a href="../import/export_versatile.php">multi-purpose export</a><br />
	<a href="../import/export_nsr.php">export voor NDA</a> (experimenteel)<br />
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

	setAjaxTreeUrl('tree_ajax_interface.php');
	
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
});
</script>

{include file="../shared/admin-footer.tpl"}

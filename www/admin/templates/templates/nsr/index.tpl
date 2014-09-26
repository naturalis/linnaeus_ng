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
	<a href="../import/export_nsr.php">export voor NDA</a> (experimenteel)
</p>
</div>

<script>
function localList(obj,txt)
{
	allLookupClearDiv();
	var buffer=Array();

	if (obj.results)
	{
		
		for(var i=0;i<obj.results.length;i++) {
			
			var d = obj.results[i];
			
			if (d.id && d.label)
			{
				buffer.push(
					'<li> \
						<a href="#"onclick="setAutoExpand('+d.id+');return false;">'+d.label+'</a> \
						&nbsp;&nbsp;<a href="taxon.php?id='+d.id+'">&nbsp;&rarr;&nbsp;</a> \
					</li>'
				);
				
			}

		}

		$('#'+allLookupListName).append('<ul>'+buffer.join('')+'</ul>');

	}

}



$(document).ready(function() {
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
	
});
</script>

{include file="../shared/admin-footer.tpl"}

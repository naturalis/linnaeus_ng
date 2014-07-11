{include file="../shared/admin-header.tpl"}

<div id="page-main">
	<form>
		naam zoeken: <input type="text" id="allLookupBox" onkeyup="allLookup()" placeholder="typ een naam"/>
	</form>
	<div id="tree-container"></div>
</div>

<script>
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
	
});
</script>

{include file="../shared/admin-footer.tpl"}

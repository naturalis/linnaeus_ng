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
	<!-- a href="actor_index.php">beheer experts & organisaties</a><br />
	<a href="../literature2/index.php">beheer literatuur</a><br / -->
	<a href="../import/export_nsr.php">export voor NDA</a>
</p>
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

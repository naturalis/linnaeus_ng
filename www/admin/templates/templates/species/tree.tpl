{include file="../shared/admin-header.tpl"}

<style type="text/css">
.node-div {
	display:inline-block;
	padding-right:5px;
	cursor:move;
}
</style>

<div id="page-main">
	Expand by clicking, and drag & drop within a branch to alter the order of the taxa. Be sure to click 'save' when you're done.<br />
	Taxa <u>cannot</u> be moved from one parent to the next; to do so, go to edit-page of a taxon by clicking &rarr;.
    <input type="button" value="save" onclick="saveNewOrder()" />
<p>
	<div id="tree-container"></div>
</p>

 <pre id="toArrayOutput">
</pre>


</div>

<script>
$(document).ready(function() {
	
	url='tree_ajax_interface.php';
	nodeCountType='taxon';
	rootNodeLabel=null;
	taxonTargetUrl='edit.php?id=%s';

	{if $tree}
		$( "#"+container ).html( {$tree} );
		makeSortable();
	{elseif $nodes}
		growbranches( {$nodes} );
		storetree();	
		makeSortable();
	{else}
		buildtree(false);
		//restoretree();
	{/if}
});
</script>

{include file="../shared/admin-footer.tpl"}

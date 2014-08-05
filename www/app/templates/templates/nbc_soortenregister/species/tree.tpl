{include file="../shared/header.tpl"}

<div id="dialogRidge">

	<div id="left">
        <div>
            <h2>&nbsp;</h2>
        </div>
	</div>

	<div id="content" class="taxon-detail">

		<div id="taxonHeader" class="hasImage">
			<div id="titles" class="full">
				<h1 class="no-subtitle">Taxonomische boom</h1>
				<h2></h2>
			</div>
			<div id="tree-container"></div>
		</div>

	</div>

	{include file="../shared/_right_column.tpl"}

</div>

<script type="text/JavaScript">
$(document).ready(function() {
	
	$('title').html('Taxonomische boom - '+$('title').html());

	{if $tree}
		$( "#"+container ).html( {$tree} );
	{elseif $nodes}
		growbranches( {$nodes} );
		storetree();	
	{else}
		buildtree(false);
		//restoretree();
	{/if}
	
});
</script>

{include file="../shared/footer.tpl"}
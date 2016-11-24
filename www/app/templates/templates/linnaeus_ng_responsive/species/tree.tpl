{include file="../shared/header.tpl"}
<!-- {include file="../shared/flexslider.tpl"} -->

<div id="dialogRidge">
	<!-- <div id="left">
	</div> -->
	<div id="content" class="taxon-detail">
		{include file="../search/_searchtabs.tpl" activeTab="taxonTree"}
		<div class="taxonTree">
			<div id="taxonHeader" class="hasImage">
				<div id="titles" class="full">
					<h2 class="no-subtitle">{t}Taxonomische boom{/t}</h2>
				</div>
				<div id="tree-container"></div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript" src="{$baseUrl}app/javascript/project_specific/soortenregister-tree.js"></script>
<script type="text/JavaScript">
$(document).ready(function() {
	
	$('title').html('{t}Taxonomische boom{/t} - '+$('title').html());
	
	{if $session.admin.project.title}
	setTopLevelLabel('{$session.app.project.title|@escape}');
	{/if}
	
	{if $tree}
		$( "#"+container ).html( {$tree} );
	{elseif $nodes}
		growbranches( {$nodes} );
		storetree();	
	{else}
		buildtree(false);
	{/if}
	{if $expand}
		setAutoExpand({$expand});
	{/if}
	
});
</script>

{include file="../shared/footer.tpl"}
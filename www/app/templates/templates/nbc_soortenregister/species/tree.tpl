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
				<h1 class="no-subtitle">{t}Taxonomische boom{/t}</h1>
				<h2></h2>
			</div>
			<div id="tree-container"></div>
		</div>

	</div>

	{include file="../shared/_right_column.tpl"}

</div>

<script type="text/javascript" src="{$baseUrl}app/javascript/project_specific/soortenregister-tree.js"></script>
<script type="text/JavaScript">
$(document).ready(function()
{
	$('title').html('{t}Taxonomische boom{/t} - '+$('title').html());
	
	{if $session.app.project.title}
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
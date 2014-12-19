{include file="../shared/header.tpl"}

<body class="html not-front not-logged-in two-sidebars page-node page-node- page-node-17 node-type-naturalis-page section-content" >

    <!--.page -->
    <div role="document" class="page">

	{include file="../shared/page_header.tpl"}

    <main role="main" class="row l-main">

        <div class="large-6 large-push-3 main columns">
      
            <a id="main-content"></a>
           
            <h2 id="page-title" class="title">{t}Taxonomische boom{/t}</h2>

            <div id="content" class="taxon-detail">
            
			<div id="tree-container"></div>
            
            </div>

    </div>
    <!--/.main region -->

	{include file="../shared/_left_column_just_search.tpl"}

	{include file="../shared/_right_column.tpl"}
    
</main>
<!--/.main-->

  
  
  </div>
<!--/.page -->


<script type="text/javascript" src="{$baseUrl}app/javascript/project_specific/soortenregister-tree.js"></script>
<script type="text/JavaScript">
$(document).ready(function() {

	$('title').html('{t}Taxonomische boom{/t} - '+$('title').html());
	
	topLevelLabel='The Orthoptera Of Europe';
	includeSpeciesStats=false;

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
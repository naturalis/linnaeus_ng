{include file="../shared/admin-header.tpl"}
<div id="page-main">

<p>
	<form>
		{t}search for a name:{/t} <input type="text" id="allLookupBox" onkeyup="allLookup()" placeholder="{t}type a name{/t}" autocomplete="off" />
	</form>
</p>
<p>
	{t}taxonomic tree:{/t}
	<div id="tree-container"></div>
</p>
<p>
	{t}tasks:{/t}<br />
	<a href="taxon_new.php">{t}new taxon concept{/t}</a><br />
	<a href="taxon_deleted.php">{t}taxon concepts marked as deleted{/t}</a><br />
	<a href="update_parentage.php">{t}update index table{/t}</a><br />
	<a href="nsr_id_resolver.php">{t}NSR ID resolver{/t}</a><br />
	<a href="../import/export_versatile.php">{t}multi-purpose export{/t}</a><br />
	<a href="image_meta_bulk.php">{t}image meta-data bulk upload{/t}</a><br />
</p>
<p>
	<a href="tabs.php">{t}passport categories ("tabs"){/t}</a><br />
	<a href="sections.php">{t}page sections{/t}</a><br />
	<a href="ranks.php">{t}taxonomic ranks{/t}</a><br />
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
});
</script>

{include file="../shared/admin-footer.tpl"}

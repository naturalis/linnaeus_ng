{include file="../shared/admin-header.tpl"}

<style>
ul{
	padding-left:5px;
}
ul.top {
	padding-left:0px;
}
li.child {
	list-style:square;
	margin-left:12px;
}
li.child.no-expand {
	list-style:circle;
}
.detail-link {
	margin-left:8px;
	font-weight:bold;
	font-size:1.1em;
}
#tree-container .rank, .child-count {
	margin-left:3px;
	font-size:0.87em;
}
#tree-container .child-count {
	margin-left:5px;
	color:#666;
}
#tree-container .rank:before {
  content: "[";
}
#tree-container .rank:after {
  content:"]";
}
#tree-container .child-count:before {
  content: "(";
}
#tree-container .child-count:after {
  content: ")";
}
#tree-container .italics {
	font-style:italic;
}
</style>


<script>

var activeNode=false;
var container='tree-container';
var url='ajax_interface.php';
var taxonTargetUrl='taxon.php?id=%s';

function buildtree(node)
{
	activeNode=node;

	$.ajax({
		url : url,
		type: "POST",
		data : ({
			action : 'get_tree_node' ,
			node : node ,
			count : 'species', // species, taxon, none
			time : allGetTimestamp()
		}),
		success : function (data)
		{
			var data=$.parseJSON(data);
			growbranches(data);
			storetree();
		}
	});
}

function growbranches(data)
{
	if (!data) return;

	var progeny='';

	for (var i in data.progeny)
	{
		var d=data.progeny[i];

		if (d.id==undefined)
			continue;

		progeny+=
			'<li class="child '+(!d.has_children?'no-expand':'')+'" id="node-'+d.id+'">'+
				(d.has_children ?'<a href="#" onclick="buildtree('+d.id+');return false;">'+d.label+'</a>':d.label)+
				(d.rank_label ? '<span class="rank">'+d.rank_label+'</span>' : '' )+
				(d.child_count && d.child_count.total>0 ?
					'<span class="child-count">'+d.child_count.total+'/'+d.child_count.established+'</span>' :
					'' 
				)+
				'<a href="'+taxonTargetUrl.replace('%s',d.id)+'" class="detail-link">&rarr;</a> \
			</li>';
	}
	
	
	if (progeny) progeny='<ul id="children-'+data.node.id+'">'+progeny+'</ul>';

	var buffer=
		'<ul class="top">'+
			'<li class="child">'+
				(!activeNode ?
					//'<a href="#" onclick="buildtree(false);return false">'+data.node.label+'</a>' :
					'<a href="#" onclick="buildtree(false);return false">Nederlands Soortenregister</a>' :
					'<a href="#" onclick="$( \'#children-'+data.node.id+'\' ).toggle();return false">'+data.node.label+'</a>'
				)+
				(data.node.rank_label ? 
					'<span class="rank">'+data.node.rank_label+'</span>' : 
					'' 
				)+
				(data.node.child_count && data.node.child_count.total>0 && !activeNode ?
					'<span class="child-count">'+data.node.child_count.total+' soorten in totaal / '+data.node.child_count.established+' gevestigde soorten</span>' :
					'' 
				)+
				(data.node.child_count && data.node.child_count.total>0 && activeNode ?
					'<span class="child-count">'+data.node.child_count.total+'/'+data.node.child_count.established+'</span>' :
					'' 
				)+
				(!activeNode ?
					'':
					'<a href="'+taxonTargetUrl.replace('%s',data.node.id)+'" class="detail-link">&rarr;</a>'
				)+
				progeny+
			'</li>'+
		'</ul>';

	if (activeNode==false)
	{
		$( "#"+container ).html( buffer );
	}
	else
	{
		$( "#node-"+activeNode ).replaceWith( buffer );
	}
	
	$('#top').addClass('fuck');
	
}

function storetree()
{
	var tree=$( "#"+container ).html().replace(/\n/ig,'').replace(/(\s+)/ig,' ');
	$.ajax({
		url : url,
		type: "POST",
		data : ({
			action : 'store_tree' ,
			tree : tree ,
			time : allGetTimestamp()
		})
	});
}

function restoretree()
{
	$.ajax({
		url : url,
		type: "POST",
		data : ({
			action : 'restore_tree' ,
			time : allGetTimestamp()
		}),
		success : function (data)
		{
			var data=$.parseJSON(data);
			
			if (data && data!==undefined)
			{
				$( "#"+container ).html( data );
			}
			else
			{
				buildtree(false);
			}
		}
	});
}

</script>


<div id="page-main">
	<form>
		<input type="text" id="allLookupBox" onkeyup="allLookup()" placeholder="type to find"/>
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

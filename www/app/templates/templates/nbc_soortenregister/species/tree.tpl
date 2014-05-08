{include file="../shared/header.tpl"}
{literal}
<script>
var activeNode=false;
var container='tree-container';
var url='ajax_interface_tree.php';

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
			//console.dir(data);
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
		progeny+=
			'<li class="child '+(!d.has_children?'no-expand':'')+'" id="node-'+d.id+'">'+
				(d.has_children ?'<a href="#" onclick="buildtree('+d.id+');return false;">'+d.label+'</a>':d.label)+
				(d.rank_label ? '<span class="rank">'+d.rank_label+'</span>' : '' )+
				(d.child_count && d.child_count.total>0 ?
					'<span class="child-count">'+d.child_count.total+'/'+d.child_count.established+'</span>' :
					'' 
				)+
				'<a href="nsr_taxon.php?id='+d.id+'" class="detail-link">&rarr;</a> \
			</li>';
	}
	
	if (progeny) progeny='<ul id="children-'+data.node.id+'">'+progeny+'</ul>';

	var buffer=
		'<ul>'+
			'<li class="child">'+
				(!activeNode ?
					'<a href="#" onclick="buildtree(false);return false">'+data.node.label+'</a>' :
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
					'<a href="nsr_taxon.php?id='+data.node.id+'" class="detail-link">&rarr;</a>'
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
			
			if (data)
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
<style>
ul.top {
	list-style:none;
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
{/literal}
<div id="dialogRidge">

	{include file="_left_column.tpl"}

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


{literal}
<script type="text/JavaScript">
$(document).ready(function(){
	//buildtree(false);
	restoretree();
});
</script>
{/literal}

{include file="../shared/footer.tpl"}
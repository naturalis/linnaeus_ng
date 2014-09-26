var activeNode=false;
var container='tree-container';
var url='ajax_interface_tree.php';
var autoExpandArray=Array();
var highlightNodes=Array();

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
			checkAutoExpand();
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
			
		var shouldHighlight=shouldHighlightNode(d.id);

		progeny+=
			'<li class="child '+(!d.has_children?'no-expand':'')+'" id="node-'+d.id+'">'+
				(shouldHighlight ? '<span class="highlight-node">' : '' )+
				(d.has_children ?'<a href="#" onclick="buildtree('+d.id+');return false;">'+d.label+'</a>':d.label)+
				(d.rank_label ? '<span class="rank">'+d.rank_label+'</span>' : '' )+
				(d.child_count && d.child_count.total>0 ?
					'<span class="child-count">'+d.child_count.total+'/'+d.child_count.established+'</span>' :
					'' 
				)+
				(shouldHighlight ? '</span>' : '' )+
				'<a href="nsr_taxon.php?id='+d.id+'" class="detail-link">&rarr;</a> \
			</li>';
	}
	
	if (progeny) progeny='<ul id="children-'+data.node.id+'">'+progeny+'</ul>';

	var buffer=
		'<ul>'+
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

function addAutoExpandNode(node)
{
	autoExpandArray.push(node);
}

function checkAutoExpand()
{
	// called from buildtree(); call directly after adding nodes with addAutoExpandNode()
	var node=autoExpandArray.shift();
	if (node)
	{
		buildtree(node);
	}
}

function setAutoExpand(id)
{
	setHighlightNode(id);
	
	$.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			action : 'get_parentage' ,
			id : id ,
			time : allGetTimestamp()
		}),
		success : function (data)
		{
			var data=$.parseJSON(data);
			for (index=1;index<data.length;++index)
			{
				addAutoExpandNode(data[index]);
			}
			buildtree(false);
		}
	});
}

function setHighlightNode(node)
{
	highlightNodes.push(node);
}

function shouldHighlightNode(node)
{
	for(var i=0;i<highlightNodes.length;i++)
	{
		if (highlightNodes[i]==node)
		{
			return true;
		}
	}
	return false;
}


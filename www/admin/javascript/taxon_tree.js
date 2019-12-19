var activeNode=false;
var container='tree-container';
var url='ajax_interface.php';
var taxonTargetUrl='taxon.php?id=%s';
var autoExpandArray=Array();
var highlightNodes=Array();
var nodeCountType='species'; // species, taxon, none
var rootNodeLabel='Nederlands Soortenregister';

function makeSortable()
{
	$('.sortable').nestedSortable({
		handle: 'div',
		items: 'li',
		toleranceElement: '> div',
		disableParentChange: true,
		doNotClear:true,
		listType: 'ul',
		protectRoot: true
	});
}


function buildtree(node)
{
	activeNode=node;

	$.ajax({
		url : url,
		type: "POST",
		data : ({
			action : 'get_tree_node' ,
			node : node ,
			count : nodeCountType ,
			time : allGetTimestamp()
		}),
		success : function (data)
		{
			//console.log(data);
			var data=$.parseJSON(data);
			growbranches(data);
			storetree();
			checkAutoExpand();
			makeSortable();
			
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
			'<li class="child '+(!d.has_children?'no-expand':'')+'" id="node-'+d.id+'"> \
				<div class="node-div">&#9617;</div>'+
				(d.has_children ?'<span><a href="#" onclick="buildtree('+d.id+');return false;">'+d.label+(d.rank_label ?
					'<span class="rank">'+d.rank_label+'</span>' : '' )+'</a></span>':d.label)+
				(nodeCountType=='species' && d.child_count && d.child_count.total>0 ?
					'<span class="child-count">'+d.child_count.total+'/'+d.child_count.established+'</span>' : '')+
				(nodeCountType=='taxon' && d.child_count && d.child_count>0 ? '<span class="child-count">'+d.child_count+'</span>':'')+
				'<a href="'+taxonTargetUrl.replace('%s',d.id)+'" class="detail-link">&rarr;</a> \
			</li>';
	}
	
	if (progeny) progeny='<ul id="children-'+data.node.id+'">'+progeny+'</ul>';

	var buffer=
		'<li class="child" id="node-'+data.node.id+'"> \
			<div class="node-div">&#9617;</div>'+
			(!activeNode ?
				'<a href="#" onclick="buildtree(false);return false">'+(rootNodeLabel ? rootNodeLabel : data.node.label)+'</a>' :
				'<a href="#" onclick="$( \'#children-'+data.node.id+'\' ).toggle();return false">'+(rootNodeLabel ? rootNodeLabel : data.node.label)+'</a>'
			)+
			(data.node.rank_label?'<span class="rank">'+data.node.rank_label+'</span>':'')+
			(nodeCountType=='species' && data.node.child_count && data.node.child_count.total>0 && !activeNode ?
				'<span class="child-count">'+
					data.node.child_count.total+' soorten in totaal / '+data.node.child_count.established+' gevestigde soorten</span>' :'' )+
			(nodeCountType=='species' &&data.node.child_count && data.node.child_count.total>0 && activeNode ?
				'<span class="child-count">'+data.node.child_count.total+'/'+data.node.child_count.established+'</span>' :'' )+
			(nodeCountType=='taxon' && data.node.child_count && data.node.child_count>0 && !activeNode ?
				'<span class="child-count">'+data.node.child_count+' taxa</span>':'' )+
			(nodeCountType=='taxon' &&data.node.child_count && data.node.child_count>0 && activeNode ?
				'<span class="child-count">'+data.node.child_count+'</span>':'' )+
			'<a href="'+taxonTargetUrl.replace('%s',data.node.id)+'" class="detail-link">&rarr;</a>'+
			progeny+
		'</li>';

	if (activeNode==false)
	{
		$( "#"+container ).html( '<ul class="sortable">' + buffer + '</ul>' );
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
			if (data) {
                for (index = 1; index < data.length; ++index) {
                    addAutoExpandNode(data[index]);
                }
            }
			buildtree(false);
			//checkAutoExpand();
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




function saveNewOrder()
{
	arraied = $('.sortable').nestedSortable('toArray', { startDepthCount: 0 } );
	//console.dir(arraied);
}
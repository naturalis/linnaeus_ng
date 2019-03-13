var activeNode=false;
var container='tree-container';
var ajaxTreeUrl='ajax_interface.php';
var taxonTargetUrl='taxon.php?id=%s';
var autoExpandArray=Array();
var highlightNodes=Array();
var nodeCountType='species'; // species, taxon, none
var rootNodeLabel='Taxonomy';
var useHighLight=true;
var detailLinkLabel='&rarr;';

function setAjaxTreeUrl(url)
{
	ajaxTreeUrl=url;
}

function setHighlightNode(node)
{
	highlightNodes.push(node);
}

function setRootNodeLabel(label)
{
	rootNodeLabel=label;
}

function getRootNodeLabel()
{
	return rootNodeLabel;
}

function setShowUpperTaxon(state)
{
	showUpperTaxon=state;
}

function getShowUpperTaxon()
{
	return showUpperTaxon;
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

function detailLink( id, label )
{
	// allows for use of alternative JS-links: taxonTargetUrl="javascript:doSomething";
	var js="javascript:";
	if (taxonTargetUrl.indexOf(js)==0)
	{
		var f=taxonTargetUrl.substring(js.length);

		if (typeof window[f]=="function")
		{
			window[f](id,label);
		}
	}
	else
	{
		window.open(taxonTargetUrl.replace('%s',id)+'&noautoexpand=1','_top');
	}
}

function buildtree(node)
{
	activeNode=node;
	
	$.ajax({
		url : ajaxTreeUrl,
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

		if (d.id==undefined) continue;
			
		var shouldHighlight=(useHighLight===true && shouldHighlightNode(d.id));

		progeny+=
			'<li class="child '+(!d.has_children?'no-expand':'')+'" id="node-'+d.id+'">'+
				(shouldHighlight ? '<span class="highlight-node">' : '' )+
				(d.has_children ?'<a href="#" onclick="buildtree('+d.id+');return false;">'+d.label+'</a>':d.label)+
				(d.rank_label ? '<span class="rank">'+d.rank_label+'</span>' : '' )+
//				(nodeCountType=='species' && d.child_count && d.child_count.total>0 ?
//					'<span class="child-count">'+d.child_count.total+'/'+d.child_count.established+'</span>' : '')+
//				(nodeCountType=='taxon' && d.child_count && d.child_count>0 ?
				(d.child_count && d.child_count>0 ? '<span class="child-count">' + d.child_count+ '</span>' : '' )+
				(shouldHighlight ? '</span>' : '' )+
				'<a href="#" onclick="detailLink('+d.id+',\''+escape(d.label)+'\');return false;" class="detail-link">'+detailLinkLabel+'</a> \
			</li>';
	}
	
	if (progeny) progeny='<ul id="children-'+data.node.id+'">'+progeny+'</ul>';

	var buffer=
		'<ul class="top">'+
			'<li class="child">'+
				(!activeNode ?
					'<a href="#" onclick="buildtree(false);return false">'+
						(data.node.parent_id==null && getShowUpperTaxon()==false && getRootNodeLabel()!="" ? getRootNodeLabel() : data.node.label)+'</a>' :
					'<a href="#" onclick="$( \'#children-'+data.node.id+'\' ).toggle();return false">'+data.node.label+'</a>'
				)+
				/*
				( (data.node.parent_id==null && getShowUpperTaxon()==false) || data.node.rank_label.length==0 ? 
					'' :
					'<span class="rank">'+data.node.rank_label+'</span>'
				)+
				(nodeCountType=='species' && data.node.child_count && data.node.child_count.total>0 && !activeNode ?
					'<span class="child-count">'+data.node.child_count.total+' soorten in totaal / '+data.node.child_count.established+' gevestigde soorten</span>' :
					'' 
				)+
				(nodeCountType=='species' &&data.node.child_count && data.node.child_count.total>0 && activeNode ?
					'<span class="child-count">'+data.node.child_count.total+'/'+data.node.child_count.established+'</span>' :
					'' 
				)+
				(nodeCountType=='taxon' && data.node.child_count && data.node.child_count>0 && !activeNode ?
					'<span class="child-count">'+data.node.child_count+' taxa</span>' :
					'' 
				)+
				(nodeCountType=='taxon' &&data.node.child_count && data.node.child_count>0 && activeNode ?
					'<span class="child-count">'+data.node.child_count+'</span>' :
					'' 
				)+
				*/
				(data.node.child_count && data.node.child_count>0 && !activeNode ?
					'<span class="child-count">'+data.node.child_count+' '+_('taxa')+'</span>' :
					'' 
				)+
				(data.node.child_count && data.node.child_count>0 && activeNode ?
					'<span class="child-count">'+data.node.child_count+'</span>' :
					'' 
				)+



				(!activeNode && getShowUpperTaxon()==false ?
					'':
					'<a href="#" onclick="detailLink('+data.node.id+',\''+escape(data.node.label)+'\');return false;" class="detail-link">'+detailLinkLabel+'</a>'
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
		// LINNA-1167: trim leading zeroes from activeNode!
		activeNode = +activeNode;
		$( "#node-"+activeNode ).replaceWith( buffer );
	}
	
}

function storetree()
{
	var tree=$( "#"+container ).html().replace(/\n/ig,'').replace(/(\s+)/ig,' ');
	$.ajax({
		url : ajaxTreeUrl,
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
		url : ajaxTreeUrl,
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
		url : ajaxTreeUrl,
		type: "POST",
		data : ({
			action : 'get_parentage' ,
			id : id ,
			time : allGetTimestamp()
		}),
		success : function (data)
		{
			//console.log(data);
			var data=$.parseJSON(data);
			if (data) {
                for (index = 1; index < data.length; ++index) {
                    addAutoExpandNode(data[index]);
                }
            }
			//console.log(autoExpandArray);
			buildtree(false);
			//checkAutoExpand();
		}
	});
}

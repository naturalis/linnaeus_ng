var activeNode=false;
var container='tree-container';
var url='ajax_interface_tree.php';
var autoExpandArray=Array();
var highlightNodes=Array();
var topLevelLabel=_('Taxonomische boom');
var taxonCountStyle='species_established'; // species_established,species_only,none
var includeSpeciesStats=true;
var inititalExpansionLevel=null;

function setTopLevelLabel(label)
{
	topLevelLabel=label;
}

function getTopLevelLabel()
{
	return topLevelLabel;
}

function setTaxonCountStyle(style)
{
	taxonCountStyle=style;
}

function getTaxonCountStyle()
{
	return taxonCountStyle;
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
			count : 'species', // species, taxon, none
			time : allGetTimestamp()
		}),
		success : function (data)
		{
			//console.log(data);
			var data=$.parseJSON(data);
			
			if ( inititalExpansionLevel!=null && data.node.parentage && data.node.parentage.length < inititalExpansionLevel )
			{
				for(var i=0;i<data.progeny.length;i++)
				{
					autoExpandArray.push(data.progeny[i].id)
				}
			}

			growbranches(data);
			storetree();
			checkAutoExpand();
		}
	});
}

function setInitialExpansionLevel( n )
{
	inititalExpansionLevel=n;
}

function unsetInitialExpand()
{
	setInitialExpansionLevel( null )
}

function formatTaxonCount(total,established,print_labels)
{
	if (taxonCountStyle=='none') return '';
	
	var buffer='<span class="child-count">';
	
	if (taxonCountStyle=='species_only')
	{
		buffer += print_labels ? sprintf(_('%s soorten'),total) : total;
	}
	else
	{
		buffer += sprintf( ( print_labels ? _('%s soorten in totaal / %s gevestigde soorten') : '%s / %s' ) , total, established )
	}
		
	return buffer + '</span>';
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

		var label =
			d.name ?
				'<span class="common-name">'+d.name +' (</span><span class="sci-name">'+d.taxon+'</span><span class="common-name">)</span>' :
				'<span class="sci-name">'+d.taxon+'</span>';

		progeny+=
			'<li class="child '+(!d.has_children?'no-expand':'')+'" id="node-'+d.id+'">'+
				(shouldHighlight ? '<span class="highlight-node">' : '' )+
				(d.has_children ?
					'<a href="#" onclick="unsetInitialExpand();buildtree('+d.id+');return false;">'+label+'</a>' : label) +
				(d.rank_label ? '<span class="rank">'+d.rank_label+'</span>' : '' ) +
				(includeSpeciesStats && d.child_count && d.child_count.total>0 ? formatTaxonCount(d.child_count.total,d.child_count.established,false) : '' ) +
				(shouldHighlight ? '</span>' : '' )+
				'<a href="nsr_taxon.php?id='+d.id+'" class="detail-link"></a> \
			</li>';
	}
	
	if (progeny) progeny='<ul id="children-'+data.node.id+'">'+progeny+'</ul>';
	
	var label =
		data.node.name ?
			'<span class="common-name">'+data.node.name +' (</span><span class="sci-name">'+data.node.taxon+'</span><span class="common-name">)</span>' :
			'<span class="sci-name">'+data.node.taxon+'</span>';

	var buffer=
		'<li class="child">'+
			(!activeNode ?
				//'<a href="#" onclick="unsetInitialExpand();buildtree(false);return false">'+data.node.label+'</a>' :
				'<a href="#" onclick="unsetInitialExpand();buildtree(false);return false">'+getTopLevelLabel()+'</a>' :
				'<a href="#" onclick="$( \'#children-'+data.node.id+'\' ).toggle();return false">'+label+'</a>'
			)+
			(data.node.rank_label ? 
				'<span class="rank">'+data.node.rank_label+'</span>' : 
				'' 
			)+
			(includeSpeciesStats && data.node.child_count && data.node.child_count.total>0 && !activeNode ?
				//'<span class="child-count">'+data.node.child_count.total+' soorten in totaal / '+data.node.child_count.established+' gevestigde soorten</span>' :
				formatTaxonCount(data.node.child_count.total,data.node.child_count.established,true) : '' 
			)+
			(includeSpeciesStats && data.node.child_count && data.node.child_count.total>0 && activeNode ?
				formatTaxonCount(data.node.child_count.total,data.node.child_count.established,false) : '' 
			)+
			(!activeNode ?
				'':
				'<a href="nsr_taxon.php?id='+data.node.id+'" class="detail-link"></a>'
			)+
			progeny+
		'</li>';
		
	if (data.node.is_top)
	{
		buffer='<ul class="tree-top">'+buffer+'</ul>';
	}
				
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
			if (data) {
                for (index = 1; index < data.length; ++index) {
                    addAutoExpandNode(data[index]);
                }
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

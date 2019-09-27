{include file="../shared/header.tpl" title="{t}Taxonomic tree{/t}"}
	<div class="page-generic-div">
		<ul class="tabs" style="background-color: white;">
			<li class="tab">
				<a href="../search/search.php">{t}Full search{/t}</a>
			</li>
			{if $hasTraits}
			<li class="tab">
				<a href="../search/nsr_search_extended.php">{t}Filter species{/t}</a>
			</li>
			{/if}
			<li class="tab-active">
				<a href="#">{t}Taxonomic tree{/t}</a>
			</li>
		</ul>
	</div>
	
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
			time : Date.now()
		}),
		success : function (data)
		{
			//console.log(data);
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
		//'<ul>'+
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
					'<span class="child-count">'+data.node.child_count.total+' {t}soorten in totaal{/t} / '+data.node.child_count.established+' {t}gevestigde soorten{/t}</span>' :
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
			'</li>';
		//'</ul>';
				
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
			time : Date.now()
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
			time : Date.now()
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


<div id="dialogRidge">

	<div id="content" class="taxon-detail">

		<div id="taxonHeader" class="hasImage">
			<div id="tree-container">{if !$isPublished}{t}No data available.{/t}{/if}</div>
		</div>

	</div>

</div>


{if $isPublished}
<script type="text/JavaScript">
$(document).ready(function()
{
	restoretree();
});
</script>
{/if}





			<div id="allLookupList" class="allLookupListInvisible"></div>
			</div>
			</div>
			</div>
			<div id="bottombar" class="navbar navbar-inverted">
				<div class="container">
					<ul class="footer-menu__list">
						<li>
							<a href="http://linnaeus.naturalis.nl/" target="_blank">
								Linnaeus
							</a>
						</li>
						<li>
							<a href="../../../admin/views/users/login.php">Login</a>
						</li>
						<li>
							<span class="decode">{$contact}</span>
						</li>
						<li>
							<a target="_blank" href="http://www.naturalis.nl">
								Naturalis Biodiversity Center
							</a>	
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/JavaScript">
$(document).ready(function()
{
	//http://fancyapps.com/fancybox/3/docs/#options
	$('[data-fancybox]').fancybox({
		arrows : false,
		infobar : true,
		animationEffect : false
	});

	$(".inline-video").each(function()
	{
		$_me = $(this);

        $_me
            .removeAttr('onclick')
				.attr('onClick', 'showVideo("' + arr_arguments[1] + '","' + arr_arguments[3] +'");');

		arr_arguments = $_me.attr("onclick").split("'");
	});


	if( jQuery().prettyDialog )
	{
		$("a[rel^='prettyPhoto']").prettyDialog();
	}

	{if $search}onSearchBoxSelect('');{/if}
	{foreach from=$requestData key=k item=v}
	{if !$v|@is_array}
	addRequestVar('{$k}','{$v|@addslashes}')
	{/if}
	{/foreach}
	chkPIDInLinks({$session.app.project.id},'{$addedProjectIDParam}');
	{if $searchResultIndexActive}
	searchResultIndexActive = {$searchResultIndexActive};
	{/if}

})
</script>
</body>
</html>


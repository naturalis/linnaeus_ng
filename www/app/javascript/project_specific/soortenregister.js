var suggestionMinInputLength=1;
var search,dosearch,listdata,suggestiontype,matchtype;
var activesuggestion=-1;

function nbcPrettyPhotoInit() {

 	$("a[rel^='prettyPhoto']").prettyPhoto({
		allow_resize:true,
		animation_speed:50,
 		opacity: 0.70, 
		show_title: false,
 		overlay_gallery: false,
 		social_tools: false
 	});

}

function prettyPhotoCycle()
{
	var id_last=$('#results-per-page').val() ? $('#results-per-page').val()-1 : 0;
	var link_prev=$('#paginator-prev-link').attr('href') ? $('#paginator-prev-link').attr('href')+'#prettyPhoto[gallery]/'+id_last+'/' : null;
	var link_next=$('#paginator-next-link').attr('href') ? $('#paginator-next-link').attr('href')+'#prettyPhoto[gallery]/0/' : null;
	
	if (set_position==0 && link_prev)
	{
		$('.pp_previous').unbind().bind('click',function(){window.open(link_prev,'_self')});
	} else
	if (set_position==$(pp_images).size()-1 && link_next)
	{
		$('.pp_next').unbind().bind('click',function(){window.open(link_next,'_self')});
	}
}


function retrieveSuggestions()
{
	var type=getSuggestionType();
	search=$('#'+type).val();

	hideSuggestions();
	validateSearch();

	if (!dosearch) return;

	$.ajax({
		url : 'nsr_ajax_interface.php',
		type: "POST",
		data : ({
			action : type+'_suggestions',
			search : search,
			match : getMatchType(),
			time : allGetTimestamp()
		}),
		success : function (data) {
			//console.log(data);
			if (!data) return;
			setListData($.parseJSON(data));
			showSuggestions();
			buildSuggestions();
		}
	});	
	
}

function setSuggestionType(type)
{
	suggestiontype=type;
}

function getSuggestionType()
{
	return suggestiontype;
}

function setMatchType(type)
{
	matchtype=type;
}

function getMatchType()
{
	return matchtype;
}

function setListData(data)
{
	listdata=data;
}

function getListData()
{
	return listdata;
}

function hideSuggestions(ele)
{
	if (ele)
		$(ele).hide();
	else
		$('div[id*=suggestion]').hide();
}

function validateSearch()
{
	dosearch=false;
	if (search.length>=suggestionMinInputLength) dosearch=true;
}

function showSuggestions()
{
	if (listdata && listdata.length>0) $('#'+getSuggestionType()+'_suggestion').show();
}

function setSuggestionId(ele)
{
	$('#'+getSuggestionType()+'_id').val($(ele).attr('ident'));
	$('#'+getSuggestionType()).val($(ele).html());
	hideSuggestions();
}

function buildSuggestions()
{
	var d=Array();
	for(var i in listdata) {
		var l=listdata[i];
		d.push('<li id="item-'+i+'" '+(l.id ? ' ident="'+l.id+'" ' : '' )+'onclick="setSuggestionId(this);" onmouseover="activesuggestion=-1">'+l.label+'</li>');
	}

	$('#'+getSuggestionType()+'_suggestion').html('<ul>'+d.join('')+'</ul>');
}

function doSuggestions(p)
{
	setSuggestionType(p.type);
	setMatchType(p.match);
	retrieveSuggestions();
}

function bindKeys()
{
	
	$('div[id$=_suggestion]').each(function(e) {
		
		var ele=$(this).attr('id').replace('_suggestion','');
		var match=$(this).attr('match');

		$('#'+ele).keyup(function(e) {
			if (e.keyCode==27) { // esc
				hideSuggestions();
				return;
			}
			$('#'+ele+'_id').val('');
			doSuggestions({type:ele,match:match});
		});
	
	});
	
/*
38 up
40 down
13 enter
*/			

}

function sortResults(ele)
{
	sortAttr=$(ele).val();
	$('div .result').sortElements(function(a, b){
		return ($(a).attr(sortAttr) > $(b).attr(sortAttr) ? 1 : -1);
	});
}



/* TAXONOMIC TREE */
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

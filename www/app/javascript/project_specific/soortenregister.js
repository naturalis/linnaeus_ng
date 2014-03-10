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

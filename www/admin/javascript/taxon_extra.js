var parent={};
var prevnew={};
var valid_name={};
var currentrank=null;
var ranks=Array();

function taxonomyCollectRanks()
{
	$("#rank > option").each(function() {
		ranks.push( { label:this.text, id:this.value } );
	});
}

function taxonomyAuthorshipSplit()
{
	var str=$( "#authorship" ).val();
	$( "#name_author" ).val( str.substring(0,str.lastIndexOf(',')).replace(/^\(/,'') );
	$( "#authorship_year" ).val( str.substring(str.lastIndexOf(',')+1).replace(/\)$/,'') );
}

function taxonomyEditParent()
{
	$( '#parent' ).html('<input type="text" id="parent-list-input">');
	$( '#parent-list-input' ).bind('keyup', function(e) { species_lookup_list(e); } );
	$( '#parent-list-input' ).focus();
}

function taxonomyCloseList()
{
	$( '#parent-list' ).html('');
	$( '#parent-list' ).toggle(false);
}

function taxonomySetParent(ele)
{
	$( '#parent' ).html($(ele).text());
	$( '#new_parent_id' ).val($(ele).attr('taxon-id'));
	$( '#revert-link' ).toggle(true);
	prevnew = { id: $(ele).attr('taxon-id'), taxon: $(ele).text() };
	taxonomyCloseList();
}

function taxonomyRevertParent()
{
	$( '#parent' ).html(parent.taxon);
	$( '#new_parent_id' ).val(null);
	taxonomyCloseList();
}

function taxonomySetPreviousChoice()
{
	$( '#parent' ).html(prevnew.taxon);
	$( '#new_parent_id' ).val(prevnew.id);
}

function species_lookup_list(e) 
{
	if (e.keyCode == 27)
	{ 
		taxonomySetPreviousChoice();
		taxonomyCloseList();
		return;
	}
	
	var text=$('#parent-list-input').val();

	if (text.length<3)
		return;

	$.ajax({
		url : "../species/ajax_interface.php",
		type: "POST",
		data : ({
			'action' : 'get_lookup_list' ,
			'search' : text,
			'get_all' : 0,
			'match_start' : 0,
			'taxa_only': 1,
			'max_results': 25,
			'formatted': 0,
			'rank_above': currentrank,
			'time' : allGetTimestamp()
		}),
		success : function (data)
		{	
			build_list($.parseJSON(data));
		}
	});

}

function build_list(data)
{
	var buffer=Array();

	buffer.push('<li><a href="#" onclick="taxonomyCloseList();taxonomySetPreviousChoice();" style="margin-bottom:10px">close</a></li>');

	for(var i in data.results)
	{
		var t=data.results[i];
		
		if (t.label)
		{
			for(var r in ranks)
			{
				if (ranks[r].id==t.rank_id)
					var rank=ranks[r].label;
			}
			buffer.push('<li><a href="#" onclick="taxonomySetParent(this)" taxon-id="'+t.id+'">'+t.label+'</a> ('+rank+')</li>');
		}
	}

	$('#parent-list').html('<ul>'+buffer.join('')+'</ul>');

	allStickElementUnderElement('parent','parent-list');

	$('#parent-list').toggle(true);
	
}

function taxonomyRevertValidName()
{
	$('#uninomial').val(valid_name.uninomial);
	$('#specific_epithet').val(valid_name.specific_epithet);
	$('#infra_specific_epithet').val(valid_name.infra_specific_epithet);
	$('#authorship').val(valid_name.authorship);
	$('#name_author').val(valid_name.name_author);
	$('#authorship_year').val(valid_name.authorship_year);
}

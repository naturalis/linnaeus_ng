var dataid=null;
var taxonrank=null;
var values=new Array();

function checkunsavedvalues()
{
	for(var i in values)
	{
		if (values[i].new)
		{
			return "Niet alle data is opgelagen!\nPagina toch verlaten?";
		}
	}

}
	
function dolookuplist(p) 
{
	e=p.e;
	callback=p.callback;
	minlength=p.minlength ? p.minlength : 3;
	data=p.data;

	if (e.keyCode == 27)
	{ 
		closedropdownlist();
		return;
	}

	if (data.search.length<minlength)
		return;

	data.time = allGetTimestamp();

	$.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : data,
		success : function (data)
		{
			//console.log(data);
			callback($.parseJSON(data));
		}
	});

}

function showdropdownlist(parent)
{
	allStickElementUnderElement(parent,'dropdown-list');
	$('#dropdown-list').toggle(true);
}

function closedropdownlist()
{
	$( '#dropdown-list-content' ).html('');
	$( '#dropdown-list' ).toggle(false);
}

function toggleedit(ele)
{
	var mode=$(ele).next('span').is(':visible') ? 'cancel' : 'edit';
	$(ele).html(mode=='edit' ? 'cancel' : 'edit');
	$(ele).next().toggle();

	if (mode=='cancel')
	{
		setnewvalue($(ele).attr('rel'));
	}
}

function setnewvalue(name,value)
{
	for (i in values)
	{
		if (values[i].name==name)
		{
			delete values[i].new;
			delete values[i].delete;

			if (value && value!=values[i].current)
			{	
				values[i].new=value;
			}
			else
			if (!value && values[i].current)
			{	
				values[i].delete=true;
			}
		}
	}
	console.dir(values);
}

function storedata(ele)
{
	setnewvalue($(ele).attr('id'),$(ele).val());
}

function savedataform()
{
	form = $("<form method=post></form>");
	form.append('<input type="hidden" name="id" value="'+dataid+'" />');
	form.append('<input type="hidden" name="action" value="save" />');

	for (i in values)
	{
		var val=values[i];
		if ((val.new && val.new!=val.current) || val.delete)
		{
			form.append('<input type="hidden" name="'+val.name+'[current]" value="'+val.current+'" />');
			if (val.delete)
				form.append('<input type="hidden" name="'+val.name+'[delete]" value="1" />');
			else
				form.append('<input type="hidden" name="'+val.name+'[new]" value="'+val.new+'" />');
		}
	}

	$(window).unbind('beforeunload');
	$('body').append(form);
	form.submit();
}




function editparent(ele)
{
	var mode=$(ele).next('span').is(':visible') ? 'cancel' : 'edit';

	if (mode=='edit')
	{
		$( '#parent' ).html('');
		closedropdownlist();
		setnewvalue($(ele).attr('rel'));
	}
	else
	{
		$( '#parent' ).html('<input type="text" id="parent-list-input" value="" placeholder="type to find"/>');
		$( '#parent-list-input' ).bind('keyup', function(e) { 
			dolookuplist({
				e:e,
				data : {
					'action' : 'species_lookup' ,
					'search' : $(this).val(),
					'get_all' : 0,
					'match_start' : 1,
					'taxa_only': 1,
					'max_results': 100,
					'formatted': 0,
					'rank_above': taxonrank
				},
				callback : buildparentlist 
			} )
		} );
		$( '#parent-list-input' ).focus();
	}
}

function buildparentlist(data)
{
	var buffer=Array();
	for(var i in data.results)
	{
		var t=data.results[i];
		if (t.label && t.id)
		{
			buffer.push('<li><a href="#" onclick="setparent(this);closedropdownlist();return false;" id="'+t.id+'">'+t.label+'</a></li>');
		}
	}

	$('#dropdown-list-content').html('<ul>'+buffer.join('')+'</ul>');

	showdropdownlist('parent');
}

function setparent(ele)
{
	setnewvalue('parent_taxon_id',$(ele).attr('id'));
	$( '#parent' ).html($(ele).text());
}




function editexpert(ele)
{
	var mode=$(ele).next('span').is(':visible') ? 'cancel' : 'edit';

	if (mode=='edit')
	{
		$( '#expert' ).html('');
		closedropdownlist();
		setnewvalue($(ele).attr('rel'));
	}
	else
	{
		$( '#expert' ).html('<input type="text" id="expert-list-input" value="" placeholder="type to find"/>');
		$( '#expert-list-input' ).bind('keyup', function(e) { 
			dolookuplist({
				e:e,
				minlength: 1,
				data : {
					action : 'expert_lookup' ,
					search : $(this).val(),
					get_all : 0,
					match_start : 1,
					max_results: 100,
				},
				callback : buildexpertlist 
			} )
		} );
		$( '#expert-list-input' ).focus();
		
	}
}

function buildexpertlist(data)
{
	var buffer=Array();

	buffer.push('<li><a href="#" onclick="setexpert(this);closedropdownlist();return false;" id="-1">n.v.t.</a></li>');

	for(var i in data.results)
	{
		var t=data.results[i];
		if (t.label && t.id && t.is_company!='1')
		{
			buffer.push('<li><a href="#" onclick="setexpert(this);closedropdownlist();return false;" id="'+t.id+'">'+t.label+'</a></li>');
		}
	}

	$('#dropdown-list-content').html('<ul>'+buffer.join('')+'</ul>');
	
	showdropdownlist('expert');
	
}

function setexpert(ele)
{
	setnewvalue('presence_expert_id',$(ele).attr('id'));
	$( '#expert' ).html($(ele).text());
}



function editpresenceorg(ele)
{
	var mode=$(ele).next('span').is(':visible') ? 'cancel' : 'edit';

	if (mode=='edit')
	{
		$( '#presenceorg' ).html('');
		closedropdownlist();
		setnewvalue($(ele).attr('rel'));
	}
	else
	{
		$( '#presenceorg' ).html('<input type="text" id="organisation-list-input" value="" placeholder="type to find"/>');
		$( '#organisation-list-input' ).bind('keyup', function(e) { 
			dolookuplist({
				e:e,
				minlength: 1,
				data : {
					action : 'expert_lookup' ,
					search : $(this).val(),
					get_all : 0,
					match_start : 1,
					max_results: 100,
				},
				callback : buildorganisationlist 
			} )
		} );
		$( '#expert-list-input' ).focus();
		
	}
}

function buildorganisationlist(data)
{
	var buffer=Array();

	buffer.push('<li><a href="#" onclick="setpresenceorg(this);closedropdownlist();return false;" id="-1">n.v.t.</a></li>');

	for(var i in data.results)
	{
		var t=data.results[i];
		if (t.label && t.id && t.is_company=='1')
		{
			buffer.push('<li><a href="#" onclick="setpresenceorg(this);closedropdownlist();return false;" id="'+t.id+'">'+t.label+'</a></li>');
		}
	}

	$('#dropdown-list-content').html('<ul>'+buffer.join('')+'</ul>');
	
	showdropdownlist('presenceorg');
	
}

function setpresenceorg(ele)
{
	setnewvalue('presence_organisation_id',$(ele).attr('id'));
	$( '#presenceorg' ).html($(ele).text());
}



function editreference(ele)
{
	var mode=$(ele).next('span').is(':visible') ? 'cancel' : 'edit';

	if (mode=='edit')
	{
		$( '#reference' ).html('');
		closedropdownlist();
		setnewvalue($(ele).attr('rel'));
	}
	else
	{
		$( '#reference' ).html('<input type="text" id="reference-list-input" value="" placeholder="type to find"/>');
		$( '#reference-list-input' ).bind('keyup', function(e) { 
			dolookuplist({
				e:e,
				minlength: 3,
				data : {
					action : 'reference_lookup' ,
					search : $(this).val(),
					get_all : 0,
					match_start : 1,
					max_results: 100,
				},
				callback : buildreferencelist 
			} )
		} );
		$( '#reference-list-input' ).focus();
		
	}
}

function buildreferencelist(data)
{
	console.dir(data);

	var buffer=Array();

	buffer.push('<li><a href="#" onclick="setreference(this);closedropdownlist();return false;" id="-1">n.v.t.</a></li>');

	for(var i in data.results)
	{
		var t=data.results[i];
		if (t.label && t.id)
		{
			buffer.push('<li><a href="#" onclick="setreference(this);closedropdownlist();return false;" id="'+t.id+'">'+
				'"'+t.label+'"'+(t.author ? ", "+t.author : "")+(t.date ? " ("+t.date+")" : "")+
			'</a></li>');
		}
	}

	$('#dropdown-list-content').html('<ul class="signed">'+buffer.join('')+'</ul>');
	
	showdropdownlist('reference');
	
}

function setreference(ele)
{
	setnewvalue('presence_reference_id',$(ele).attr('id'));
	$( '#reference' ).html($(ele).text());
}

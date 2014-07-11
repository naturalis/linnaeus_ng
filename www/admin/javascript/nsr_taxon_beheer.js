var dataid=null;
var parentid=null;
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
	targetvar=p.targetvar;

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
			callback($.parseJSON(data),targetvar);
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
}

function setnewvalue(p)
{
	name=p.name;
	value=p.value;
	revert=p.revert;
	
	for (i in values)
	{
		if (values[i].name==name)
		{
			delete values[i].new;
			delete values[i].delete;

			if (revert)
			{	
				// do nothing
				//values[i].current=values[i].current;
			}
			else
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
	//console.dir(values);
}

function storedata(ele)
{
	setnewvalue({name:$(ele).attr('id'),value:$(ele).val()});
}

function checkmandatory()
{
	for (i in values)
	{
		var val=values[i];
		if (
			val.mandatory && 
			(
				(val.new && val.new.length==0) ||
				(!val.new && val.current.length==0) ||
				(val.delete))
			) 
		{
			alert('Vul alle verplichte velden in.');
			return false;
		}
	}
	return true;
}


function savedataform()
{
	if (!checkmandatory())
		return;
	
	form = $("<form method=post></form>");
	form.append('<input type="hidden" name="action" value="save" />');

	if (dataid)
	{
		form.append('<input type="hidden" name="id" value="'+dataid+'" />');
	}
	if (parentid)
	{
		form.append('<input type="hidden" name="parentid" value="'+parentid+'" />');
	}

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
	var targetvar=$(ele).attr('rel');

	if (mode=='edit')
	{
		$( '#parent' ).html('');
		closedropdownlist();
		setnewvalue({name:$(ele).attr('rel'),revert:true});
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
				callback: buildparentlist,
				targetvar: targetvar
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
	setnewvalue({name:'parent_taxon_id',value:$(ele).attr('id')});
	$( '#parent' ).html($(ele).text());
}



function editexpert(ele)
{
	var mode=$(ele).next('span').is(':visible') ? 'cancel' : 'edit';
	var targetvar=$(ele).attr('rel');

	if (mode=='edit')
	{
		$( '#expert' ).html('');
		closedropdownlist();
		setnewvalue({name:$(ele).attr('rel'),revert:true});
	}
	else
	{
		$( '#expert' ).html('<input type="text" id="expert-list-input" value="" placeholder="type to find"/>');
		$( '#expert-list-input' ).bind('keyup', function(e) { 
			dolookuplist({
				e:e,
				minlength: 1,
				data : {
					action : 'expert_lookup',
					search : $(this).val(),
					get_all : 0,
					match_start : 1,
					max_results: 100,
				},
				callback : buildexpertlist,
				targetvar: targetvar
			} )
		} );
		$( '#expert-list-input' ).focus();
		
	}
}

function buildexpertlist(data,targetvar)
{
	var buffer=Array();

	buffer.push('<li><a href="#" onclick="setexpert(this,\''+targetvar+'\');closedropdownlist();return false;" id="-1">n.v.t.</a></li>');

	for(var i in data.results)
	{
		var t=data.results[i];
		if (t.label && t.id && t.is_company!='1')
		{
			buffer.push('<li><a href="#" onclick="setexpert(this,\''+targetvar+'\');closedropdownlist();return false;" id="'+t.id+'">'+t.label+'</a></li>');
		}
	}

	$('#dropdown-list-content').html('<ul>'+buffer.join('')+'</ul>');
	
	showdropdownlist('expert');
	
}

function setexpert(ele,targetvar)
{
	setnewvalue({name:targetvar,value:$(ele).attr('id')});
	$( '#expert' ).html($(ele).text());
}



function editorganisation(ele)
{
	var mode=$(ele).next('span').is(':visible') ? 'cancel' : 'edit';
	var targetvar=$(ele).attr('rel');

	if (mode=='edit')
	{
		$( '#presence' ).html('');
		closedropdownlist();
		setnewvalue({name:$(ele).attr('rel'),revert:true});
	}
	else
	{
		$( '#organisation' ).html('<input type="text" id="organisation-list-input" value="" placeholder="type to find"/>');
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
				callback: buildorganisationlist ,
				targetvar: targetvar
			} )
		} );
		$( '#expert-list-input' ).focus();
		
	}
}

function buildorganisationlist(data,targetvar)
{
	var buffer=Array();

	buffer.push('<li><a href="#" onclick="setorganisation(this,\''+targetvar+'\');closedropdownlist();return false;" id="-1">n.v.t.</a></li>');

	for(var i in data.results)
	{
		var t=data.results[i];
		if (t.label && t.id && t.is_company=='1')
		{
			buffer.push('<li><a href="#" onclick="setorganisation(this,\''+targetvar+'\');closedropdownlist();return false;" id="'+t.id+'">'+t.label+'</a></li>');
		}
	}

	$('#dropdown-list-content').html('<ul>'+buffer.join('')+'</ul>');
	
	showdropdownlist('organisation');
	
}

function setorganisation(ele,targetvar)
{
	setnewvalue({name:targetvar,value:$(ele).attr('id')});
	$( '#organisation' ).html($(ele).text());
}



function editreference(ele)
{
	var mode=$(ele).next('span').is(':visible') ? 'cancel' : 'edit';
	var targetvar=$(ele).attr('rel');

	if (mode=='edit')
	{
		$( '#reference' ).html('');
		closedropdownlist();
		setnewvalue({name:$(ele).attr('rel'),revert:true});
	}
	else
	{
		$( '#reference' ).html('<input type="text" id="reference-list-input" value="" placeholder="type to find"/>');
		$( '#reference-list-input' ).bind('keyup', function(e) { 
			dolookuplist({
				e:e,
				minlength: 1,
				data : {
					action : 'reference_lookup' ,
					search : $(this).val(),
					get_all : 0,
					match_start : 1,
					max_results: 100,
				},
				callback: buildreferencelist,
				targetvar: targetvar 
			} )
		} );
		$( '#reference-list-input' ).focus();
		
	}
}

function buildreferencelist(data,targetvar)
{
	var buffer=Array();

	buffer.push('<li><a href="#" onclick="setreference(this,\''+targetvar+'\');closedropdownlist();return false;" id="-1">n.v.t.</a></li>');

	for(var i in data.results)
	{
		var t=data.results[i];
		if (t.label && t.id)
		{
			buffer.push('<li><a href="#" onclick="setreference(this,\''+targetvar+'\');closedropdownlist();return false;" id="'+t.id+'">'+
				'"'+t.label+'"'+(t.author ? ", "+t.author : "")+(t.date ? " ("+t.date+")" : "")+
			'</a></li>');
		}
	}

	$('#dropdown-list-content').html('<ul class="signed">'+buffer.join('')+'</ul>');
	
	showdropdownlist('reference');
	
}

function setreference(ele,targetvar)
{
	setnewvalue({name:targetvar,value:$(ele).attr('id')});
	$( '#reference' ).html($(ele).text());
}


function deleteform()
{
	if (!dataid) return;

	if (confirm('Weet u het zeker?'))
	{
		form = $("<form method=post></form>");
		form.append('<input type="hidden" name="action" value="delete" />');
		form.append('<input type="hidden" name="id" value="'+dataid+'" />');
		$(window).unbind('beforeunload');
		$('body').append(form);
		form.submit();
	}
}
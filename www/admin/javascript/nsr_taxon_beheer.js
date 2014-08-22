var timerinit={timer:null,ms:0,callbacktimer:null};
var timer=timerinit;

function timedCount() {
    timer.ms++;
    timer.timer=setTimeout(function(){timedCount()}, 1);
}

function getTimedCount() {
	return timer.ms;
}

function startTimedCount() {
	var c=timer.ms;
	timer.ms=0;
	timedCount();
}

function stopTimedCount() {
    clearTimeout(timer.timer);
	timer=timerinit;
}

function registerCallbackTimer(interval,callback,parameters) {
	clearCallbackTimer();
    timer.callbacktimer=setTimeout(function(){callback(parameters)},interval+1);
}

function clearCallbackTimer() {
	clearTimeout(timer.callbacktimer);
}

var keyInterval=50;

var dataid=null;
var nameownerid=null;
var taxonrank=null;
var inheritablename=null;
var values=new Array();
var searchdata=
	{
		action: null,
		search: null,
		get_all : 0,
		match_start : 1,
		max_results: 100,
		buffer_keys: false,
		url: "ajax_interface.php"
	};

function dolookuplist(p) 
{
	if (p.buffer_keys) 
	{
		registerCallbackTimer(keyInterval,__dolookuplist,p);
		var t=getTimedCount();
		startTimedCount();
		if (t<keyInterval) return;
		clearCallbackTimer();
	}
	__dolookuplist(p);
}

function __dolookuplist(p) 
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

	if (data.url.length==0)
		return;

	data.time = allGetTimestamp();

	$.ajax({
		url : data.url,
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
	var result=true;
	var buffer='Vul alle verplichte velden in:';

	for (i in values)
	{
		var val=values[i];
		if (val.name.substr(0,2)=='__') continue;
		if (
			val.mandatory && 
			(
				(val.new && val.new.length==0) ||
				(!val.new && val.current.length==0) ||
				(val.delete))
			) 
		{
			//console.(val.name);
			if (val.label)
				buffer=buffer+"\n"+val.label;
			result=false;
		}
	}

	if (!result)
		alert(buffer);

	return result;
}

function checkcompletesets()
{
	return true;
}

function savedataform()
{
	//console.dir(values);return;
	
	if (!checkmandatory())
		return;

	if (!checkcompletesets())
		return;
	
	form = $("<form method=post></form>");
	form.append('<input type="hidden" name="action" value="save" />');

	if (dataid)
	{
		form.append('<input type="hidden" name="id" value="'+dataid+'" />');
	}
	if (nameownerid)
	{
		form.append('<input type="hidden" name="nameownerid" value="'+nameownerid+'" />');
	}

	for (i in values)
	{
		var val=values[i];

		if (val.name.substr(0,2)=='__') continue;

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

			searchdata.action='species_lookup';
			searchdata.search=$(this).val();
			searchdata.taxa_only=1;
			searchdata.formatted=0;
			searchdata.rank_above=taxonrank;
			searchdata.url='ajax_interface.php';
		
			dolookuplist({
				e:e,
				data :searchdata,
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
			buffer.push('<li><a href="#" onclick="setparent(this);closedropdownlist();return false;" id="'+t.id+'" inheritable_name="'+t.inheritable_name+'">'+t.label+'</a></li>');
		}
	}

	$('#dropdown-list-content').html('<ul>'+buffer.join('')+'</ul>');

	showdropdownlist('parent');
}

function setparent(ele)
{
	setnewvalue({name:'parent_taxon_id',value:$(ele).attr('id')});
	$( '#parent' ).html($(ele).text());
	inheritablename=$(ele).attr('inheritable_name');
	partstoname();
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
		
			searchdata.action='expert_lookup';
			searchdata.search=$(this).val();
			searchdata.url='ajax_interface.php';
		
			dolookuplist({
				e:e,
				minlength: 1,
				data : searchdata,
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
			
			searchdata.action='expert_lookup' ;
			searchdata.search=$(this).val();
			searchdata.url='ajax_interface.php';		

			dolookuplist({
				e:e,
				minlength: 1,
				data : searchdata,
				callback: buildorganisationlist ,
				targetvar: targetvar
			} );
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
		
			searchdata.action='reference_lookup';
			searchdata.search=$(this).val();
			searchdata.url='../literature2/ajax_interface.php';
		
			dolookuplist({
				e:e,
				minlength: 1,
				data : searchdata,
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
				(t.author ? t.author+" - " : "")+
				(t.label)+
				(t.date ? " ("+t.date+")" : "")+
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

function partstoname()
{
	
	if (dataid) return;

	if (inheritablename && $('#name_uninomial').val().length==0 && !$('#name_uninomial').is(":focus"))
	{
		if (inheritablename.indexOf(" ")==-1)
		{
			$('#name_uninomial').val(inheritablename).trigger('change'); 
		}
		else
		{
			var d=inheritablename.split(" ");
			$('#name_uninomial').val(d[0]).trigger('change'); 
			$('#name_specific_epithet').val(d[1]).trigger('change'); 
		}
	}
	
	var author=$('#name_authorship').val().trim();

	if (author.indexOf('(')==0 && author.lastIndexOf(')')==author.length-1)
	{
		author=author.substring(1,author.length-1);
	}

	var year="";
	var yearstart=author.lastIndexOf(" ");
	if (yearstart!=-1) {
		year=author.substr(yearstart);
		if (isNaN(year))
		{
			year="";
		}
		else
		{
			author=author.substr(0,yearstart).replace(/[,\s]+$/gm,'').trim();
		}
	}
	
	if (!$('#name_name_author').is(":focus"))
		$('#name_name_author').val(author.trim()).trigger('change'); 
	if (!$('#name_authorship_year').is(":focus"))
		$('#name_authorship_year').val(year.trim()).trigger('change'); 

	var u=$.trim($('#name_uninomial').val());
	var s=$.trim($('#name_specific_epithet').val());
	var i=$.trim($('#name_infra_specific_epithet').val());
	var a=$.trim($('#name_authorship').val());

	var taxon=(u?u+' ':'')+(s?s+' ':'')+(i?i+' ':'')+(a?a:'');

	$('#concept_taxon').val($.trim(taxon)).trigger('change'); 
	
}

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


$(document).ready(function(){

	$('body').click(function() {
		closedropdownlist();
	});

});



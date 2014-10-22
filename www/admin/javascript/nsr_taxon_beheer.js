function prettyDialog(p)
{
	$( "#dialog-message-body-content" ).html(p.content);
	$( "#dialog-message" ).dialog({
		modal: true,
		title: p.title,
		height:600,
		width:500,
		buttons: [{
			text: p.closetext ? p.closetext : _('Close'),
			click:function() { $( this ).dialog( "close" ); }
		}]
	});
};


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

function checkMandatory()
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
			if (val.label)
				buffer=buffer+"\n"+val.label;
			else
				buffer=buffer+"\n"+val.name;
			result=false;
		}
	}

	if (!result) alert(buffer);

	return result;
}

var genusBaseRankid=null;
var speciesBaseRankid=null;

function checkNameAgainstRank()
{
	var rank = $('#concept_rank_id :selected').attr('base_rank_id');
	var ranklabel = $('#concept_rank_id :selected').text();

	var p1=$('#name_uninomial').val().length;
	var p2=$('#name_specific_epithet').val().length;
	var p3=$('#name_infra_specific_epithet').val().length;

	var result=true;
	var buffer=[];

	if (rank<genusBaseRankid)
	{
		if (p1==0) buffer.push("Uninomial is niet ingevuld.");
		if (p2!=0) buffer.push("Een "+ranklabel+" kan geen soortsnaam bevatten.");
		if (p3!=0) buffer.push("Een "+ranklabel+" kan geen derde naamdeel bevatten.");
	}
	else
	if (rank>=genusBaseRankid && rank<speciesBaseRankid)
	{
		if (p1==0) buffer.push("Genus is niet ingevuld.");
		if (p2!=0) buffer.push("Een "+ranklabel+" kan geen soortsnaam bevatten.");
		if (p3!=0) buffer.push("Een "+ranklabel+" kan geen derde naamdeel bevatten.");
	}
	else
	if (rank==speciesBaseRankid)
	{
		if (p1==0) buffer.push("Genus is niet ingevuld.");
		if (p2==0) buffer.push("Soortsnaam is niet ingevuld.");
		if (p3!=0) buffer.push("Een "+ranklabel+" kan geen derde naamdeel bevatten.");
	}
	else
	if (rank>speciesBaseRankid)
	{
		if (p1==0) buffer.push("Genus is niet ingevuld.");
		if (p2==0) buffer.push("Soortsnaam is niet ingevuld.");
		if (p3==0) buffer.push("Derde naamdeel is niet ingevuld.");
	}

	if (buffer.length>0) alert(buffer.join("\n"));

	return buffer.length==0;
}

function checkPresenceDataSpecies()
{
	var rank = $('#concept_rank_id :selected').attr('base_rank_id');
	var buffer=[];

	if (rank>=speciesBaseRankid)
	{
		if ($('#presence_presence_id :selected').val()==-1)
			buffer.push("Voorkomen: status is niet ingevuld.");

		if ($('#presence_expert_id :selected').val()==-1)
			buffer.push("Voorkomen: expert is niet ingevuld.");

		if ($('#presence_organisation_id :selected').val()==-1)
			buffer.push("Voorkomen: organisatie is niet ingevuld.");

		if ($('#presence_reference_id').val().length==0)
			buffer.push("Voorkomen: publicatie is niet ingevuld.");
	}

	if (buffer.length>0) alert(buffer.join("\n"));

	return buffer.length==0;
}

function checkScientificName()
{
	var result=true;
	var buffer=[];

	if ($('#name_expert_id :selected').val().length==0) buffer.push("Wetenschappelijke naam: expert");
	if ($('#name_organisation_id :selected').val().length==0) buffer.push("Wetenschappelijke naam: organisatie");
	if ($('#name_reference_id').val().length==0) buffer.push("Wetenschappelijke naam: publicatie");

	return buffer;
}

function checkDutchName()
{
	var buffer=[];

	if ($('#dutch_name').val().length!=0)
	{
		if ($('#dutch_name_expert_id :selected').val().length==0) buffer.push("Nederlandse naam: expert");
		if ($('#dutch_name_organisation_id :selected').val().length==0) buffer.push("Nederlandse naam: organisatie");
		if ($('#dutch_name_reference_id').val().length==0) buffer.push("Nederlandse naam: publicatie");
	}

	return buffer;
}

function checkPresenceDataHT()
{
	var rank = $('#concept_rank_id :selected').attr('base_rank_id');
	
	var buffer=[];

	if (rank<speciesBaseRankid)
	{
		var p1=$('#presence_presence_id :selected').val()==-1;
		var p2=$('#presence_expert_id :selected').val()==-1;
		var p3=$('#presence_organisation_id :selected').val()==-1;
		var p4=$('#presence_reference_id').val().length==0;
			
		if ((p1 && p2 && p3 && p4)!=true)
		{
			if (p1) buffer.push("Voorkomen: status is niet ingevuld.");
			if (p2) buffer.push("Voorkomen: expert is niet ingevuld.");
			if (p3) buffer.push("Voorkomen: organisatie is niet ingevuld.");
			if (p4) buffer.push("Voorkomen: publicatie is niet ingevuld.");
		}
	}

	return buffer;
}


function savedataform()
{
	
	// lethal checks
	if (!checkMandatory()) return;
	if (!checkNameAgainstRank()) return;
	if (!checkPresenceDataSpecies()) return;

	// warnings
	var notifications=[];

	notifications=notifications.concat(
		checkScientificName(),
		checkDutchName(),
		checkPresenceDataHT()
	);
	
	if (notifications.length>0)
	{
		if (!confirm("Onderstaande velden zijn niet ingevuld. Toch opslaan?"+"\n"+notifications.join("\n"))) return;
	}
	
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


function deletedataform(style)
{
	if (style)
	{
		var msg=
			"Wilt u dit taxon markeren als verwijderd?\n"+
			"Gemarkeerde taxa worden niet werkelijk verwijderd, maar zijn niet langer zichtbaar.";
	}
	else
	{
		var msg="Wilt u dit taxon weer zichtbaar maken?";
	}
	
	if (!confirm(msg)) return;

	if (dataid)
	{
		form = $("<form method=post></form>");
		form.append('<input type="hidden" name="action" value="'+(style==false?'undelete':'delete')+'" />');
		form.append('<input type="hidden" name="rnd" value="'+$('#rnd').val()+'" />');
		form.append('<input type="hidden" name="id" value="'+dataid+'" />');

		$(window).unbind('beforeunload');
		$('body').append(form);
		form.submit();
	}
	else
	{
		alert("Fout opgetreden: geen ID gevonden.");
	}

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

	if (confirm("Weet u het zeker?"))
	{
		form = $("<form method=post></form>");
		form.append('<input type="hidden" name="action" value="delete" />');
		form.append('<input type="hidden" name="id" value="'+dataid+'" />');
		$(window).unbind('beforeunload');
		$('body').append(form);
		form.submit();
	}
}

function namepartscomplete(caller)
{
	if ($('#name_uninomial').val().length==0 && $('#name_specific_epithet').val().length!=0)
	{
		$('#name_specific_epithet').val('');
		$('#name_specific_epithet_message').
		html('Vul eerst een genus in!').toggle(true).fadeOut({duration:2000,easing:'easeInQuint'});
	}
	else
	{
		$('#name_specific_epithet_message').html('');
	}
	if ($('#name_specific_epithet').val().length==0 && $('#name_infra_specific_epithet').val().length!=0)
	{
		$('#name_infra_specific_epithet').val('');
		$('#name_infra_specific_epithet_message').
			html('Vul eerst een soortsnaam in!').toggle(true).fadeOut({duration:2000,easing:'easeInQuint'});
	}
	else
	{
//		$('#name_infra_specific_epithet_message').html('');
	}
}

function partstoname()
{

	//if (dataid) return;

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

	if (yearstart!=-1)
	{
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
		if (values[i].new && values[i].nocheck!=true)
		{
			return "Niet alle data is opgelagen!\nPagina toch verlaten?";
		}
	}

}

function getinheritablename()
{
	$.ajax({
		url : 'ajax_interface.php',
		type: "POST",
		data : {id:$('#parent_taxon_id').val(),action:'get_inheritable_name'},
		success : function (data)
		{
			inheritablename=data;
			partstoname()
		}
	});

}

function doDelete(msg)
{
	if (confirm(msg ? msg : "Weet u het zeker?"))
	{
		$( '#action' ).val('delete');
		$( '#theForm' ).submit();
	}
}

function dropListDialog(ele,title)
{	
	var target=$(ele).attr('rel');
	var id='__'+target+'_INPUT';

	prettyDialog({
		title:title,
		content :
			'<p><input type="text" class="medium" id="'+id+'" /></p> \
			 <p><div id="droplist-list-container"></div></p>'
	});

	$('#'+id).attr('autocomplete','off').bind('keyup', function(e) { 
		doNsrDropList({ e:e, id: $(this).attr('id'), target: target } )
	});	
}

function doNsrDropList(p)
{
	// calling element (input)
	var element=$('#'+p.id);
	// variable to lookup and to assign resulting value to
	var variable=element.attr('id').replace(/(^(__))/,'').replace(/((_INPUT)$)/,'');
	// value (entered text)
	var value=element.val();
	// minimal length of value to trigger list
	var minlength=$('#'+p.target).attr('droplistminlength') ? $('#'+p.target).attr('droplistminlength') : 1;

	if (value.length<minlength)
		return;

	if (variable.indexOf('reference_id')!=-1)
	{
		url = '../literature2/ajax_interface.php';
	}
	else
	{
		url = 'ajax_interface.php';
	}

	data = {
		action: variable,
		search: value,
		time: allGetTimestamp()
	}

	$.ajax({
		url : url,
		type: "POST",
		data : data,
		success : function (data)
		{
			//console.log(data);
			buildDropList($.parseJSON(data),variable);
		}
	});
	
}

function setNsrDropListValue(ele,variable)
{
	// don't change order of lines
	$('#'+variable.replace(/(_id)$/,'')).html( $(ele).attr('display-text') ? $(ele).attr('display-text') : $(ele).text() );
	$('#'+variable).val($(ele).attr('value')).trigger('change');
}

function buildDropList(data,variable)
{
	var buffer=Array();

	buffer.push('<li><a href="#" onclick="setNsrDropListValue(this,\''+variable+'\');$( \'#dialog-message\' ).dialog( \'close\' );return false;" value="-1">geen waarde toekennen</a></li>');

	if (!data.results)
	{
		buffer.push('<li>niets gevonden</li>');
	}
	else	
	{
		for(var i in data.results)
		{
			var t=data.results[i];
	
			if (variable=='dutch_name_organisation_id' && t.is_company!='1') continue;
			if (variable=='dutch_name_expert_id' && t.is_company=='1') continue;
			if (variable=='presence_organisation_id' && t.is_company!='1') continue;
			if (variable=='presence_expert_id' && t.is_company=='1') continue;
			if (variable=='name_organisation_id' && t.is_company!='1') continue;
			if (variable=='name_expert_id' && t.is_company=='1') continue;
			
			if (1==1 || variable.indexOf('reference_id')!=-1)
			{
				var label=
					(t.author ? t.author+", " : "")+
					(t.label)+
					(t.date ? " ("+t.date+")" : "");
			}
			else 
			{
				var label=t.label;
			}
	
			if (t.label && t.id)
			{
				buffer.push(
					'<li><a href="#" display-text="'+t.label.replace(/'/g,"\'")+'" title="'+label.replace(/'/g,"\'")+'" onclick="setNsrDropListValue(this,\''+variable+'\');$( \'#dialog-message\' ).dialog( \'close\' );return false;" value="'+t.id+'">'+label+'</a></li>'
				);
			}
		}
		
	}

	$('#droplist-list-container').html('<ul>'+buffer.join('')+'</ul>');

}

function disconnectimage(p)
{
	if (confirm('Weet u het zeker?'))
	{
		$('<form>', {
			'html':
				'<input type="hidden" name="action" value="delete" /> \
				<input type="hidden" name="id" value="'+p.id+'" /> \
				<input type="hidden" name="image" value="'+p.image+'" />',
			'action': window.url,
			'method': 'post'
		}).appendTo(document.body).submit();		
	}
}



$(document).ready(function(){

	closedropdownlist();

	$('body').click(function() {
		closedropdownlist();
	});
	
//	$('<div id="dropdown-list"><div id="dropdown-list-content"></div></div>').appendTo('body');
	$('<div id="dialog-message" title="title" style="display:none"><div id="dialog-message-body-content"></div></div>').appendTo('body');


});





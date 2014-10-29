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

	var p1=$('#presence_presence_id :selected').val();
	var p2=$('#presence_expert_id :selected').val();
	var p3=$('#presence_organisation_id :selected').val();
	var p4=$('#presence_reference_id').val();
		
	if (rank>=speciesBaseRankid)
	{
		if (p1==-1) buffer.push("Voorkomen: status is niet ingevuld.");
		if (p2==-1) buffer.push("Voorkomen: expert is niet ingevuld.");
		if (p3==-1) buffer.push("Voorkomen: organisatie is niet ingevuld.");
		if (p4.length==0) buffer.push("Voorkomen: publicatie is niet ingevuld.");
	}
	else
	if (rank<speciesBaseRankid)
	{
		if (p1!=-1 || p2!=-1 || p3!=-1 || p4.length!=0) 
			buffer.push("Voorkomen kan niet worden ingevuld voor hogere taxa.");
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

	if ($('#dutch_name').val() && $('#dutch_name').val().length!=0)
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


function savedataform(type)
{

	if (!checkMandatory()) return;

	if (type=='existing')
	{
		if (!checkPresenceDataSpecies()) return;
	} 
	else
	if (type!='name')
	{
		// lethal checks
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

function dropListDialog(ele,title,params)
{
	var target=$(ele).attr('rel');
	var id='__'+target+'_INPUT';

	prettyDialog({
		title:title,
		content :
			'<p><input type="text" class="medium" id="'+id+'" /></p> \
			 <p> \
			 <a href="#" onclick="setNsrDropListValue(this,\''+target+'\');$( \'#dialog-message\' ).dialog( \'close\' );return false;" display-text=" " style="font-size: 0.8em;">geen waarde toekennen</a> \
			 <div id="droplist-list-container"></div></p>'
	});

	$('#'+id).attr('autocomplete','off').bind('keyup', function(e) { 
		doNsrDropList({ e:e, id: $(this).attr('id'), target: target, params: params } )
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
	
	if (p.params) $.extend(data, p.params);

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
	$('#'+variable.replace(/(_id)$/,'')).html( $(ele).attr('display-text') ? $.trim($(ele).attr('display-text')) : $(ele).text() );
	$('#'+variable).val($(ele).attr('value')).trigger('change');
}

function buildDropList(data,variable)
{
	var buffer=Array();

//	buffer.push('<li><a href="#" onclick="setNsrDropListValue(this,\''+variable+'\');$( \'#dialog-message\' ).dialog( \'close\' );return false;" value="-1">geen waarde toekennen</a></li>');

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

var prefnames=Array();
var preferrednameid=null;
var currentnameid=null;

function storeprefname(p)
{
	prefnames.push(p);
}

function checkprefnameavail()
{
	var l=$('#name_language_id').val();
	var prefnameexists=false;
	
	for(var i=0;i<prefnames.length;i++)
	{
		if (prefnames[i].language_id==l && prefnames[i].id!=currentnameid)
		{
			prefnameexists=true;
			break;
		}
	}

	if (prefnameexists)
	{
		$('#nametype-'+preferrednameid).attr('disabled','disabled');
		$("#nametype-none").attr("selected","selected") ;
	}
	else
	{
		$('#nametype-'+preferrednameid).removeAttr('disabled');
	}
}


$(document).ready(function()
{
	$('<div id="dialog-message" title="title" style="display:none"><div id="dialog-message-body-content"></div></div>').appendTo('body');
});

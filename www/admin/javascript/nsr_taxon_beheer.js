dataid=null;
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
var main_language_display_label="nederlandse naam";
var baseRanks=Array();
var dropListSelectedTextStyle='concise'; // 'full';
var closeDialogAfterSelect=true;

function toggleedit(ele)
{
	var mode=$(ele).next('span').is(':visible') ? 'cancel' : 'edit';
	$(ele).html(mode=='edit' ? 'cancel' : 'edit');
	$(ele).next().toggle();
}

function getRankId( rank )
{
	for(var i=0;i<baseRanks.length;i++)
	{
		if (baseRanks[i].rank==rank && baseRanks[i].rank)
		{
			return baseRanks[i].id;
		}
	}
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
	var buffer=_('Vul alle verplichte velden in:');

	for (i in values)
	{
		var val=values[i];

		if (val.name.substr(0,2)=='__' || val.hidden) continue;
		if (
			val.mandatory &&
			(
				(val.new && val.new.length==0) ||
				(!val.new && val.current.length==0) ||
				(val.delete))
			)
		{
			if (val.label)
				buffer=buffer+"\n"+_(val.label);
			else
				buffer=buffer+"\n"+_(val.name);
			result=false;
		}
	}

	if (!result) alert(buffer);

	return result;
}



var genusBaseRankid=null;
var speciesBaseRankid=null;

/*
function checkAuthorshipAgainstRank()
{
	var rank = $('#concept_rank_id :selected').attr('base_rank_id');
	var ranklabel = $('#concept_rank_id :selected').text();

	var p1=$('#name_authorship').val() ? $('#name_authorship').val().length : 0;
	//var p2=$('#name_name_author').val() ? $('#name_name_author').val().length : 0;
	//var p3=$('#name_authorship_year').val() ? $('#name_authorship_year').val().length : 0;

	var result=true;
	var buffer=[];

	if (rank>genusBaseRankid)
	{
		if (p1==0) buffer.push(_('Authorship has not been entered.'));
	}

	if (buffer.length>0) alert(buffer.join("\n"));

	return buffer.length==0;
}

function checkAuthorshipAgainstRankGenus()
{
	var rank = $('#concept_rank_id :selected').attr('base_rank_id');
	var ranklabel = $('#concept_rank_id :selected').text();
	var p1=$('#name_authorship').val() ? $('#name_authorship').val().length : 0;
	var buffer=[];

	//if (rank=<genusBaseRankid)
	if ( rank==getRankId( 'genus' ) )
	{
		if (p1==0) buffer.push(_("Authorship"));
	}

	return buffer;
}
*/

function checkAuthorshipAgainstRank()
{
	var rank = $('#concept_rank_id :selected').attr('base_rank_id');
	var ranklabel = $('#concept_rank_id :selected').text();
	var p1=$('#name_authorship').val() ? $('#name_authorship').val().length : 0;
	var buffer=[];

	if ( rank>=getRankId( 'genus' ) )
	{
		if (p1==0) buffer.push(_("Authorship"));
	}

	return buffer;
}

function checkNameAgainstRank()
{
	var rank = $('#concept_rank_id :selected').attr('base_rank_id');
	var ranklabel = $('#concept_rank_id :selected').text();

	var p1=$('#name_uninomial').val() ? $('#name_uninomial').val().length : 0;
	var p2=$('#name_specific_epithet').val() ? $('#name_specific_epithet').val().length : 0;
	var p3=$('#name_infra_specific_epithet').val() ? $('#name_infra_specific_epithet').val().length : 0;

	var result=true;
	var buffer=[];

	if (rank<getRankId( 'genus' ))
	{
		if (p1==0) buffer.push( _("Uninomial has not been entered.") );
		if (p2!=0) buffer.push( sprintf(_("A %s cannot contain a species name."), ranklabel ) );
		if (p3!=0) buffer.push( sprintf(_("A %s cannot contain a third name element."), ranklabel ) );
	}
	else
	if ( ( rank>=getRankId( 'genus' ) && rank<getRankId( 'species' ) ) )
	{
		if (p1==0) buffer.push( _("Genus has not been entered.") );
		if (p2!=0) buffer.push( sprintf(_("A %s cannot contain a species name."), ranklabel ) );
		if (p3!=0) buffer.push( sprintf(_("A %s cannot contain a third name element."), ranklabel ) );
	}
	else
	if ( rank==getRankId( 'species' ) || rank==getRankId( 'nothospecies' ) )
	{
		if (p1==0) buffer.push( _("Genus has not been entered.") );
		if (p2==0) buffer.push( _("Species name has not been entered.") );
		if (p3!=0) buffer.push( sprintf(_("A %s cannot contain a third name element."), ranklabel ) );
	}
	else
	if ( rank>getRankId( 'nothospecies' ) )
	{
		if (p1==0) buffer.push( _("Genus has not been entered.") );
		if (p2==0) buffer.push( _("Species name has not been entered.") );
		if (p3==0) buffer.push( _("Third name element has not been entered.") );
	}

	if (buffer.length>0) alert(buffer.join("\n"));

	return buffer.length==0;
}

function checkPresenceDataSpecies()
{
	var rank = $('#concept_rank_id :selected').attr('base_rank_id');
	if (!rank)
	{
		rank=taxonrank;
	}


	var buffer=[];
	
	
	if ( $('#presence_presence_id').length && $('#presence_expert_id').length && $('#presence_organisation_id').length && $('#presence_reference_id').length )
	{
		var p1=$('#presence_presence_id :selected').val();
		var p2=$('#presence_expert_id :selected').val();
		var p3=$('#presence_organisation_id :selected').val();
		var p4=$('#presence_reference_id').val();
	
		if (rank>=speciesBaseRankid)
		{
			if (p1 && p1==-1) buffer.push(_("Presence: status has not been entered."));
			if (p2 && p2==-1) buffer.push(_("Presence: expert has not been entered."));
			if (p3 && p3==-1) buffer.push(_("Presence: organisation has not been entered."));
			if (p4 && p4.length==0) buffer.push(_("Presence: publication has not been entered."));
		}
		else
		if (rank<speciesBaseRankid)
		{
			if (p1!=-1 || p2!=-1 || p3!=-1 || p4.length!=0)
				buffer.push("Presence cannot be entered for higher taxa.");
		}
	}

	//if (buffer.length>0) alert(buffer.join("\n"));

	return buffer;
}

function checkPresenceDataHT()
{
	var buffer=[];
	var rank = $('#concept_rank_id :selected').attr('base_rank_id');

	if (!rank)
	{
		rank=taxonrank;
	}

	if ( $('#presence_presence_id').length && $('#presence_expert_id').length && $('#presence_organisation_id').length && $('#presence_reference_id').length )
	{
		if ( rank<speciesBaseRankid )
		{
			var p1=$('#presence_presence_id :selected').val()==-1;
			var p2=$('#presence_expert_id :selected').val()==-1;
			var p3=$('#presence_organisation_id :selected').val()==-1;
			var p4=$('#presence_reference_id').val().length==0;
	
			if ((p1 && p2 && p3 && p4)!=true)
			{
				if (p1) buffer.push(_("Presence: status has not been entered."));
				if (p2) buffer.push(_("Presence: expert has not been entered."));
				if (p3) buffer.push(_("Presence: organisation has not been entered."));
				if (p4) buffer.push(_("Presence: publication has not been entered."));
			}
		}
	}

	return buffer;
}

function checkScientificName()
{
	var result=true;
	var buffer=[];

	if ($('#name_expert_id').length && $('#name_expert_id :selected').val().length==0) buffer.push(_("Scientific name: expert"));
	if ($('#name_organisation_id').length && $('#name_organisation_id :selected').val().length==0) buffer.push(_("Scientific name: organisation"));
	if ($('#name_reference_id').length && $('#name_reference_id').val().length==0) buffer.push(_("Scientific name: publication"));

	return buffer;
}

function checkDutchName()
{
	var buffer=[];

	if ($('#main_language_name').val() && $('#main_language_name').val().length!=0)
	{
		if ($('#main_language_name_expert_id').length && $('#main_language_name_expert_id :selected').val().length==0) buffer.push(main_language_display_label + ": " + _("expert"));
		if ($('#main_language_name_organisation_id').length && $('#main_language_name_organisation_id :selected').val().length==0) buffer.push(main_language_display_label + ": " + _("organisation"));
		if ($('#main_language_name_reference_id').length && $('#main_language_name_reference_id').val().length==0) buffer.push(main_language_display_label + ": " + _("publication"));
	}

	return buffer;
}

function saveconcept()
{
	if (!checkMandatory()) return;

	var notifications=[];

	notifications=notifications.concat(
		checkPresenceDataHT(),
		checkPresenceDataSpecies()
	);
	saveform(notifications);
}

/*
function savenewconcept()
{
	if (!checkMandatory()) return;

	var notifications=[];

	if (!checkNameAgainstRank()) return;
	if (!checkAuthorshipAgainstRank()) return;

	notifications=notifications.concat(
		checkAuthorshipAgainstRankGenus(),
		checkPresenceDataSpecies(),
		checkScientificName(),
		checkDutchName(),
		checkPresenceDataHT()
	);

	saveform(notifications);
}
*/

function savenewconcept()
{
	if (!checkMandatory()) return;

	var notifications=[];

	if (!checkNameAgainstRank()) return;

	notifications=notifications.concat(
		checkAuthorshipAgainstRank(),
		checkPresenceDataSpecies(),
		checkScientificName(),
		checkDutchName(),
		checkPresenceDataHT()
	);

	saveform(notifications);
}

function savesynonym()
{
	if (!checkMandatory()) return;

	var notifications=[];

	// warnings
	notifications=notifications.concat(checkScientificName());
	saveform(notifications);
}

function savename()
{
	if (!checkMandatory()) return;
	saveform();
}

function saveform(notifications)
{
	if (notifications && notifications.length>0)
	{
		if (!confirm(_("The fields below have not been entered. Save anyway?") +"\n"+notifications.join("\n"))) return;
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
			_("Do you want to mark this taxon as deleted?") + "\n"+
			_("Marked taxa are not really deleted, but they are no longer visible.");
	}
	else
	{
		var msg= _("Do you want to make this taxon visible again?");
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
		alert(_("An error occurred: could not locate an ID."));
	}

}

function irrevocablydelete()
{
	if (!confirm(_("Are you sure you want to irrevocably delete this taxon and its associated data?"))) return;

	if (dataid)
	{
		form = $('<form method=post action="taxon_irrevocably_delete.php"></form>');
		form.append('<input type="hidden" name="rnd" value="'+$('#rnd').val()+'" />');
		form.append('<input type="hidden" name="id" value="'+dataid+'" />');

		$(window).unbind('beforeunload');
		$('body').append(form);
		form.submit();
	}
	else
	{
		alert(_("An error occurred: could not locate an ID."));
	}

}

function deleteform()
{
	if (!dataid) return;

	if (confirm("Are you sure?"))
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
			html(_('First enter a species name!')).toggle(true).fadeOut({duration:2000,easing:'easeInQuint'});
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
		year=author.substr(yearstart).trim();

		// in botany, authorship van be "L. (1753)"
		if (year.indexOf('(')==0 && year.lastIndexOf(')')==year.length-1)
		{
			year=year.substring(1,year.length-1);
		}

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
			return _('Not all data has been saved!\nLeave page anyway?');
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
	if (confirm(msg ? msg : _('Are you sure?')))
	{
		$( '#action' ).val('delete');
		$( '#theForm' ).submit();
	}
}




var timer=
	{
		threshold:200,//ms
		threshold_grace:10,//ms
		then:0,
		now:0,
		passed:999,
		keys_hit:0,
		timer_id:null
	}

function tempus_fugit( callback, params )
{
	var d=new Date();

	if (timer.keys_hit==0)
	{
		timer.then=d.getTime();
		timer.passed=999;
	}

	timer.now=d.getTime();
	timer.keys_hit++;
	timer.passed=timer.now-timer.then;
	timer.then=timer.now;

	clearTimeout( timer.timer_id );
	timer.timer_id=setTimeout( function() { callback(params) }, timer.threshold + timer.threshold_grace );

	if ( timer.passed > timer.threshold )
	{
		callback(params);
	}
}

var closeLabel = _('assign no value');
var inputPlaceHolder = _('type to find');

function setDropListCloseLabel( label )
{
	closeLabel=label;
}

function getDropListCloseLabel()
{
	return closeLabel;
}

function setInputPlaceHolder( label )
{
	inputPlaceHolder;
}

function getInputPlaceHolder()
{
	return inputPlaceHolder;
}

function dropListDialog(ele,title,params)
{
	var target=$(ele).attr('rel');
	var id='__'+target+'_INPUT';

	//onclick="setNsrDropListValue(this,\''+target+'\'); ...

	prettyDialog({
		title:title,
		content :
			'<p><input type="text" class="medium" id="'+id+'" placeholder="' + getInputPlaceHolder() + '" /></p> \
			 <p> \
			 <a href="#" onclick="setNsrDropListValue(null,\''+target+'\');$( \'#dialog-message\' ).dialog( \'close\' );return false;" display-text=" " style="font-size: 0.8em;"> \
				 ' + getDropListCloseLabel() + ' \
			</a> \
			<div id="droplist-list-container"></div> \
			</p>'
	});

	$( '#'+id ).attr( 'autocomplete' , 'off' ).bind( 'keyup' , function(e)
	{
		if (typeof tempus_fugit=="function")
		{
			tempus_fugit( doNsrDropList, { e:e, id: $(this).attr('id'), target: target, params: params } );
		}
		else
		{
			doNsrDropList( { e:e, id: $(this).attr('id'), target: target, params: params } )
		}
	}).focus();
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

	if (
		(variable.indexOf('reference_id')!=-1) ||
		(variable.indexOf('publishedin_id')!=-1) ||
		(variable.indexOf('periodical_id')!=-1)
	)
	{
		url = '../literature2/ajax_interface.php';
	}
	else
	{
		url = '../nsr/ajax_interface.php';
	}

	data = {
		action: variable,
		search: value,
		time: allGetTimestamp(),
		base_rank_id: (typeof taxonrank !== "undefined" && taxonrank) ? taxonrank : null
	}

	try {
		if (p.params.dropListSelectedTextStyle=='full') dropListSelectedTextStyle='full';
	} catch (e) {}

	try {
		if (p.params.closeDialogAfterSelect===false) closeDialogAfterSelect=p.params.closeDialogAfterSelect
	} catch (e) {}

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
	if (ele==null)
	{
		var lbl='-';
		var val=null;
	}
	else
	{
		var lbl=$(ele).attr('display-text') ? $.trim($(ele).attr('display-text')) : $(ele).text()
		var val=$(ele).attr('value');
	}
	$('#'+variable.replace(/(_id)$/,'')).html( lbl );
	$('#'+variable.replace(/(_id)$/,'')).val( lbl );
	$('#'+variable).val( val ).trigger('change');
}

function buildDropList(data,variable)
{
	var buffer=Array();

//	buffer.push('<li><a href="#" onclick="setNsrDropListValue(this,\''+variable+'\');$( \'#dialog-message\' ).dialog( \'close\' );return false;" value="-1">geen waarde toekennen</a></li>');

	if (!data.results)
	{
		buffer.push('<li>' + _('nothing found') + '</li>');
	}
	else
	{
		for(var i in data.results)
		{
			var t=data.results[i];

			if (variable=='main_language_name_organisation_id' && t.is_company!='1') continue;
			if (variable=='main_language_name_expert_id' && t.is_company=='1') continue;
			if (variable=='presence_organisation_id' && t.is_company!='1') continue;
			if (variable=='presence_expert_id' && t.is_company=='1') continue;
			if (variable=='name_organisation_id' && t.is_company!='1') continue;
			if (variable=='name_expert_id' && t.is_company=='1') continue;

			if (variable.indexOf('reference_id')!=-1)
			{
				var authors='';

				if ( t.authors )
				{
					for(var j=0;j<t.authors.length;j++)
					{
						authors += 
							t.authors[j].name +
							(t.authors.length>1 && j<t.authors.length-1 ? (j==t.authors.length-2 ? ' & ' : ', ') : "" )
							;
					}
				}
				
				var label=
					//(authors ? authors+" " : (t.author ? t.author+" " : ""))+
					(t.author ? t.author+" " : (authors ? authors+" " : ""))+
					(t.date ? t.date+". " : "")+
					(t.label);
			}
			else
			{
				var label=t.label;
			}

			if (t.label && t.id)
			{
				var disptext = dropListSelectedTextStyle=='full'? label.replace(/'/g,"\'") : t.label.replace(/'/g,"\'");
				buffer.push(
					'<li><a href="#" display-text="'+disptext+'" title="'+label.replace(/'/g,"\'")+'" onclick="\
						setNsrDropListValue(this,\''+variable+'\');\
						' + (closeDialogAfterSelect ? '$( \'#dialog-message\' ).dialog( \'close\' );' : '') + '\
						return false;\
						" value="'+t.id+'">'+label+'</a></li>'
				);
			}
		}

	}

	$('#droplist-list-container').html('<ul>'+buffer.join('')+'</ul>');

}

function disconnectimage(p)
{
	if (confirm(_('Are you sure?')))
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
	}
	else
	{
		$('#nametype-'+preferrednameid).removeAttr('disabled');
	}
}



function treeDialog(ele,title,params)
{
	prettyDialog({
		title:title,
		content :
			'<p> \
			 <a href="#" onclick="$( \'#dialog-message\' ).dialog( \'close\' );return false;" display-text=" " style="font-size: 0.8em;"> \
				 ' + getDropListCloseLabel() + ' \
			</a> \
			<div id="dialog_tree"></div> \
			</p>'
	});

	$('#dialog_tree').load( '/linnaeus_ng/admin/views/nsr/tree.php', function ()
	{
		taxonTargetUrl="javascript:selectTreeNode";
	});

}

function selectTreeNode(id,label)
{
	$( '#parent_taxon').html( unescape(label) );
	$( '#parent_taxon_id').val( id );
	$( '#dialog-message' ).dialog( 'close' );
}




var suggestionMinInputLength=3;
var search,dosearch,listdata,suggestiontype,matchtype;
var activesuggestion=-1;
var suggestionsCallback;

function nbcPrettyPhotoInit() {
	/*
 	$("a[rel^='prettyPhoto']").prettyPhoto({
		allow_resize:true,
		animation_speed:50,
 		opacity: 0.70, 
		show_title: false,
 		overlay_gallery: false,
 		social_tools: false
 	});
	*/

	$('[data-fancybox]').fancybox({
		arrows : false,
		infobar : true,
		animationEffect : false
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
	var order=$('#'+type).attr('order');

	hideSuggestions();
	validateSearch();

	if (!dosearch) return;

	$.ajax({
		url : '../search/nsr_ajax_interface.php',
		type: "POST",
		data : ({
			action : cleanSuggestionId(type)+'_suggestions',
			search : search,
			order : order,
			match : getMatchType(),
			time : allGetTimestamp()
		}),
		success : function (data)
		{
			if (!data) return;
			setListData($.parseJSON(data));
			showSuggestions();
			buildSuggestions();
		}
	});	
	
}

function cleanSuggestionId( type )
{
	var prefs=['desktop','mobile'];
	
	for (var i=0;i<prefs.length;i++)
	{
		if (type.indexOf(prefs[i])===0) 
		{
			return type.substr(prefs[i].length);
		}
	}
	return type;
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
	var searchStr=stripTags($(ele).attr("data-sci-name"));


	$('#'+cleanSuggestionId(getSuggestionType())+'_id').val($(ele).attr('ident'));
	$('#'+cleanSuggestionId(getSuggestionType())).val(stripTags($(ele).html()));
	$('input[type=text][name='+cleanSuggestionId(getSuggestionType())+']').val(searchStr);

	if ($('#formSearchFacetsSpecies').length)
	{
			$('#formSearchFacetsSpecies')
					.append('<input type=hidden value="'+$('input[type=text][name=group]').val()+'" name=group>')
					.submit();
	}
	if ($('#inlineformsearch').length)
	{
			$('#name').val(searchStr);
			$('#inlineformsearch')
					.append('<input type=hidden value="'+searchStr+'" name=group>')
					.submit();
	}

	/*
	if ($('input.zoekknop').length)
	{
		// old: user clicks search button
		// trigger an Enter keyup in the receiving input, so we can hook a submit
		var e = $.Event('keyup');
		e.which = 13;
		$('#'+getSuggestionType()).trigger(e);
		hideSuggestions();	
	}
	else
	{
		// new: no search button
		$('#formSearchFacetsSpecies').append('<input type=hidden value="'+$('input[type=text][name=group]').val()+'" name=group>');
		$('#formSearchFacetsSpecies').submit();
	}
	*/
}

var lineTpl='<li id="item-%IDX%" ident="%IDENT%" onclick="setSuggestionId(this);" onmouseover="activesuggestion=-1" data-sci-name="%SCIENTIFIC_NAME_DATA%">%LABEL%</li>';

function buildSuggestions()
{
	if (getSuggestionType()=='name' && fetchTemplate( 'lineTpl' )!=='')
	{
		thisTpl=fetchTemplate( 'lineTpl' );
	}
	else
	{
		thisTpl=lineTpl;
	}
	
	var d=Array();
	for(var i in listdata)
	{
		var l=listdata[i];
		
		d.push(
			thisTpl
				.replace('%IDX%',i)
				.replace(/%IDENT%/g,( l.id ? l.id : '' ))
				.replace(/%LABEL%/g,l.label)
				.replace(/%SCIENTIFIC_NAME%/g,l.scientific_name ? l.scientific_name : '' )
				.replace(/%SCIENTIFIC_NAME_DATA%/g,l.scientific_name ? l.scientific_name : '' )
				.replace(/%COMMON_NAME%/g,l.common_name ? l.common_name : ( l.nomen ? l.nomen : l.scientific_name ) )
			
		);
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

		$('#'+ele).keyup(function(e)
		{
			if (e.keyCode==27) // esc
			{
				hideSuggestions();
				return;
			}

			if (e.keyCode!=undefined && e.keyCode!=13) // !enter
			{
				// empty ID value of user 
				$('#'+ele+'_id').val('');
			}

			if ($.inArray(e.keyCode,[37,38,39,40])==-1) // !(l,r,t,d)
			{
				doSuggestions({type:ele,match:match});
			}
		});
	
	});
}

function sortResults(ele)
{
	sortAttr=$(ele).val();
	$('div .result').sortElements(function(a, b){
		return ($(a).attr(sortAttr) > $(b).attr(sortAttr) ? 1 : -1);
	});
}





var search_parameters=[];
var trait_group=null;
var init=true;

function addSearchParameter(id)
{
	
	if (!id) return;

	var ele=$('#'+id);
	var tagtype=ele.prop('tagName');
	var varlabel=$('label[for='+id+']').text().trim();	
	var istrait=ele.attr('id') && ele.attr('id').indexOf('trait-')===0;

	var traitid=null;
	var valueid=null;
	var value=null;
	var valuetext=null;
	var value2=null;
	var valuetext2=null;
	var operator=null;
	var operatorlabel=null;

	if (tagtype=='SELECT')
	{
		traitid=ele.attr('trait-id');
		valueid=$('#'+id+' :selected').val();
		if (valueid) value='on';
		valuetext=$('#'+id+' :selected').text().trim();

		if (valueid.indexOf(':')!=-1)
		{
			var d=valueid.split(':');
			valueid=d[0];
			value=d[1];
		}

	}
	else
	if (tagtype=='INPUT')
	{
		traitid=ele.attr('trait-id');
		valueid=null;
		value=ele.val();
		valuetext=value;

		var ele2=$('#'+id+'-2');
		//if (ele2.is(':visible'))
		{
			value2=ele2.val();
			valuetext2=value2;
		}

		var d=$(':selected','#operator-'+id.replace('trait-','')).val();
		if (d)
		{
			operator=d;
			operatorlabel=$(':selected','#operator-'+id.replace('trait-','')).text();
		}
	}

	if (!value || value.length==0)
	{
		return;
	}

	for(var i=0;i<search_parameters.length;i++)
	{
		var e=search_parameters[i];
		if (e.valueid==valueid && e.value==value && e.value2==value2 && e.operator==operator && e.istrait==istrait)
		{
			return;
		}
	}

	
	search_parameters.push(
	{ 
		traitid:traitid,
		valueid:valueid,
		value:value,
		valuetext:valuetext,
		varlabel:varlabel,
		istrait:istrait,
		operator:operator,
		operatorlabel:operatorlabel,
		value2:value2,
		valuetext2:valuetext2
	} );
	
	printParameters();
	submitSearchParams();
}

function printParameters()
{
	$('#search-parameters').empty();

	for(var i=0;i<search_parameters.length;i++)
	{
		var e=search_parameters[i];
		$('#search-parameters').
			append(
				$(
					'<li>'+
						e.varlabel+': '+
						(e.operatorlabel ? e.operatorlabel+' ' : '' )+
						e.valuetext+
						(e.valuetext2 ? ' & ' + e.valuetext2 : '' )+
					' <a href="#" onclick="removeSearchParameter('+i+');submitSearchParams();return false;"> X </a></li>'));
	}
	
	if(getTraitGroup())
	{
		$('#search-parameters').
			append(
				$(
					'<li>' + _('Taxa met') + ' ' + getTraitGroupName() +
					' <a href="#" onclick="setTraitGroup(null);submitSearchParams();return false;"> X </a></li>'));
	}

	$('#remove-all').toggle(search_parameters.length>0 || getTraitGroup()!=null);
	$('.selected-parameters').toggle(search_parameters.length>0 || getTraitGroup()!=null);
	 

}

function removeSearchParameter(i)
{
	search_parameters.splice(i,1);
	printParameters();
}

function removeAllSearchParameters()
{
	search_parameters.splice(0);
	just_species=0;
	setTraitGroup(null);
	printParameters();
}

function addEstablished()
{
	addEstablishedOrNot('1');
	printParameters();
}

function addNonEstablished()
{
	addEstablishedOrNot('0');
	printParameters();
}

function addEstablishedOrNot(state)
{
	var varlabel=$('label[for=presenceStatusList]').text().trim();
	
	$( "#presenceStatusList option" ).each(function()
	{
		var valueid=$(this).val().trim();
		for(var i=0;i<search_parameters.length;i++)
		{
			if (search_parameters[i].valueid==valueid)
			{
				removeSearchParameter(i);
			}
		}
	
		if ($(this).attr('established')==state)
		{
			search_parameters.push( { valueid:valueid,value:'on',valuetext:$(this).text().trim(),varlabel:varlabel,istrait:false } );
		}
	});	
}

function setTraitGroup(id)
{
	trait_group=id;
}

function getTraitGroup()
{
	return trait_group;
}

function setTraitGroupName(name)
{
	trait_group_name=name;
}

function getTraitGroupName()
{
	return trait_group_name;
}

function toggle_panel(ele)
{
	$('#'+$(ele).attr('panel')).toggle();
}

function hover_panel_toggle(ele,out)
{
	var p=$('#'+$(ele).attr('panel'));
	var c=$(ele).children().children('div.arrow'); 
	if (out)
	{
		c.removeClass('arrow-se').addClass(p.is(':visible') ? 'arrow-s' :  'arrow-e')
	}
	else
	{
		c.removeClass('arrow-s').removeClass('arrow-e').addClass('arrow-se')
	}
}

function toggle_all_panels()
{
	var allopen=true;
	$('label').each(function()
	{
		if ($(this).attr('panel') && !$('#'+$(this).attr('panel')).is(':visible'))
		{
			allopen=false;
		}
	});
	$('label').each(function()
	{
		if ($(this).attr('panel') && (allopen || (!allopen && !$('#'+$(this).attr('panel')).is(':visible'))))
		{
			toggle_panel(this);
			hover_panel_toggle(this);
            hover_panel_toggle(this,true);
		}
	});
}

function submitSearchParams()
{
	if (init) return;

	var form=$('<form method="get"></form>').appendTo('body');
	form.append('<input type="hidden" name="group_id" value="'+$('#group_id').val()+'" />');


	var g="";
	$('input[type=text]').each(function()
	{
		if($(this).attr('name') && $(this).is(':visible'))
		{
			g=$(this).val();
		}
	});
	form.append('<input type="hidden" name="group" value="'+g+'" />');
	//form.append('<input type="hidden" name="group" value="'+$('#group').val()+'" />');

	//form.append('<input type="hidden" name="author_id" value="'+$('#author_id').val()+'" />');
	//form.append('<input type="hidden" name="author" value="'+$('#author').val()+'" />');
	form.append('<input type="hidden" name="sort" value="'+$('#sort').val()+'" />');

	var traits={};
	var j=0;

	for (var i=0;i<search_parameters.length;i++)
	{
		var param=search_parameters[i];

		if (param.istrait)
		{
			traits[j++]=param;
		}
		else
		{
			form.append('<input type="hidden" name="'+param.valueid+'" value="'+param.value+'" />');
		}
	}
	
	form.append('<input type="hidden" name="traits" value="'+ encodeURIComponent(JSON.stringify(traits))+'" />');

	var panels={};
	var j=0;

	$('.options-panel').each(function()
	{
		panels[j++]={ id:$(this).attr('id'),visible:$(this).is(':visible') };
	});

	form.append('<input type="hidden" name="panels" value="'+ encodeURIComponent(JSON.stringify(panels))+'" />');
	
	if (trait_group)
	{
		form.append('<input type="hidden" name="trait_group" value="'+ trait_group+'" />');
	}

	form.append('<input type="hidden" name="just_species" value="'+getJustSpeciesToggle()+'" />');

	form.submit();	
}

var justSpeciesToggle=0;

function setJustSpeciesToggle(state)
{
	justSpeciesToggle=state;
}

function getJustSpeciesToggle()
{
	return justSpeciesToggle;
}

function toggleJustSpeciesToggle()
{
	setJustSpeciesToggle(justSpeciesToggle==0 ? 1 : 0);
}

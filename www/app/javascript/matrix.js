var __translations=Array();

function __(text)
{
	for(var i=0;i<__translations.length;i++)
	{
		if (__translations[i].label==text)
		{
			return __translations[i].translation;
		}
	}
	
	return text;

	// _() is sloooooow!
	//return _(text);
}

/*
	hooks:
	hook_prePrintResults();
	hook_postPrintResults();
	hook_prePrintMenu();
	hook_postPrintMenu();
	hook_preApplyScores();
	hook_postApplyScores();
	hook_preSetStateValue(); // setStateValue() halts when hook-function returns false
*/

var matrixsettings={
	matrixId: 0,
	projectId: 0,
	perPage: 16,
	perLine: 4,
	start: 0,
	expandedShowing: 0,
	expandResults: true,
	useEmergingCharacters: true,
	showSpeciesDetails: true,
	alwaysShowDetails: false,
	imageRootSkin: "",  // for skin images (icons and such)
	imageRootProject: "",  // for local project images
	imageOrientation: "portrait",  // portrait, landscape
	defaultSpeciesImages: {},
	defaultSpeciesImage: "",
	browseStyle: 'paginate', // expand, paginate, show_all
	expandedPrevious: 0,
	paginate: true,
	currPage: 0,
	lastPage: 0,
	scoreThreshold: 0,
	mode: "identify", // similar, search
	groupsAlwaysOpen: false,
	generalSpeciesInfoUrl: ""
};

var data={
	menu: Array(), // full menu
	dataset: Array(), // full dataset
	resultset: Array(), // result subset
	states: {}, // user-selected states
	characters: {}, // remaining states/taxa per character
	statecount: {}, // remaining taxa per state
	scores: {}, // match scores based on selection
	related: {}, // related species
	found: {} // search results
}

var prevmatrixsettings={};

//var initialize=true;
var lastScrollPos=0;
var tempstatevalue="";
var openGroups=Array();
var searchedfor="";

var	labels={
	details: __('onderscheidende kenmerken'),
	similar: __('gelijkende soorten'),
	show_all: __('alle onderscheidende kenmerken tonen'),
	hide_all: __('kenmerken verbergen'),
	info_link: __('Meer informatie over soort/taxon'),
	info_dialog_title:__('Informatie over soort/taxon'),
	popup_species_link:__('Meer informatie'),
}

function retrieveMenu()
{
	setCursor('wait');

	$.ajax({
		url : 'ajax_interface.php',
		type: 'POST',
		data : ({
			action : 'get_menu',
			time : getTimestamp(),
			key : matrixsettings.matrixId,
			p : matrixsettings.projectId
		}),
		success : function (data)
		{
			//console.log(data);
			setMenu($.parseJSON(data));
			printMenu();
			setCursor();
		}
	});
}

function resetMatrix()
{
	clearStateValue();
	openGroups.splice(0,openGroups.length);
	closeSimilar();
	closeSearch();
	printCountHeader();
}

function printResults()
{
	if (typeof hook_prePrintResults == 'function') { hook_prePrintResults(); }
	
	var resultset = getResultSet();

	// pre-emptively remove show_more-button (clicking similar automatically switches to browseStyle='show_all')
	$("#show-more").remove();
	$("#footerPagination").removeClass('noline');

	if (resultset && matrixsettings.browseStyle=='expand') 
	{
		printResultsExpanded();
	}
	else
	if (resultset && matrixsettings.browseStyle!='expand') // (non-)paginated
	{
		printResultsPaginated();
		if (matrixsettings.browseStyle=='paginate')
		{
			printPaging();
		}
	}
	
	if (resultset.length==0)
	{
		$('#results-container').html(noResultHtmlTpl.replace('%MESSAGE%',__('Geen resultaten.')));
	}

	clearOverhead();
	printHeader();
	prettyPhotoInit();
	disableImgContextMenu();

	$('.result-icon').on('mouseover',function()
	{	
		$(this).find('img').attr('src', $(this).find('img').attr('src') ? $(this).find('img').attr('src').replace('_grijs','')  : "" );
	}).on('mouseout',function()
	{
		$(this).find('img').attr('src', $(this).find('img').attr('src') ? $(this).find('img').attr('src').replace('.png','_grijs.png')  : "" );
	});
	
	if (typeof hook_postPrintResults == 'function') { hook_postPrintResults(); }
}

function shouldDisableChar( id )
{
	/*	
	if the character has states that are already selected, we don't disable
	*/
	var activestates=getActiveStates(id);
	if (activestates && activestates.length>=1) return false;


	/*
	if there is no or just one taxon left that "has" a state from this character, we disable
	*/
	var charactercounts=getCharacterCounts(id);
	return (charactercounts.distinct_state_count<=1);
}

function shouldDisableEmergentChar( id )
{
	/*
	usage of emergent characters can be turned off in project matrixsettings
	*/
	if (!matrixsettings.useEmergingCharacters) return false;
	
	/*
	types other than text or media (i.e., "free-entry types", 'range' etc.)
	can never be emergent
	*/
	var character=getCharacter(id);
	if (character.prefix!="c") return false
	
	/*
	the distinct taxon count for all its states that are  still available is
	smaller than the current result set. put differently: there are remaining
	species that have no defined state for this particular character.
	*/
	var charactercounts=getCharacterCounts(id);
	return charactercounts.taxon_count < getResultSet().length;
}

function printMenu()
{
	
	if (typeof hook_prePrintMenu == 'function') { hook_prePrintMenu(); }
	
	$('#facet-categories-menu').html('');
	
	var menu=getMenu();
	var buffer=Array();
	var groupcount=0;
	var lastgroupid;

	for (var i in menu)
	{
		var item = menu[i];

		var s="";
		
		if (item.type=='group')
		{
			groupcount++;
			lastgroupid=item.id;			

			var c="";

			for (var j in item.chars)
			{
				if (matrixsettings.groupsAlwaysOpen) openGroups.push(item.id);
				
				var char=item.chars[j];

				char.disabled = shouldDisableChar(char.id);
				char.emergent_disabled = shouldDisableEmergentChar(char.id);

				var activestates=getActiveStates(char.id);

				var l=""

				if (activestates)
				{
					openGroups.push(item.id);

					var t="";
					for (var k in activestates)
					{
						var state = activestates[k];
						t=t+menuSelStateHtmlTpl
							.replace('%VALUE%',(state.value ? state.value : ''))
							.replace('%LABEL%',(state.label ? state.label : ''))
							.replace('%COEFF%',(state.separationCoefficient ? '('+state.separationCoefficient+') ' : ''))
							.replace('%STATE-ID%',state.val)
							.replace('%IMG-URL%',matrixsettings.imageRootSkin+'clearSelection.gif');
					}
					
					l=menuSelStatesHtmlTpl.replace('%STATES%',t);
				}

				if (char.disabled==true)
				{
					c=c+menuCharDisabledHtmlTpl
						.replace('%CLASS%',(j==(item.chars.length-1)?' last':''))
						.replace('%ID%',char.id)
						.replace('%LABEL%',char.label) //  + ':' + charactercounts.taxon_count
						.replace('%TITLE%',__( "Dit kenmerk is bij de huidige selectie niet langer onderscheidend." ))
						.replace('%VALUE%',(char.value?' '+char.value:''))
						.replace('%SELECTED%',l);
				}
				else
				if (char.emergent_disabled==true)
				{

					//var charactercounts=getCharacterCounts(char.id);
					//console.log(char.label,charactercounts.taxon_count);
					
					c=c+menuCharEmergentDisabledHtmlTpl
						.replace('%CLASS%',(j==(item.chars.length-1)?' last':''))
						.replace('%ID%',char.id)
						.replace('%LABEL%',char.label) //  + ':' + charactercounts.taxon_count
						.replace('%TITLE%',__( "Dit kenmerk is bij de huidige selectie nog niet onderscheidend." ))
						.replace('%VALUE%',(char.value?' '+char.value:''))
						.replace('%SELECTED%',l);
				}
				else
				{
					c=c+menuCharHtmlTpl
						.replace('%CLASS%',(j==(item.chars.length-1)?' last':''))
						.replace('%ID%',char.id)
						.replace('%LABEL%',char.label) //  + ':' + charactercounts.taxon_count
						.replace('%VALUE%',(char.value?' '+char.value:''))
						.replace('%SELECTED%',l);
				}
			}
			
			s=menuGroupHtmlTpl
				.replace(/%ID%/g,item.id)
				.replace('%LABEL%',item.label)
				.replace('%CHARACTERS%',c);

		}
		else
		if (item.type=='char')
		{
			item.disabled = shouldDisableChar(item.id);
			item.emergent_disabled = shouldDisableEmergentChar(item.id);
			
			var activestates=getActiveStates(item.id);
			
			var l=""
	
			if (activestates)
			{
				var t="";
				for (var k in activestates)
				{
					var state=activestates[k];
					t=t+menuSelStateHtmlTpl
						.replace('%VALUE%',(state.value ? state.value : ''))
						.replace('%LABEL%',(state.label ? state.label : ''))
						.replace('%COEFF%',(state.separationCoefficient ? '('+state.separationCoefficient+') ' : ''))
						.replace('%STATE-ID%',state.val)
						.replace('%IMG-URL%',matrixsettings.imageRootSkin+'clearSelection.gif');
				}
				
				l=menuSelStatesHtmlTpl.replace('%STATES%',t);
			}
			
			if (item.disabled==true)
			{
				s=menuLoneCharDisabledHtmlTpl
					.replace('%CLASS%',"")
					.replace('%ID%',item.id)
					.replace('%LABEL%',item.label)
					.replace('%TITLE%',__( "Dit kenmerk is bij de huidige selectie niet langer onderscheidend." ))
					.replace('%VALUE%',(item.value?' '+item.value:''))
					.replace('%SELECTED%',l);
			}
			else
			if (item.emergent_disabled==true)
			{
				s=menuLoneCharEmergentDisabledHtmlTpl
					.replace('%CLASS%',"")
					.replace('%ID%',item.id)
					.replace('%LABEL%',item.label)
					.replace('%TITLE%',__( "Dit kenmerk is bij de huidige selectie nog niet onderscheidend." ))
					.replace('%VALUE%',(item.value?' '+item.value:''))
					.replace('%SELECTED%',l);
			}
			else
			{
				s=menuLoneCharHtmlTpl
					.replace('%CLASS%',"")
					.replace('%ID%',item.id)
					.replace('%LABEL%',item.label)
					.replace('%VALUE%',(item.value?' '+item.value:''))
					.replace('%SELECTED%',l);
			}
		}
		
		buffer.push(s);

	}

	if (groupcount==1 && lastgroupid) openGroups.push(lastgroupid);
	
	$('#facet-categories-menu').html( menuOuterHtmlTpl.replace('%MENU%',buffer.join('\n') ) );
	
	for(var i in $.unique(openGroups))
	{
		toggleGroup( openGroups[i], true );
	}
	
	var states=getStates();
	
	if (!states || states.count==0)
	{
		$('#clearSelectionContainer').addClass('ghosted');
	}
	else
	{
		showRestartButton();
	}	
	
	if (typeof hook_postPrintMenu == 'function') { hook_postPrintMenu(); }
	
}

function clearResults()
{
	$('#results-container').html('');
	setSetting({expandedShowing:0});
}

function printHeader()
{
	if (matrixsettings.mode=="search")
	{
		printSearchHeader();
	}
	else
	if (matrixsettings.mode=="similar")
	{
		printSimilarHeader();
	}
	else
	{
		printCountHeader();
	}
}

function printResultsExpanded()
{
	var resultset = getResultSet();

	var s="";
	var printed=0;
	var d=0;
	
	for(var i=0;i<resultset.length;i++)
	{
		if (i < matrixsettings.expandedShowing+matrixsettings.perPage)
		{
			s=s+formatResult(resultset[i]);

			printed++;

			if (++d==matrixsettings.perLine)
			{
				s=s+resultsLineEndHtmlTpl;
				d=0;
			}
		}
	}

	$('#results-container').html(
		resultBatchHtmlTpl
			.replace('%STYLE%',"")
			.replace('%RESULTS%', resultsHtmlTpl.replace('%RESULTS%',s))
	);

	// parallel processing using show() causes mayhem when clicking the 'show more'-button fast.
	//		.replace('%STYLE%',(matrixsettings.expandedShowing>0  ? 'display:none' : ''))
	//	$('.result-batch:hidden').show('normal');
	
	matrixsettings.expandedShowing=printed;

	if (matrixsettings.expandedShowing<resultset.length)
	{
		if (!$("#show-more-").is(':visible'))
		{
			$("#paging-footer").append( buttonMoreHtmlTpl.replace('%LABEL%',__('meer resultaten laden')) );
			$("#footerPagination").addClass('noline');
		}
	}
	
	if (matrixsettings.expandedShowing>0) 
	{
		//window.scrollBy(0,99999);
	}

}

function printResultsPaginated()
{
	var resultset = getResultSet();

	var s="";
	var d=0;

	for(var i=0;i<resultset.length;i++)
	{
		if (
			(matrixsettings.browseStyle=='paginate' && i>=matrixsettings.start && i<matrixsettings.start+matrixsettings.perPage) || 
			matrixsettings.browseStyle=='show_all'
		)
		{
			s=s+formatResult(resultset[i]);
			if (++d==matrixsettings.perLine)
			{
				s=s+resultsLineEndHtmlTpl;
				d=0;
			}
		}
	}

	$('#results-container').html(resultsHtmlTpl.replace('%RESULTS%',s));
}

function formatResult( data )
{
	if ( data.type=='taxon' )
	{
		//var sciName=data.label;
		var sciName='<i>'+data.taxon+'</i>';
		var commonName=data.commonname ? data.commonname : "";
	}
	else
	if ( data.type=='variation' )
	{
		//var sciName=data.taxon.label;
		var sciName='<i>'+data.taxon.taxon+'</i>';
		var commonName=(data.taxon.commonname ? data.taxon.commonname : "" ) + " " + (data.label ? "(" + data.label + ")" : "");
		commonName.trim();
	}
	else
	if ( data.type=='matrix' )
	{
		//var sciName=data.taxon.label;
		var sciName='<i>'+data.label+'</i>';
		var commonName="";
	}

	if (matrixsettings.showSpeciesDetails && data.states)
	{
		var states = Array();

		for(var i in data.states)
		{
			var state=data.states[i];
			
			if (state.characteristic==undefined)
				continue;
			
			var statelabels = Array();
			
			if (state.characteristic.indexOf('|')!=false)
			{
				var t = state.characteristic.split('|');
				t = t[0];
			} 
			else
			{
				var t = state.characteristic;
			}
			
			for(var j in state.states)
			{
				statelabels.push(state.states[j].label);
			}

			if (statelabels.length>1)
			{
				var l = statelabels.join('; ');
			}
			else
			{
				var l = statelabels[0];
			}

			states.push(
				speciesStateItemHtmlTpl
					.replace('%GROUP%', state.group_label ? state.group_label + ' > ' : '')
					.replace('%CHARACTER%',t)
					.replace('%STATE%',l)
			);
		}
	}

	var image="";

	if (data.info && data.info.url_image)
	{
		image=data.info.url_image;
		if (image && !image.match(/^(http:\/\/|https:\/\/)/i)) image=matrixsettings.imageRootProject+image;
	}
	else
	{
		if (matrixsettings.defaultSpeciesImage) image=matrixsettings.defaultSpeciesImage;
	}

	var thumb="";

	if (data.info && (data.info.url_thumbnail || data.info.url_thumb))
	{
		thumb=data.info.url_thumbnail ? data.info.url_thumbnail : data.info.url_thumb;
		if (thumb && !thumb.match(/^(http:\/\/|https:\/\/)/i)) thumb=matrixsettings.imageRootProject+thumb;
	}
	else
	{
		thumb=image;
	}
	
	var id = data.type+'-'+data.id;
	var showStates = states && states.length > 0;

	photoLabelHtml=
		photoLabelHtmlTpl
			.replace('%SCI-NAME%',sciName)
			.replace('%GENDER%',(data.gender && data.gender.gender ?
				photoLabelGenderHtmlTpl
					.replace('%IMG-SRC%', matrixsettings.imageRootSkin + data.gender.gender+'.png')
					.replace('%GENDER-LABEL%', data.gender.gender_label)
				: "" ))
			.replace('%COMMON-NAME%',(commonName ? brHtmlTpl + commonName : ""))
			.replace('%PHOTO-DETAILS%',(data.info && data.info.photographer ? 
				photoLabelPhotographerHtmlTpl
					.replace('%PHOTO-LABEL%', __('foto')+' &copy;' )
					.replace('%PHOTOGRAPHER%', data.info.photographer )
				: ""));

	imageHtml=
		imageHtmlTpl
			.replace('%IMAGE-URL%',image)
			.replace('%THUMB-URL%',thumb)
			.replace('%PHOTO-LABEL%',encodeURIComponent(photoLabelHtml))
			.replace('%PHOTO-CREDIT%',(data.info && data.info.photographer ? __('foto')+' &copy;'+data.info.photographer : ''))
		;	

	resultHtml=
		resultHtmlTpl
			.replace('%CLASS-HIGHLIGHT%',(data.h ? ' result-highlight' : ''))
			.replace('%IMAGE-HTML%',(image ? imageHtml : ""))
			.replace('%GENDER%',(data.gender && data.gender.gender ? 
				genderHtmlTpl
					.replace('%ICON-URL%', matrixsettings.imageRootSkin+data.gender.gender+'.png') 
					.replace('%GENDER-LABEL%', data.gender.gender_label) 
				: "" )
			)
			.replace('%SCI-NAME%', sciName)
			.replace('%SCI-NAME-TITLE%', addSlashes(stripTags(sciName)) )
			.replace('%MATRIX-LINK%', (data.type=='matrix' ? 
				matrixLinkHtmlTpl.replace("%MATRIX-ID%",data.id).replace("%MATRIX-LINK-TEXT%",__('Ga naar sleutel'))
				: ""))
			.replace('%COMMON-NAME%', commonName)
			.replace('%COMMON-NAME-TITLE%', addSlashes(commonName) )
			.replace('%REMOTE-LINK-CLASS%', data.info && data.info.url_external_page ? "" : " no-content")
			.replace('%REMOTE-LINK-CLICK%', data.info && data.info.url_external_page ?  
				remoteLinkClickHtmlTpl
					.replace('%REMOTE-LINK%', data.info.url_external_page)
					.replace('%TITLE%', labels.info_link)
					.replace('%SCI-NAME%', encodeURIComponent(data.taxon))
				: "")
			.replace('%REMOTE-LINK-ICON%', data.info && data.info.url_external_page ?
				iconUrlHtmlTpl.replace('%IMG-URL%',matrixsettings.imageRootSkin+"information_grijs.png") : "")
			.replace('%SHOW-STATES-CLASS%', showStates ? " icon-details" : " no-content")
			.replace('%SHOW-STATES-CLICK%', showStates ?  statesClickHtmlTpl.replace('%TITLE%',labels.details) : "")
			.replace('%SHOW-STATES-ICON%', showStates ?
				iconInfoHtmlTpl.replace('%IMG-URL%',matrixsettings.imageRootSkin+"lijst_grijs.png") : "")
			.replace('%RELATED-CLASS%', data.related_count>0 ? " icon-resemblance" : " no-content")
			.replace('%RELATED-CLICK%', (data.related_count>0 ?  
				relatedClickHtmlTpl
					.replace('%TYPE%', data.type)
					.replace('%ID%', data.id)
					.replace('%TITLE%', labels.similar)
				: "" )
			)
			.replace('%RELATED-ICON%', data.related_count>0 ?
				iconSimilarTpl.replace('%IMG-URL%',matrixsettings.imageRootSkin+"gelijk_grijs.png") : "")
			.replace('%STATES%', showStates ? statesHtmlTpl.replace( '%STATES%',states.join(statesJoinHtmlTpl)) : "")
			.replace(/%LOCAL-ID%/g,id)
			.replace(/%ID%/g,data.id)
			.replace('%SCORE%', matrixsettings.showScores && data.score ? resultScoreHtmlTpl.replace( '%SCORE%',data.score) : "")
			;

	return resultHtml;
}

function clearOverhead()
{
	$('#result-count').html('');
	$('#similarSpeciesHeader').removeClass('visible').addClass('hidden');
	$('#similarSpeciesHeader').html('');
}

function printCountHeader()
{
	var resultset = getResultSet();
	
	if (matrixsettings.browseStyle=='expand')
	{
		$('#result-count').html(
			counterExpandHtmlTpl
				.replace('%START-NUMBER%',(matrixsettings.expandedShowing > 1 ? "1-" : "" ))
				.replace('%NUMBER-SHOWING%',matrixsettings.expandedShowing)
				.replace('%FROM-LABEL%',__('van'))
				.replace('%NUMBER-TOTAL%',resultset.length)
		);
	}
	else
	if (matrixsettings.browseStyle=='paginate')
	{
		$('#result-count').html(
			counterPaginateHtmlTpl
				.replace('%FIRST-NUMBER%', (matrixsettings.start+1))
				.replace('%LAST-NUMBER%',(matrixsettings.start+matrixsettings.perPage))
				.replace('%NUMBER-LABEL%',__('van'))
				.replace('%NUMBER-TOTAL%',resultset.length)
		);
	}
	else
	{
		$('#result-count').html(
			counterPaginateHtmlTpl
				.replace('%FIRST-NUMBER%',1)
				.replace('%LAST-NUMBER%',resultset.length)
				.replace('%NUMBER-LABEL%',"")
				.replace('%NUMBER-TOTAL%',"")
		);
	}
	
	if (resultset.length==0)
	{
		$('#result-count').html("");
	};	
}

function clearPaging()
{
	$('#paging-header').html('');	
	$('#paging-footer').html('');	
}

function printPaging()
{
	var resultset = getResultSet();

	setSetting({lastPage:Math.ceil(resultset.length / matrixsettings.perPage)});
	setSetting({currPage:Math.floor(matrixsettings.start / matrixsettings.perPage)});

	if (matrixsettings.lastPage > 1 && matrixsettings.currPage!=0)
	{
		$("#paging-header").append( pagePrevHtmlTpl );
	}
	
	if (matrixsettings.lastPage>1)
	{ 
		for (var i=0;i<matrixsettings.lastPage;i++)
		{
			if (i==matrixsettings.currPage)
			{
				$("#paging-header").append( pageCurrHtmlTpl.replace('%NR%',(i+1)) );
			}
		    else
			{
				$("#paging-header").append( pageNumberHtmlTpl.replace('%NR%',(i+1)).replace('%INDEX%',i) );
			}
		}
	}

	if (matrixsettings.lastPage > 1 && matrixsettings.currPage<matrixsettings.lastPage-1)
	{
		$("#paging-header").append( pageNextHtmlTpl );
	}

	$("#paging-footer").html($("#paging-header").html());
}

function browsePage( id )
{
	if (id=='n')
		setSetting({start: matrixsettings.start+matrixsettings.perPage});
	else if (id=='p')
		setSetting({start: matrixsettings.start-matrixsettings.perPage});
	else if (!isNaN(id))
		setSetting({start:id * matrixsettings.perPage});
	else
		return;

	clearResults();
	printResults();
	clearPaging();
	printPaging();

}

function getActiveStates( id )
{
	var states=getStates();
	
	if (!states) return;
	
	var res=Array();
	
	for(var i in states)
	{
		var state=states[i];
		if (state.characteristic_id==id)
		{
			res.push(state);
		}
	}
	
	return (res.length>0 ? res : null);
}

function getCharacterCounts( id )
{
	var characters=getCharacters();

	if (!characters) return;
	
	for(var c in characters)
	{
		if (c==id)
		{
			return characters[c];
		}
	}

	return {taxon_count:0,distinct_state_count:0};
}

function toggleGroup( id, forceOpen )
{
	if ( $('#character-group-'+id).css('display')=='none' || forceOpen )
	{
		$('#character-group-'+id).removeClass('hidden').addClass('visible');
		$('#character-item-'+id).removeClass('closed').addClass('open');
		openGroups.push(id);
	}
	else
	{
		$('#character-group-'+id).removeClass('visible').addClass('hidden');
		$('#character-item-'+id).removeClass('open').addClass('closed');

		for(var i=openGroups.length-1; i>=0; i--)
		{
			if(openGroups[i]===id)
			{
			   openGroups.splice(i,1);
			}
		}
	}
} 

function showStates(id)
{
	setCursor('wait');

	$.ajax({
		url : 'character_states.php',
		type: 'GET',
		data : ({
			id: id,
			time : getTimestamp(),
			key : matrixsettings.matrixId,
			p : matrixsettings.projectId
		}),
		success : function( page )
		{
			var char=getCharacter(id);
			showDialog(char.label,page,{showOk:(char.type=='media' || char.type=='text' ? false : true)});
			setCursor();
		}
	});

}

function clearStateValue(state)
{
	setState({state:state,action:'clear_state'});
}

function setStateValue(state)
{
	if (typeof hook_preSetStateValue == 'function')
	{
		var d=hook_preSetStateValue(state);
		if (d===false)
		{
			return d;
		}
	}

	var state=state?state:$('#state-id').val();
	setState({state:state,value:tempstatevalue});
}

function setState( p )
{
	setCursor('wait');

	$.ajax({
		url : 'ajax_interface.php',
		type: 'POST',
		data : ({
			action : (p && p.action) ? p.action : 'set_state' ,
			state : p.state,
			value : p.value,
			time : getTimestamp(),
			key : matrixsettings.matrixId,
			p : matrixsettings.projectId
		}),
		success : function(data)
		{
			var d=$.parseJSON(data);

			setScores(d.scores);
			setStates(d.states);
			setStateCount(d.statecount);
			setCharacters(d.characters);

			closeSimilar();
			closeSearch();

			applyScores();
			clearResults();
			printResults();
			printMenu();

			setCursor();
		}
	});
}

function applyScores()
{

	if (typeof hook_preApplyScores == 'function') { hook_preApplyScores(); }

	var scores=getScores();
	var states=getStates();
	var dataset=getDataSet();
	var resultset=getResultSet();

	// scores are sorted in the controller
	
	// clean slate (also include states to be sure it's not a selection that returns zero matches)
	if ((!states || states.length==0) && (!scores || scores.length==0))
	{
		resultset=dataset.slice();
	}
	else
	{
		resultset.splice(0,resultset.length);

		for(var i in scores)
		{
			var score=scores[i];
			for(var j in dataset)
			{
				var item=dataset[j];
				if (score.id==item.id && score.type==item.type && (matrixsettings.scoreThreshold==0 || score.score>=matrixsettings.scoreThreshold))
				{
					item.score=score.score;
					resultset.push(item);
				}
			}
		}
	}
	
	setResultSet(resultset);

	setSetting({showSpeciesDetails: matrixsettings.alwaysShowDetails || (resultset.length <= matrixsettings.perPage)});
	
	matrixsettings.start=0;
	
	if (typeof hook_postApplyScores == 'function') { hook_postApplyScores(); }

}

function applyRelated()
{
	var related=getRelated();
	var dataset=getDataSet();
	var resultset=getResultSet();

	if ((!related || related.length==0))
	{
		resultset=dataset.slice();
	}
	else
	{
		resultset.splice(0,resultset.length);

		for(var i in related)
		{
			var relate=related[i];
			for(var j in dataset)
			{
				var item=dataset[j];
				if (relate.relation_id==item.id && relate.ref_type==item.type)
				{
					resultset.push(item);
				}
			}
		}
	}
	
	setResultSet(resultset);
}

function applyFound()
{
	var found=getFound();
	var dataset=getDataSet();
	var resultset=getResultSet();

	resultset.splice(0,resultset.length);

	for(var i in found)
	{
		var tfound=found[i];
		for(var j in dataset)
		{
			var item=dataset[j];
			if (tfound.id==item.id && tfound.type==item.type)
			{
				resultset.push(item);
			}
		}
	}
	
	setResultSet(resultset);
}

function setSimilar( p )
{
	setCursor('wait');

	$.ajax({
		url : 'ajax_interface.php',
		type: 'POST',
		data : ({
			action : 'get_similar' ,
			id : p.id,
			type : p.type,
			time : getTimestamp(),
			key : matrixsettings.matrixId,
			p : matrixsettings.projectId
		}),
		success : function(data)
		{
			var related=$.parseJSON(data);
			setRelated(related);

			setPrevmatrixsettings();
			setLastScrollPos();

			setSetting({mode:"similar"});
			setSetting({start:0});
			setSetting({expandedShowing:0});
			setSetting({browseStyle:'show_all'});
			setSetting({showSpeciesDetails: true});
			
			clearPaging();
			clearResults();

			applyRelated();
			removeSimilarCharacters();
			printResults();
			printSimilarHeader();
			window.scroll(0,0);
			setCursor();
			showRestartButton();
		}
	});
	
}

function peersHaveIdenticalValues(fcharacter,fvalues)
{
	var resultset=getResultSet();
	
	if (resultset.length<=1)
	{
		return false;
	}

	fvalues.sort();
	
	foundnothing=true;
	
	for(var i=0;i<resultset.length;i++)
	{
		var result=resultset[i];
		for(var c in result.states)
		{
			if (c==fcharacter)
			{
				foundnothing=false;
				var character=result.states[c];
				var values=Array();
				
				for(var s in character.states)
				{
					var state=character.states[s];
					values.push(state.id);
				}
				
				values.sort();
				
				if (values.join(';')!=fvalues.join(';')) return false;
			}
		}
	}
	
	if (foundnothing)
		return false;
	else
		return true;
}

function removeSimilarCharacters()
{
	var resultset=getResultSet();
	//console.dir(resultset);
	
	var filteredStates=Array();
	
	for(var i=0;i<resultset.length;i++)
	{
		var result=resultset[i];
		var filteredCharacters={};
		for(var c in result.states)
		{
			var character=result.states[c];
			//console.dir(character);
			var values=Array();
			for(var s in character.states)
			{
				var state=character.states[s];
				//console.dir(state);
				values.push(state.id);
			}
			values.sort();
			if (!peersHaveIdenticalValues(c,values))
			{
				var myObj = new Object;
				myObj[c] = character;
				$.extend(filteredCharacters,myObj);
			}
		}

		resultset[i].states=filteredCharacters;
	}
}

function clearSimilarHeader()
{
	$('#similarSpeciesHeader').html('');	
}

function printSimilarHeader()
{
	var resultset = getResultSet();

	$('#similarSpeciesHeader').html(
		similarHeaderHtmlTpl
			.replace('%HEADER-TEXT%', __('Gelijkende soorten van'))
			.replace('%SPECIES-NAME%', resultset[0].label)
			.replace('%BACK-TEXT%', __('terug'))
			.replace('%SHOW-STATES-TEXT%', labels.show_all)
			.replace('%NUMBER-START%', matrixsettings.start+1)
			.replace('%NUMBER-END%', data.resultset.length)
	).removeClass('hidden').addClass('visible');
	
	$('.result-icon.related').find('img').remove();

}

function toggleAllDetails()
{
	if ($('.result-detail:visible').length < getResultSet().length)
	{
		$('.result-detail').toggle(true);
		$('#showAllLabel').html(labels.hide_all);
	}
	else
	{
		$('.result-detail').toggle(false);
		$('#showAllLabel').html(labels.show_all);
	}

}

function toggleDetails(id)
{
	$('#det-'+id).toggle();
}

function setSearch( p )
{
	var s=$('#inlineformsearchInput').val();
	
	if (s.length==0) return;
	
	setCursor('wait');
	
	searchedfor=s;

	$.ajax({
		url : 'ajax_interface.php',
		type: 'POST',
		data : ({
			action : 'get_search' ,
			search : s,
			time : getTimestamp(),
			key : matrixsettings.matrixId,
			p : matrixsettings.projectId
		}),
		success : function(data)
		{
			var found=$.parseJSON(data);
			setFound(found);

			setPrevmatrixsettings();
			setLastScrollPos();

			setSetting({mode:"search"});
			setSetting({start:0});
			setSetting({expandedShowing:0});
			setSetting({browseStyle:'expand'});
			setSetting({showSpeciesDetails: true});
			
			clearPaging();
			clearResults();

			applyFound();
			removeSimilarCharacters();
			printResults();
			printSearchHeader();
			window.scroll(0,0);
			setCursor();
			showRestartButton();
		}
	});
	
}

function printSearchHeader()
{
	$('#similarSpeciesHeader').html(
		searchHeaderHtmlTpl
			.replace('%HEADER-TEXT%', __('Zoekresultaten voor'))
			.replace('%SEARCH-TERM%', searchedfor)
			.replace('%BACK-TEXT%', __('terug'))
			.replace('%NUMBER-START%', matrixsettings.start+1)
			.replace('%NUMBER-END%', matrixsettings.expandedShowing)
			.replace('%OF-TEXT%', __('van'))
			.replace('%NUMBER-TOTAL%', data.resultset.length)
	).removeClass('hidden').addClass('visible');
}

function closeSimilarSearch()
{
	setSetting({mode:"identify"});
	clearSimilarHeader();
	applyScores();
	clearResults();
	setSetting(getPrevmatrixsettings());
	setSetting({expandedShowing:matrixsettings.expandedShowing-matrixsettings.perPage});
	printResults();
	window.scroll(0,getLastScrollPos());
}

function closeSimilar()
{
	setRelated();
	closeSimilarSearch();
}

function closeSearch()
{
	setFound();
	$('#inlineformsearchInput').val("");
	closeSimilarSearch();
}

function prettyPhotoInit()
{
	if(jQuery().prettyPhoto)
	{
		$("a[rel^='prettyPhoto']").prettyPhoto({
			allow_resize:true,
			animation_speed:50,
			opacity: 0.70, 
			show_title: false,
			overlay_gallery: false,
			social_tools: false
		});
	}
}

function bindDialogKeyUp()
{
    $("#state-value").keydown(function(event)
	{
        // Allow: backspace, delete, tab, escape, and enter
        if (event.keyCode==46 || event.keyCode==8 || event.keyCode==9 || event.keyCode==27 || event.keyCode==13 || 
             // Allow: Ctrl+A
            (event.keyCode==65 && event.ctrlKey===true) || 
             // Allow: home, end, left, right
            (event.keyCode>=35 && event.keyCode<=39))
		{
			// let it happen, don't do anything
			return;
        }
        else
		{
			// Ensure that it is a number or a dot and stop the keypress
			if (event.shiftKey || (event.keyCode<48 || event.keyCode>57) && (event.keyCode<96 || event.keyCode>105) && event.keyCode!=190)
			{
				event.preventDefault(); 
			}   
        }
    });

	$('#state-value').keyup(function(e)
	{
		if (e.keyCode==13)
		{
			// return
			setStateValue();
			closeDialog();
		}
		return;
	});

}

function jDialogOk()
{
	// dialog button function, called from main.js::showDialog 
	setStateValue();
	closeDialog();
}

function jDialogCancel()
{
	// dialog button function, called from main.js::showDialog 
	closeDialog();
}


function setLastScrollPos()
{
	lastScrollPos=getPageScroll();
}

function getLastScrollPos()
{
	return lastScrollPos;
}

function setScores(scores)
{
	data.scores=scores;
}

function getScores()
{
	return data.scores;
}

function setStates(states)
{
	data.states=states;
}

function getStates()
{
	return data.states;
}

function setCharacters(characters)
{
	data.characters=characters;
}

function getCharacters()
{
	return data.characters;
}

function setStateCount(statecount)
{
	data.statecount=statecount;
}

function getStateCount()
{
	return data.statecount;
}

function setDataSet(dataset)
{
	data.dataset=dataset;
}

function getDataSet()
{
	return data.dataset;
}

function setResultSet(resultset)
{
	data.resultset=resultset;
}

function getResultSet()
{
	return data.resultset;
}

function setMenu(menu)
{
	data.menu=menu;
}

function getMenu()
{
	return data.menu;
}

function setRelated(related)
{
	data.related=related;
}

function getRelated()
{
	return data.related;
}

function setFound(found)
{
	data.found=found;
}

function getFound()
{
	return data.found;
}

function setPrevmatrixsettings()
{
	prevmatrixsettings = jQuery.extend({}, matrixsettings);
}

function getPrevmatrixsettings()
{
	return prevmatrixsettings;
}

function setSetting( p )
{
	$.extend(matrixsettings, p);
}

function getCharacter( id )
{
	var menu=getMenu();

	for (var i in menu)
	{
		var item = menu[i];

		if (item.type=='group')
		{
			for (var j in item.chars)
			{
				if (item.chars[j].id==id) return item.chars[j];
			}
		}
		else
		if (item.type=='char')
		{
			if (item.id==id) return item;
		}
	}
}

function disableImgContextMenu()
{
    $("img").on("contextmenu",function()
	{
       return false;
    });
}

function showRestartButton()
{
	$('#clearSelectionContainer').removeClass('ghosted');
}

function doRemoteLink( url, name )
{
	if (matrixsettings.generalSpeciesInfoUrl.length>0)
	{
		setCursor('wait');

		var iurl=matrixsettings.generalSpeciesInfoUrl
			.replace('%PID%',matrixsettings.projectId)
			.replace('%TAXON%',name);
			
		//console.log( iurl );
			
		$.ajax({
			url : iurl,
			type: 'GET',
			dataType: "jsonp",
			success : function ( data )
			{
				//console.dir( data );

				printInfo( 
					data.page.body, 
					labels.info_dialog_title,
					url
				);
				setCursor();
			}
		});
	}
	else
	if (url.length>0)
	{
		window.open( url, "_blank" );
	}
}

function printInfo( info, title, url )
{
	if (info)
	{
		showDialog(
			title,
			infoDialogHtmlTpl
				.replace('%BODY%',info)
				.replace('%URL%', url ? 
					infoDialogUrlHtmlTpl
						.replace('%URL%',url)
						.replace('%LINK-LABEL%',labels.popup_species_link)
					 : "" ),
			{showOk:false});
	}
}

function matrixInit()
{
	//nbcLabelClose = __('sluiten');
	//nbcLabelBack = __('terug');

	$('#legendDetails').html( labels.details );
	$('#legendSimilarSpecies').html( labels.similar );
	$('#legendExternalLink').html( labels.info_link );

	matrixsettings.defaultSpeciesImage=matrixsettings.defaultSpeciesImages[matrixsettings.imageOrientation];

	// inititializing scores, results and menu
	setCursor('wait');
	applyScores();
	clearResults();
	printResults();
	setCursor();
	retrieveMenu();

}

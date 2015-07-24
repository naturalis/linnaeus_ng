function __(text)
{
	// _() is sloooooow!
	return text;
	return _(text);
}

var settings={
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
	imageOrientation: "portrait",
	defaultSpeciesImages: {},
	defaultSpeciesImage: "",
	browseStyle: 'paginate', // expand, paginate, show_all
	expandedPrevious: 0,
	paginate: true,
	currPage: 0,
	lastPage: 0,
	scoreThreshold: 0,
	mode: "identify", // similar, search
	groupsAlwaysOpen: false
};

var data={
	menu: Array(), // full menu
	dataset: Array(), // full dataset
	resultset: Array(), // result subset
	states: {}, // user-selected states
	characters: {}, // remaining states/taxa per character
	scores: {}, // match scores based on selection
	related: {}, // related species
	found: {} // search results
}

var prevSettings={};

var initialize=true;
var lastScrollPos=0;
var tempstatevalue="";
var openGroups=Array();
var searchedfor="";


function retrieveDataSet()
{
	setCursor('wait');

	$.ajax({
		url : 'ajax_interface.php',
		type: 'POST',
		data : ({
			action : 'get_dataset',
			time : getTimestamp(),
			key : settings.matrixId,
			p : settings.projectId
		}),
		success : function ( d )
		{
			setDataSet($.parseJSON( d ));
			applyScores();
			clearResults();
			printResults();
			setCursor();
			
			if (initialize)
			{
				initialize=false;
				retrieveMenu();
			}
		}
	});
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
			key : settings.matrixId,
			p : settings.projectId
		}),
		success : function (data)
		{
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
	var resultset = getResultSet();

	// pre-emptively remove show_more-button (clicking similar automatically switches to browseStyle='show_all')
	$("#show-more").remove();
	$("#footerPagination").removeClass('noline');

	if (resultset && settings.browseStyle=='expand') 
	{
		printResultsExpanded();
	}
	else
	if (resultset && settings.browseStyle!='expand') // (non-)paginated
	{
		printResultsPaginated();
		if (settings.browseStyle=='paginate')
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
	usage of emergent characters can be turned off in project settings
	*/
	if (!settings.useEmergingCharacters) return false;
	
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
				if (settings.groupsAlwaysOpen) openGroups.push(item.id);
				
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
							.replace('%IMG-URL%',settings.imageRootSkin+'clearSelection.gif');
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
						.replace('%IMG-URL%',settings.imageRootSkin+'clearSelection.gif');
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
}

function clearResults()
{
	$('#results-container').html('');
	setSetting({expandedShowing:0});
}

function printHeader()
{
	if (settings.mode=="search")
	{
		printSearchHeader();
	}
	else
	if (settings.mode=="similar")
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
		if (i < settings.expandedShowing+settings.perPage)
		{
			s=s+formatResult(resultset[i]);

			printed++;

			if (++d==settings.perLine)
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
	//		.replace('%STYLE%',(settings.expandedShowing>0  ? 'display:none' : ''))
	//	$('.result-batch:hidden').show('normal');
	
	settings.expandedShowing=printed;

	if (settings.expandedShowing<resultset.length)
	{
		if (!$("#show-more-").is(':visible'))
		{
			$("#paging-footer").append( buttonMoreHtmlTpl.replace('%LABEL%',__('meer resultaten laden')) );
			$("#footerPagination").addClass('noline');
		}
	}
	
	if (settings.expandedShowing>0) 
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
			(settings.browseStyle=='paginate' && i>=settings.start && i<settings.start+settings.perPage) || 
			settings.browseStyle=='show_all'
		)
		{
			s=s+formatResult(resultset[i]);
			if (++d==settings.perLine)
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
	console.dir(data);
	
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

	if (settings.showSpeciesDetails && data.states)
	{
		var states = Array();

		for(var i in data.states)
		{
			var state=data.states[i];
			
			if (state.characteristic==undefined)
				continue;
			
			var labels = Array();
			
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
				labels.push(state.states[j].label);
			}

			if (labels.length>1)
			{
				var l = labels.join('; ');
			}
			else
			{
				var l = labels[0];
			}

			states.push(
				speciesStateItemHtmlTpl
					.replace('%GROUP%',state.group_label + ' > ')
					.replace('%CHARACTER%',t)
					.replace('%STATE%',l)
			);
		}
	}

	var image="";

	if (data.info && data.info.url_image)
	{
		image=data.info.url_image;
		if (image && !image.match(/^(http:\/\/|https:\/\/)/i)) image=settings.imageRootProject+image;
	}
	else
	{
		if (settings.defaultSpeciesImage) image=settings.defaultSpeciesImage;
	}

	var thumb="";

	if (data.info && (data.info.url_thumbnail || data.info.url_thumb))
	{
		thumb=data.info.url_thumbnail ? data.info.url_thumbnail : data.info.url_thumb;
		if (thumb && !thumb.match(/^(http:\/\/|https:\/\/)/i)) thumb=settings.imageRootProject+thumb;
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
					.replace('%IMG-SRC%', settings.imageRootSkin + data.gender.gender+'.png')
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
					.replace('%ICON-URL%', settings.imageRootSkin+data.gender.gender+'.png') 
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
					.replace('%TITLE%', nbcLabelExternalLink)
				: "")
			.replace('%REMOTE-LINK-ICON%', data.info && data.info.url_external_page ?
				iconUrlHtmlTpl.replace('%IMG-URL%',settings.imageRootSkin+"information_grijs.png") : "")
			.replace('%SHOW-STATES-CLASS%', showStates ? " icon-details" : " no-content")
			.replace('%SHOW-STATES-CLICK%', showStates ?  statesClickHtmlTpl.replace('%TITLE%',nbcLabelDetails) : "")
			.replace('%SHOW-STATES-ICON%', showStates ?
				iconInfoHtmlTpl.replace('%IMG-URL%',settings.imageRootSkin+"lijst_grijs.png") : "")
			.replace('%RELATED-CLASS%', data.related_count>0 ? " icon-resemblance" : " no-content")
			.replace('%RELATED-CLICK%', (data.related_count>0 ?  
				relatedClickHtmlTpl
					.replace('%TYPE%', data.type)
					.replace('%ID%', data.id)
					.replace('%TITLE%', nbcLabelSimilarSpecies)
				: "" )
			)
			.replace('%RELATED-ICON%', data.related_count>0 ?
				iconSimilarTpl.replace('%IMG-URL%',settings.imageRootSkin+"gelijk_grijs.png") : "")
			.replace('%STATES%', showStates ? statesHtmlTpl.replace( '%STATES%',states.join(statesJoinHtmlTpl)) : "")
			.replace(/%LOCAL-ID%/g,id)
			.replace(/%ID%/g,data.id)
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
	
	if (settings.browseStyle=='expand')
	{
		$('#result-count').html(
			counterExpandHtmlTpl
				.replace('%START-NUMBER%',(settings.expandedShowing > 1 ? "1-" : "" ))
				.replace('%NUMBER-SHOWING%',settings.expandedShowing)
				.replace('%FROM-LABEL%',__('van'))
				.replace('%NUMBER-TOTAL%',resultset.length)
		);
	}
	else
	if (settings.browseStyle=='paginate')
	{
		$('#result-count').html(
			counterPaginateHtmlTpl
				.replace('%FIRST-NUMBER%', (settings.start+1))
				.replace('%LAST-NUMBER%',(settings.start+settings.perPage))
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

	setSetting({lastPage:Math.ceil(resultset.length / settings.perPage)});
	setSetting({currPage:Math.floor(settings.start / settings.perPage)});

	if (settings.lastPage > 1 && settings.currPage!=0)
	{
		$("#paging-header").append( pagePrevHtmlTpl );
	}
	
	if (settings.lastPage>1)
	{ 
		for (var i=0;i<settings.lastPage;i++)
		{
			if (i==settings.currPage)
			{
				$("#paging-header").append( pageCurrHtmlTpl.replace('%NR%',(i+1)) );
			}
		    else
			{
				$("#paging-header").append( pageNumberHtmlTpl.replace('%NR%',(i+1)).replace('%INDEX%',i) );
			}
		}
	}

	if (settings.lastPage > 1 && settings.currPage<settings.lastPage-1)
	{
		$("#paging-header").append( pageNextHtmlTpl );
	}

	$("#paging-footer").html($("#paging-header").html());
}

function browsePage( id )
{
	if (id=='n')
		setSetting({start: settings.start+settings.perPage});
	else if (id=='p')
		setSetting({start: settings.start-settings.perPage});
	else if (!isNaN(id))
		setSetting({start:id * settings.perPage});
	else
		return;
			
//	nbcSaveSessionSetting('nbcStart',nbcStart);
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
			key : settings.matrixId,
			p : settings.projectId
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
			key : settings.matrixId,
			p : settings.projectId
		}),
		success : function(data)
		{
			var d=$.parseJSON(data);

			setScores(d.scores);
			setStates(d.states);
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
				if (score.id==item.id && score.type==item.type && (settings.scoreThreshold==0 || score.score>=settings.scoreThreshold))
				{
					resultset.push(item);
				}
			}
		}
	}
	
	setResultSet(resultset);

	setSetting({showSpeciesDetails: settings.alwaysShowDetails || (resultset.length <= settings.perPage)});
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
			key : settings.matrixId,
			p : settings.projectId
		}),
		success : function(data)
		{
			var related=$.parseJSON(data);
			setRelated(related);

			setPrevSettings();
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
			.replace('%SHOW-STATES-TEXT%', nbcLabelShowAll)
			.replace('%NUMBER-START%', settings.start+1)
			.replace('%NUMBER-END%', data.resultset.length)
	).removeClass('hidden').addClass('visible');
	
	$('.result-icon.related').find('img').remove();

}

function toggleAllDetails()
{
	if ($('.result-detail:visible').length < getResultSet().length)
	{
		$('.result-detail').toggle(true);
		$('#showAllLabel').html(nbcLabelHideAll);
	}
	else
	{
		$('.result-detail').toggle(false);
		$('#showAllLabel').html(nbcLabelShowAll);
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
			key : settings.matrixId,
			p : settings.projectId
		}),
		success : function(data)
		{
			//console.log(data);
			var found=$.parseJSON(data);
			setFound(found);

			setPrevSettings();
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
			.replace('%NUMBER-START%', settings.start+1)
			.replace('%NUMBER-END%', settings.expandedShowing)
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
	setSetting(getPrevSettings());
	setSetting({expandedShowing:settings.expandedShowing-settings.perPage});
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
 	$("a[rel^='prettyPhoto']").prettyPhoto({
		allow_resize:true,
		animation_speed:50,
 		opacity: 0.70, 
		show_title: false,
 		overlay_gallery: false,
 		social_tools: false
 	});

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

function setPrevSettings()
{
	prevSettings = jQuery.extend({}, settings);
}

function getPrevSettings()
{
	return prevSettings;
}

function setSetting( p )
{
	$.extend(settings, p);
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
    $("img").on("contextmenu",function(){
       return false;
    });
}

function showRestartButton()
{
	$('#clearSelectionContainer').removeClass('ghosted');
}




function matrixInit()
{
	nbcLabelClose = __('sluiten');
	nbcLabelDetails = __('onderscheidende kenmerken');
	nbcLabelBack = __('terug');
	nbcLabelSimilarSpecies = __('gelijkende soorten');
	nbcLabelShowAll = __('alle onderscheidende kenmerken tonen');
	nbcLabelHideAll = __('kenmerken verbergen');
	nbcLabelExternalLink = __('Meer informatie over soort/taxon');

	$('#legendDetails').html(nbcLabelDetails);
	$('#legendSimilarSpecies').html(nbcLabelSimilarSpecies);
	$('#legendExternalLink').html(nbcLabelExternalLink);

	settings.defaultSpeciesImage=settings.defaultSpeciesImages[settings.imageOrientation];

	/*
	if ("ontouchstart" in document) {
		// touch only code (tablets)
		$('#legendDivider').removeClass('hidden'); // show icon legend
		$('#legendContainer').removeClass('hidden'); // show icon legend
	} else {
		// "desktop" code
	}
	*/
}
